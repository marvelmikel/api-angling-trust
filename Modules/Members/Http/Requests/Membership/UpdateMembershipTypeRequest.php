<?php

namespace Modules\Members\Http\Requests\Membership;

use App\Http\Requests\FormRequest;

class UpdateMembershipTypeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'wp_id' => ['required'],
            'name' => ['required'],
            'price' => ['required']
        ];
    }
}
