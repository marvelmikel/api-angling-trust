<?php

namespace Modules\FishingDraw\Http\Requests\FishingDrawEntry;

use App\Http\Requests\FormRequest;

class StoreFishingDrawEntryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'email' => ['required'],
            'quantity' => ['required'],
            'payment_amount' => ['required']
        ];
    }
}
