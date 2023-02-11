<?php

namespace Modules\Events\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;

class EventAgeRange implements Rule
{
    private $event;
    private $message;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function passes($attribute, $value)
    {
        $start_date = $this->event->start_date->format('Y-m-d');
        $date_of_birth = $value['year'] . '-' . $value['month'] . '-' . $value['day'];

        $age = date_diff(date_create($date_of_birth), date_create($start_date))->format('%y');

        if ($this->event->min_age) {
            if ($age < $this->event->min_age) {
                $this->message = 'You are too young for this competition';
                return false;
            }
        }

        if ($this->event->max_age) {
            if ($age > $this->event->max_age) {
                $this->message = 'You are too old for this competition';
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
