<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

class AuthenticateClientAccessToken extends CheckClientCredentials
{
    public function handle($request, Closure $next, ...$scopes)
    {
        try {
            return parent::handle($request, $next, $scopes);
        } catch (AuthenticationException $exception) {
            return response([
                'success' => false,
                'error' => [
                    'code' => 401,
                    'message' => 'Invalid Token Provided'
                ]
            ]);
        }
    }
}
