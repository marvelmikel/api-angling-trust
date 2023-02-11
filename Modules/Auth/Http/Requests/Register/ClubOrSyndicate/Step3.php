<?php

namespace Modules\Auth\Http\Requests\Register\ClubOrSyndicate;

use App\Http\Requests\FormRequest;

class Step3 extends FormRequest
{
    public function rules()
    {
        return [
            'meta.disciplines' => ['required'],
            'meta.fishing_rights' => ['required']
        ];
    }
}
