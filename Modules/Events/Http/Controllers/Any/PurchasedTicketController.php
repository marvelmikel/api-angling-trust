<?php

namespace Modules\Events\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Services\WPNotification;
use Modules\Events\Emails\TicketSoldEmail;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Entities\Ticket;
use Modules\Events\Enums\EventType;
use Modules\Events\Http\Requests\Any\PurchasedTicket\CompleteFreePurchasedTicketRequest;
use Modules\Events\Http\Requests\Any\PurchasedTicket\CompletePurchasedTicketRequest;
use Modules\Events\Repositories\PurchasedTicketRepository;
use Modules\Events\Services\PurchaseTicketValidator;
use Modules\Events\Services\TicketBasketManager;
use Modules\Events\Services\TicketFreezer;
use Modules\Events\Transformers\PurchasedTicketTransformer;
use Modules\Store\Repositories\PaymentRepository;

class PurchasedTicketController extends Controller
{
    public function store($ref, Request $request)
    {
        $basket = TicketBasketManager::findForCurrentUser();

        $ticket = Ticket::query()
            ->where('ref', $ref)
            ->firstOrFail();

        $event = $ticket->event;

        PurchaseTicketValidator::validate($event);

        $price = $ticket->price;

        if ($event->has_pools_payments) {
            $choices = $request->get('pools_payment');

            $pools_payments = $event->pools_payments;

            foreach ($pools_payments as $index => $item) {
                if ($choices[$index] === true) {
                    $price += $item['amount'];
                }
            }
        }

        $purchased_ticket = PurchasedTicketRepository::create($basket, $ticket, $price, $request->all());

        if ($member = current_member()) {
            $member->setMeta('rod_licence_number', $request->get('fishing_licence_number'));
        }

        $basket->updatePrice();

        return $this->success([
            'purchased_ticket' => $purchased_ticket
        ]);
    }

    public function cancel(PurchasedTicket $purchasedTicket)
    {
        $member = current_member();

        if ($purchasedTicket->member_id != $member->id) {
            return $this->error('Unauthorised', 401);
        }

        if (!$purchasedTicket->cancel()) {
            return $this->error();
        }

        $user = $member->user;
        $event = $purchasedTicket->event;
        $eventCategory = $event->category;

        $shortcodes = [
            '[member.full_name]' => $member->full_name,
            '[user.reference]' => $user->reference,
            '[user.email]' => $user->email,
            '[member.address_postcode]' => $member->address_postcode,
            '[eventCategory.name]' => 'None',
            '[event.name]' => $event->name,
            '[event.department_code]' => $event->department_code,
            '[event.nominal_code]' => $event->nominal_code,
            '[purchasedTicket.canceled_at]' => $purchasedTicket->canceled_at->format('d/m/Y H:i'),
            '[purchasedTicket.reference]' => $purchasedTicket->reference
        ];

        if ($eventCategory) {
            $shortcodes['[eventCategory.name]'] = $eventCategory->name;
        }

        WPNotification::sendAdminNotification('am-ticket-canceled', $shortcodes);

        $purchasedTicket->refresh();
        $purchasedTicket->load(['event', 'ticket']);

        return $this->success([
            'purchasedTicket' => Transform::entity($purchasedTicket, PurchasedTicketTransformer::class)
        ]);
    }

    public function complete(PurchasedTicket $purchasedTicket, CompletePurchasedTicketRequest $request)
    {
        try {

            $this->log($purchasedTicket, 'Starting ticket completion process');

            if (!PurchasedTicketRepository::complete($purchasedTicket, $request->input('payment_id'))) {
                $this->log($purchasedTicket, 'Failed to complete ticket', true);

                return $this->error();
            }

            TicketFreezer::delete($purchasedTicket->ticket, $request->input('frozen_ticket_token'));

            $this->log($purchasedTicket, 'Deleted frozen ticket');

            $event = $purchasedTicket->event;

            if (!$event) {
                $this->log($purchasedTicket, 'Event not found', true);
            }

            if (current_member()) {
                PaymentRepository::createPaymentRecordForStripe(
                    sprintf('Ticket (%s %s)', $event->department_code, $event->nominal_code),
                    $purchasedTicket->price,
                    current_member()
                );
                $this->log($purchasedTicket, 'Created payment record');
            }

            $data = [
                'purchased_ticket_id' => $purchasedTicket->id
            ];

            $this->log($purchasedTicket, 'Adding notification to queue');

            WPNotification::sendCustomerNotification('cm-ticket-purchased', null, $data);

            $this->log($purchasedTicket, 'Notification added to queue');

            return $this->success();

        } catch (\Exception $exception) {
            $this->log($purchasedTicket, $exception->getMessage(), true);

            return $this->error();
        }
    }

    public function completeFree(PurchasedTicket $purchasedTicket, CompleteFreePurchasedTicketRequest $request)
    {
        try {

            if ($purchasedTicket->price !== 0) {
                return $this->error('Not Free');
            }

            if (!PurchasedTicketRepository::completeFree($purchasedTicket)) {
                return $this->error();
            }

            TicketFreezer::delete($purchasedTicket->ticket, $request->input('frozen_ticket_token'));

            $data = [
                'purchased_ticket_id' => $purchasedTicket->id
            ];

            WPNotification::sendCustomerNotification('cm-ticket-purchased', null, $data);

            return $this->success();

        } catch (\Exception $exception) {
            return $this->error();
        }
    }

    public function resendTickets(Request $request) {
        Artisan::call('core:resend-tickets-for-event ' . $request->post_id);
        return response()->json('hello');
    }

    private function log($purchasedTicket, $message, $isError = false)
    {
        $message = "({$purchasedTicket->id}) {$message}";

        if ($isError) {
            \Log::channel('purchasedTickets')
                ->error($message);
        } else {
            \Log::channel('purchasedTickets')
                ->info($message);
        }
    }
}
