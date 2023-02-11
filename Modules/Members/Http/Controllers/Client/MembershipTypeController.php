<?php

namespace Modules\Members\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Http\Requests\Membership\UpdateMembershipTypeRequest;
use Modules\Members\Repositories\MembershipTypeRepository;
use Modules\Members\Transformers\MembershipTypeTransformer;

class MembershipTypeController extends Controller
{
    public function index()
    {
        $membership_types = MembershipType::all();

        return $this->success([
            'membership_types' => Transform::entities($membership_types, MembershipTypeTransformer::class)
        ]);
    }

    public function show(MembershipType $membershipType)
    {
        return $this->success([
            'membership_type' => Transform::entity($membershipType, MembershipTypeTransformer::class)
        ]);
    }

    public function update(UpdateMembershipTypeRequest $request)
    {
        MembershipTypeRepository::createOrUpdate($request->input('wp_id'), $request->all());

        return $this->success();
    }

    public function destroy(MembershipType $membershipType)
    {
        $membershipType->delete();

        return $this->success();
    }

    public function restore(MembershipType $membershipType)
    {
        $membershipType->restore();

        return $this->success();
    }
}
