<?php

namespace Modules\Auth\Http\Requests\PasswordReset;

use App\Http\Requests\FormRequest;
use Modules\Auth\Rules\StrongPassword;

class CompletePasswordResetRequest extends FormRequest
{
    public function rules()
    {
        return [
            'password' => ['required', new StrongPassword()]
        ];
    }
}
