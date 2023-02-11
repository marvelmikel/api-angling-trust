<?php

namespace Modules\Events\Http\Requests\Event;

use App\Http\Requests\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'slug' => ['required'],
            'ticket_sales_open' => ['required'],
            'ticket_sales_close' => ['required']
        ];
    }
}
