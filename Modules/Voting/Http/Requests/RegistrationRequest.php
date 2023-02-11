<?php

namespace Modules\Voting\Http\Requests;

use App\Http\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Voting\Repositories\FormRepository;

class RegistrationRequest extends HasFormRequest
{
    public function rules(): array
    {
        return [
            'registration' => [
                'required',
                'bool',
            ]
        ];
    }

    public function registration(): bool
    {
        return filter_var($this->input('registration'), FILTER_VALIDATE_BOOL);
    }
}
