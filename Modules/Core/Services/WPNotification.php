<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\NotificationQueue;

class WPNotification
{
    public static function sendCustomerNotification($reference, $to, $data = [])
    {
        $notification = NotificationQueue::create([
            'customer_notification' => true,
            'reference' => $reference,
            'to' => $to,
            'data' => $data
        ]);
    }

    public static function sendAdminNotification($reference, $data = [])
    {
        NotificationQueue::create([
            'admin_notification' => true,
            'reference' => $reference,
            'data' => $data
        ]);
    }
}
