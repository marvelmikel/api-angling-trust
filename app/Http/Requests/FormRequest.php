<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Validation\ValidationException;

class FormRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function asValidator()
    {
        return ValidatorFactory::make(request()->all(), $this->rules(), $this->messages(), $this->attributes());
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response([
                'success' => false,
                'error' => [
                    'message' => 'Validation Failed',
                    'code' => 422
                ],
                'data' => [
                    'errors' => $errors
                ]
            ])
        );
    }

    public function messages()
    {
        return [
            '*.required' => 'This field is required'
        ];
    }
}
