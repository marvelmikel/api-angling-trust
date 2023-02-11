<?php

namespace Modules\Auth\Http\Requests\Register\IndividualMember;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class Step3 extends FormRequest
{
    public function rules(): array
    {
        $member = current_member();

        $rules =[
            'meta.registration_source' => ['required'],
            'meta.reason_for_joining' => ['required'],
            'meta.disciplines' => ['required'],
        ];

        return $rules;
    }
}
