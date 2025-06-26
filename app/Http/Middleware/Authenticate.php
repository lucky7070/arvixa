<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            $guard = getGuardFromURL($request);
            $redirectTo = null;
            if ($guard          == 'distributor') {
                $redirectTo     = 'distributor';
            } elseif ($guard    == 'main_distributor') {
                $redirectTo     = 'main_distributor';
            } elseif ($guard    == 'retailer') {
                $redirectTo     = 'retailer';
            } elseif ($guard    == 'employee') {
                $redirectTo     = 'employee';
            } elseif ($guard    == 'customer') {
                $redirectTo     = 'customer';
            } elseif ($guard    == 'web') {
                $redirectTo     = 'admin';
            }

            if ($redirectTo != null) return route('loginPage', $redirectTo);

            return route('login');
        }
    }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->is('api/*')) {
            $data = [
                'status'    => false,
                'message'   => 'Unauthenticated Access..!!',
                'data'      => []
            ];

            return abort(response()->json($data, 401));
        } else {
            throw new AuthenticationException(
                'Unauthenticated.',
                $guards,
                $this->redirectTo($request)
            );
        }
    }
}
