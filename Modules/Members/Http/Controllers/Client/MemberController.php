<?php

namespace Modules\Members\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use EliPett\EntityTransformer\Facades\Transform;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Auth\Enums\SiteOrigin;
use Modules\Core\Services\WPNotification;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;
use Modules\Members\Entities\MembershipTypeCategory;
use Modules\Members\Enums\CreatedVia;
use Modules\Members\Http\Requests\Member\Client\AddToPaymentHistoryRequest;
use Modules\Members\Http\Requests\Member\Client\StoreMemberRequest;
use Modules\Members\Http\Requests\Member\Client\UpdateMemberExpiresAtRequest;
use Modules\Members\Http\Requests\Member\Client\UpdateMemberRequest;
use Modules\Members\Repositories\MemberRepository;
use Modules\Members\Transformers\Client\MemberIndexTransformer;
use Modules\Members\Transformers\Client\MemberTransformer;
use Modules\Store\Entities\Payment;
use Modules\Store\Repositories\PaymentRepository;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = MemberRepository::quickSearch($request);

        $active_count = Member::query()->whereActive();
        $expired_count = Member::query()->whereExpired();
        $incomplete_count = Member::query()->whereIncomplete();

        if (get_site_origin() === SiteOrigin::FISH_LEGAL) {
            $active_count->where('fl_member', 1);
            $expired_count->where('fl_member', 1);
            $incomplete_count->where('fl_member', 1);
        }

        return $this->success([
            'counts' => [
                'active' => $active_count->count(),
                'expired' => $expired_count->count(),
                'incomplete' => $incomplete_count->count()
            ],
            'members' => [
                'items' => Transform::entities($members->items(), MemberIndexTransformer::class),
                'current_page' => $members->currentPage(),
                'total' => $members->total(),
                'total_pages' => ceil($members->total() / 20)
            ]
        ]);
    }

    public function show(Member $member)
    {
        $member->load(['user', 'membershipType']);

        if ($member->category_id) {
            $member->load('category');
        }

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class)
        ]);
    }

    public function store(StoreMemberRequest $request)
    {
        $membershipType = MembershipType::query()
            ->where('slug', $request->input('membership_type_slug'))
            ->firstOrFail();

        if (!$member = MemberRepository::create($request->all(), $membershipType)) {
            return $this->error('Member could not be created.', 500);
        }

        $member->created_via = CreatedVia::ADMIN_FORM;
        $member->created_by = $request->get('created_by', null);
        $member->save();

        $member->setMeta('raffle_opt_out', true, 'boolean');
        $member->updateFullName();

        return $this->success([
            'member' => Transform::entity($member, MemberTransformer::class)
        ]);
    }

    public function update(Member $member, UpdateMemberRequest $request)
    {
//        (new MemberValidator('Modules\Members\Http\Requests\Member\Client'))
//            ->validate($member->membershipType->slug, 'UpdateMemberRequest');

        $data = $request->all();

        if ($data['opt_in_1'] === null) {
            $data['opt_in_1'] = false;
        }

        if ($data['opt_in_2'] === null) {
            $data['opt_in_2'] = false;
        }

        MemberRepository::updateCategory($member, $data);
        MemberRepository::update($member, $data);

        if ($request->input('meta')) {
            foreach ($request->input('meta') as $key => $value) {

                if (in_array($key, ['raffle_opt_out'])) {
                    $member->setMeta($key, $value, 'boolean');
                } else {
                    $member->setMeta($key, $value);
                }

            }
        }

        $member->updateFullName();

        return $this->success();
    }

    public function updateATFLAccess(Member $member, Request $request)
    {
        $at_member = $request->get('at_member', false);
        $fl_member = $request->get('fl_member', false);

        $current_category = $member->category;
        $category_slug = $current_category->slug;

        $new_category = MembershipTypeCategory::query()
            ->where('membership_type_id', $current_category->membership_type_id)
            ->where('slug', $category_slug)
            ->where('at_member', $at_member)
            ->where('fl_member', $fl_member)
            ->first();

        if (!$new_category) {
            return $this->error('Invalid choice for this membership type');
        }

        $member->at_member = $at_member;
        $member->fl_member = $fl_member;
        $member->category_id = $new_category->id;

        $member->save();

        return $this->success();
    }

    public function updateExpiresAt(Member $member, UpdateMemberExpiresAtRequest $request)
    {
        $expires_at = Carbon::createFromFormat('d/m/Y', $request->get('expires_at'));

        $member->expires_at = $expires_at;
        $member->save();

        return $this->success();
    }

    public function markPackAsSent(Member $member)
    {
        $member->membership_pack_sent_at = now();
        $member->save();

        return $this->success();
    }

    public function completeRegistration(Member $member)
    {
        $member->registered_at = now();

        if (!in_array($member->category->slug, ['life', 'fl-life'])) {
            if ($member->category->slug === 'junior') {
                $date_of_birth = $member->getMetaValue('date_of_birth');
                $date_of_birth = Carbon::createFromFormat('Y-m-d', $date_of_birth['year'] . '-' . $date_of_birth['month'] . '-' . $date_of_birth['day']);
                $member->expires_at = $date_of_birth->addYears(18);
            } else {
                $member->expires_at = now()->addYear();
            }
        }

        $member->save();

        return $this->success();
    }

    public function cancelRecurringPayment(Member $member)
    {
        $member->payment_is_recurring = false;
        $member->save();

        return $this->success();
    }

    public function makeRecurringPayment(Member $member)
    {
        $member->payment_is_recurring = true;
        $member->save();

        return $this->success();
    }

    public function renew(Member $member)
    {
        $member->renew();

        if ($email = $member->user->email) {
            WPNotification::sendCustomerNotification('cm-manual-renew', $email);
        }

        return $this->success();
    }

    public function suspend(Member $member)
    {
        $member->suspend();

        return $this->success();
    }

    public function unsuspend(Member $member)
    {
        $member->unsuspend();

        return $this->success();
    }

    public function destroy(Member $member)
    {
        MemberRepository::delete($member);

        return $this->success();
    }

    public function addToPaymentHistory(Member $member, AddToPaymentHistoryRequest $request)
    {
        $payment = new Payment();
        $payment->reference = random_reference();
        $payment->payment_provider = $request->input('payment_provider');
        $payment->purpose = $request->input('purpose');

        $payment->member()->associate($member);

        $payment->amount = ((float) $request->input('amount'));
        $payment->auto_renew = (bool) $request->input('auto_renew');
        $payment->completed_at = Carbon::createFromFormat('d/m/Y H:i', $request->input('completed_at.date') . ' ' . $request->input('completed_at.time'));
        $payment->description = $request->input('description', null);

        $payment->save();

        return $this->success();
    }
}
