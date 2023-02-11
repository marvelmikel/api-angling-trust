<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AddAdditionalContactToOrgMembers extends Command
{
    protected $name = 'members:add-additional-contact-to-org-members';

    private $count = 0;

    public function handle()
    {
        $members = Member::query()
            ->where('membership_type_id', '!=', 4)
            ->get();

        $this->info('');

        foreach ($members as $member) {
            $member->setMeta('additional_contact', '{"title":null,"first_name":null,"last_name":null,"email":null,"home_telephone":null,"mobile_telephone":null,"address_line_1":null,"address_line_2":null,"address_town":null,"address_county":null,"address_postcode":null}', 'array');
            $this->count++;
            $this->printProgress();
        }
    }

    private function printProgress()
    {
        $this->output->write("\033[1A");
        $this->info($this->count);
    }
}
