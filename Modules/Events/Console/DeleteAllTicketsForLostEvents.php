<?php

namespace Modules\Events\Console;

use Illuminate\Console\Command;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\PurchasedTicket;

class DeleteAllTicketsForLostEvents extends Command
{
    protected $name = 'events:delete-all-tickets-for-lost-events';

    public function handle()
    {
        $purchasedTickets = PurchasedTicket::all();

        $total = count($purchasedTickets);
        $this->info("Found {$total} purchased tickets");

        $deleted = 0;
        $brokenMembers = collect();

        foreach ($purchasedTickets as $purchasedTicket) {
            if (!$this->checkPurchasedTicket($purchasedTicket)) {
                $purchasedTicket->delete();

                $deleted++;

                if ($purchasedTicket->member_id) {
                    $brokenMembers->add($purchasedTicket->member_id);
                }
            }
        }

        $brokenMembers = $brokenMembers->unique()->count();

        $this->info("Deleted {$deleted} purchased tickets");
        $this->info("{$brokenMembers} broken members were fixed");
    }

    public function checkPurchasedTicket($purchasedTicket)
    {
        $eventId = $purchasedTicket->event_id;

        return Event::withTrashed()
            ->where('id', $eventId)
            ->exists();
    }
}
