<?php

namespace Modules\Members\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use EliPett\EntityTransformer\Facades\Transform;
use Illuminate\Http\Request;
use Modules\Core\Services\WPNotification;
use Modules\Members\Entities\Member;
use Modules\Members\Http\Requests\Member\Personal\UpdateDonationRequest;
use Modules\Members\Http\Requests\Member\Personal\UpdateMembershipTypeRequest;
use Modules\Members\Http\Requests\Member\Personal\UpdateMemberRequest;
use Modules\Members\Http\Requests\Member\Personal\UpdatePreferencesRequest;
use Modules\Members\Repositories\MemberRepository;
use Modules\Members\Services\MemberValidator;
use Modules\Members\Transformers\Personal\MemberTransformer;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Repositories\DonationRepository;
use Stripe\Stripe;
use Stripe\BillingPortal\Session as StripeBillingSession;

class MemberController extends Controller
{
    private $loads = ['user', 'membershipType', 'donations'];

    public function me()
    {
        $member = current_member();

        $load = ['user',
            'membershipType',
            'donations',
            'purchasedTickets',
            'purchasedTickets.ticket',
            'purchasedTickets.event'
        ];

        if ($member->category_id) {
            $load[] = 'category';
        }

        $member->load($load);

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class)
        ]);
    }

    public function updateMe(Request $request)
    {
        $member = current_member();

        (new MemberValidator('Modules\Members\Http\Requests\Member\Personal'))
            ->validate($member->membershipType->slug, 'UpdateMemberRequest');

        MemberRepository::update($member, $request->all());

        if ($request->input('meta')) {
            foreach ($request->input('meta') as $key => $value) {
                $member->setMeta($key, $value);
            }
        }

        $member->updateFullName();

        if (!$member->category_id) {
            $member->load(['user', 'membershipType', 'donations']);
        } else {
            $member->load(['user', 'membershipType', 'donations', 'category']);
        }

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class)
        ]);
    }

    public function updateMyPreferences(Request $request)
    {
        $member = current_member();

        (new MemberValidator('Modules\Members\Http\Requests\Member\Personal'))
            ->validate($member->membershipType->slug, 'UpdateMemberPreferencesRequest');

        MemberRepository::update($member, $request->all());

        if ($request->input('meta')) {
            foreach ($request->input('meta') as $key => $value) {
                $member->setMeta($key, $value);
            }
        }

        if (!$member->category_id) {
            $member->load(['user', 'membershipType', 'donations']);
        } else {
            $member->load(['user', 'membershipType', 'donations', 'category']);
        }

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class)
        ]);
    }

    public function updateMyMembershipType(UpdateMembershipTypeRequest $request)
    {
        $member = current_member();

        MemberRepository::update($member, $request->all());
        MemberRepository::updateMembershipType($member, $request->get('membership_type_id'));

        $member->refresh();
        $member->load($this->loads);

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class)
        ]);
    }

    public function updateMyChosenPaymentMethod(Request $request)
    {
        $member = current_member();
        $provider = null;

        if ($request->get('method') === 'credit-card') {
            $provider = PaymentProvider::STRIPE;
        }

        if ($request->get('method') === 'direct-debit') {
            $provider = PaymentProvider::SMART_DEBIT;
        }

        if ($request->get('method') === 'other') {
            $provider = PaymentProvider::OTHER;
        }

        if (!$provider) {
            return $this->error('Invalid payment provider');
        }

        $member->payment_provider = $provider;
        $member->save();

        $member->refresh();
        $member->load($this->loads);

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class)
        ]);
    }

    public function updateMapPermission(Request $request)
    {
        $member = current_member();

        $allow = (bool) $request->input('allow');

        $member->setMeta('map_permission', $allow, 'boolean');

        if ($allow) {
            WPNotification::sendAdminNotification('ad-map-permission', [
                '[membership_type]' => $member->membershipType->name,
                '[member_number]' => $member->user->reference
            ]);
        }

        return $this->success();
    }

    public function getStripePortalLink()
    {
        $member = current_member();

        if ($member->payment_provider !== PaymentProvider::STRIPE || ((bool)$member->payment_is_recurring) !== true) {
            return $this->error();
        }

        Stripe::setApiKey(config('stripe.secret'));

        $session = StripeBillingSession::create([
            'customer' => $member->user->stripe_id,
            'return_url' => env('WP_URL') . '/members/dashboard/',
        ]);

        return $this->success([
            'url' => $session->url
        ]);
    }
}
