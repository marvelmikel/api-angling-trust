<?php

namespace Modules\Store\Console;

use Illuminate\Console\Command;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Services\SmartDebit;

class CheckLinkedSmartDebitMembers extends Command
{
    protected $name = 'store:check-linked-smart-debit-members';

    private const BATCH_SIZE = 200;
    private const STATE_CANCELLED = '11';
    private const STATE_LIVE = '10';

    private $smartDebit;

    public function __construct(SmartDebit $smartDebit)
    {
        parent::__construct();

        $this->smartDebit = $smartDebit;
    }

    public function handle()
    {
        $audit = fopen(storage_path('exports/sd-audit.csv'), 'w');
        fwrite($audit, "ID,User ID,Reference,Name,Email,Bacs Ref,Email Check,Name Check,Status Check\n");

        $done = false;
        $batch = 1;

        while (!$done) {
            $members = $this->getMembers($batch);

            foreach ($members as $index => $member) {
                $line = '';

                $breakdown = $this->getBreakdown($member);

                foreach ($breakdown as $col) {
                    $line .= '"' . $col . '",';
                }

                $line = trim($line, ',');
                $line .= "\n";

                fwrite($audit, $line);
            }

            if (count($members) < self::BATCH_SIZE) {
                $done = true;
            }

            $this->info("Batch $batch Complete");
            $batch++;
        }

        fclose($audit);
    }

    private function getMembers($batch)
    {
        return Member::query()
            ->where('payment_provider', PaymentProvider::SMART_DEBIT)
            ->where('payment_is_recurring', 1)
            ->where('membership_type_id', '!=', 18)
            ->where('at_member', 1)
            ->skip(self::BATCH_SIZE * ($batch - 1))
            ->take(self::BATCH_SIZE)
            ->get();
    }

    private function getSmartDebitRecord($reference)
    {
        $record = $this->smartDebit->getPayerByReference($reference)
            ->parsed();

        return json_decode(json_encode($record['Data']->PayerDetails), true)['@attributes'];
    }

    private function getBreakdown(Member $member)
    {
        $user = $member->user;

        return array_merge([
            'id' => $member->id,
            'user_id' => $user->id,
            'reference' => $user->reference,
            'name' => $member->full_name,
            'email' => $user->email,
            'smart_debit_id' => $user->smart_debit_id,
        ], $this->getScore($member, $user));
    }

    private function getScore(Member $member, User $user)
    {
        $total = 0;
        $failed = [
            'email_check' => '',
            'name_check' => '',
            'status_check' => ''
        ];

        try {
            $record = $this->getSmartDebitRecord($user->smart_debit_id);
        } catch (\Exception $exception) {
            return array_merge([
                'score' => 99,
            ], $failed);
        }

        if (!$this->emailMatches($user->email, $record['email_address'])) {
            $total += 1;
            $failed['email_check'] = "{$user->email} !== {$record['email_address']}";
        }

        if (!$this->nameMatches($member->full_name, $record['account_name'])) {
            $total += 1;
            $failed['name_check'] = "{$member->full_name} !== {$record['account_name']}";
        }

        if (!$this->statusMatches($member->hasExpired(), $record['current_state'])) {
            $total += 1;
            $expired = $member->hasExpired() ? 'Expired' : 'Active';
            $failed['status_check'] = "{$expired} !== {$record['current_state']}";
        }

        return array_merge([
            'score' => $total,
        ], $failed);
    }

    private function emailMatches($user_email, $record_email)
    {
        return $user_email === $record_email;
    }

    private function nameMatches($member_name, $record_name)
    {
        $member_name_parts = explode(' ', trim(strtolower($member_name)));

        foreach ($member_name_parts as $part) {
            if (strlen($part) > 2) {
                if (strpos(strtolower($record_name), $part) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private function statusMatches($expired, $record_status)
    {
        if ($expired && $record_status === self::STATE_LIVE) {
            return false;
        }

        if (!$expired && $record_status === self::STATE_CANCELLED) {
            return false;
        }

        return true;
    }
}
