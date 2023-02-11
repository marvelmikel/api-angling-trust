<?php

namespace Modules\Events\Services\PurchasedTicketExport;

use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Services\PurchasedTicketExport;

class PairExport extends PurchasedTicketExport
{
    protected function print_headers()
    {
        $this->add_header('Reference', 25);
        $this->add_header('Member Number', 20);
        $this->add_header('Purchased At', 25);
        $this->add_header('Cancelled At', 25);
        $this->add_header('Transaction Value', 20);
        $this->add_header('Ticket Name', 25);
        $this->add_header('Ticket A', 50);
        $this->add_header('Ticket B', 50);

        $this->add_header('POC First Name', 20);
        $this->add_header('POC Last Name', 20);
        $this->add_header('POC Telephone', 20);
        $this->add_header('POC Email', 20);
        $this->add_header('POC Address Line 1', 20);
        $this->add_header('POC Address Line 2', 20);
        $this->add_header('POC Address Town', 20);
        $this->add_header('POC Address County', 20);
        $this->add_header('POC Address Postcode', 20);

        $this->add_header('AA First Name', 20);
        $this->add_header('AA Last Name', 20);
        $this->add_header('AA Email', 20);
        $this->add_header('AA Date of Birth', 20);
        $this->add_header('AA Rod Licence', 20);
        $this->add_header('AA Contact Number', 20);

        $this->add_header('AB First Name', 20);
        $this->add_header('AB Last Name', 20);
        $this->add_header('AB Email', 20);
        $this->add_header('AB Date of Birth', 20);
        $this->add_header('AB Rod Licence', 20);
        $this->add_header('AB Contact Number', 20);
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
        $this->add_cell(env('WP_URL') . "/tickets/{$purchasedTicket->reference}_a.pdf");
        $this->add_cell(env('WP_URL') . "/tickets/{$purchasedTicket->reference}_b.pdf");

        $this->add_cell($data['point_of_contact']['first_name']);
        $this->add_cell($data['point_of_contact']['last_name']);
        $this->add_cell($data['point_of_contact']['telephone']);
        $this->add_cell($data['point_of_contact']['email']);
        $this->add_cell($data['point_of_contact']['address_line_1']);
        $this->add_cell($data['point_of_contact']['address_line_2']);
        $this->add_cell($data['point_of_contact']['address_town']);
        $this->add_cell($data['point_of_contact']['address_county']);
        $this->add_cell($data['point_of_contact']['address_postcode']);

        $this->add_cell($data['angler_a']['first_name']);
        $this->add_cell($data['angler_a']['last_name']);
        $this->add_cell($data['angler_a']['email']);

        $date_of_birth = $data['angler_a']['date_of_birth']['day'] . '/' . $data['angler_a']['date_of_birth']['month'] . '/' . $data['angler_a']['date_of_birth']['year'];
        $this->add_cell($date_of_birth);

        $this->add_cell($data['angler_a']['rod_licence']);
        $this->add_cell($data['angler_a']['contact_number']);

        $this->add_cell($data['angler_b']['first_name']);
        $this->add_cell($data['angler_b']['last_name']);
        $this->add_cell($data['angler_b']['email']);

        $date_of_birth = $data['angler_b']['date_of_birth']['day'] . '/' . $data['angler_b']['date_of_birth']['month'] . '/' . $data['angler_b']['date_of_birth']['year'];
        $this->add_cell($date_of_birth);

        $this->add_cell($data['angler_b']['rod_licence']);
        $this->add_cell($data['angler_b']['contact_number']);
    }
}
