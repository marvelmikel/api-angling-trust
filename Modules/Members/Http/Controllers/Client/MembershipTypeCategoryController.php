<?php

namespace Modules\Members\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Illuminate\Http\Request;
use Modules\Members\Entities\MembershipTypeCategory;
use Modules\Members\Transformers\MembershipTypeCategoryTransformer;

class MembershipTypeCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = MembershipTypeCategory::query()
            ->where('membership_type_id', $request->input('membership_type_id'))
            ->where('at_member', $request->input('at_member'))
            ->where('fl_member', $request->input('fl_member'))
            ->get();

        return $this->success([
            'categories' => Transform::entities($categories, MembershipTypeCategoryTransformer::class)
        ]);
    }
}
