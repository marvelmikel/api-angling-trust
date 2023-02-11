<?php

namespace Modules\Auth\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class CorrectAgeRange implements Rule
{
    private $message;
    private $wiggle;

    public function __construct($wiggle)
    {
        $this->wiggle = $wiggle;
    }

    public function passes($attribute, $value)
    {
        $category = request()->get('category');
        $date_of_birth = Carbon::createFromFormat('d-m-Y', "{$value['day']}-{$value['month']}-{$value['year']}");

        switch ($category) {
            case 'junior':
                return $this->passesAsJunior($date_of_birth);
            case 'young-adult':
                return $this->passesAsYoungAdult($date_of_birth);
            case 'adult':
                return $this->passesAsAdult($date_of_birth);
            case 'senior-citizen':
                return $this->passesAsSeniorCitizen($date_of_birth);
        }

        return true;
    }

    public function passesAsJunior($date_of_birth)
    {
        $max = now()->subYears(18)->addDays($this->wiggle);

        if ($date_of_birth < $max) {
            $this->message = 'You are too old to qualify for a Junior membership';
            return false;
        }

        return true;
    }

    public function passesAsYoungAdult($date_of_birth)
    {
        $min = now()->subYears(18)->addDays($this->wiggle);

        if ($date_of_birth > $min) {
            $this->message = 'You are too young to qualify for a Young Adult membership';
            return false;
        }

        $max = now()->subYears(21)->addDays($this->wiggle);

        if ($date_of_birth < $max) {
            $this->message = 'You are too old to qualify for a Young Adult membership';
            return false;
        }

        return true;
    }

    public function passesAsAdult($date_of_birth)
    {
        $min = now()->subYears(21)->addDays($this->wiggle);

        if ($date_of_birth > $min) {
            $this->message = 'You are too young to qualify for an Adult membership';
            return false;
        }

        $max = now()->subYears(65)->addDays($this->wiggle);

        if ($date_of_birth < $max) {
            $this->message = 'You are too old to qualify for an Adult membership';
            return false;
        }

        return true;
    }

    public function passesAsSeniorCitizen($date_of_birth)
    {
        $min = now()->subYears(65)->addDays($this->wiggle);

        if ($date_of_birth > $min) {
            $this->message = 'You are too young to qualify for a Senior Citizen membership';
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
