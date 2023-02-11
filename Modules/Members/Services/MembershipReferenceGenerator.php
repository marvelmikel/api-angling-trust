<?php

namespace Modules\Members\Services;

use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;

class MembershipReferenceGenerator
{
    public static function generate(MembershipType $membershipType)
    {
        $prefix = self::getPrefix($membershipType->slug);

        $latestMember = Member::withTrashed()
            ->where('membership_type_id', $membershipType->id)->latest()->first();

        $latestMemberReferenceID =preg_replace("/[^0-9]/", "", $latestMember->user->reference );


//        $count = Member::withTrashed()
//            ->where('membership_type_id', $membershipType->id)
//            ->count();

        $count = $latestMemberReferenceID;

        $count++;

        $valid = false;

        while (!$valid) {
            $exists = User::withTrashed()
                ->where('reference', $prefix . $count)
                ->exists();

            if (!$exists) {
                $valid = true;
            } else {
                $count++;
            }
        }

        return $prefix . $count;
    }

    public static function getPrefix($slug)
    {
        switch ($slug) {
            case 'individual-member':
                return 'IM';
            case 'club-or-syndicate':
                return 'COS';
            case 'fishery':
                return 'FI';
            case 'trade-member':
                return 'RA';
            case 'trade-supporter':
                return 'TS';
            case 'consultatives':
                return 'CON';
            case 'federation':
                return 'FED';
            case 'charter-boat':
                return 'CB';
            case 'coach':
                return 'COH';
            case 'caag':
                return 'CAG';
            case 'affiliate':
                return 'AFF';
            case 'salmon-fishery-board':
                return 'SFB';
            case 'lapsed':
                return 'LAP';
        }

        return null;

    }
}
