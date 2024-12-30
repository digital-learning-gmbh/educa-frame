<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($guard == "verwaltung" && Auth::guard($guard)->check()) {
            return redirect('/');
        }
        if ($guard == "dozent" && Auth::guard($guard)->check()) {
            return redirect('/dozent');
        }
        if ($guard == "unternehmen" && Auth::guard($guard)->check()) {
            return redirect('/unternehmen');
        }
        if (Auth::guard($guard)->check()) {
            return redirect('/');
        }

        return $next($request);
    }
}
