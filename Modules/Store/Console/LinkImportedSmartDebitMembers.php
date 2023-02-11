<?php

namespace Modules\Store\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Services\SmartDebit;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LinkImportedSmartDebitMembers extends Command
{
    protected $name = 'store:link-imported-smart-debit-members';

    private $payers;
    private $counts = [];

    private const BATCH_SIZE = 500;
    private const STATE_CANCELLED = '11';

    public function handle()
    {
        $this->payers = $this->getPayers();

        $done = false;
        $batch = 1;

        while (!$done) {
            $members = $this->getMembers($batch);

            foreach ($members as $member) {
                $matches = $this->getMatches($member, $member->user);
                $count = count($matches);

                if (!isset($this->counts[$count])) {
                    $this->counts[$count] = 1;
                } else {
                    $this->counts[$count]++;
                }

                if ($count === 1) {
                    $this->linkMember($member, $matches[0]);
                }
            }

            if (count($members) < self::BATCH_SIZE) {
                $done = true;
            }

            $this->info("Batch $batch Complete");
            $this->outputCurrentCount();

            $batch++;
        }

        $this->outputFinalCount();
    }

    private function outputCurrentCount()
    {
        ksort($this->counts);

        foreach($this->counts as $index => $count) {
            $this->line("[$index] $count");
        }
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

    private function getPayers()
    {
        return json_decode(
            file_get_contents(__DIR__ . '/data')
        );
    }

    private function getMembers($batch)
    {
        return Member::query()
            ->where('payment_provider', PaymentProvider::SMART_DEBIT)
            ->where('payment_is_recurring', 1)
            ->where('is_imported', 1)
            ->where('membership_type_id', '!=', 18)
            ->skip(self::BATCH_SIZE * ($batch - 1))
            ->take(self::BATCH_SIZE)
            ->get();
    }

    private function getMatches(Member $member, User $user)
    {
        $matches = [];

        foreach($this->payers as $payer) {
            if ($payer->current_state === self::STATE_CANCELLED) {
                continue;
            }

            if ($payer->payerReference === $user->reference) {
                $matches[] = $payer;
                continue;
            }

            if ($payer->payerReference === "0{$user->reference}") {
                $matches[] = $payer;
                continue;
            }

            if ($payer->payerReference === "00{$user->reference}") {
                $matches[] = $payer;
                continue;
            }

            if ($payer->reference_number === $user->reference) {
                $matches[] = $payer;
                continue;
            }

            if ($payer->reference_number === "0{$user->reference}") {
                $matches[] = $payer;
                continue;
            }

            if ($payer->reference_number === "00{$user->reference}") {
                $matches[] = $payer;
                continue;
            }
        }

        return $matches;
    }

    private function linkMember(Member $member, $match)
    {
        $user = $member->user;

        $user->smart_debit_id = $match->reference_number;
        $user->smart_debit_frequency = $match->frequency_type;
        $user->save();
    }
}
