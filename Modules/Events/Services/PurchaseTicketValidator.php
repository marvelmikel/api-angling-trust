<?php

namespace Modules\Events\Services;

use App\Http\Requests\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Modules\Events\Entities\Event;

class PurchaseTicketValidator
{
    public static function get_validator(Event $event): Validator
    {
        $type = upper_camel_case($event->type);
        $class = "Modules\Events\Http\Requests\Any\PurchasedTicket\StoreTicketFor{$type}EventRequest";

        /** @var FormRequest $validator */
        $validator = new $class();
        $validator->setEvent($event);

        return $validator->asValidator();
    }

    public static function validate(Event $event)
    {
        $validator = self::get_validator($event);

        if ($validator->passes()) {
            return true;
        }

        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response([
                'success' => false,
                'error' => [
                    'message' => 'Validation Failed',
                    'code' => 422
                ],
                'data' => [
                    'errors' => $errors
                ]
            ])
        );
    }
}
