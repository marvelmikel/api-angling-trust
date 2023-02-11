<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use EliPett\EntityTransformer\Facades\Transform;
use Illuminate\Http\Request;
use Modules\Auth\Entities\User;
use Modules\Auth\Enums\SiteOrigin;
use Modules\Auth\Http\Requests\Register\Step1;
use Modules\Auth\Services\RegistrationManager;
use Modules\Auth\Services\RegistrationValidator;
use Modules\Core\Services\WPNotification;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Enums\CreatedVia;
use Modules\Members\Repositories\MemberRepository;
use Modules\Members\Transformers\Personal\MemberTransformer;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Repositories\DonationRepository;
use Modules\Store\Repositories\PaymentRepository;
use Modules\Store\Services\SmartDebit;
use Modules\Store\Services\StripeSubscription;

class SessionController extends Controller
{
    private $smartDebit;

    public function __construct(SmartDebit $smartDebit)
    {
        $this->smartDebit = $smartDebit;
    }

    public function registerStep1(Step1 $request)
    {
        $membershipType = MembershipType::query()
            ->where('slug', $request->input('membership_type'))
            ->firstOrFail();

        $member = MemberRepository::create($request->all(), $membershipType);

        $member->created_via = CreatedVia::SELF_REGISTRATION;
        $member->save();

        $token = $member->user->loginWithToken();

        RegistrationManager::run($member->membershipType->slug, 1, $member, $request);

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class),
            'access_token' => $token->accessToken,
            'membership_type' => $member->membershipType->slug,
            'expires' => $token->token->expires_at->timestamp,
        ]);
    }

    public function registerStep2(Request $request)
    {
        $member = current_member();

        RegistrationValidator::validate($member->membershipType->slug, 2, $request);

        MemberRepository::update($member, $request->all());

        foreach ($request->input('meta') as $key => $value) {
            if ($value) {
                $member->setMeta($key, $value);
            }
        }

        RegistrationManager::run($member->membershipType->slug, 2, $member, $request);

        MemberRepository::updateCategory($member, $request->all());

        $member->updateFullName();

        $member->load(['user', 'membershipType', 'category']);

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class)
        ]);
    }

    public function registerStep3(Request $request)
    {
        $member = current_member();

        RegistrationValidator::validate($member->membershipType->slug, 3, $request);

        foreach ($request->input('meta') as $key => $value) {
            if ($value) {
                $member->setMeta($key, $value);
            }
        }

        if ($member->membershipType->slug === 'individual-member' && $member->category->slug === 'junior') {
            $member->setMeta('raffle_opt_out', false, 'boolean');
        } else {
            $member->setMetaCast('raffle_opt_out', 'boolean');
        }

        if ($donation = $request->get('donation')) {
            DonationRepository::createOrUpdateForMember(
                $member,
                (float) ($donation['amount']),
                $donation['destination'],
                $donation['note'],
            );
        }

        RegistrationManager::run($member->membershipType->slug, 3, $member, $request);

        $member->load(['user', 'membershipType', 'donations', 'category']);

        if ($member->membershipType->slug === 'individual-member' && $member->category->slug === 'junior') {
            if ($member->donations()->count() === 0) {

                MemberRepository::completeRegistration($member, false);

                $member->refresh();

                WPNotification::sendCustomerNotification('cm-welcome', $member->user->email);

                return $this->success([
                    'member' => Transform::entity($member, MemberTransformer::class),
                    'skip_payment' => true
                ]);
            }
        }

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class),
            'skip_payment' => false
        ]);
    }

    public function registerStep4(Request $request)
    {
        $member = current_member();
        $user = current_user();

        RegistrationValidator::validate($member->membershipType->slug, 4, $request);

        RegistrationManager::run($member->membershipType->slug, 4, $member, $request);

        MemberRepository::completeRegistration($member, $request->get('payment_is_recurring'));

        $member->refresh();

        if ($member->membershipType->slug === 'trade-member') {
            WPNotification::sendCustomerNotification('cm-retail-welcome', $member->user->email);
        } else {
            WPNotification::sendCustomerNotification('cm-welcome', $member->user->email);
        }

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class)
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['reference', 'password']);

        /** @var $user User */
        if (!$user = Sentinel::authenticate($credentials)) {
            return $this->error('Your login details appear to be incorrect');
        }

        $member = Member::query()
            ->where('user_id', $user->id)
            ->firstOrFail();

        $origin = get_site_origin();

        if ($origin === SiteOrigin::ANGLING_TRUST) {
            if (!$member->at_member) {
                return $this->error('Your login details appear to be incorrect');
            }
        }

        if ($origin === SiteOrigin::FISH_LEGAL) {
            if (!$member->fl_member) {
                return $this->error('Your login details appear to be incorrect');
            }
        }

        if ($member->is_suspended) {
            return $this->error('Your login has been suspended', 423);
        }

        $user->invalidateAllTokens();
        $token = $user->loginWithToken();

        return $this->success([
            'access_token' => $token->accessToken,
            'expires' => $token->token->expires_at->timestamp,
            'membership_type' => $member->membershipType->slug
        ]);
    }

    public function logout()
    {
        $user = current_user();
        $user->invalidateAllTokens();

        return $this->success();
    }
}
