<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckExpiryDateDrift extends Command
{
    protected $name = 'members:check-expiry-date-drift';

    private $expires_at = [
        '01' => 0,
        '02' => 0,
        '03' => 0,
        '04' => 0,
        '05' => 0,
        '15' => 0,
        '16' => 0,
        '17' => 0,
        '18' => 0,
        '19' => 0
    ];

    private $renewed_at = [
        '01' => 0,
        '02' => 0,
        '03' => 0,
        '04' => 0,
        '05' => 0,
        '15' => 0,
        '16' => 0,
        '17' => 0,
        '18' => 0,
        '19' => 0
    ];

    public function handle()
    {
        $members = Member::query()
            ->where('payment_provider', PaymentProvider::SMART_DEBIT)
            ->where('payment_is_recurring', 1)
            ->where('membership_type_id', '!=', 18)
            ->get();

        foreach ($members as $member) {
            $this->checkExpiresAt($member);
            $this->checkRenewedAt($member);
        }

        $this->info('Expires At');
        foreach ($this->expires_at as $key => $value) {
            $this->line("$key => $value");
        }

        $this->line('');

        $this->info('Renewed At');
        foreach ($this->renewed_at as $key => $value) {
            $this->line("$key => $value");
        }
    }

    private function checkExpiresAt(Member $member)
    {
        $expires_at = $member->expires_at;

        $days = array_keys($this->expires_at);

        foreach ($days as $day) {
            if ($this->matches($day, $expires_at)) {
                $this->expires_at[$day] ++;
            }
        }
    }

    private function checkRenewedAt(Member $member)
    {
        if (!$renewed_at = $member->renewed_at) {
            return false;
        }

        $days = array_keys($this->renewed_at);

        foreach ($days as $day) {
            if ($this->matches($day, $renewed_at)) {
                $this->renewed_at[$day] ++;
            }
        }
    }

    private function matches($day, $date)
    {
        return preg_match("/\d{4}-\d{2}-$day/", $date->format('Y-m-d'));
    }
}
