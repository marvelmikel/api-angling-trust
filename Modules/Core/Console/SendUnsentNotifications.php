<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Modules\Core\Entities\NotificationQueue;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SendUnsentNotifications extends Command
{
    protected $name = 'core:send-unsent-notifications';

    public function handle()
    {
        $notifications = NotificationQueue::query()
            ->whereNotSent()
            ->get();

        foreach ($notifications as $notification) {
            $notification->send();
        }
    }
}
