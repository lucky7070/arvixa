<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

class Profile
{
    // Profile Routes Group
    public static function routes()
    {
        Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
        Route::post('update-password', [ProfileController::class, 'update_password'])->name('update-password');
        Route::post('profile', [ProfileController::class, 'update'])->name('profile');
        Route::post('update-image', [ProfileController::class, 'upload_image'])->name('update-image');
        Route::get('wallet', [ProfileController::class, 'wallet'])->name('wallet');
        Route::get('request-money', [ProfileController::class, 'request_money'])->name('request-money');
        Route::post('request-money', [ProfileController::class, 'request_money_save'])->name('request-money');

        Route::post('upi-payment', [ProfileController::class, 'upi_payment'])->name('upi-payment');
        // Route::post('upi-payment-qkqr', [ProfileController::class, 'upi_payment_qkqr'])->name('upi-payment-qkqr');


        // Route::get('payment-request', [ProfileController::class, 'payment_request'])->name('payment-request')->middleware('isAllow:114,can_view');
        // Route::post('payment-request', [ProfileController::class, 'payment_request_update'])->name('payment-request')->middleware('isAllow:114,can_edit');
        // Route::get('payment-request/export', [ProfileController::class, 'payment_request_export'])->name('payment-request.export')->middleware('isAllow:114,can_view');
        Route::get('lock',  [ProfileController::class, 'lock'])->name('lock');
        Route::post('lock', [ProfileController::class, 'unlock'])->name('lock');
        Route::get('logout', [ProfileController::class, 'logout'])->name('logout');
    }
}
