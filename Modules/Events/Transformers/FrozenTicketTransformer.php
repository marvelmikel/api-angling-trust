<?php

namespace Modules\Events\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Events\Entities\FrozenTicket;

class FrozenTicketTransformer implements EntityTransformer
{
    /**
     * @param FrozenTicket $frozen_ticket
     * @return array
     */
    public function data($frozen_ticket): array
    {
        return [
            'id' => $frozen_ticket->id,
            'ticket_id' => $frozen_ticket->event_id,
            'token' => $frozen_ticket->token,
            'expires_at' => $frozen_ticket->expires_at
        ];
    }

    public function relations(): array
    {
        return [];
    }
}
