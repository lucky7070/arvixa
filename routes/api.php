<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return response()->json([
        'message'   => "Adiyogi Fintech :: Api Working Fine."
    ]);
});

Route::get('clear-all', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('storage:link');
    return '<h1>Clear All</h1>';
});

Route::get('nsdl-refund', function () {
    Artisan::call('nsdl:refund');
    return 'Refund Cron Run Successfully..!!';
});

Route::fallback(function () {
    return response()->json(['message' => 'Page Not Found'], 404);
});
