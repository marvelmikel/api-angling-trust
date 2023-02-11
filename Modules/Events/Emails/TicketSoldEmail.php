<?php

namespace Modules\Events\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Events\Entities\PurchasedTicket;

class TicketSoldEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $event;
    private $ticket;
    private $your_details;

    public function __construct(PurchasedTicket $purchasedTicket)
    {
        $this->subject = 'Ticket Sold';

        $this->event = $purchasedTicket->event;
        $this->ticket = $purchasedTicket->ticket;
        $this->your_details = $purchasedTicket->your_details;
    }

    public function build()
    {
        return $this->markdown('events::mail.notification.ticket-sold', [
            'event' => $this->event,
            'ticket' => $this->ticket,
            'your_details' => $this->your_details
        ]);
    }
}
