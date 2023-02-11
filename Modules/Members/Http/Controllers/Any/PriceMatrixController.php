<?php

namespace Modules\Members\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Entities\MembershipTypeCategory;

class PriceMatrixController extends Controller
{
    public function clubOrSyndicate(Request $request)
    {
        $membershipType = MembershipType::query()
            ->where('slug', 'club-or-syndicate')
            ->firstOrFail();

        $at_only_categories = MembershipTypeCategory::query()
            ->where('membership_type_id', $membershipType->id)
            ->where('at_member', true)
            ->where('fl_member', false)
            ->get();

        $al_and_fl_categories = MembershipTypeCategory::query()
            ->where('membership_type_id', $membershipType->id)
            ->where('at_member', true)
            ->where('fl_member', true)
            ->get();

        $prices = [
            'at_only' => [],
            'at_and_fl' => []
        ];

        foreach ($at_only_categories as $category) {
            $prices['at_only'][$category->slug] = $category->price;
        }

        foreach ($al_and_fl_categories as $category) {
            $prices['at_and_fl'][$category->slug] = $category->price;
        }

        return $this->success([
            'price_matrix' => $prices
        ]);
    }
}
