<?php

namespace Modules\Events\Repositories;

use Modules\Events\Entities\Event;
use Modules\Events\Entities\Ticket;

class TicketRepository
{
    public static function updateAllForEvent(Event $event, $tickets)
    {
        $ticketRefs = array_column($tickets, 'ref');

        foreach ($tickets as $ticket) {
            self::createOrUpdateForEvent($event, $ticket['ref'], $ticket);
            self::deleteForEventWhereRefNotIn($event, $ticketRefs);
        }
    }

    public static function findOrNewForEvent(Event $event, string $ref)
    {
        $ticket = Ticket::query()
            ->where('event_id', $event->id)
            ->where('ref', $ref)
            ->firstOrNew([]);

        $ticket->event_id = $event->id;
        $ticket->ref = $ref;

        return $ticket;
    }

    public static function createOrUpdateForEvent(Event $event, string $ref, array $data): Ticket
    {
        $ticket = self::findOrNewForEvent($event, $ref);
        $ticket->fill($data);
        $ticket->save();

        return $ticket;
    }

    public static function deleteForEventWhereRefNotIn(Event $event, array $refs)
    {
        $tickets = $event->tickets()
            ->whereNotIn('ref', $refs)
            ->get();

        foreach ($tickets as $ticket) {
            $ticket->delete();
        }

        return true;
    }
}
