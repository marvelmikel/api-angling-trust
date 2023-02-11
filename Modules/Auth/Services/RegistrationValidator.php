<?php

namespace Modules\Auth\Services;

use App\Http\Requests\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class RegistrationValidator
{
    public static function get_validator($membership_type, $step, $request): Validator
    {
        $type = upper_camel_case($membership_type);
        $class = "Modules\Auth\Http\Requests\Register\\$type\Step$step";

        /** @var FormRequest $validator */
        $validator = new $class();

        return $validator->asValidator();
    }

    public static function validate($membership_type, $step, $request)
    {
        $validator = self::get_validator($membership_type, $step, $request);

        if ($validator->passes()) {
            return true;
        }

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
}
