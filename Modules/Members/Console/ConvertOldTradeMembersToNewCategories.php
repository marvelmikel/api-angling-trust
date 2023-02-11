<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ConvertOldTradeMembersToNewCategories extends Command
{
    protected $name = 'members:convert-old-trade-members-to-new-categories';

    public function handle()
    {
        $membership = MembershipType::query()
            ->where('slug', 'trade-member')
            ->firstOrFail();

        $members = Member::query()
            ->where('membership_type_id', $membership->id)
            ->get();

        foreach ($members as $member) {
            $member->category_id = '76';
            $member->save();
        }
    }
}
