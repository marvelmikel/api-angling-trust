<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Modules\Members\Services\MemberIndexBuilder;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RebuildMembersIndex extends ProgressCommand
{
    protected $signature = 'members:rebuild-members-index {batch}';

    private const BATCH_SIZE = 5000;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $batch = $this->argument('batch');

        $query =  Member::query();

        if ($batch !== 1) {
            $query->skip(self::BATCH_SIZE * ($batch - 1));
        }

        $members = $query
            ->take(self::BATCH_SIZE)
            ->get();

        $this->setTotal(count($members));

        $this->start();

        foreach ($members as $member) {
            $this->handleItem(function() use ($member) {
                if (MemberIndexBuilder::hasBeenIndexed($member)) {
                    return true;
                }

                return MemberIndexBuilder::updateMember($member);
            });
        }

        $this->stop();
    }
}
