<?php

namespace Modules\Members\Http\Requests\Member\Personal\ClubOrSyndicate;

use App\Http\Requests\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'user.email' => ['required', 'email'],
            'address_postcode' => ['required'],
            'meta.primary_contact.title' => ['required'],
            'meta.primary_contact.first_name' => ['required'],
            'meta.primary_contact.last_name' => ['required'],
            'meta.primary_contact.address_postcode' => ['required'],
            'meta.raffle_opt_out' => ['required']
        ];
    }
}
