<?php declare(strict_types=1);

namespace Modules\Members\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Modules\Members\Entities\MemberIndex;

class MembershipPriceUpdater
{
    private const CAT__ADULT = 22;
    private const CAT__JUNIOR = 23;
    private const CAT__SENIOR = 25;

    private const KEY__KNOWN = 'known';
    private const KEY__UNKNOWN = 'unknown';

    private const FILE__OUTPUT_TEMPLATE = ('payer-template.csv');
    private const FILE__INPUT_DATA = ('Report2022-05-10.csv');
    private const FILE__OUTPUT_DATA__IGNORED = ('output--ignored.csv');
    private const FILE__OUTPUT_DATA__MODIFIED = ('output--modified.csv');

    private Collection $inputData;
    /**
     * @var Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private \Illuminate\Database\Eloquent\Collection $memberIndexesByReference;

    /**
     * @var Collection[]|Collection
     */
    private Collection $matchedImportData;
    /**
     * @var Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private $memberIndexesByEmail;
    private Collection $filteredData;

    public function generate(): void
    {
        $this->fetchInputData();
        $this->filterInputData();
        $this->findMemberIndexesByReference();
        $this->findMemberIndexesByEmail();
        $this->matchInputData();
        $this->outputKnownMembers();
        $this->outputUnknownMembers();
    }

    private function path(string $file): string
    {
        return storage_path(sprintf('membership-price-update/%s', $file));
    }

    private function fetchInputData(): void
    {
        $handle = fopen($this->path(self::FILE__INPUT_DATA), 'r');
        $headers = fgetcsv($handle);
        $this->inputData = new Collection();
        while ($line = fgetcsv($handle)) {
            $this->inputData->push(collect(array_combine($headers, $line)));
        }

        fclose($handle);
    }

    private function filterInputData(): void
    {
        $grouped = $this->inputData->groupBy(
            function (Collection $row) {
                // If they are paying OVER £50/year and they HAVE entered a company name, assume they are not individual
                if ($row->get('CompanyName') !== '' && $row->get('RegularAmount') > 50) {
                    return 'remove';
                }

                // If they are paying OVER £100/year, assume they are not individual
                if ($row->get('RegularAmount') > 100) {
                    return 'remove';
                }

                return 'include';
            }
        );

        $this->inputData = $grouped->get('include');
        $this->filteredData = $grouped->get('remove');
    }


    private function trimReference(string $reference): string
    {
        return preg_replace('/^(?:0+|i)(\d)/', '$1', $reference);
    }

    private function findMemberIndexesByReference(): void
    {

        $this->memberIndexesByReference = $this->memberIndexQuery()
            ->where(function (Builder $query) {
                $customerIds = $this->inputData->map(
                    fn(Collection $row) => $this->trimReference($row->get('CustomerId'))
                )->filter()->values();
                $bacsReferences = $this->inputData->map(
                    fn(Collection $row) => $this->trimReference($row->get('BacsReference'))
                )->filter()->values();
                $query->whereIn('reference', $customerIds)
                    ->orWhereIn('reference', $bacsReferences);
        })
            ->get()
            ->keyBy('reference');
    }

    private function findMemberIndexesByEmail(): void
    {
        $emails = $this->inputData->map(
            fn(Collection $row) => $row->get('EmailAddress')
        )->filter()->values();
        $knownIds = $this->memberIndexesByReference->pluck('id');
        $this->memberIndexesByEmail = $this->memberIndexQuery()
            ->whereNotIn('id', $knownIds)
            ->whereIn('email', $emails)
            ->get()
            ->keyBy('email');
    }

    private function memberIndexQuery(): Builder
    {
        return MemberIndex::with([
            'member' => fn(BelongsTo $builder) => $builder->whereIn('category_id', [
                MembershipPriceUpdater::CAT__ADULT,
                MembershipPriceUpdater::CAT__JUNIOR,
                MembershipPriceUpdater::CAT__SENIOR
            ]),
            'member.category',
        ])->where('membership_type_slug', 'individual-member');
    }

    private function matchInputData(): void
    {
        $this->matchedImportData = $this->inputData->groupBy(
            function (Collection $row) {
                $bacsReference = $this->trimReference($row->get('BacsReference'));
                $customerId = $this->trimReference($row->get('CustomerId'));

                return $this->memberIndexesByReference->has($bacsReference) ||
                    $this->memberIndexesByReference->has($customerId) ? self::KEY__KNOWN : self::KEY__UNKNOWN;
            }
        );

        $unknowns = $this->matchedImportData->get(self::KEY__UNKNOWN);

        $unknowns->each(function(Collection $row, int $ix) {
            $emailAddress = $this->trimReference($row->get('EmailAddress'));
            $this->memberIndexesByEmail->forget('admin@anglingtrust.net');

            if ($this->memberIndexesByEmail->has($emailAddress)) {
                $this->matchedImportData->get(self::KEY__KNOWN)->push($row);
                $this->matchedImportData->get(self::KEY__UNKNOWN)->forget($ix);
            }
        });

        $unknowns->each(function(Collection $row, int $ix) {
            if (collect([10, 25, 29])->contains((int) $row->get('RegularAmount'))) {
                $this->matchedImportData->get(self::KEY__KNOWN)->push($row);
                $this->matchedImportData->get(self::KEY__UNKNOWN)->forget($ix);
            }
        });
    }

    private function getOutputHeaders(): array
    {
        $handle = fopen($this->path(self::FILE__OUTPUT_TEMPLATE), 'r');
        $outputHeaders = fgetcsv($handle);
        fclose($handle);

        return $outputHeaders;
    }

    private function outputKnownMembers(): void
    {
        $this->outputMembers(
            self::FILE__OUTPUT_DATA__MODIFIED,
            $this->matchedImportData->get(self::KEY__KNOWN),
            1
        );
    }

    private function outputUnknownMembers(): void
    {
        $this->outputMembers(
            self::FILE__OUTPUT_DATA__IGNORED,
            $this->matchedImportData->get(
                self::KEY__UNKNOWN)->concat($this->filteredData)
        );
    }

    private function outputMembers(string $file, Collection $members, int $amountModifier = 0): void
    {
        $handle = fopen($this->path($file), 'w');

        $members->each(fn (Collection $row) => fputcsv(
            $handle,
            $this->rowToOutput($row, $amountModifier)
        ));
        fclose($handle);
    }

    private function rowToOutput(Collection $smartDebit, int $amountModifier = 0): array
    {
        $regularAmount = (float) str_replace(',', '', $smartDebit->get('RegularAmount'));

        return [
                '',
                $smartDebit->get('BacsReference'),
                $smartDebit->get('AccountName'),
                $smartDebit->get('SortCode'),
                $smartDebit->get('AccountNumber'),
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $smartDebit->get('Title'),
                $smartDebit->get('FirstName'),
                $smartDebit->get('LastName'),
                $smartDebit->get('CompanyName'),
                $smartDebit->get('Address1'),
                $smartDebit->get('Address2'),
                $smartDebit->get('Address3'),
                $smartDebit->get('Town'),
                $smartDebit->get('County'),
                $smartDebit->get('Postcode'),
                $smartDebit->get('Country'),
                '',
                '',
                $smartDebit->get('EmailAddress'),
                '',
                $smartDebit->get('Frequency'),
                number_format($regularAmount + $amountModifier, 2),
                $smartDebit->get('FirstAmount') ? number_format((float) $smartDebit->get('FirstAmount'), 2) : '',
                '',
                '',
                Carbon::createFromFormat('d/m/Y', $smartDebit->get('StartDate'))->format('Y'),
                Carbon::createFromFormat('d/m/Y', $smartDebit->get('StartDate'))->format('m'),
                Carbon::createFromFormat('d/m/Y', $smartDebit->get('StartDate'))->format('d'),
                $smartDebit->get('EndDate') ? Carbon::createFromFormat('d/m/Y', $smartDebit->get('EndDate'))->format('Y') : '',
                $smartDebit->get('EndDate') ? Carbon::createFromFormat('d/m/Y', $smartDebit->get('EndDate'))->format('m') : '',
                $smartDebit->get('EndDate') ? Carbon::createFromFormat('d/m/Y', $smartDebit->get('EndDate'))->format('d') : '',
                '',
                '',
                '',
                $smartDebit->get('FrequencyFactor'),
                '',
                '',
            ];
    }

    private function initOutputFile()
    {
        $handle = fopen($this->path(self::FILE__OUTPUT_DATA), 'w');
        fputcsv($handle, $this->getOutputHeaders());
        fclose($handle);
    }
}
