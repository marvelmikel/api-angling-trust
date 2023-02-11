<?php

namespace Modules\Events\Console;

use Illuminate\Console\Command;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Enums\EventType;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckTicketsHaveBeenGenerated extends Command
{
    protected $name = 'events:check-tickets-have-been-generated';

    private $total = 0;
    private $success = 0;
    private $failure = [];

    public function handle()
    {
        $purchasedTickets = PurchasedTicket::query()
            ->whereNotNull('purchased_at')
            ->orderByDesc('purchased_at')
            ->get();

        $this->total = count($purchasedTickets);

        foreach ($purchasedTickets as $purchasedTicket) {
            if (!$this->handlePurchasedTicket($purchasedTicket)) {
                $this->failure[] = $purchasedTicket;
            } else {
                $this->success++;
            }
        }

        $failureCount = count($this->failure);

        $this->info("Total: {$this->total} | Success: {$this->success} | Failure: {$failureCount}");

        sleep(3);

        foreach ($this->failure as $purchasedTicket) {
            $this->line("[{$purchasedTicket->purchased_at}] \e[1;31m{$purchasedTicket->reference}\033[0m");
        }
    }

    private function handlePurchasedTicket(PurchasedTicket $purchasedTicket)
    {
        $event = Event::find($purchasedTicket->event_id);

        if (!$event) {
            return true;
        }

        if ($event->type === EventType::PAIR) {
            return $this->ticketHasBeenGenerated($purchasedTicket->reference . '_a') && $this->ticketHasBeenGenerated($purchasedTicket->reference . '_b');
        }

        return $this->ticketHasBeenGenerated($purchasedTicket->reference);
    }

    private function ticketHasBeenGenerated($reference)
    {
        return file_exists(env('WP_PATH') . "/tickets/{$reference}.pdf");
    }
}
