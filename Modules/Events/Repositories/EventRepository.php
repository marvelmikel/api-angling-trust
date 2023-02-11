<?php

namespace Modules\Events\Repositories;

use Modules\Events\Entities\Event;

class EventRepository
{
    public static function findOrNew($wp_id): Event
    {
        return Event::query()
            ->where('wp_id', $wp_id)
            ->firstOrNew([]);
    }

    public static function createOrUpdate($wp_id, array $data): Event
    {
        $event = self::findOrNew($wp_id);
        $event->fill($data);
        $event->save();

        if (isset($data['tickets'])) {
            TicketRepository::updateAllForEvent($event, $data['tickets']);
        }

        return $event;
    }
}
