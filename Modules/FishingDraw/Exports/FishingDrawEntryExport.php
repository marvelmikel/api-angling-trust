<?php

namespace Modules\FishingDraw\Exports;

use Modules\FishingDraw\Entities\FishingDrawEntry;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FishingDrawEntryExport
{
    protected $spreadsheet;
    protected $sheet;
    protected $row;
    protected $column;
    protected $event;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    public function run($entries)
    {
        $this->row = 1;
        $this->column = 1;

        $this->print_headers();
        $this->next_row();

        foreach ($entries as $entry) {
            $this->print_row($entry);
            $this->next_row();
        }
    }

    public function print_headers()
    {
        $this->add_header('Reference', 25);
        $this->add_header('Member Number', 25);
        $this->add_header('Draw', 25);
        $this->add_header('Prize', 25);
        $this->add_header('Name', 25);
        $this->add_header('Email', 40);
        $this->add_header('Quantity', 25);
        $this->add_header('Total', 25);
        $this->add_header('Stripe Reference', 25);
        $this->add_header('Purchased At', 25);
    }

    public function print_row(FishingDrawEntry $entry)
    {
        $this->add_cell($entry->reference);

        if ($entry->member_id && $entry->member) {
            $this->add_cell($entry->member->user->reference);
        } else {
            $this->add_cell('');
        }

        $this->add_cell($entry->draw->name);
        if(isset($entry->prize->name)){
            $this->add_cell($entry->prize->name);
        }
        else {
            $this->add_cell('');
        }
        $this->add_cell($entry->name);
        $this->add_cell($entry->email);
        $this->add_cell($entry->quantity);
        $this->add_cell("£" . number_format($entry->payment_amount / 100, 2));
        $this->add_cell($entry->payment_id);
        $this->add_cell($entry->created_at->format('d/m/Y H:i'));
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
}
