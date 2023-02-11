<?php

namespace Modules\Store\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipTypeCategory;
use Modules\Store\Entities\StripeSubscriptionPlan;
use Modules\Store\Enums\PaymentProvider;
use Stripe\Collection;
use Stripe\Stripe;
use Stripe\Subscription;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ConvertStripeSubscriptionPrice extends Command
{
    protected $name = 'store:convert-stripe-subscription-price';

    private $stripe;
    private $from;
    private $to;

    public function handle()
    {
        $this->stripe = new \Stripe\StripeClient(
            config('stripe.secret')
        );

        $category_id = 54;

        $this->from = ['price_1H7epICZMJcBKVwSoLTlHc0o', 'price_1H7epICZMJcBKVwSm9M05CxF', 'price_1JUoNmCZMJcBKVwS3U8bdksY'];
        $this->to = 'price_1KC46lCZMJcBKVwSZ2hMRlh3';

        $category = MembershipTypeCategory::findOrFail($category_id);

        $members = Member::query()
            ->where('category_id', $category->id)
            ->where('payment_provider', PaymentProvider::STRIPE)
            ->where('payment_is_recurring', 1)
            ->get();

        foreach ($members as $member) {
            $this->updatePrice($member);
        }
    }

    private function updatePrice(Member $member)
    {
        /** @var Collection $subscriptions */
        $subscriptions = $member->user->asStripeCustomer()['subscriptions'];

        if (!isset($subscriptions->data[0])) {
            throw new \Exception("No subscriptions found: {$member->id}");
        }

        /** @var Subscription $subscription */
        $subscription = $subscriptions->data[0];
        $this->line($subscription->id);

        $current = $subscription->plan->id;

        if (!in_array($current, $this->from)) {
            if ($current === $this->to) {
                $this->line("#{$member->id} {$subscription->id} skipping");
                return;
            }

            throw new \Exception("Expected price '...' but got '{$current}'");
        }

        $this->stripe->subscriptions->update($subscription->id, [
            'cancel_at_period_end' => false,
            'proration_behavior' => 'none',
            'items' => [
                [
                    'id' => $subscription->items->data[0]->id,
                    'price' => $this->to,
                ],
            ],
        ]);

        $this->info("#{$member->id} {$subscription->id} updated");
    }
}
