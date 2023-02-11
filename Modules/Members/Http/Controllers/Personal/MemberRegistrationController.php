<?php

namespace Modules\Members\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Services\WPNotification;

class MemberRegistrationController extends Controller
{
    public function completeWithPayment(Request $request)
    {
        $member = current_member();

        $payment_is_recurring = $request->get('payment_is_recurring');

        $member->payment_is_recurring = $payment_is_recurring;
        $member->save();

        if ($request->get('payment_date', null)) {
            $member->expires_at = Carbon::createFromFormat('Y-m-d', $request->get('payment_date'))->addYear();
            $member->registered_at = now();
            $member->save();
        } else {
            $member->expires_at = now()->addYear();
            $member->registered_at = now();
            $member->save();
        }

        return $this->success();
    }
}
