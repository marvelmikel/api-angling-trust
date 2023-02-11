<?php

namespace Modules\Events\Services\PurchasedTicketExport;

use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Services\PurchasedTicketExport;

class TeamExport extends PurchasedTicketExport
{
    protected function print_headers()
    {
        $this->add_header('Reference', 25);
        $this->add_header('Member Number', 20);
        $this->add_header('Purchased At', 25);
        $this->add_header('Cancelled At', 25);
        $this->add_header('Transaction Value', 20);
        $this->add_header('Ticket Name', 25);
        $this->add_header('Ticket', 50);

        $this->add_header('Team Name', 50);

        $this->add_header('POC First Name', 20);
        $this->add_header('POC Last Name', 20);
        $this->add_header('POC Telephone', 20);
        $this->add_header('POC Email', 20);
        $this->add_header('POC Address Line 1', 20);
        $this->add_header('POC Address Line 2', 20);
        $this->add_header('POC Address Town', 20);
        $this->add_header('POC Address County', 20);
        $this->add_header('POC Address Postcode', 20);

        for ($i = 1; $i <= $this->event->team_size; $i++) {
            $this->add_header("A{$i} First Name", 20);
            $this->add_header("A{$i} Last Name", 20);
        }
    }

    protected function print_row(PurchasedTicket $purchasedTicket)
    {
        $data = $purchasedTicket->data;

        $this->add_cell($purchasedTicket->reference);

        if ($purchasedTicket->member_id) {
            $this->add_cell($purchasedTicket->member->user->reference);
        } else {
            $this->add_cell('');
        }

        $this->add_cell($purchasedTicket->purchased_at->format('d/m/Y H:i:s'));

        if ($purchasedTicket->canceled_at) {
            $this->add_cell($purchasedTicket->canceled_at->format('d/m/Y H:i:s'));
        } else {
            $this->add_cell('');
        }

        $this->add_cell(as_price($purchasedTicket->price));
        $this->add_cell($purchasedTicket->ticket->name);
        $this->add_cell(env('WP_URL') . "/tickets/{$purchasedTicket->reference}.pdf");

        $this->add_cell($data['team_name']);

        $this->add_cell($data['point_of_contact']['first_name']);
        $this->add_cell($data['point_of_contact']['last_name']);
        $this->add_cell($data['point_of_contact']['telephone']);
        $this->add_cell($data['point_of_contact']['email']);
        $this->add_cell($data['point_of_contact']['address_line_1']);
        $this->add_cell($data['point_of_contact']['address_line_2']);
        $this->add_cell($data['point_of_contact']['address_town']);
        $this->add_cell($data['point_of_contact']['address_county']);
        $this->add_cell($data['point_of_contact']['address_postcode']);

        for ($i = 1; $i <= $this->event->team_size; $i++) {
            $this->add_cell($data['angler'][$i - 1]['first_name']);
            $this->add_cell($data['angler'][$i - 1]['last_name']);
        }
    }
}
