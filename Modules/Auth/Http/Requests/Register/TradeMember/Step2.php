<?php

namespace Modules\Auth\Http\Requests\Register\TradeMember;

use App\Http\Requests\FormRequest;

class Step2 extends FormRequest
{
    public function rules()
    {
        return [
            'category' => ['required', 'exists:membership_type_categories,slug'],
            'meta.club_name' => ['required'],
            'title' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'address_line_1' => ['required'],
            'address_town' => ['required'],
            'address_county' => ['required'],
            'address_postcode' => ['required'],
            'meta.primary_contact.title' => ['required'],
            'meta.primary_contact.first_name' => ['required'],
            'meta.primary_contact.last_name' => ['required'],
            'meta.primary_contact.email' => ['required'],
            'meta.primary_contact.address_line_1' => ['required'],
            'meta.primary_contact.address_town' => ['required'],
            'meta.primary_contact.address_county' => ['required'],
            'meta.primary_contact.address_postcode' => ['required']
        ];
    }
}
