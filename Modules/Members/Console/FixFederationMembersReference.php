<?php

namespace Modules\Members\Console;

use Illuminate\Console\Command;
use Modules\Members\Entities\Member;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FixFederationMembersReference extends Command
{
    protected $name = 'members:fix-federation-members-reference';

    public function handle()
    {
        $members = Member::query()
            ->where('membership_type_id', 8)
            ->get();

        foreach ($members as $member) {
            $user = $member->user;

            if (strpos($user->reference, 'FED') !== 0) {
                $user->reference = 'FED' . $user->reference;
                $user->save();
            }
        }
    }
}
