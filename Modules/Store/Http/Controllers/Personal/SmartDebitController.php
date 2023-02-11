<?php

namespace Modules\Store\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Members\Services\MemberExpiryDateCalculator;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Http\Requests\Personal\ValidateDirectDebitRequest;
use Modules\Store\Repositories\DonationRepository;
use Modules\Store\Repositories\PaymentRepository;
use Modules\Store\Services\SmartDebit;

class SmartDebitController extends Controller
{
    private SmartDebit $smartDebit;

    public function __construct(SmartDebit $smartDebit)
    {
        $this->smartDebit = $smartDebit;
    }

    public function validateDetails(ValidateDirectDebitRequest $request)
    {
        if (!$response = $this->smartDebit->validateRecurringPayment($request->all())) {
            return $this->error('Failed to validate with Smart Debit');
        }

        if (!$response->success()) {
            return $this->error('Failed to validate with Smart Debit', 1);
        }

        return $this->success();
    }

    public function donation(Request $request)
    {
        $member = current_member();
        $user = $member->user;
        $donation = $request->get('donation');

        $payment = PaymentRepository::createOneOffPaymentRecordForSmartDebit(PaymentPurpose::DONATION, (float) $donation['amount'], $member);

        DonationRepository::createOrUpdateForMember(
            $member,
            (float) ($donation['amount']),
            $donation['destination'],
            $donation['note'],
        );

        $smart_debit_details = array_merge($request->get('fields'), [
            'payer_reference' => $user->reference,
            'email' => $user->email,
            'address_line_1' => $member->address_line_1,
            'address_line_2' => $member->address_line_2,
            'address_town' => $member->address_town,
            'address_county' => $member->address_county,
            'address_postcode' => $member->address_postcode
        ]);

        $response = $this->smartDebit->createOneOffPayment($payment->reference, $donation['amount'], $smart_debit_details);

        if (!$response->success()) {
            return $this->error($response->errors());
        }

        return $this->success();
    }

    public function membership(Request $request)
    {
        $member = current_member();
        $user = $member->user;
        $price = $member->category->price_recurring;

        $payment = PaymentRepository::createPaymentRecordForSmartDebit(PaymentPurpose::MEMBERSHIP . " ({$member->membershipType->name})", $price, $member);

        $payment_date = MemberExpiryDateCalculator::calculateStartFrom($member, today()->format('Y-m-d'));

        $smart_debit_details = array_merge($request->all(), [
            'payer_reference' => $user->reference,
            'email' => $user->email,
            'address_line_1' => $member->address_line_1,
            'address_line_2' => $member->address_line_2,
            'address_town' => $member->address_town,
            'address_county' => $member->address_county,
            'address_postcode' => $member->address_postcode,
            'start_date' => $payment_date->format('Y-m-d')
        ]);

        $response = $this->smartDebit->createRecurringPayment($payment->reference, $price, $smart_debit_details);

        if (!$response->success()) {
            return $this->error($response->errors());
        }

        $user->smart_debit_id = $payment->reference;
        $user->smart_debit_frequency = 'Y';
        $user->save();

        return $this->success([
            'response' => $response,
            'payment_date' => $payment_date->format('Y-m-d')
        ]);
    }

    public function renewMembership(Request $request)
    {
        $member = current_member();
        $user = $member->user;
        $price = $member->category->price_recurring;

        $payment = PaymentRepository::createPaymentRecordForSmartDebit(PaymentPurpose::MEMBERSHIP . " ({$member->membershipType->name})", $price, $member);

        $expires_at = $member->expires_at->format('Y-m-d');

        if ($expires_at >= today()->format('Y-m-d')) {
            $from = $expires_at;
        } else {
            $from = today()->format('Y-m-d');
        }

        $payment_date = MemberExpiryDateCalculator::calculateStartFrom($member, $from);

        $smart_debit_details = array_merge($request->all(), [
            'payer_reference' => $user->reference,
            'email' => $user->email,
            'address_line_1' => $member->address_line_1,
            'address_line_2' => $member->address_line_2,
            'address_town' => $member->address_town,
            'address_county' => $member->address_county,
            'address_postcode' => $member->address_postcode,
            'start_date' => $payment_date->format('Y-m-d')
        ]);

        $response = $this->smartDebit->createRecurringPayment($payment->reference, $price, $smart_debit_details);

        $user->smart_debit_id = $payment->reference;
        $user->smart_debit_frequency = 'Y';
        $user->save();

        if (!$response->success()) {
            return $this->error($response->errors());
        }

        return $this->success([
            'response' => $response,
            'payment_date' => $payment_date->format('Y-m-d')
        ]);
    }
}
