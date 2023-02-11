<?php

namespace Modules\Events\Repositories;

use Carbon\Carbon;
use Modules\Events\Entities\FrozenTicket;
use Modules\Events\Entities\Ticket;
use Modules\Events\Entities\TicketBasket;
use Webpatser\Uuid\Uuid;

class FrozenTicketRepository
{
    public static function create(TicketBasket $basket, Ticket $ticket)
    {
        $frozen_ticket = new FrozenTicket();
        $frozen_ticket->basket_id = $basket->id;
        $frozen_ticket->ticket_id = $ticket->id;
        $frozen_ticket->token = Uuid::generate()->string;
        $frozen_ticket->expires_at = $basket->expires_at;
        $frozen_ticket->save();

        return $frozen_ticket;
    }

    public static function findOrNull(Ticket $ticket, $token)
    {
        return FrozenTicket::query()
            ->where('ticket_id', $ticket->id)
            ->where('token', $token)
            ->first();
    }
}
