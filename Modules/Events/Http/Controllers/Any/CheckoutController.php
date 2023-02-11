<?php

namespace Modules\Events\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Services\WPNotification;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Services\TicketBasketManager;
use Modules\Store\Repositories\PaymentRepository;

class CheckoutController extends Controller
{
    public function data(Request $request)
    {
        $basket = TicketBasketManager::findForCurrentUser();

        if ($basket->purchased_at !== null) {
            return $this->error('An error has occurred, please refresh the page.');
        }

        return $this->success([
            'basket' => [
                'reference' => $basket->reference,
                'price' => $basket->price,
                'expires_at' => $basket->expires_at,
                'tickets' => $basket->purchasedTickets()->with('event')->get()
            ]
        ]);
    }

    public function complete(Request $request)
    {
        $basket = TicketBasketManager::findForCurrentUser();

        TicketBasketManager::markAsPaid($basket, $request->input('payment_id'));
        TicketBasketManager::clearFrozenTickets($basket);

        if (current_member()) {
            PaymentRepository::createPaymentRecordForStripe(
                sprintf('Tickets (%s)', $basket->reference),
                $basket->price,
                current_member()
            );
        }

        foreach ($basket->purchasedTickets as $ticket) {
            WPNotification::sendCustomerNotification('cm-ticket-purchased', null, [
                'purchased_ticket_id' => $ticket->id
            ]);
        }

        return $this->success([
            'reference' => $basket->reference
        ]);
    }

    public function removeTicket(Request $request, $ticket)
    {
        $basket = TicketBasketManager::findForCurrentUser();

        $purchasedTicket = PurchasedTicket::query()
            ->where('reference', $ticket)
            ->where('basket_id', $basket->id)
            ->firstOrFail();

        $purchasedTicket->delete();
        // todo: delete matching frozen ticket (instead of waiting for it to expire)
        // todo: if no tickets remain, delete basket?

        $basket->updatePrice();

        return $this->success([
            'basket' => [
                'reference' => $basket->reference,
                'price' => $basket->price,
                'expires_at' => $basket->expires_at,
                'tickets' => $basket->purchasedTickets()->with('event')->get()
            ]
        ]);
    }
}
