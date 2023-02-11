<?php

namespace Modules\Auth\Http\Requests\Register;

use App\Http\Requests\FormRequest;
use Modules\Auth\Rules\StrongPassword;

class Step1 extends FormRequest
{
    public function rules()
    {
        return [
            'user.email' => ['required', 'email'],
            'membership_type' => ['required', 'exists:membership_types,slug'],
            'user.password' => ['required', new StrongPassword(), 'confirmed']
        ];
    }

    public function attributes()
    {
        return [
            'user.email' => 'email',
            'user.password' => 'password'
        ];
    }
}
