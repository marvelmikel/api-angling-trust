<?php

namespace Modules\Auth\Services;

use Illuminate\Http\Request;
use Modules\Members\Entities\Member;

class RegistrationManager
{
    public static function run($membershipType, $step, Member $member, Request $request)
    {
        $method = "{$membershipType}-step-{$step}";
        $method = lower_camel_case($method);

        if (method_exists(self::class, $method)) {
            self::$method($member, $request);
        }
    }

    public static function fisheryStep1(Member $member, Request $request)
    {
        $member->fl_member = true;
        $member->save();
    }

    public static function clubOrSyndicateStep2(Member $member, Request $request)
    {
        $member->fl_member = $request->input('fl_member') ?? false;
        $member->save();
    }

    public static function individualMemberStep4(Member $member, Request $request)
    {
        if ($member->category->slug === 'life') {
            $member->payment_is_recurring = false;
            $member->save();
        }
    }
}
