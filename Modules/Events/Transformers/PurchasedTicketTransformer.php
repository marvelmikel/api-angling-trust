<?php

namespace Modules\Events\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Members\Transformers\Client\MemberTransformer;

class PurchasedTicketTransformer implements EntityTransformer
{
    /**
     * @param PurchasedTicket $purchased_ticket
     * @return array
     */
    public function data($purchased_ticket): array
    {
        return [
            'id' => $purchased_ticket->id,
            'member_id' => $purchased_ticket->member_id,
            'event_id' => $purchased_ticket->event_id,
            'ticket_id' => $purchased_ticket->ticket_id,
            'reference' => $purchased_ticket->reference,
            'data' => $purchased_ticket->data,
            'price' => $purchased_ticket->price,
            'purchased_at' => $purchased_ticket->purchased_at,
            'canceled_at' => $purchased_ticket->canceled_at
        ];
    }

    public function relations(): array
    {
        return [
            'member' => MemberTransformer::class,
            'event' => EventTransformer::class,
            'ticket' => TicketTransformer::class
        ];
    }
}
