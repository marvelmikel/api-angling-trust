<?php

namespace Modules\Store\Repositories;

use Carbon\Carbon;
use Modules\Members\Entities\Member;
use Modules\Store\Entities\Payment;
use Modules\Store\Enums\PaymentProvider;

class PaymentRepository
{
    public static function createPaymentRecordForSmartDebit(
        $purpose,
        $amount,
        Member $member = null,
        ?string $reference = null,
        $autoRenew = true
    ): Payment {
        $payment = new Payment();
        $payment->reference = $reference ?? random_reference();
        $payment->payment_provider = PaymentProvider::SMART_DEBIT;
        $payment->purpose = $purpose;

        if ($member) {
            $payment->member()->associate($member);
        }

        $payment->amount = $amount / 100;
        $payment->auto_renew = $autoRenew;
        $payment->save();

        return $payment;
    }

    public static function createOneOffPaymentRecordForSmartDebit(
        $purpose,
        $amount,
        Member $member = null,
        ?string $reference = null
    ): Payment {
        return self::createPaymentRecordForSmartDebit(
            $purpose,
            $amount,
            $member,
            $reference,
            false,
        );
    }

    public static function createPaymentRecordForStripe(
        string $purpose,
        int $amount,
        ?Member $member = null
    ): Payment {
        $payment = new Payment();
        $payment->reference = random_reference();
        $payment->payment_provider = PaymentProvider::STRIPE;
        $payment->purpose = $purpose;

        if ($member) {
            $payment->member()->associate($member);
        }

        $payment->amount = $amount / 100;
        $payment->auto_renew = false;
        $payment->completed_at = now();
        $payment->save();

        return $payment;
    }

    public static function createRecurringPaymentRecordForStripe(
        string $purpose,
        int $amount,
        Member $member,
        bool $completed = true,
        ?string $reference = null
    ): Payment {
        $payment = new Payment();
        $payment->reference = $reference ?? random_reference();
        $payment->payment_provider = PaymentProvider::STRIPE;
        $payment->purpose = $purpose;
        $payment->member()->associate($member);
        $payment->amount = $amount / 100;
        $payment->auto_renew = true;
        $payment->completed_at = $completed ? now() : null;
        $payment->save();

        return $payment;
    }

    public static function createOfflinePaymentRecord($purpose, $amount, Member $member, $description): Payment
    {
        $payment = new Payment();
        $payment->reference = random_reference();
        $payment->payment_provider = PaymentProvider::OTHER;
        $payment->purpose = $purpose;
        $payment->description = $description;
        $payment->member()->associate($member);
        $payment->amount = $amount / 100;
        $payment->auto_renew = false;
        $payment->completed_at = now();
        $payment->save();

        return $payment;
    }

    private static function createSubscriptionFailedPaymentRecord(
        Member $member,
        string $provider,
        string $description = ''
    ): Payment {
        $payment = new Payment();

        $payment->reference = random_reference();
        $payment->purpose = "Collection Attempt Failed";
        $payment->amount = 0;
        $payment->auto_renew = false;
        $payment->completed_at = Carbon::now();

        $payment->member_id = $member->id;
        $payment->payment_provider = $provider;
        $payment->description = $description;

        $payment->save();

        return $payment;
    }

    public static function createStripeSubscriptionFailedPaymentRecord(Member $member): Payment
    {
        return self::createSubscriptionFailedPaymentRecord(
            $member,
            PaymentProvider::STRIPE,
            'Payment failed'
        );
    }

    public static function createSmartDebitFailedPaymentRecord(Member $member): Payment
    {
        return self::createSubscriptionFailedPaymentRecord(
            $member,
            PaymentProvider::SMART_DEBIT,
            'Membership Payment failed'
        );
    }
}
