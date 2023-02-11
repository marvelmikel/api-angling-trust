<?php

namespace Modules\Members\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use Cache;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\PaymentActionRequired;
use Laravel\Cashier\Exceptions\PaymentFailure;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Repositories\PaymentRepository;
use Modules\Store\Services\SmartDebit;
use Modules\Store\Services\StripeSubscription;
use Psr\SimpleCache\InvalidArgumentException;

class MemberPaymentController extends Controller
{
    private SmartDebit $smartDebit;

    public function __construct(SmartDebit $smartDebit)
    {
        $this->smartDebit = $smartDebit;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function makeAPayment()
    {
        $member = current_member();
        $user = current_user();

        $total = $member->category->price;

        if ($member->payment_provider == PaymentProvider::SMART_DEBIT) {

            if (!$details = Cache::get(sprintf('member.%s.direct-debit-details', $member->id))) {
                return $this->error('Direct debit details expired', 1);
            }

            $payment = PaymentRepository::createPaymentRecordForSmartDebit(
                sprintf("%s (%s)", PaymentPurpose::MEMBERSHIP, $member->membershipType->name),
                $total,
                $member
            );

            $smart_debit_details = array_merge($details, [
                'payer_reference' => $user->reference,
                'email' => $user->email,
                'address_line_1' => $member->address_line_1,
                'address_line_2' => $member->address_line_2,
                'address_town' => $member->address_town,
                'address_county' => $member->address_county,
                'address_postcode' => $member->address_postcode
            ]);

            $response = $this->smartDebit->createRecurringPayment($payment->reference, $total, $smart_debit_details);

            if (!$response->success()) {
                return $this->error($response->errors(), 2);
            }

            Cache::delete(sprintf('member.%s.direct-debit-details', $member->id));

        }

        if ($member->payment_provider == PaymentProvider::STRIPE) {

            $paymentMethod = $user->paymentMethods()->first()->asStripePaymentMethod();

            if ($member->payment_is_recurring) {

                $plan = StripeSubscription::getPlanForMember($member);

                try {
                    $subscription = $user->newSubscription('default', $plan->api_id)
                        ->create($paymentMethod);
                } catch (PaymentActionRequired|PaymentFailure $e) {
                    return $this->error(config('app.env') === 'production' ? '' : $e->getMessage());
                }

                if (!$subscription) {
                    return $this->error();
                }

                PaymentRepository::createRecurringPaymentRecordForStripe(
                    sprintf("%s (%s)", PaymentPurpose::MEMBERSHIP, $member->membershipType->name),
                    $total,
                    $member
                );

            } else {

                try {
                    $charge = $user->charge($total, $paymentMethod, [
                        'description' => 'Angling Trust Membership - '.$member->membershipType->name,
                    ]);
                } catch (PaymentActionRequired|PaymentFailure $e) {
                    return $this->error();
                }

                if ($charge->asStripePaymentIntent()->status !== "succeeded") {
                    return $this->error();
                }

                PaymentRepository::createPaymentRecordForStripe(
                    sprintf("%s (%s)", PaymentPurpose::MEMBERSHIP, $member->membershipType->name),
                    $total,
                    $member
                );
            }
        }

        if ($member->expires_at !== null) {
            $member->expires_at = now()->addYear();
        }

        $member->renewed_at = now();
        $member->save();

        return $this->success();
    }

    public function makeAnOfflinePayment(Request $request)
    {
        $member = current_member();

        $total = $member->category->price;

        $description = $request->get('description');

        PaymentRepository::createOfflinePaymentRecord(
            sprintf('%s (%s)', PaymentPurpose::MEMBERSHIP, $member->membershipType->name),
            $total,
            $member,
            $description
        );

        if ($member->expires_at !== null) {
            $member->expires_at = now()->addYear();
        }

        $member->renewed_at = now();
        $member->save();

        return $this->success();
    }
}
