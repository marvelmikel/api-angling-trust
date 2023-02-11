<?php

namespace Modules\Members\Http\Requests\Member\Personal;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function rules()
    {
        $user = current_user();

        return [
            'user.first_name' => ['required'],
            'user.last_name' => ['required'],
            'user.email' => ['required', 'email', "unique:users,email,{$user->id}"],
            'date_of_birth' => ['required'],
            'address.line_1' => ['required'],
            'address.town' => ['required'],
            'address.postcode' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'user.first_name.required' => 'Your first name is required',
            'user.last_name.required' => 'Your last name is required',
            'user.email.required' => 'Your email is required',
            'user.email.unique' => 'This email has already been taken',
            'user.email.email' => 'Please enter a valid email address',
            'user.date_of_birth.email' => 'Your date of birth is required',
            'address.line_1.required' => 'Your address line 1 is required',
            'address.town.required' => 'Your address town is required',
            'address.postcode.required' => 'Your address postcode is required'
        ];
    }
}
