<?php

namespace Modules\Members\Services;

use Modules\Members\Entities\Member;
use Modules\Members\Entities\MemberIndex;

class MemberIndexBuilder
{
    public static function hasBeenIndexed(Member $member)
    {
        return MemberIndex::query()
            ->where('member_id', $member->id)
            ->exists();
    }

    public static function hadBeenIndexedSinceUpdate(Member $member)
    {
        $index = MemberIndex::query()
            ->where('member_id', $member->id)
            ->first();

        if (!$index) {
            return false;
        }

        if ($index->expires_at && $member->expires_at) {
            if ($index->expires_at->format('Y-m-d') !== $member->expires_at->format('Y-m-d')) {
                return false;
            }
        }

        if ($index->registered_at && $member->registered_at) {
            if ($index->registered_at->format('Y-m-d') !== $member->registered_at->format('Y-m-d')) {
                return false;
            }
        }

        if ($index->registered_at === null && $member->registered_at !== null) {
            return false;
        }

        return $index->updated_at >= $member->updated_at;
    }

    public static function updateMember(Member $member)
    {
        $data = [
            'member_id' => $member->id,
            'at_member' => $member->at_member,
            'fl_member' => $member->fl_member,
            'reference' => $member->user->reference,
            'membership_type_id' => $member->membership_type_id,
            'membership_type_slug' => $member->membershipType->slug,
            'membership_type_name' => $member->membershipType->name,
            'full_name' => $member->full_name,
            'email' => $member->user->email,
            'address_postcode' => $member->address_postcode,
            'is_suspended' => $member->is_suspended ?? false,
            'expires_at' => $member->expires_at,
            'registered_at' => $member->registered_at,
            'updated_at' => $member->updated_at
        ];

        if ($member->hasMeta('primary_contact')) {
            $data['primary_contact'] = $member->getMeta('primary_contact')->getRawValue();
        }

        $index = MemberIndex::query()
            ->where('member_id', $member->id)
            ->first();

        if (!$index) {
            MemberIndex::create($data);
        } else {
            MemberIndex::query()
            ->where('member_id', $member->id)
            ->update($data);
        }

        return true;
    }

    public static function removeMember(Member $member)
    {
        $index = MemberIndex::query()
            ->where('member_id', $member->id)
            ->first();

        if (!$index) {
            return true;
        }

        $index->delete();

        return true;
    }
}
