<?php

namespace Modules\Events\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Events\Entities\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->where('end_date', '>=', Carbon::today())
            ->get();

        return $this->success([
            'events' => $events
        ]);
    }
}
