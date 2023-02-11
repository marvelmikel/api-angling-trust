<?php

namespace Modules\Members\Observers;

use Modules\Members\Entities\Member;
use Modules\Members\Services\MemberIndexBuilder;

class MemberObserver
{
    public function saved(Member $member)
    {
        // todo: move to job and dispatch instead

        MemberIndexBuilder::updateMember($member);
    }

    public function deleted(Member $member)
    {
        MemberIndexBuilder::removeMember($member);
    }
}
