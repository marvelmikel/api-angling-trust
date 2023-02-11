<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Entities\MembershipTypeCategory;
use Modules\Store\Entities\Payment;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Services\SmartDebit;
use Modules\Store\Services\StripeSubscription;
use Stripe\StripeClient;

class ConvertAdultMembersToSeniorCitizens extends Command
{
    protected $signature = 'members:convert-adult-members-to-senior-citizens {offset=0} {--debug}';

    private $smartDebit;
    private $stripe;

    private $debug;
    private $targetCategory;

    public function __construct(SmartDebit $smartDebit)
    {
        $this->smartDebit = $smartDebit;

        parent::__construct();
    }

    public function handle()
    {
        $this->stripe = new StripeClient(
            config('stripe.secret')
        );

        $this->debug = $this->option('debug');
        $offset = (int) $this->argument('offset');

        $membershipType = MembershipType::query()
            ->where('slug', 'individual-member')
            ->firstOrFail();

        $this->targetCategory = MembershipTypeCategory::query()
            ->where('membership_type_id', $membershipType->id)
            ->where('slug', 'senior-citizen')
            ->firstOrFail();

        $category = MembershipTypeCategory::query()
            ->where('membership_type_id', $membershipType->id)
            ->where('slug', 'adult')
            ->firstOrFail();

        $members = Member::query()
            ->where('membership_type_id', $membershipType->id)
            ->where('category_id', $category->id)
            ->whereHas('meta', function(Builder $query) use ($offset) {
                $cutoff = now()->subYears(65)->subDays($offset);

                $query
                    ->where('key', 'date_of_birth')
                    ->where('value', '{"day":"' . $cutoff->format('d') . '","month":"' . $cutoff->format('m') . '","year":"' . $cutoff->format('Y') . '"}');
            })
            ->get();

        $total = count($members);

        $this->line("Found {$total} members");

        foreach ($members as $member) {
            if ($this->convertMember($member)) {
                $this->info(" - Converted: {$member->full_name} ({$member->user->reference})");
            } else {
                $this->error(" - Failed: {$member->full_name} ({$member->user->reference})");
            }
        }
    }

    private function convertMember(Member $member)
    {
        try {

            if ($member->payment_is_recurring) {
                if (!$this->downgradeSubscription($member)) {
                    return false;
                }
            }

            $member->category_id = $this->targetCategory->id;
            $member->save();

            return true;

        } catch (\Exception $exception) {
            if ($this->debug) {
                throw new \Exception($exception->getMessage());
            }

            return false;
        }
    }

    private function downgradeSubscription(Member $member)
    {
        if ($member->payment_provider === PaymentProvider::STRIPE) {
            return $this->downgradeStripeSubscription($member);
        }

        if ($member->payment_provider === PaymentProvider::SMART_DEBIT) {
            return $this->downgradeSmartDebitSubscription($member);
        }

        throw new \Exception("Payment provider not found: {$member->payment_provider}");
    }

    private function downgradeStripeSubscription(Member $member)
    {
        $customer_id = $member->user->stripe_id;

        $subscription = $this->stripe->subscriptions->all([
            'customer' => $customer_id
        ])->data[0];

        $sub_id = $subscription->id;
        $item_id = $subscription->items->data[0]->id;

        $plan_id = StripeSubscription::getPlanForCategory($this->targetCategory)->api_id;

        $this->stripe->subscriptions->update($sub_id, [
            'cancel_at_period_end' => false,
            'proration_behavior' => 'create_prorations',
            'items' => [
                [
                    'id' => $item_id,
                    'price' => $plan_id,
                ],
            ],
        ]);

        return true;
    }

    private function downgradeSmartDebitSubscription(Member $member)
    {
        $payments = Payment::query()
            ->where('member_id', $member->id)
            ->where('payment_provider', PaymentProvider::SMART_DEBIT)
            ->where('purpose' , 'Membership (Individual)')
            ->where('auto_renew', 1)
            ->get();

        if (count($payments) > 1) {
            throw new \Exception("Multiple possible payments found");
        }

        if (count($payments) === 0) {
            throw new \Exception("No possible payments found");
        }

        $payment = $payments[0];
        $reference = $payment->reference;

        $new_amount = $this->targetCategory->price;

        $response = $this->smartDebit->updateRecurringPayment($reference, [
            'regular_amount' => $new_amount,
        ]);

        return $response->success();
    }
}
