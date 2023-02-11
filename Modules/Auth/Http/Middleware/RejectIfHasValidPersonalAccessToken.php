<?php

namespace Modules\Auth\Http\Middleware;

use Closure;

class RejectIfHasValidPersonalAccessToken
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth()->guard('api')->check()) {
            return response([
                'success' => false,
                'error' => [
                    'code' => 403,
                    'message' => 'Forbidden'
                ]
            ]);
        }

        return $next($request);
    }
}
