<?php declare(strict_types=1);

namespace Modules\Store\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Store\Entities\Payment;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Repositories\PaymentRepository;
use Modules\Store\Services\SmartDebit;
use SimpleXMLElement;

class UpdateSmartDebitMemberships extends Command
{
    protected $signature = 'store:update-smart-debit-memberships {--days-ago=5} {--date=}';

    private SmartDebit $smartDebit;
    private string $date;

    private array $updates = [
        'not_found' => [],
        'new' => [],
        'existing' => [],
        'cancelled' => []
    ];

    public function __construct(SmartDebit $smartDebit)
    {
        parent::__construct();
        $this->smartDebit = $smartDebit;
    }

    public function info($string, $verbosity = null)
    {
        parent::info(
            sprintf('[%s] %s', Carbon::now()->format('Y-m-d H:i:s'), $string),
            $verbosity
        );
    }

    public function handle()
    {
        $this->date = $this->getDate();

        $this->info(sprintf('Fetching payments taken on %s', $this->date));

        $response = $this->smartDebit->getPaymentsTakenOn($this->date)->parsed();
        if (!isset($response['Successes']) && !isset($response['Rejects'])) {
            $this->info('No payments found');

            return;
        }

        $this->info('Processing successes');
        foreach ($response['Successes'] ?? [] as $xml) {
            $data = $this->parseData($xml);
            if ($member = $this->getMemberFromResponse($data)) {
                $this->handleSuccess($member, $data);
            }
        }
        $this->info('Finished processing successes');

        $this->info('Processing rejects');
        foreach ($response['Rejects'] ?? [] as $xml) {
            $data = $this->parseData($xml);
            if ($member = $this->getMemberFromResponse($data)) {
                $this->handleReject($member, $data);
            }
        }
        $this->info('Finished processing rejects');

        file_put_contents(storage_path('exports/sd-changes.json'), json_encode($this->updates));
    }

    private function parseData(SimpleXMLElement $xml): array
    {
        $data = [];

        foreach ($xml->attributes() as $key => $value) {
            $data[$key] = (string) $value;
        }

        return $data;
    }

    private function handleSuccess(Member $member, array $data): void
    {
        $reference = $data['reference_number'];

        $this->info(sprintf('Processing success for member \'%s\'', $reference));

        $amount = (int) ((float) $data['amount'] * 100);

        $this->addSuccessfulPaymentRecord($member, $reference, $amount);
    }

    private function addSuccessfulPaymentRecord(Member $member, string $reference, int $amount)
    {
        $payment = $this->getPayment($member, $reference, $amount);

        $payment->amount = $amount / 100;
        $payment->completed_at = $this->date;
        $payment->save();
    }

    private function getPayment(Member $member, string $reference, int $amount): Payment
    {
        $payment = Payment::query()
            ->where('member_id', $member->id)
            ->where('reference', $reference)
            ->first();

        if (!$payment) {
            $this->updates['new'][] = [
                'member' => $member->id,
                'reference' => $reference
            ];

            return PaymentRepository::createPaymentRecordForSmartDebit(
                sprintf("%s (%s)", PaymentPurpose::MEMBERSHIP, $member->membershipType->name),
                $amount,
                $member,
            );
        }

        $this->updates['existing'][] = [
            'member' => $member->id,
            'reference' => $reference
        ];

        return $payment;
    }

    private function handleReject(Member $member, array $data): void
    {
        $reference = $data['reference_number'];

        if ($member->hasExpired(Carbon::parse($this->date))) {
            $this->info(sprintf('Member \'%s\' already expired', $reference));

            return;
        }

        $this->updates['cancelled'][] = [
            'member' => $member->id,
            'reference' => $reference
        ];

        $this->rollbackMember($member);
        $this->addFailedPaymentRecord($member);
        $this->info(sprintf('Rolled back member \'%s\'', $reference));
    }

    private function getMemberBySmartDebitReference($ref): ?Member
    {
        $user = User::query()
            ->where('smart_debit_id', $ref)
            ->first();

        if (!$user) {
            return null;
        }

        return Member::query()
            ->where('user_id', $user->id)
            ->first();
    }

    private function rollbackMember(Member $member): void
    {
        $user = $member->user;

        $member->expires_at = $this->date;
        $member->payment_is_recurring = false;

        if ($member->renewed_at !== null) {
            if ($user->smart_debit_frequency === 'M') {
                $member->renewed_at = Carbon::createFromFormat('Y-m-d', $this->date)->subMonth()->format('Y-m-d');
            } else {
                $member->renewed_at = Carbon::createFromFormat('Y-m-d', $this->date)->subYear()->format('Y-m-d');
            }
        }

        $member->save();

    }

    private function addFailedPaymentRecord(Member $member)
    {
        PaymentRepository::createSmartDebitFailedPaymentRecord($member);
    }

    private function getMemberFromResponse(array $data): ?Member
    {
        $reference = $data['reference_number'];
        $member = $this->getMemberBySmartDebitReference($reference);

        if (!$member) {
            $this->warn(sprintf('Unknown member \'%s\'', $reference));
            $this->updates['not_found'][] = [
                'reference' => $reference
            ];

            return null;
        }

        return $member;
    }

    private function getDate(): string
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : null;

        if ($date === null) {
            $daysAgo = (int) $this->option('days-ago');
            $date = now()->subDays($daysAgo);
        }

        return $date->format('Y-m-d');
    }

}
