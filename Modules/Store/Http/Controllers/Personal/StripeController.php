<?php

namespace Modules\Store\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Store\Entities\Payment;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Http\Requests\Personal\ConfirmStripePaymentRequest;
use Modules\Store\Repositories\PaymentRepository;
use Modules\Store\Services\StripeSubscription;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\Subscription;

class StripeController extends Controller
{
    public function intent()
    {
        $user = current_user();
        $intent = $user->createSetupIntent();

        return $this->success([
            'client_secret' => $intent->client_secret
        ]);
    }

    public function subscribe(Request $request)
    {
        try {
        $member = current_member();
        $user = $member->user;

        Stripe::setApiKey(config('stripe.secret'));

        $payment_method = PaymentMethod::retrieve($request->get('payment_method_id'));

        $payment_method->attach([
            'customer' => $user->stripe_id,
        ]);

        Customer::update($user->stripe_id, [
            'invoice_settings' => [
                'default_payment_method' => $request->get('payment_method_id')
            ]
        ]);

        $plan = StripeSubscription::getPlanForMember($member);
            $subscription = Subscription::create([
                'customer' => $user->stripe_id,
                'payment_behavior' => 'allow_incomplete',
                'items' => [
                    [
                        'price' => $plan->api_id,
                    ],
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);
        } catch (Exception $exception) {
            return $this->error(config('app.env') === 'production' ? '' : $exception->getMessage());
        }

        $price = $member->category->price_recurring;

        $complete = $subscription->status === 'active';

        PaymentRepository::createRecurringPaymentRecordForStripe(
            sprintf("%s (%s)", PaymentPurpose::MEMBERSHIP, $member->membershipType->name),
            $price,
            $member,
            $complete,
            $subscription->latest_invoice->payment_intent->id,
        );

        return $this->success([
            'complete' => $complete,
            'secret' => $subscription->latest_invoice->payment_intent->client_secret ?? null,
        ]);
    }

    public function completePayment(ConfirmStripePaymentRequest $request): Response
    {
        $payment = Payment::whereReference($request->reference())->firstOrFail();

        $payment->complete();

        return $this->success();
    }

    public function recordPayments(Request $request)
    {
        $member = current_member();

        $membership = $request->get('membership');
        $donation = $request->get('donation');

        if ($membership) {
            PaymentRepository::createPaymentRecordForStripe(PaymentPurpose::MEMBERSHIP . " ({$member->membershipType->name})", $membership, $member);
        }

        if ($donation) {
            PaymentRepository::createPaymentRecordForStripe(PaymentPurpose::DONATION, $donation['amount'], $member);
        }

        return $this->success();
    }

    public function recordOther(Request $request)
    {
        $member = current_member();

        $price = $request->get('price');
        $purpose = $request->get('purpose');

        PaymentRepository::createPaymentRecordForStripe($purpose, $price, $member);

        return $this->success();
    }
}
