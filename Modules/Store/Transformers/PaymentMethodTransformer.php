<?php

namespace Modules\Store\Transformers;

use Laravel\Cashier\PaymentMethod;

class PaymentMethodTransformer
{
    public static function all($paymentMethods)
    {
        $data = [];

        foreach ($paymentMethods as $paymentMethod) {
            $data[] = self::one($paymentMethod);
        }

        return $data;
    }

    public static function one(PaymentMethod $paymentMethod)
    {
        return $paymentMethod->asStripePaymentMethod();
    }
}
