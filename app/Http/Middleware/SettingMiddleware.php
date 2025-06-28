<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SettingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $data = Setting::select('setting_name', 'filed_value')->get()->pluck('filed_value', 'setting_name')->toArray();

        if (!$request->is('api/*')) {
            View::share('site_settings', $data);
            View::share('config', [
                'distributors_list_url'     => route('retailers.distributors_list'),
            ]);

            View::share('config_front', []);
        }

        $input = $request->all();
        if ($data) {
            $input['site_settings'] = $data;
            $request->replace($input);
        }

        return $next($request);
    }
}
