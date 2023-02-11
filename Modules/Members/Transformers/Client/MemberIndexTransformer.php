<?php

namespace Modules\Members\Transformers\Client;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Auth\Transformers\UserTransformer;
use Modules\Core\Transformers\AddressTransformer;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MemberMeta;
use Modules\Members\Transformers\MembershipTypeCategoryTransformer;
use Modules\Members\Transformers\MembershipTypeTransformer;
use Modules\Store\Transformers\DonationTransformer;
use Modules\Store\Transformers\PaymentTransformer;

class MemberIndexTransformer implements EntityTransformer
{
    /**
     * @param Member $member
     * @return array
     */
    public function data($member): array
    {
        $data = [
            'id' => $member->member_id,
            'at_member' => (bool) $member->at_member,
            'fl_member' => (bool) $member->fl_member,
            'reference' => $member->reference,
            'membership_type_id' => $member->membership_type_id,
            'membership_type_slug' => $member->membership_type_slug,
            'membership_type_name' => $member->membership_type_name,
            'full_name' => $member->full_name,
            'email' => $member->email,
            'address_postcode' => $member->address_postcode,
            'primary_contant' => $member->primary_contact,
            'is_suspended' => $member->is_suspended,
            'registered_at' => null,
            'expires_at' => null
        ];

        if ($member->registered_at) {
            $data['registered_at'] = $member->registered_at->format('Y-m-d');
        }

        if ($member->expires_at) {
            $data['expires_at'] = $member->expires_at->format('Y-m-d');
        }

        return $data;
    }

    public function relations(): array
    {
        return [

        ];
    }
}
