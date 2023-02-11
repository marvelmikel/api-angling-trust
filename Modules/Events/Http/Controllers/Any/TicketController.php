<?php

namespace Modules\Events\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use EliPett\EntityTransformer\Facades\Transform;
use Illuminate\Http\Request;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Entities\Ticket;
use Modules\Events\Services\TicketBasketManager;
use Modules\Events\Services\TicketFreezer;
use Modules\Events\Transformers\EventTransformer;
use Modules\Events\Transformers\FrozenTicketTransformer;
use Modules\Events\Transformers\TicketTransformer;

class TicketController extends Controller
{
    public function show(string $ref, Request $request)
    {
        $basket = TicketBasketManager::findOrNewForCurrentUser();

        $ticket = Ticket::query()
            ->where('ref', $ref)
            ->firstOrFail();

        $event = $ticket->event;

        $token = $request->input('frozen_ticket_token', null);

        if ($frozen_ticket = TicketFreezer::getFrozenTicket($ticket, $token)) {
            return $this->data($basket, $ticket, $event, $frozen_ticket);
        }

        // todo: limit to 5 tickets
        if ($basket->purchasedTickets()->count() >= 5) {
            return $this->error('You already have 5 tickets in your basket, please complete checkout before purchasing more tickets.');
        }

        if ($event->member_only) {
            $member = current_member();

            if ($member->hasExpired()) {
                return $this->error('Please renew your membership before purchasing a ticket for this competition');
            }

            $previousPurchasesForThisEvent = PurchasedTicket::query()
                ->where('member_id', $member->id)
                ->where('event_id', $event->id)
                ->whereNotNull('purchased_at')
                ->whereNull('canceled_at')
                ->count();

            if ($previousPurchasesForThisEvent !== 0) {
                return $this->error('You have already purchased a ticket for this competition');
            }

            $relatedEvents = Event::query()
                ->where('category_id', $event->category_id)
                ->pluck('id');

            $previousPurchasesForRelatedEvents = PurchasedTicket::query()
                ->where('member_id', $member->id)
                ->whereIn('event_id', $relatedEvents)
                ->whereNotNull('purchased_at')
                ->whereNull('canceled_at')
                ->count();

            $category = $event->category;
            $limit = $category->ticket_limit;

            if ($limit) {
                if ($previousPurchasesForRelatedEvents >= $limit) {
                    return $this->error("You have already purchased {$previousPurchasesForRelatedEvents} tickets for this competition category");
                }
            }
        }

        if (!TicketFreezer::canFreezeTicket($ticket)) {
            return $this->error('No tickets are available');
        }

        $frozen_ticket = TicketFreezer::freeze($basket, $ticket);

        return $this->data($basket, $ticket, $event, $frozen_ticket);
    }

    private function data($basket, $ticket, $event, $frozen_ticket)
    {
        return $this->success([
            'basket' => $basket->reference,
            'ticket' => Transform::entity($ticket, TicketTransformer::class),
            'event' => Transform::entity($event, EventTransformer::class),
            'frozen_ticket' => Transform::entity($frozen_ticket, FrozenTicketTransformer::class),
            'cookie_expiry' => $basket->expires_at->format('D, j M Y H:i:s') . ' GMT'
        ]);
    }
}
