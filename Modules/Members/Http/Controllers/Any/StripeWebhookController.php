<?php

namespace Modules\Members\Http\Controllers\Any;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Modules\Auth\Entities\User;
use App\Http\Controllers\Controller;
use Modules\Members\Entities\Member;
use Modules\Members\Services\MemberCardExpiryUpdater;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Repositories\PaymentRepository;

class StripeWebhookController extends Controller
{
    public function paymentUpdated(Request $request)
    {
        // todo: check secret

        $stripe_id = $request->input('data.object.id');
        $member = $this->getMemberByStripeId($stripe_id);

        MemberCardExpiryUpdater::update($member);

        $this->log($member, "card updated");

        return $this->stripeSuccessResponse();
    }

    public function paymentFailed(Request $request)
    {
        // todo: check secret

        $billingReason = $request->input('data.object.billing_reason');

        if ($billingReason !== 'subscription_cycle') {
            return $this->stripeSuccessResponse('Ignored, not related to subscription');
        }

        $stripe_id = $request->input('data.object.customer');
        $member = $this->getMemberByStripeId($stripe_id);

        PaymentRepository::createStripeSubscriptionFailedPaymentRecord($member);

        $this->log($member, "payment failed");

        return $this->stripeSuccessResponse();
    }

    public function invoicePaid(Request $request)
    {
        // todo: check secret

        $billingReason = $request->input('data.object.billing_reason');

        if ($billingReason !== 'subscription_cycle') {
            return $this->stripeSuccessResponse('Ignored, not related to subscription');
        }

        $stripe_id = $request->input('data.object.customer');
        $member = $this->getMemberByStripeId($stripe_id);

        $lines = $request->input('data.object.lines.data');
        $periodEnd = $lines[0]['period']['end'];

        $member->expires_at = Carbon::createFromTimestamp($periodEnd);
        $member->renewed_at = Carbon::now();
        $member->save();

        $amountPaid = $request->input('data.object.amount_paid');
        PaymentRepository::createRecurringPaymentRecordForStripe(
            sprintf('%s (%s)', PaymentPurpose::MEMBERSHIP, $member->membershipType->name),
            $amountPaid,
            $member
        );

        $this->log($member, "renewed");

        return $this->stripeSuccessResponse();
    }

    private function log(Member $member, $message)
    {
        Log::channel('expiredMembership')
            ->info(sprintf('#%s %s', $member->id, $message));
    }

    private function getMemberByStripeId($stripe_id): Member
    {
        $user = User::query()
            ->where('stripe_id', $stripe_id)
            ->firstOrFail();

        return Member::query()
            ->where('user_id', $user->id)
            ->firstOrFail();
    }

    private function stripeSuccessResponse($message = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    private function stripeErrorResponse($message)
    {
        return response()->json([
            'success' => false,
            'error' => $message
        ], 500);
    }
}
