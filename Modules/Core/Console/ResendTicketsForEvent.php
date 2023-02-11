<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Modules\Core\Services\WPNotification;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\PurchasedTicket;
class ResendTicketsForEvent extends Command
{
protected $signature = 'core:resend-tickets-for-event {event-wp-id}';

    public function handle()
    {
        $wordpressId = $this->argument('event-wp-id');

        $updatedEvents = Event::where('wp_id', $wordpressId)->get();

        foreach($updatedEvents as $event) {

            if($event->created_at === $event->updated_at) {
                continue;
            }

            $purchasedTickets = PurchasedTicket::where('event_id', $event->id)->get();

            foreach($purchasedTickets as $ticket) {

                $data = [
                    'purchased_ticket_id' => $ticket->id
                ];

                $this->log($ticket, 'Adding notification to queue');

                WPNotification::sendCustomerNotification('cm-ticket-updated', null, $data);
    
                $this->log($ticket, 'Notification added to queue');
                
            }
        }
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
