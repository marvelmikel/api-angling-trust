<?php

namespace Modules\Members\Console;

use Carbon\Carbon;
use Modules\Core\Console\ProgressCommand;
use Modules\Members\Entities\Member;
use Modules\Members\Services\MemberIndexBuilder;

class ReindexMembersUpdatedSince extends ProgressCommand
{
    protected $signature = 'members:reindex-members-updated-since {days}';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $daysAgo = $this->argument('days');

        $cutoff = Carbon::now()->subDays($daysAgo);

        $members =  Member::query()
            ->where('updated_at', '>=', $cutoff)
            ->get();

        $this->setTotal(count($members));

        $this->start();

        foreach ($members as $member) {
            $this->handleItem(function() use ($member) {
                return MemberIndexBuilder::updateMember($member);
            });
        }

        $this->stop();
    }
}
