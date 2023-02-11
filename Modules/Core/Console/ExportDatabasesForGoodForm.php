<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ExportDatabasesForGoodForm extends Command
{
    protected $name = 'core:export-databases-for-goodform';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->exportDatabase('angling_trust_api');
        $this->exportDatabase('angling_trust_wp');
    }

    private function exportDatabase($database)
    {
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        exec("mysqldump -u {$username} -p{$password} {$database} --no-tablespaces > /home/goodform/export/{$database}.sql");
    }
}
