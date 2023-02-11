<?php

namespace Modules\Events\Http\Requests\Any\PurchasedTicket;

use App\Http\Requests\FormRequest;

class CompletePurchasedTicketRequest extends FormRequest
{
    public function rules()
    {
        return [
            'payment_id' => ['required'],
            'frozen_ticket_token' => ['required']
        ];
    }
}
