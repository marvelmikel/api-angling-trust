<?php

namespace Modules\Store\Http\Requests\Personal;

use App\Http\Requests\FormRequest;

class ValidateDirectDebitRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'account_name' => ['required', 'max:18'],
            'account_number' => ['required', 'numeric', 'digits:8'],
            'sort_code' => ['required', 'regex:/\d{2}-\d{2}-\d{2}/', 'size:8']
        ];
    }

    public function messages()
    {
        return [
            'sort_code.regex' => 'The sort code must match the format XX-XX-XX'
        ];
    }
}
