<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class OptInAllImportedMembersToGambling extends ProgressCommand
{
    protected $name = 'members:opt-in-all-imported-members-to-gambling';

    public function handle()
    {
        $members = Member::query()
            ->where('is_imported', true)
            ->get();

        $this->setTotal(count($members));

        $this->start();

        foreach ($members as $member) {
            $this->handleItem(function() use ($member) {
                return $member->setMeta('raffle_opt_out', true, 'boolean');
            });
        }

        $this->stop();
    }
}
