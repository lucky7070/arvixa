<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\StoreController;

class Store
{
    // Profile Routes Group
    public static function routes()
    {
        Route::any('cart', [StoreController::class, 'cart'])->name('cart');
        Route::post('apply-voucher', [StoreController::class, 'apply_voucher'])->name('apply-voucher');
        Route::get('remove-voucher', [StoreController::class, 'remove_voucher'])->name('remove-voucher');
        Route::get('checkout', [StoreController::class, 'checkout'])->name('checkout');
        Route::post('place-order', [StoreController::class, 'placeOrder'])->name('place-order');
        Route::get('my-orders', [StoreController::class, 'my_orders'])->name('my-orders');
        Route::post('cancel-order', [StoreController::class, 'cancel_order'])->name('cancel-order');
    }
}
