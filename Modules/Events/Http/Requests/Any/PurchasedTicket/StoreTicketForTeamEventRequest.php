<?php

namespace Modules\Events\Http\Requests\Any\PurchasedTicket;

use App\Http\Requests\FormRequest;
use Modules\Events\Entities\Event;
use Modules\Events\Rules\EventAgeRange;

class StoreTicketForTeamEventRequest extends FormRequest
{
    private $event;

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    public function rules()
    {
        $rules = [
            'team_name' => ['required'],
            'point_of_contact.first_name' => ['required'],
            'point_of_contact.last_name' => ['required'],
            'point_of_contact.telephone' => ['required'],
            'point_of_contact.email' => ['required', 'email'],
            'point_of_contact.address_line_1' => ['required'],
            'point_of_contact.address_town' => ['required'],
            'point_of_contact.address_postcode' => ['required']
        ];

        for ($i = 0; $i < $this->event->team_size; $i++) {
            $rules = array_merge($rules, [
                "angler.{$i}.first_name" => ['required'],
                "angler.{$i}.last_name" => ['required']
            ]);
        }

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
