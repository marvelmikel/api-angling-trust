<?php

namespace Modules\Core\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\NotificationQueue;
use Modules\Core\Services\WPNotification;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\PurchasedTicket;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateTicketsForUpdatedEvents extends Command
{
    protected $name = 'core:update-tickets-for-updated-events';

    public function handle()
    {
        $updatedEvents = Event::whereDate('updated_at', ">=", Carbon::now()->subMinute())->get();

        foreach($updatedEvents as $event) {

            if($event->created_at === $event->updated_at) {
                continue;
            }

            $purchasedTickets = PurchasedTicket::where('event_id', $event->id)->get();

            foreach($purchasedTickets as $ticket) {
                $path = env('WP_PATH');
                $wp_cli = env('WP_CLI');
        
                $id = $ticket->id;

                shell_exec("{$wp_cli} --path='{$path}' update_event_ticket {$id}");
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
