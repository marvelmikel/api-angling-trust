<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Modules\Members\Services\MemberIndexBuilder;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RemoveDeletedMembersFromMembersIndex extends ProgressCommand
{
    protected $signature = 'members:remove-deleted-members-from-members-index';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $members =  Member::onlyTrashed()->get();

        $this->setTotal(count($members));

        $this->start();

        foreach ($members as $member) {
            $this->handleItem(function() use ($member) {
                return MemberIndexBuilder::removeMember($member);
            });
        }

        $this->stop();
    }
}
