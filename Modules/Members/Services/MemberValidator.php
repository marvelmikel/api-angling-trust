<?php

namespace Modules\Members\Services;

use App\Http\Requests\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class MemberValidator
{
    private $namespace;

    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    private function get_validator($membership_type, $name): Validator
    {
        $type = upper_camel_case($membership_type);
        $class = "{$this->namespace}\\$type\\$name";

        /** @var FormRequest $validator */
        $validator = new $class();

        return $validator->asValidator();
    }

    public function validate($membership_type, $name)
    {
        $validator = $this->get_validator($membership_type, $name);

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
