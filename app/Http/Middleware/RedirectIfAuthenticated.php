<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next, ...$guards)
    {
        $redirectTo = null;
        if (Auth::guard('distributor')->check()) {
            $redirectTo = 'distributor/dashboard';
        } elseif (Auth::guard('main_distributor')->check()) {
            $redirectTo = 'main_distributor/dashboard';
        } elseif (Auth::guard('retailer')->check()) {
            $redirectTo = 'retailer/dashboard';
        } elseif (Auth::guard('employee')->check()) {
            $redirectTo = 'employee/dashboard';
        } elseif (Auth::guard('web')->check()) {
            $redirectTo = '/dashboard';
        }

        if ($redirectTo != null) {
            return redirect($redirectTo);
        }

        $guards = empty($guards) ? [null] : $guards;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
