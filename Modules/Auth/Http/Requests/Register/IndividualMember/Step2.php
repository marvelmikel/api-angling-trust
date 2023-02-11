<?php

namespace Modules\Auth\Http\Requests\Register\IndividualMember;

use App\Http\Requests\FormRequest;
use Modules\Auth\Rules\CorrectAgeRange;

class Step2 extends FormRequest
{
    public function rules()
    {
        return [
            'category' => ['required', 'exists:membership_type_categories,slug'],
            'title' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'meta.date_of_birth' => ['required', new CorrectAgeRange(5)],
            'meta.ethnicity' => ['required'],
            'address_line_1' => ['required'],
            'address_town' => ['required'],
            'address_county' => ['required'],
            'address_postcode' => ['required'],
            'meta.disability_1' => ['required']
        ];
    }

    public function attributes()
    {
        return [
            'meta.date_of_birth' => 'date of birth'
        ];
    }
}
