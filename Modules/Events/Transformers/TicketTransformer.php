<?php

namespace Modules\Events\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Events\Entities\FrozenTicket;
use Modules\Events\Entities\Ticket;

class TicketTransformer implements EntityTransformer
{
    /**
     * @param Ticket $ticket
     * @return array
     */
    public function data($ticket): array
    {
        return [
            'id' => $ticket->id,
            'ref' => $ticket->ref,
            'event_id' => $ticket->event_id,
            'name' => $ticket->name,
            'price' => $ticket->price,
            'formatted_price' => $ticket->formatted_price,
            'total_remaining' => $ticket->total_remaining,
            'total_available' => $ticket->total_available
        ];
    }

    public function relations(): array
    {
        return [];
    }
}
