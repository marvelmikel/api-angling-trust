<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\OnboardRecord;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateOnboardRecordsForAllMembers extends Command
{
    protected $name = 'members:create-onboard-records-for-all-members';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $members = Member::all();

        $count = count($members);

        $this->info("Creating onboard records for {$count} members");
        $this->info("");
        $completed = 0;

        foreach ($members as $member) {
            $record = new OnboardRecord();
            $record->member_id = $member->id;
            $record->save();

            $completed++;

            $this->output->write("\033[1A");
            $this->output->write("                                                  \n");
            $this->output->write("\033[1A");
            $this->info($completed);
        }
    }
}
