<?php

namespace Modules\Events\Http\Requests\Any\PurchasedTicket;

use App\Http\Requests\FormRequest;

class StorePurchasedTicketRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'contact_number' => ['required'],
            'date_of_birth' => ['required'],
            'address.line_1' => ['required'],
            'address.town' => ['required'],
            'address.postcode' => ['required'],
            'fishing_licence_number' => ['required'],
        ];
    }
}
