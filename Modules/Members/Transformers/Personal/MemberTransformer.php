<?php

namespace Modules\Members\Transformers\Personal;

use EliPett\EntityTransformer\Contracts\EntityTransformer;
use Modules\Auth\Transformers\UserTransformer;
use Modules\Core\Transformers\AddressTransformer;
use Modules\Events\Transformers\PurchasedTicketTransformer;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MemberMeta;
use Modules\Members\Transformers\MembershipTypeCategoryTransformer;
use Modules\Members\Transformers\MembershipTypeTransformer;
use Modules\Store\Transformers\DonationTransformer;

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
            'payment_provider' => $member->payment_provider,
            'payment_is_recurring' => (bool) $member->payment_is_recurring,
            'has_stripe_portal' => $member->hasStripePortal(),
            'should_alert_about_stripe' => $member->shouldAlertAboutStripe(),
            'opt_in_1' => (bool) $member->opt_in_1,
            'opt_in_2' => (bool) $member->opt_in_2,
            'registered_at' => null,
            'renewed_at' => null,
            'expires_at' => null,
            'has_expired' => $member->hasExpired(),
            'expires_soon' => $member->expiresSoon(),
            'meta' => []
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
            'purchasedTickets' => PurchasedTicketTransformer::class
        ];
    }
}
