<?php

namespace Modules\Members\Http\Requests\Member\Client;

use App\Http\Requests\FormRequest;

class UpdateMemberExpiresAtRequest extends FormRequest
{
    public function rules()
    {
        return [
            'expires_at' => ['required'],
        ];
    }
}
