<?php

namespace Modules\Events\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Modules\Events\Entities\Event;
use Modules\Events\Http\Requests\Event\UpdateEventRequest;
use Modules\Events\Repositories\EventRepository;
use Modules\Events\Transformers\TicketTransformer;

class EventController extends Controller
{
    public function index()
    {
        return Event::all();
    }

    public function show(Event $event)
    {
        return $event;
    }

    public function update(UpdateEventRequest $request)
    {
        EventRepository::createOrUpdate($request->input('wp_id'), $request->all());

        return $this->success();
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return $this->success();
    }

    public function restore(Event $event)
    {
        $event->restore();

        return $this->success();
    }

    public function tickets(Event $event)
    {
        return $this->success([
            'tickets' => Transform::entities($event->tickets, TicketTransformer::class)
        ]);
    }
}
