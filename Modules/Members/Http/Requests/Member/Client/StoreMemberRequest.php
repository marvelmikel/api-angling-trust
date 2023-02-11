<?php

namespace Modules\Members\Http\Requests\Member\Client;

use App\Http\Requests\FormRequest;
use Modules\Members\Rules\ATMember;

class StoreMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user.email' => ['required', 'email'],
            'user.password' => ['required', 'confirmed'],
            'membership_type_slug' => ['required', 'exists:membership_types,slug'],
            'title' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'address_postcode' => ['required'],
            'at_member' => [new ATMember()]
        ];
    }
}
