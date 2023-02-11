<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FixExpiryDateDrift extends ProgressCommand
{
    protected $name = 'members:fix-expiry-date-drift';

    public function handle()
    {
        $members = Member::query()
            ->where('payment_provider', PaymentProvider::SMART_DEBIT)
            ->where('payment_is_recurring', 1)
            ->where('renewed_at', '2021-11-16')
            ->where('expires_at', '2022-11-16')
            ->get();

        $this->info(count($members));

        foreach ($members as $member) {
            $member->renewed_at = '2021-11-15';
            $member->expires_at = '2022-11-15';
            $member->save();
        }
    }
}
