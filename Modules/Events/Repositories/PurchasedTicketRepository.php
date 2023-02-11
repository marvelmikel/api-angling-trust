<?php

namespace Modules\Events\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Entities\Ticket;
use Modules\Events\Entities\TicketBasket;

class PurchasedTicketRepository
{
    public static function create(TicketBasket $basket, Ticket $ticket, $price, $data)
    {
        $purchased_ticket = new PurchasedTicket();
        $purchased_ticket->basket_id = $basket->id;
        $purchased_ticket->event_id = $ticket->event_id;
        $purchased_ticket->ticket_id = $ticket->id;
        $purchased_ticket->reference = random_reference();
        $purchased_ticket->data = $data;

        $bursary_code = $purchased_ticket->event->bursary_code;

        if(array_key_exists('bursary_code', $data) && $bursary_code == $data['bursary_code'] && !empty($bursary_code)) {
            $purchased_ticket->price = round(($price / 2));
        } else {
            $purchased_ticket->price = $price;
        }

        if ($member = current_member()) {
            $purchased_ticket->member_id = $member->id;
        }

        $purchased_ticket->save();

        return $purchased_ticket;
    }

    public static function complete(PurchasedTicket $purchased_ticket, $payment_id)
    {
        $purchased_ticket->payment_id = $payment_id;
        $purchased_ticket->purchased_at = Carbon::now();

        return $purchased_ticket->save();
    }

    public static function completeFree(PurchasedTicket $purchased_ticket)
    {
        $purchased_ticket->purchased_at = Carbon::now();

        return $purchased_ticket->save();
    }
}
