<?php

namespace Modules\Events\Transformers;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Events\Entities\Event;

class EventTransformer implements EntityTransformer
{
    /**
     * @param Event $event
     * @return array
     */
    public function data($event): array
    {
        $data = [
            'id' => $event->id,
            'wp_id' => $event->wp_id,
            'post_type' => $event->post_type,
            'name' => $event->name,
            'slug' => $event->slug,
            'type' => $event->type,
            'department_code' => $event->department_code,
            'nominal_code' => $event->nominal_code,
            'min_age' => $event->min_age,
            'max_age' => $event->min_age,
            'member_only' => $event->member_only,
            'has_pools_payments' => (bool) $event->has_pools_payments,
            'pools_payments' => $event->pools_payments,
            'team_size' => (int) $event->team_size,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
            'details' => $event->details,
            'resend_ticket_on_update' => $event->resend_ticket_on_update,
            'bursary_code' => $event->bursary_code,
        ];

        if ($event->category_id) {
            $data['category_logo_url'] = $event->category->logo_url;
            $data['category'] = $event->category->wp_id;
            $data['category_limit'] = $event->category->ticket_limit;
        }

        return $data;
    }

    public function relations(): array
    {
        return [];
    }
}
