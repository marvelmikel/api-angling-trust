<?php

namespace Modules\Events\Console;

use Illuminate\Console\Command;
use Modules\Events\Entities\PurchasedTicket;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ExportRecentlyCanceledTickets extends Command
{
    protected $name = 'events:export-recently-canceled-tickets';

    public function handle()
    {
        $tickets = PurchasedTicket::query()
            ->where('canceled_at', '>=', now()->subDays(50))
            ->orderByDesc('canceled_at')
            ->get();

        $file = fopen(storage_path('exports/canceled_tickets.csv'), 'w');

        foreach ($tickets as $ticket) {
            $data = [];

            if (isset($ticket->data['first_name'])) {
                $data[] = $ticket->data['first_name'] . ' ' . $ticket->data['last_name'];
                $data[] = $ticket->data['email'];
            }

            if (isset($ticket->data['point_of_contact'])) {
                $data[] = $ticket->data['point_of_contact']['first_name'] . ' ' . $ticket->data['point_of_contact']['last_name'];
                $data[] = $ticket->data['point_of_contact']['email'];
            }

            if (count($data) === 0) {
                throw new \InvalidArgumentException('Name not found');
            }

            $data[] = $ticket->reference;
            $data[] = $ticket->event->name;
            $data[] = $ticket->canceled_at->format('d/m/Y');

            fputcsv($file, $data);
        }

        fclose($file);
    }
}
