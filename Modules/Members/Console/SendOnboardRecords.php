<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\OnboardRecord;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SendOnboardRecords extends Command
{
    protected $signature = 'members:send-onboard-records {pass} {limit}';

    private $succeeded = 0;
    private $failed = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pass = (int) $this->argument('pass');
        $limit = (int) $this->argument('limit');

        $records = OnboardRecord::query()
            ->whereNotCompleted()
            ->wherePass($pass)
            ->limit($limit)
            ->get();

        $count = count($records);

        $this->info("Found {$count} incomplete onboard records");
        $this->info('');
        $this->printProgress();

        foreach ($records as $record) {
            if ($record->sendEmail()) {
                $this->succeeded++;
            } else {
                $this->failed++;
            }

            $this->printProgress();
        }
    }

    private function printProgress()
    {
        $this->output->write("\033[1A");
        $this->output->write("                                                  \n");
        $this->output->write("\033[1A");
        $this->info('F: ' . $this->failed . ' | S: ' . $this->succeeded);
    }
}
