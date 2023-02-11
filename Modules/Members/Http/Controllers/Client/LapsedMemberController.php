<?php

namespace Modules\Members\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Http\Requests\Member\Client\ActivateLapsedMemberRequest;
use Modules\Members\Services\MembershipReferenceGenerator;

class LapsedMemberController extends Controller
{
    public function activate(Member $member, ActivateLapsedMemberRequest $request)
    {
        $membership_type = MembershipType::findOrFaiL($request->get('membership_type_id'));

        $member->membership_type_id = $request->get('membership_type_id');
        $member->at_member = $request->get('at_member');
        $member->fl_member = $request->get('fl_member');
        $member->category_id = $request->get('category_id');

        $user = $member->user;

        if ($membership_type->slug !== 'individual-member') {
            $user->reference = MembershipReferenceGenerator::generate($membership_type);
        }

        $member->save();
        $user->save();

        $member->updateFullName();

        return $this->success();
    }
}
