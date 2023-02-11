<?php

namespace Modules\Members\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Services\WPNotification;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class NotifyMembersOfExpiringCards extends Command
{
    protected $name = 'members:notify-members-of-expiring-cards';

    public function handle()
    {
        $members = Member::query()
            ->where('payment_provider', PaymentProvider::STRIPE)
            ->where('payment_is_recurring', true)
            ->where('is_imported', false)
            ->where('expires_at', now()->addDays(30)->format('Y-m-d'))
            ->get();

        foreach ($members as $member) {
            $this->checkMember($member);
        }
    }

    private function checkMember(Member $member)
    {
        if (!$member->card_expires_month && !$member->card_expires_year) {
            return false;
        }

        $cardExpires = Carbon::createFromFormat('Y-n-d', $member->card_expires_year . '-' . $member->card_expires_month . '-01')
            ->endOfMonth();

        if ($member->expires_at < $cardExpires) {
            return false;
        }

        WPNotification::sendCustomerNotification('cm-stripe-payment-issue', $member->user->email);

        return true;
    }
}
