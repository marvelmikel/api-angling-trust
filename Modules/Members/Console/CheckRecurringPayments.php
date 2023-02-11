<?php

namespace Modules\Members\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Store\Entities\Payment;

class CheckRecurringPayments extends Command
{
    protected $signature = 'members:check-recurring-payments';

    public function handle()
    {
        $expiredMembers = Member::whereHasStripePortal()
            ->where('expires_at', '<=', Carbon::now())
            ->where('membership_type_id', '!=', 18)
            ->get();

        foreach ($expiredMembers as $member) {
            $local_subscription = $member->user->subscription('default');

            if ($local_subscription) {
                $this->info('Yes');
            } else {
                $this->info('No');
            }
        }
    }
}
