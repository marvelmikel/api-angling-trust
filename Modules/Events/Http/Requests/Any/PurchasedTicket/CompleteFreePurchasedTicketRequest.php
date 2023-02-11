<?php

namespace Modules\Events\Http\Requests\Any\PurchasedTicket;

use App\Http\Requests\FormRequest;

class CompleteFreePurchasedTicketRequest extends FormRequest
{
    public function rules()
    {
        return [
            'frozen_ticket_token' => ['required']
        ];
    }
}
