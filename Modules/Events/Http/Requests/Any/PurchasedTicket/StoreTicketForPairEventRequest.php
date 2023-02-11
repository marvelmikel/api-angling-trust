<?php

namespace Modules\Events\Http\Requests\Any\PurchasedTicket;

use App\Http\Requests\FormRequest;
use Modules\Events\Entities\Event;
use Modules\Events\Rules\EventAgeRange;

class StoreTicketForPairEventRequest extends FormRequest
{
    private $event;

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    public function rules()
    {
        $rules = [
            'point_of_contact.first_name' => ['required'],
            'point_of_contact.last_name' => ['required'],
            'point_of_contact.telephone' => ['required'],
            'point_of_contact.email' => ['required', 'email'],
            'point_of_contact.address_line_1' => ['required'],
            'point_of_contact.address_town' => ['required'],
            'point_of_contact.address_postcode' => ['required'],
            'angler_a.first_name' => ['required'],
            'angler_a.last_name' => ['required'],
            'angler_a.email' => ['required', 'email'],
            'angler_a.date_of_birth' => ['required', new EventAgeRange($this->event)],
            'angler_a.rod_licence' => ['required'],
            'angler_a.contact_number' => ['required'],
            'angler_b.first_name' => ['required'],
            'angler_b.last_name' => ['required'],
            'angler_b.email' => ['required', 'email'],
            'angler_b.date_of_birth' => ['required', new EventAgeRange($this->event)],
            'angler_b.rod_licence' => ['required'],
            'angler_b.contact_number' => ['required']
        ];

        if ($this->event->post_type === 'competition') {
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
