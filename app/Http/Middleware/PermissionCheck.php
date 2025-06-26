<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionCheck
{
    public function handle(Request $request, Closure $next, int $module = 0, string $type = '')
    {
        if (Auth::guard('web')->check()) {
            $result = array_filter($request->permission, function ($item) use ($module, $type) {
                if (($item['module_id'] == $module) && ($item['allow_all'] == 1 || $item[$type] == 1)) {
                    return $item;
                }
                return false;
            });

            if (count($result) > 0) {
                return $next($request);
            } else {

                if ($request->ajax()) {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Route not found'
                    ], 404);
                }
                return abort(404);
            }
        } else {
            return $next($request);
        }
    }
}
