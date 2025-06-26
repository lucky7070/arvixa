<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user()->load('permission');
        $data = $user->permission->toArray();
        $input = $request->all();
        if ($data) {
            $input['permission'] = $data;
            $request->replace($input);
        }

        return $next($request);
    }
}
