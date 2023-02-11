<?php

namespace Modules\Auth\Http\Middleware;

use Closure;

class AuthenticatePersonalAccessToken
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (!auth()->guard('api')->check()) {
            return response([
                'success' => false,
                'error' => [
                    'code' => 401,
                    'message' => 'Invalid Token Provided'
                ]
            ]);
        }

        return $next($request);
    }
}
