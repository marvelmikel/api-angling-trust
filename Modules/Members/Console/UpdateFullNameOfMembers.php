<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateFullNameOfMembers extends ProgressCommand
{
    protected $signature = 'members:update-full-name-of-members {membership_type_id}';

    public function handle()
    {
        $membership_type_id = $this->argument('membership_type_id');

        $members = Member::query()
            ->where('membership_type_id', $membership_type_id)
            ->get();

        $this->setTotal(count($members));

        $this->start();

        foreach ($members as $member) {
            $this->handleItem(function() use ($member) {
                if ($member->first_name !== null) {
                    $member->full_name = $member->first_name . ' ' . $member->last_name;
                } else {
                    $member->full_name = $member->getMetaValue('club_name');
                }

                return $member->save();
            });
        }

        $this->stop();
    }
}
