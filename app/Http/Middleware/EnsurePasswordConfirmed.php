<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordConfirmed
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $timeout = config('auth.password_timeout', 10800);
        $confirmedAt = (int) $request->session()->get('auth.password_confirmed_at', 0);

        if (time() - $confirmedAt <= $timeout) {
            return $next($request);
        }

        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('password.confirm');
    }
}

