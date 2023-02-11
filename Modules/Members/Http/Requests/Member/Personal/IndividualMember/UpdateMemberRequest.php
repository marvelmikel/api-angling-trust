<?php

namespace Modules\Members\Http\Requests\Member\Personal\IndividualMember;

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
            'meta.ethnicity' => ['required'],
            'meta.disability_1' => ['required'],
            'meta.raffle_opt_out' => ['required']
        ];
    }
}
