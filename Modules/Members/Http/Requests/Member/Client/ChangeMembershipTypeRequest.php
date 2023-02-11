<?php

namespace Modules\Members\Http\Requests\Member\Client;

use App\Http\Requests\FormRequest;

class ChangeMembershipTypeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'membership_type_id' => ['required', 'exists:membership_types,id']
        ];
    }
}
