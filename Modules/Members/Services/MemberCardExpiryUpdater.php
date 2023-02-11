<?php

namespace Modules\Members\Services;

use Modules\Members\Entities\Member;
use Stripe\StripeClient;

class MemberCardExpiryUpdater
{
    public static function update(Member $member)
    {
        $stripe = new StripeClient(config('stripe.secret'));

        if (!$customer = $stripe->customers->retrieve($member->user->stripe_id)) {
            return false;
        }

        if (!$methodId = $customer->invoice_settings->default_payment_method) {
            return false;
        }

        $paymentMethod = $stripe->paymentMethods->retrieve($methodId);

        $member->card_expires_month = $paymentMethod->card->exp_month;
        $member->card_expires_year = $paymentMethod->card->exp_year;

        return $member->save();
    }
}
