<?php

namespace Modules\Members\Repositories;

use Illuminate\Http\Request;
use Modules\Auth\Enums\SiteOrigin;
use Modules\Members\Entities\MemberIndex;
use Modules\Members\Entities\MembershipTypeCategory;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use DB;
use Modules\Core\Entities\Address;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Enums\CreatedVia;
use Modules\Members\Services\MemberExpiryDateCalculator;
use Modules\Members\Services\MembershipReferenceGenerator;
use Modules\Store\Enums\PaymentProvider;
use Webpatser\Uuid\Uuid;

class MemberRepository
{
    public static function create(array $data, MembershipType $membershipType)
    {
        DB::beginTransaction();

        $user = Sentinel::registerAndActivate(array_merge($data['user'], [
            'reference' => MembershipReferenceGenerator::generate($membershipType)
        ]));

        $member = new Member();
        $member->user_id = $user->id;
        $member->membership_type_id = $membershipType->id;
        $member->at_member = $data['at_member'] ?? true;
        $member->fl_member = $data['fl_member'] ?? false;

        unset($data['user']);

        $member->fill($data);

        if (isset($data['opt_in_1']) && $data['opt_in_1']) {
            $member->opt_in_1 = true;
        } else {
            $member->opt_in_1 = false;
        }

        if (isset($data['opt_in_2']) && $data['opt_in_2']) {
            $member->opt_in_2 = true;
        } else {
            $member->opt_in_2 = false;
        }

        $member->save();

        DB::commit();

        return $member;
    }

    public static function import(array $data, MembershipType $membershipType)
    {
        DB::beginTransaction();

        $user = Sentinel::registerAndActivate(array_merge($data['user']));

        $member = new Member();
        $member->user_id = $user->id;
        $member->membership_type_id = $membershipType->id;
        $member->at_member = $data['at_member'];
        $member->fl_member = $data['fl_member'];

        unset($data['user']);

        $member->fill($data);

        if (isset($data['opt_in_1']) && $data['opt_in_1']) {
            $member->opt_in_1 = true;
        } else {
            $member->opt_in_1 = false;
        }

        if (isset($data['opt_in_2']) && $data['opt_in_2']) {
            $member->opt_in_2 = true;
        } else {
            $member->opt_in_2 = false;
        }

        $member->save();

        DB::commit();

        return $member;
    }

    public static function changeMembershipType(Member $member, $id)
    {
        $member->membership_type_id = $id;

        return $member->save();
    }

    public static function updateCategory(Member $member, $data)
    {
        if (isset($data['category_id'])) {
            $category_id = $data['category_id'];
        } else {
            $category = MembershipTypeCategory::query()
                ->where('membership_type_id', $member->membership_type_id)
                ->where('slug', $data['category'])
                ->where('at_member', $member->at_member)
                ->where('fl_member', $member->fl_member)
                ->firstOrFail();

            $category_id = $category->id;
        }

        $member->category_id = $category_id;
        return $member->save();
    }

    public static function update(Member $member, array $data)
    {
        try {

            $member->fill($data);
            $member->save();

            $member->user->update($data['user']);

            return true;

        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function updateAddress(Member $member, array $data)
    {
        $member->address->update($data);
        $member->postcode = $data['postcode'];

        return $member->save();
    }

    public static function updatePreferences(Member $member, array $data)
    {
        $preferences = [];

        $preferences = array_merge($preferences, $data['disciplines']);
        $preferences = array_merge($preferences, $data['regions']);

        if (isset($data['division_id']) && $data['division_id'] !== '0') {
            $preferences[] = $data['division_id'];
        }

        $member->preferences()->sync($preferences);
    }

    public static function updateNotes(Member $member, $notes)
    {
        $member->notes = $notes;

        return $member->save();
    }

    public static function completeRegistration(Member $member, $payment_is_recurring): bool
    {
        $member->registered_at = Carbon::now();
        $member->payment_is_recurring = $payment_is_recurring;

        if ($member->isLifetimeMember()) {
            return $member->save();
        }

        $member->expires_at = MemberExpiryDateCalculator::calculate($member);
        $member->save();

        return $member->save();
    }

    public static function updateMembershipType(Member $member, $id)
    {
        $member->membership_type_id = $id;

        return $member->save();
    }

    public static function delete(Member $member)
    {
        return DB::transaction(function() use ($member) {
            $user = $member->user;
            $member->delete();
            $user->delete();
            return true;
        });
    }

    public static function quickSearch(Request $request)
    {
        $query = MemberIndex::query();

        if (get_site_origin() === SiteOrigin::FISH_LEGAL) {
            $query->where('fl_member', 1);
        }
        $status = $request->get('status');

        if ($status === 'active') {
            $query->whereActive();
        } else if ($status === 'expired'){
            $query->whereExpired();
        } else if ($status === 'incomplete') {
            $query->whereIncomplete();
        } else {
            throw new \InvalidArgumentException("Invalid member status: {$status}");
        }

        if ($request->has('membership_type_id')) {
            $query->where('membership_type_slug', $request->get('membership_type_id'));
        }

        if ($request->has('is_frozen')) {
            $is_frozen = $request->get('is_frozen');

            if ($is_frozen === 'true') {
                $query->where('is_suspended', true);
            }

            if ($is_frozen === 'false') {
                $query->where('is_suspended', false);
            }
        }

        if ($request->has('reference')) {
            $query->where('reference', $request->get('reference'));
        }

        if ($request->has('full_name')) {
            $query->where('full_name', 'LIKE', '%' . $request->get('full_name') . '%');
        }

        if ($request->has('email')) {
            $query->where('email', $request->get('email'));
        }

        if ($request->has('postcode')) {
            $query->where('address_postcode', $request->get('postcode'));
        }

        if ($request->has('primary_contact')) {
            $primary_contact = $request->get('primary_contact');

            $query->whereNotNull('primary_contact');

            $primary_contact_bits = explode(' ', $primary_contact);

            foreach ($primary_contact_bits as $bit) {
                $query->where('primary_contact', 'LIKE', '%' . $bit . '%');
            }
        }

        $members = $query->orderBy($request->get('sort_by'), $request->get('sort'))
            ->paginate(20, '*', 'paged');

        return $members;
    }
}
