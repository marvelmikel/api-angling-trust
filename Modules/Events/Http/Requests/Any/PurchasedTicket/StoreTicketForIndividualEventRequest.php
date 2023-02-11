<?php

namespace Modules\Events\Http\Requests\Any\PurchasedTicket;

use App\Http\Requests\FormRequest;
use Modules\Events\Entities\Event;
use Modules\Events\Rules\EventAgeRange;

class StoreTicketForIndividualEventRequest extends FormRequest
{
    private $event;

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    public function rules()
    {
        $rules = [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'contact_number' => ['required'],
            'date_of_birth' => ['required', new EventAgeRange($this->event)],
            'address_line_1' => ['required'],
            'address_town' => ['required'],
            'address_postcode' => ['required']
        ];

        if ($this->event->post_type === 'competition') {
            $rules['fishing_licence_number'] = ['required'];
            $rules['anti_doping_policy'] = ['accepted'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            '*.required' => 'This field is required',
            'anti_doping_policy.accepted' => 'The terms and conditions must be accepted'
        ];
    }
}
