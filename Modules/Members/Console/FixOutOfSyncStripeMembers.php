<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MemberSelectOption;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Entities\MembershipTypeCategory;
use Modules\Members\Repositories\MemberRepository;
use Modules\Store\Enums\PaymentProvider;
use Stripe\Collection;
use Stripe\Subscription;

class FixOutOfSyncStripeMembers extends Command
{
    protected $signature = 'members:fix-out-of-sync-stripe-members {filename} {skip?}';

    public function handle()
    {
        $filename = $this->argument('filename');
        $path = storage_path("imports/{$filename}");

        ini_set('auto_detect_line_endings', TRUE);

        $handle = fopen($path, 'r');

        $skip = (int) $this->argument('skip') ?? 0;

        for ($i = 0; $i < $skip; $i++) {
            fgetcsv($handle);
        }

        while (($data = fgetcsv($handle)) !== FALSE) {
            $this->fixMember($data);
        }

        ini_set('auto_detect_line_endings', FALSE);
        fclose($handle);
    }

    private function fixMember($data)
    {
        $member = Member::findOrFail($data[0]);

        /** @var Collection $subscriptions */
        $subscriptions = $member->user->asStripeCustomer()['subscriptions'];

        if (!isset($subscriptions->data[0])) {
            throw new \Exception("No subscriptions found: : {$data[0]}");
        }

        /** @var Subscription $subscription */
        $subscription = $subscriptions->data[0];
        $stripeExpiresAt = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end);
        $stripeRenewedAt = \Carbon\Carbon::createFromTimestamp($subscription->current_period_start);

        if ($subscription['status'] !== 'active') {
            throw new \Exception("Subscription not active: {$data[0]}");
        }

        if ($member->payment_provider !== PaymentProvider::STRIPE || !$member->payment_is_recurring) {
            throw new \Exception("Not set to recurring stripe payments: {$data[0]}");
        }

        if ($member->expires_at->format('Y-m-d') === $stripeExpiresAt->format('Y-m-d')) {
            $this->info("Skipped: {$data[0]}");
        } else {
            $member->expires_at = $stripeExpiresAt->format('Y-m-d');
            $member->renewed_at = $stripeRenewedAt->format('Y-m-d');
            $member->save();

            $this->info("Fixed: {$data[0]}");
        }
    }
}
