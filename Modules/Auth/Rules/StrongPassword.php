<?php

namespace Modules\Auth\Rules;

use Illuminate\Contracts\Validation\Rule;
use ZxcvbnPhp\Zxcvbn;

class StrongPassword implements Rule
{
    private $message;

    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        if (env('APP_ENV') !== 'production') {
            if ($value === 'pass' || $value === 'asd') { // override for testing
                return true;
            }
        }

        $zxcvbn = new Zxcvbn();
        $score = $zxcvbn->passwordStrength($value)['score'];

        if ($score < 2) {
            $this->message = "Please set a stronger password by adding letters, numbers or special characters";
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
