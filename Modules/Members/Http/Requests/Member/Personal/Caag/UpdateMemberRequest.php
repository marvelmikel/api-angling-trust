<?php

namespace Modules\Members\Http\Requests\Member\Personal\Caag;

use App\Http\Requests\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user.email' => ['required', 'email']
        ];
    }
}
