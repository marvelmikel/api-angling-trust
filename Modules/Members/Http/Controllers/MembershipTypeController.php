<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Entities\MembershipTypeCategory;
use Modules\Members\Transformers\MembershipTypeTransformer;

class MembershipTypeController extends Controller
{
    public function index()
    {
        $membership_types = MembershipType::all(); // todo: all that you can register for

        return $this->success([
            'membership_types' => Transform::entities($membership_types, MembershipTypeTransformer::class)
        ]);
    }

    public function categories(int $membershipTypeId): Response
    {
        return $this->success(MembershipTypeCategory::whereMembershipTypeId($membershipTypeId)
            ->whereNotNull('slug')
            ->whereNotNull('name')
            ->where('slug', '!=', '')
            ->where('name', '!=', '')
            ->get()
        );
    }
}
