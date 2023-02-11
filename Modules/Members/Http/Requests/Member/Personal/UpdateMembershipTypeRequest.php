<?php

namespace Modules\Members\Http\Requests\Member\Personal;

use App\Http\Requests\FormRequest;

class UpdateMembershipTypeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'membership_type_id' => ['required', 'exists:membership_types,id']
        ];
    }
}
