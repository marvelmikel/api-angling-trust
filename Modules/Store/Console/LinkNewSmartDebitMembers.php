<?php

namespace Modules\Store\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Store\Entities\Payment;
use Modules\Store\Enums\PaymentProvider;

class LinkNewSmartDebitMembers extends Command
{
    protected $name = 'store:link-new-smart-debit-members';

    private $payers;
    private $counts = [];

    private const STATE_CANCELLED = '11';

    public function handle()
    {
        $this->payers = $this->getPayers();

        foreach ($this->getMembers() as $member) {
            $matches = $this->getMatches($member);
            $count = count($matches);

            if ($count === 1) {
                $this->linkMember($member, $matches[0]);
            }

            if (!isset($this->counts[$count])) {
                $this->counts[$count] = 1;
            } else {
                $this->counts[$count]++;
            }
        }

        $this->outputFinalCount();
    }

    private function getPayers()
    {
        return json_decode(
            file_get_contents(__DIR__ . '/data')
        );
    }

    private function getMembers()
    {
        return Member::query()
            ->where('payment_provider', PaymentProvider::SMART_DEBIT)
            ->where('payment_is_recurring', 1)
            ->where('is_imported', 0)
            ->where('membership_type_id', '!=', 18)
            ->get();
    }

    private function getMatches(Member $member)
    {
        $payment = Payment::query()
            ->where('member_id', $member->id)
            ->where('payment_provider', PaymentProvider::SMART_DEBIT)
            ->where('auto_renew', 1)
            ->whereNotNull('completed_at')
            ->first();

        if (!$payment) {
            return [];
        }

        $matches = [];

        foreach($this->payers as $payer) {
            if ($payer->current_state === self::STATE_CANCELLED) {
                continue;
            }

            if ($payer->reference_number === $payment->reference) {
                $matches[] = $payer;
                continue;
            }
        }

        return $matches;
    }

    private function outputFinalCount()
    {
        ksort($this->counts);

        $this->info('All Batches Complete');

        $total = 0;

        foreach($this->counts as $index => $count) {
            $total += $count;
        }

        foreach($this->counts as $index => $count) {
            $percentage = number_format(($count / $total) * 100, 2);
            $this->line("[$index] $count ({$percentage}%)");
        }
    }

    private function linkMember(Member $member, $match)
    {
        $user = $member->user;

        $user->smart_debit_id = $match->reference_number;
        $user->smart_debit_frequency = $match->frequency_type;
        $user->save();
    }
}
