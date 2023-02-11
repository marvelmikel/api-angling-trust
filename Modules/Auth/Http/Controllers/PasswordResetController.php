<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Cartalyst\Sentinel\Reminders\EloquentReminder;
use Illuminate\Http\Request;
use Modules\Auth\Entities\User;
use Modules\Auth\Http\Requests\PasswordReset\CompletePasswordResetRequest;
use Modules\Core\Services\WPNotification;
use Modules\Members\Entities\Member;

class PasswordResetController extends Controller
{
    public function generate(Request $request)
    {
        $reference = $request->get('reference');

        $user = User::query()
            ->where('reference', $reference)
            ->first();

        if (!$user) {
            return $this->error();
        }

        $member = Member::query()
            ->where('user_id', $user->id)
            ->first();

        if ($member->membershipType->slug === 'lapsed') {
            return $this->error();
        }

        $reminder = Reminder::create($user);

        $origin = get_site_origin();

        WPNotification::sendCustomerNotification('cm-password-reset', $user->email, [
            'token' => $reminder->code,
            'origin' => $origin
        ]);

        return $this->success();
    }

    public function check(Request $request)
    {
        $token = $request->get('token');

        $exists = EloquentReminder::query()
            ->where('code', $token)
            ->whereNull('completed_at')
            ->exists();

        return $this->success([
            'exists' => $exists
        ]);
    }

    public function complete(CompletePasswordResetRequest $request)
    {
        $token = $request->get('token');

        $reminder = EloquentReminder::query()
            ->where('code', $token)
            ->whereNull('completed_at')
            ->first();

        if (!$reminder) {
            return $this->error();
        }

        $user = User::findOrFail($reminder->user_id);

        if (!Reminder::complete($user, $token, $request->get('password'))) {
            return $this->error();
        }

        return $this->success();
    }
}
