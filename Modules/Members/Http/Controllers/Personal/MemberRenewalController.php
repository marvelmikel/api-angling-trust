<?php

namespace Modules\Members\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Services\WPNotification;
use Modules\Store\Enums\PaymentProvider;
use Modules\Store\Enums\PaymentPurpose;
use Modules\Store\Repositories\PaymentRepository;
use Modules\Store\Services\SmartDebit;
use Modules\Store\Services\StripeSubscription;

class MemberRenewalController extends Controller
{
    private $smartDebit;

    public function __construct(SmartDebit $smartDebit)
    {
        $this->smartDebit = $smartDebit;
    }

    public function renew(Request $request)
    {
        $member = current_member();

        $payment_is_recurring = $request->get('payment_is_recurring');

        $member->payment_is_recurring = $payment_is_recurring;
        $member->save();

        if ($request->get('payment_date', null)) {
            $member->expires_at = Carbon::createFromFormat('Y-m-d', $request->get('payment_date'))->addYear();
            $member->renewed_at = now();
            $member->save();
        } else {
            $member->renew();
        }

        if ($email = $member->user->email) {
            WPNotification::sendCustomerNotification('cm-manual-renew', $email);
        }

        return $this->success();
    }
}
