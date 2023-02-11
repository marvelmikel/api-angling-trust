<?php

namespace Modules\Auth\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Modules\Members\Entities\Member;

class SessionController extends Controller
{
    public function loginAs(Member $member)
    {
        $user = $member->user;

        $user->invalidateAllTokens();
        $token = $user->loginWithToken();

        return $this->success([
            'access_token' => $token->accessToken,
            'expires' => $token->token->expires_at->timestamp,
            'membership_type' => $member->membershipType->slug
        ]);
    }
}
