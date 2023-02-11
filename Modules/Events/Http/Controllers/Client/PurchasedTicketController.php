<?php

namespace Modules\Events\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Enums\EventType;
use Modules\Events\Services\PurchasedTicketExport\IndividualExport;
use Modules\Events\Services\PurchasedTicketExport\JuniorIndividualExport;
use Modules\Events\Services\PurchasedTicketExport\PairExport;
use Modules\Events\Services\PurchasedTicketExport\TeamExport;
use Modules\Events\Transformers\PurchasedTicketTransformer;

class PurchasedTicketController extends Controller
{
    public function show($reference)
    {
        $purchasedTicket = PurchasedTicket::query()
            ->where('reference', $reference)
            ->first();

        if (!$purchasedTicket) {
            return $this->error('Invalid ticket reference');
        }

        if ($purchasedTicket->member_id) {
            $purchasedTicket->load('member');
        }

        $purchasedTicket->load('event');

        return $this->success([
            'purchasedTicket' => Transform::entity($purchasedTicket, PurchasedTicketTransformer::class)
        ]);
    }

    public function cancel($reference)
    {
        $purchasedTicket = PurchasedTicket::query()
            ->where('reference', $reference)
            ->first();

        if (!$purchasedTicket) {
            return $this->error('Invalid ticket reference');
        }

        if (!$purchasedTicket->cancel()) {
            return $this->error();
        }

        return $this->success();
    }

    public function export(Event $event)
    {
        $purchased_tickets = PurchasedTicket::query()
            ->where('event_id', $event->id)
            ->whereNotNull('purchased_at')
            ->get();

        $export = null;

        if ($event->type === EventType::INDIVIDUAL) {
            $export = new IndividualExport($event);
        }

        if ($event->type === EventType::JUNIOR_INDIVIDUAL) {
            $export = new JuniorIndividualExport($event);
        }

        if ($event->type === EventType::PAIR) {
            $export = new PairExport($event);
        }

        if ($event->type === EventType::TEAM) {
            $export = new TeamExport($event);
        }

        if (!$export) {
            return $this->error('No Exporter Found');
        }

        $export->run($purchased_tickets);

        return $this->success([
            'data' => $export->get_base64()
        ]);
    }
}
