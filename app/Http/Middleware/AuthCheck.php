<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if (Auth::check() || Auth::guard('main_distributor')->check() || Auth::guard('distributor')->check() || Auth::guard('retailer')->check() || Auth::guard('employee')->check() || Auth::guard('customer')->check()) {

            if ($request->session()->has('locked') && request()->is('*lock') == false && request()->is('*logout') == false) {
                $redirectTo = null;
                if (Auth::guard('distributor')->check()) {
                    $redirectTo = 'distributor/lock';
                } elseif (Auth::guard('main_distributor')->check()) {
                    $redirectTo = 'main_distributor/lock';
                } elseif (Auth::guard('retailer')->check()) {
                    $redirectTo = 'retailer/lock';
                } elseif (Auth::guard('employee')->check()) {
                    $redirectTo = 'employee/lock';
                } elseif (Auth::guard('customer')->check()) {
                    $redirectTo = 'customer/lock';
                } elseif (Auth::guard('web')->check()) {
                    $redirectTo = '/lock';
                }

                if ($redirectTo != null) {
                    return redirect($redirectTo);
                }
            }
            return $next($request);
        }

        abort(401);
    }
}
