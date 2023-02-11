<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateFullNameOfAllMembers extends ProgressCommand
{
    protected $name = 'members:update-full-name-of-all-members';

    public function handle()
    {
        $members = Member::query()
            ->where('membership_type_id', '!=', 18)
            ->whereNull('full_name')
            ->orWhere('full_name', '')
            ->get();

        $this->setTotal(count($members));

        $this->start();

        foreach ($members as $member) {
            $this->handleItem(function() use ($member) {
                return $member->updateFullName();
            });
        }

        $this->stop();
    }
}
