<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Sentinel;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (!Sentinel::check()) {
            return redirect()->to(wp_url('members/login'));
        }

        return $next($request);
    }
}
