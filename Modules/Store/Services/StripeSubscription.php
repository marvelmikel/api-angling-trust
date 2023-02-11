<?php

namespace Modules\Store\Services;

use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipTypeCategory;
use Modules\Store\Entities\StripeSubscriptionPlan;

class StripeSubscription
{
    public static function getPlanForMember(Member $member)
    {
        return StripeSubscriptionPlan::query()
            ->where('category_id', $member->category_id)
            ->firstOrFail();
    }

    public static function getPlanForCategory(MembershipTypeCategory $category)
    {
        return StripeSubscriptionPlan::query()
            ->where('category_id', $category->id)
            ->firstOrFail();
    }
}
