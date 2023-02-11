<?php

namespace Modules\Store\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Services\SmartDebit;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateSmartDebitPrices extends Command
{
    protected $name = 'store:update-smart-debit-prices';

    private $smartDebit;

    const NOT_FOUND = 'not_found';
    const ALREADY_UPDATED = 'already_updated';
    const UPDATED = 'updated';
    const SPECIAL_CASE_WARNING = 'special_case_warning';

    public function __construct(SmartDebit $smartDebit)
    {
        parent::__construct();

        $this->smartDebit = $smartDebit;
    }

    public function handle()
    {
        $members = Member::query()
            ->where('payment_provider', PaymentProvider::SMART_DEBIT)
            ->where('payment_is_recurring', 1)
            ->where('membership_type_id', 6)
            ->get();

        $breakdown = [
            self::NOT_FOUND => [],
            self::ALREADY_UPDATED => [],
            self::UPDATED => [],
            self::SPECIAL_CASE_WARNING => []
        ];

        foreach ($members as $member) {
            $status = $this->update($member);
            $breakdown[$status][] = $member->id;
            sleep(1);
        }

        file_put_contents(storage_path('fishery_update_breakdown.json'), json_encode($breakdown));
    }

    private function update(Member $member)
    {
        $user = $member->user;
        $reference = $user->smart_debit_id;

        if (!$reference) {
            $this->error($member->id);
            return self::NOT_FOUND;
        }

        try {

            $payer = $this->smartDebit->getPayerByReference($reference)
                ->clean();

            $payer = $payer['Data']['PayerDetails']['@attributes'];

        } catch (\Exception $exception) {
            $this->error($member->id);
            return self::NOT_FOUND;
        }

        if ($payer['current_state'] !== '10') {
            $this->error('canceled');
            return self::NOT_FOUND;
        }

        $expected = [
            '£199.00', '£265.00', '£209.00'
        ];

        $amount = [
            'current' => $payer['regular_amount'],
            'new' => '£' . number_format($member->category->price / 100, 2)
        ];

        $debug = $this->getDebugText($member, $amount);

        if ($amount['current'] === $amount['new']) {
            $this->info($debug);
            return self::ALREADY_UPDATED;
        } else {
            if (in_array($amount['current'], $expected)) {
                $this->line($debug);
                $this->smartDebit->updatePrice($reference, $member->category->price);
                return self::UPDATED;
            } else {
                $this->error($debug);
                return self::SPECIAL_CASE_WARNING;
            }
        }
    }

    private function getDebugText(Member $member, $amount)
    {
        return $member->id . ' (' . $amount['current'] . ' ' . $amount['new'] . ')';
    }
}
