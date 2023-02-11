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

class MemberTransformer implements EntityTransformer
{
    /**
     * @param Member $member
     * @return array
     */
    public function data($member): array
    {
        $data = [
            'id' => $member->id,
            'membership_type_id' => $member->membership_type_id,
            'membership_type_slug' => $member->membershipType->slug,
            'category_id' => $member->category_id,
            'at_member' => (bool) $member->at_member,
            'fl_member' => (bool) $member->fl_member,
            'reference' => $member->user->reference,
            'title' => $member->title,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'full_name' => $member->full_name,
            'home_telephone' => $member->home_telephone,
            'mobile_telephone' => $member->mobile_telephone,
            'address_line_1' => $member->address_line_1,
            'address_line_2' => $member->address_line_2,
            'address_town' => $member->address_town,
            'address_county' => $member->address_county,
            'address_postcode' => $member->address_postcode,
            'notes' => $member->notes,
            'payment_provider' => $member->payment_provider,
            'payment_is_recurring' => (bool) $member->payment_is_recurring,
            'membership_pack_sent_at' => $member->membership_pack_sent_at,
            'total_donated' => $member->getTotalDonated(),
            'is_suspended' => $member->is_suspended,
            'is_imported' => $member->is_imported,
            'opt_in_1' => (bool) $member->opt_in_1,
            'opt_in_2' => (bool) $member->opt_in_2,
            'registered_at' => null,
            'renewed_at' => null,
            'expires_at' => null,
            'has_expired' => $member->hasExpired(),
            'expires_soon' => $member->expiresSoon(),
            'created_at' => $member->created_at,
            'created_via' => $member->created_via,
            'created_by' => $member->created_by
        ];

        if ($member->registered_at) {
            $data['registered_at'] = $member->registered_at->format('Y-m-d');
        }

        if ($member->renewed_at) {
            $data['renewed_at'] = $member->renewed_at->format('Y-m-d');
        }

        if ($member->expires_at) {
            $data['expires_at'] = $member->expires_at->format('Y-m-d');
        }

        /** @var MemberMeta $meta */
        foreach ($member->meta as $meta) {
            $data['meta'][$meta->key] = $meta->getValue();
        }

        return $data;
    }

    public function relations(): array
    {
        return [
            'membershipType' => MembershipTypeTransformer::class,
            'category' => MembershipTypeCategoryTransformer::class,
            'user' => UserTransformer::class,
            'donations' => DonationTransformer::class,
            'payments' => PaymentTransformer::class
        ];
    }
}
