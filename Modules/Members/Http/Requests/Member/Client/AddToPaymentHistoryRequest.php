<?php

namespace Modules\Members\Http\Requests\Member\Client;

use App\Http\Requests\FormRequest;
use Modules\Store\Enums\PaymentProvider;

class AddToPaymentHistoryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'purpose' => ['required'],
            'payment_provider' => ['required', 'in:' . PaymentProvider::STRIPE . ',' . PaymentProvider::SMART_DEBIT . ',' . PaymentProvider::OTHER],
            'auto_renew' => ['required', 'in:1,0'],
            'amount' => ['required', 'regex:/^\d+(.\d{2})?$/'],
            'completed_at.date' => ['required', 'date_format:d/m/Y'],
            'completed_at.time' => ['required', 'date_format:H:i']
        ];
    }
}
