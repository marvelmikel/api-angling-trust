<?php

namespace Modules\Events\Services;

use Modules\Events\Entities\Event;
use Modules\Events\Entities\PurchasedTicket;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

abstract class PurchasedTicketExport
{
    protected $spreadsheet;
    protected $sheet;
    protected $row;
    protected $column;
    protected $event;

    public function __construct(Event $event)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->event = $event;
    }

    public function run($purchased_tickets)
    {
        $this->row = 1;
        $this->column = 1;

        $this->print_headers();
        $this->print_pools_headers();
        $this->next_row();

        foreach ($purchased_tickets as $purchased_ticket) {
            $this->print_row($purchased_ticket);
            $this->print_pools_row($purchased_ticket);
            $this->next_row();
        }
    }

    public function get_base64()
    {
        $writer = new Xlsx($this->spreadsheet);

        ob_start();
        $writer->save('php://output');
        $data = ob_get_clean();

        return base64_encode($data);
    }

    protected function add_header($name, $width)
    {
        $this->sheet->setCellValueExplicit($this->get_coordinate(), $name, DataType::TYPE_STRING);
        $this->sheet->getColumnDimension($this->get_column())->setWidth($width);

        $this->column++;
    }

    protected function add_cell($value)
    {
        $this->sheet->setCellValueExplicit($this->get_coordinate(), $value, DataType::TYPE_STRING);

        $this->column++;
    }

    protected function get_column()
    {
        $c = intval($this->column);
        if ($c <= 0) return '';

        $column = '';

        while($c != 0){
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $column = chr(65 + $p) . $column;
        }

        return $column;
    }

    protected function get_row()
    {
        return $this->row;
    }

    protected function next_row()
    {
        $this->row++;
        $this->column = 1;
    }

    protected function get_coordinate()
    {
        return $this->get_column() . $this->get_row();
    }

    private function print_pools_headers()
    {
        $pools_payments = $this->event->pools_payments ?? [];

        if (count($pools_payments) >= 1) {
            $this->add_header($pools_payments[0]['label'], 18);
        }

        if (count($pools_payments) >= 2) {
            $this->add_header($pools_payments[1]['label'], 18);
        }

        if (count($pools_payments) >= 3) {
            $this->add_header($pools_payments[2]['label'], 18);
        }

        if (count($pools_payments) >= 4) {
            $this->add_header($pools_payments[3]['label'], 18);
        }

        if (count($pools_payments) >= 5) {
            $this->add_header($pools_payments[4]['label'], 18);
        }
    }

    private function print_pools_row(PurchasedTicket $purchasedTicket)
    {
        $data = $purchasedTicket->data;

        if ($data['pools_payment'][0] === true) {
            $this->add_cell('Yes');
        }

        if ($data['pools_payment'][1] === true) {
            $this->add_cell('Yes');
        }

        if ($data['pools_payment'][2] === true) {
            $this->add_cell('Yes');
        }

        if ($data['pools_payment'][3] === true) {
            $this->add_cell('Yes');
        }

        if ($data['pools_payment'][4] === true) {
            $this->add_cell('Yes');
        }
    }

    abstract protected function print_headers();
    abstract protected function print_row(PurchasedTicket $purchasedTicket);
}
