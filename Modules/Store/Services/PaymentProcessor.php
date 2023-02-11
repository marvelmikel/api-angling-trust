<?php

namespace Modules\Store\Services;

use Cache;
use Laravel\Cashier\Exceptions\PaymentActionRequired;
use Laravel\Cashier\Exceptions\PaymentFailure;
use Modules\Members\Entities\Member;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Repositories\PaymentRepository;

class PaymentProcessor
{
    private SmartDebit $smartDebit;

    public function __construct(SmartDebit $smartDebit)
    {
        $this->smartDebit = $smartDebit;
    }

    public function process(Member $member, $donation = null): bool
    {
        $price = $member->category->price;

        if ($member->payment_provider == PaymentProvider::SMART_DEBIT) {
            if ($donation) {
                return $this->processSmartDebitPaymentWithDonation($member, $price, $donation);
            } else {
                return $this->processSmartDebitPayment($member, $price);
            }
        }

        if ($member->payment_provider == PaymentProvider::STRIPE) {
            if ($member->payment_is_recurring) {
                if ($donation) {
                    return $this->processRecurringStripePaymentWithDonation($member, $price, $donation);
                } else {
                    return $this->processRecurringStripePayment($member, $price);
                }
            } else {
                if ($donation) {
                    return $this->processOneOffStripePaymentWithDonation($member, $price, $donation);
                } else {
                    return $this->processOneOffStripePayment($member, $price);
                }
            }
        }

        return false;
    }

    private function processSmartDebitPayment(Member $member, $price): bool
    {
        if (!$details = Cache::get(sprintf('member.%s.direct-debit-details', $member->id))) {
            return false;
        }

        $user = $member->user;

        $payment = PaymentRepository::createPaymentRecordForSmartDebit(
            PaymentPurpose::MEMBERSHIP." ({$member->membershipType->name})",
            $price,
            $member
        );

        $smart_debit_details = array_merge($details, [
            'payer_reference' => $user->reference,
            'email' => $user->email,
            'address_line_1' => $member->address_line_1,
            'address_line_2' => $member->address_line_2,
            'address_town' => $member->address_town,
            'address_county' => $member->address_county,
            'address_postcode' => $member->address_postcode
        ]);

        $response = $this->smartDebit->createRecurringPayment(
            $payment->reference,
            $price,
            $smart_debit_details
        );

        if (!$response->success()) {
            return false;
        }

        Cache::delete("member.{$member->id}.direct-debit-details");

        return true;
    }

    private function processSmartDebitPaymentWithDonation(): bool
    {
        return false;
    }

    private function processOneOffStripePayment(Member $member, $price): bool
    {
        $user = $member->user;
        $paymentMethod = $user->paymentMethods()->first()->asStripePaymentMethod();

        $charge = $user->charge($price, $paymentMethod, [
            'description' => 'Angling Trust Membership - ' . $member->membershipType->name
        ]);

        if ($charge->asStripePaymentIntent()->status !== "succeeded") {
            return $this->error();
        }

        PaymentRepository::createPaymentRecordForStripe(
            PaymentPurpose::MEMBERSHIP." ({$member->membershipType->name})",
            $price,
            $member
        );

        return false;
    }

    private function processOneOffStripePaymentWithDonation(Member $member, $price, $donation): bool
    {
        return false;
    }

    private function processRecurringStripePayment(Member $member, $price): bool
    {
        $user = $member->user;
        $paymentMethod = $user->paymentMethods()->first()->asStripePaymentMethod();

        $plan = StripeSubscription::getPlanForMember($member);

        try {
            $subscription = $user->newSubscription('default', $plan->api_id)
                ->create($paymentMethod);
        } catch (PaymentActionRequired|PaymentFailure $e) {
            return $this->error(config('app.env') === 'production' ? '' : $e->getMessage());
        }

        if (!$subscription) {
            return $this->error();
        }

        PaymentRepository::createRecurringPaymentRecordForStripe(
            PaymentPurpose::MEMBERSHIP." ({$member->membershipType->name})",
            $price,
            $member
        );

        return false;
    }

    private function processRecurringStripePaymentWithDonation(): bool
    {
        return false;
    }
}
