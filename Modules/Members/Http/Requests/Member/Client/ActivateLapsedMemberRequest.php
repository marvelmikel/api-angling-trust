<?php

namespace Modules\Members\Http\Requests\Member\Client;

use App\Http\Requests\FormRequest;

class ActivateLapsedMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'membership_type_id' => ['required', 'exists:membership_types,id'],
            'at_member' => ['required', 'boolean'],
            'fl_member' => ['required', 'boolean'],
            'category_id' => ['required', 'exists:membership_type_categories,id'],
        ];
    }
}
