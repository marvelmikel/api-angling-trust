<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Members\Services\MemberIndexBuilder;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RepairMembersIndex extends Command
{
    protected $signature = 'members:repair-members-index';

    const BATCH_SIZE = 500;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $batch = 0;

        while ($members = $this->getMembers($batch)) {
            $this->line("[Batch {$batch}]");

            foreach ($members as $member) {
                $this->checkMember($member);
            }

            $batch++;
        }
    }

    private function getMembers($batch)
    {
        $members = Member::query()
            ->limit(self::BATCH_SIZE)
            ->skip($batch * self::BATCH_SIZE)
            ->get();

        if (count($members) > 0) {
            return $members;
        }

        return null;
    }

    private function checkMember(Member $member)
    {
        if (MemberIndexBuilder::hadBeenIndexedSinceUpdate($member)) {
            return true;
        }

        MemberIndexBuilder::updateMember($member);
        $this->info("- {$member->id}");

        return true;
    }
}
