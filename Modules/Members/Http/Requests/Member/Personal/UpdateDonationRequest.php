<?php

namespace Modules\Members\Http\Requests\Member\Personal;

use App\Http\Requests\FormRequest;

class UpdateDonationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'donation' => ['required']
        ];
    }
}
