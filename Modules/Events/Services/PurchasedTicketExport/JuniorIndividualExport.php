<?php

namespace Modules\Events\Services\PurchasedTicketExport;

use Modules\Events\Entities\PurchasedTicket;
use Modules\Events\Services\PurchasedTicketExport;

class JuniorIndividualExport extends PurchasedTicketExport
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
        $this->add_header('First Name', 10);
        $this->add_header('Last Name', 10);
        $this->add_header('Email', 25);
        $this->add_header('Contact Number', 20);
        $this->add_header('Date of Birth', 15);
        $this->add_header('Disability', 20);
        $this->add_header('Fishing Licence Number', 25);

        $this->add_header('Parent Name', 20);
        $this->add_header('Parent Email', 20);
        $this->add_header('Parent Telephone', 20);
        $this->add_header('Photos Consent', 20);
        $this->add_header('Medical Treatment Consent', 20);

        $this->add_header('Address Line 1', 20);
        $this->add_header('Address Line 2', 20);
        $this->add_header('Address Town', 20);
        $this->add_header('Address County', 20);
        $this->add_header('Address Postcode', 20);
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
        $this->add_cell($data['first_name']);
        $this->add_cell($data['last_name']);
        $this->add_cell($data['email']);
        $this->add_cell($data['contact_number']);

        $date_of_birth = $data['date_of_birth']['day'] . '/' . $data['date_of_birth']['month'] . '/' . $data['date_of_birth']['year'];
        $this->add_cell($date_of_birth);

        $this->add_cell($data['disability']);
        $this->add_cell($data['fishing_licence_number']);

        $this->add_cell($data['parent']['name']);
        $this->add_cell($data['parent']['email']);
        $this->add_cell($data['parent']['telephone']);

        if ($data['consent']['photos'] == true) {
            $this->add_cell('Yes');
        } else {
            $this->add_cell('No');
        }

        if ($data['consent']['medical_treatment'] == true) {
            $this->add_cell('Yes');
        } else {
            $this->add_cell('No');
        }

        $this->add_cell($data['address_line_1']);
        $this->add_cell($data['address_line_2']);
        $this->add_cell($data['address_town']);
        $this->add_cell($data['address_county']);
        $this->add_cell($data['address_postcode']);
    }
}
