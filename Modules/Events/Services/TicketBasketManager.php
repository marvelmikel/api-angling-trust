<?php

namespace Modules\Events\Services;

use Carbon\Carbon;
use Modules\Events\Entities\TicketBasket;
use Webpatser\Uuid\Uuid;

class TicketBasketManager
{
    public static function findForCurrentUser(): ?TicketBasket
    {
        if (!$reference = request()->input('ticket_basket_token')) {
            return null;
        }

        // todo: where not expired

        return TicketBasket::query()
            ->where('reference', $reference)
            ->first();
    }

    public static function findOrNewForCurrentUser()
    {
        if ($ticketBasket = self::findForCurrentUser()) {
            return $ticketBasket;
        }

        $ticketBasket = new TicketBasket();
        $ticketBasket->reference = Uuid::generate()->string;
        $ticketBasket->expires_at = Carbon::now()->addMinutes(TicketBasket::LIFETIME);

        if ($member = current_member()) {
            $ticketBasket->member_id = $member->id;
        }

        $ticketBasket->save();

        session()->put('ticket_basket_id', $ticketBasket->id);

        return $ticketBasket;
    }

    public static function markAsPaid(TicketBasket $basket, $payment_id)
    {
        $basket->payment_id = $payment_id;
        $basket->purchased_at = now()->format('Y-m-d H:i:s');
        $basket->save();

        foreach ($basket->purchasedTickets as $ticket) {
            $ticket->payment_id = $payment_id;
            $ticket->purchased_at = now()->format('Y-m-d H:i:s');
            $ticket->save();
        }
    }

    public static function clearFrozenTickets(TicketBasket $basket)
    {
        foreach ($basket->frozenTickets as $ticket) {
            $ticket->delete();
        }
    }
}
