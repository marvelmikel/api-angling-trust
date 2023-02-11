<?php

namespace Modules\Members\Http\Requests\Member\Client\IndividualMember;

use App\Http\Requests\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'category_id' => ['required', 'exists:membership_type_categories,id'],
            'user.email' => ['required', 'email'],
            'title' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'address_postcode' => ['required']
        ];
    }
}
