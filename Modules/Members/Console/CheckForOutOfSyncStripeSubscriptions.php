<?php

namespace Modules\Members\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Stripe\Collection;
use Stripe\Subscription;

class CheckForOutOfSyncStripeSubscriptions extends Command
{
    protected $name = 'members:check-for-out-of-sync-stripe-subscriptions';
    private $fails = [];

    public function handle()
    {
        $members = Member::query()
            ->where('payment_provider', PaymentProvider::STRIPE)
            ->where('payment_is_recurring', true)
            ->where('is_imported', true)
            ->get();

        foreach ($members as $member) {
            $this->checkMember($member);
        }

        $export = fopen(storage_path('exports/stripe-sync-errors.csv'), 'w');

        fputcsv($export, ['ID', 'Reference', 'Email', 'Website Expiry', 'Website Link', 'Stripe Expiry', 'Stripe Link', 'Difference (Days)']);

        foreach ($this->fails as $fail) {
            fputcsv($export, $fail);
        }

        fclose($export);
    }

    private function checkMember(Member $member)
    {
        /** @var Collection $subscriptions */
        $subscriptions = $member->user->asStripeCustomer()['subscriptions'];

        if (!isset($subscriptions->data[0])) {
            return;
        }

        /** @var Subscription $subscription */
        $subscription = $subscriptions->data[0];
        $subscriptionExpiresAt = Carbon::createFromTimestamp($subscription->current_period_end);

        if ($subscription['status'] !== 'active') {
            $this->info("[{$member->id}] Skipped");
            return;
        }

        if ($subscriptionExpiresAt->format('Y-m-d') === $member->expires_at->format('Y-m-d')) {
            $this->info("[{$member->id}] Correct");
        } else {
            $diff = $subscriptionExpiresAt->diffInDays($member->expires_at) + 1;
            $this->error("[{$member->id}] Out by $diff days");

            $this->fails[] = [
                'id' => $member->id,
                'reference' => $member->user->reference,
                'email' => $member->user->email,
                'website_expiry' => $member->expires_at->format('Y-m-d'),
                'website_link' => env('WP_URL') . "/wp/wp-admin/admin.php?page=at-members&id={$member->id}",
                'stripe_expiry' => $subscriptionExpiresAt->format('Y-m-d'),
                'stripe_link' => "https://dashboard.stripe.com/customers/{$member->user->stripe_id}",
                'difference' => $diff
            ];
        }
    }
}
