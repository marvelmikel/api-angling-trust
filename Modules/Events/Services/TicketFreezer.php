<?php

namespace Modules\Events\Services;

use Modules\Events\Entities\Ticket;
use Modules\Events\Entities\TicketBasket;
use Modules\Events\Repositories\FrozenTicketRepository;

class TicketFreezer
{
    public static function canFreezeTicket(Ticket $ticket)
    {
        return $ticket->total_remaining > 0;
    }

    public static function getFrozenTicket(Ticket $ticket, $ref)
    {
        if ($ref === null) {
            return false;
        }

        return FrozenTicketRepository::findOrNull($ticket, $ref);
    }

    public static function freeze(TicketBasket $basket, Ticket $ticket)
    {
        return FrozenTicketRepository::create($basket, $ticket);
    }

    public static function delete(Ticket $ticket, $ref)
    {
       $frozen_ticket = self::getFrozenTicket($ticket, $ref);
       $frozen_ticket->delete();
    }
}
