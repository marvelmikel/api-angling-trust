<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Core\Services\DataWarehouse;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Enums\EventType;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Entities\MembershipTypeCategory;

class SendDataToWarehouse extends Command
{
    protected $name = 'members:send-data-to-warehouse';

    private $batch_limit = 500;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DataWarehouse::logInfo('==== STARTING SCRIPT ====');

        $membership_types = MembershipType::all()
            ->toArray();

        DataWarehouse::postData('membershiptypes', 1, $membership_types);

        $membership_type_categories = MembershipTypeCategory::all()
            ->toArray();

        DataWarehouse::postData('membershiptypecategories', 1, $membership_type_categories);

        $done = false;
        $batch = 1;

        while (!$done) {
            $members = $this->getMembers($batch);
            DataWarehouse::postData('members', $batch, $members);

            if (count($members) < $this->batch_limit) {
                $done = true;
            }

            $batch++;
        }

        $done = false;
        $batch = 1;

        while (!$done) {
            $purchasedTickets = $this->getPurchasedTickets($batch);
            DataWarehouse::postData('purchasedtickets', $batch, $purchasedTickets);

            if (count($purchasedTickets) < $this->batch_limit) {
                $done = true;
            }

            $batch++;
        }

        DataWarehouse::logInfo('==== COMPLETED SCRIPT ====');
    }

    private function getMembers($batch)
    {
        $query =  Member::query();

        if ($batch !== 1) {
            $query->skip($this->batch_limit * ($batch - 1));
        }

        $members = $query
            ->take($this->batch_limit)
            ->get();

        $data = [];

        foreach ($members as $index => $member) {
            $data[] = $this->transformMember($member);
        }

        return $data;
    }

    private function transformMember(Member $member)
    {
        $user = $member->user;
        $data = $member->toArray();

        $data['email'] = $user->email;
        $data['membership_number'] = $user->reference;
        $data['meta'] = [];

        foreach ($member->meta as $meta) {
            $data['meta'][$meta->key] = $meta->value;
        }

        return $data;
    }

    private function getPurchasedTickets($batch)
    {
        $query = PurchasedTicket::query();

        if ($batch !== 1) {
            $query->skip($this->batch_limit * ($batch - 1));
        }

        $purchasedTickets = $query
            ->take($this->batch_limit)
            ->get();

        $data = [];

        foreach ($purchasedTickets as $index => $purchasedTicket) {
            $data[] = $this->transformPurchasedTicket($purchasedTicket);
        }

        return $data;
    }

    private function transformPurchasedTicket(PurchasedTicket $purchasedTicket)
    {
        $event = Event::withTrashed()
            ->where('id', $purchasedTicket->event_id)
            ->firstOrFail();

        $data = [
            'member_id' => $purchasedTicket->member_id,
            'event' => $this->transformEvent($event),
            'reference' => $purchasedTicket->reference,
            'price' => $purchasedTicket->price,
            'purchased_at' => $purchasedTicket->purchased_at,
            'data' => $purchasedTicket->data
        ];

        if ($event->type === EventType::PAIR) {
            $data['ticket_a'] = env('WP_URL') . '/tickets/' . $purchasedTicket->reference . '_a.pdf';
            $data['ticket_b'] = env('WP_URL') . '/tickets/' . $purchasedTicket->reference . '_b.pdf';
        } else {
            $data['ticket'] = env('WP_URL') . '/tickets/' . $purchasedTicket->reference . '.pdf';
        }

        return $data;
    }

    private function transformEvent(Event $event)
    {
        if (!is_array($event->details)) {
            return [
                'name' => $event->name,
            ];
        }

        return array_merge([
            'name' => $event->name,
        ], $event->details);
    }
}
