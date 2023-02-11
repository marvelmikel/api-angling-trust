<?php

namespace Modules\Store\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;

class PaymentTransformer implements EntityTransformer
{
    public function data($payment): array
    {
        return [
            'id' => $payment->id,
            'member_id' => $payment->member_id,
            'reference' => $payment->reference,
            'payment_provider' => $payment->payment_provider,
            'purpose' => $payment->purpose,
            'description' => $payment->description,
            'amount' => $payment->amount,
            'auto_renew' => $payment->auto_renew,
            'completed_at' => $payment->completed_at
        ];
    }

    public function relations(): array
    {
        return [];
    }
}
