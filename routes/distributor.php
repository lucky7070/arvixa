<?php

use App\Routes\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Distributor\RetailerController;
use App\Http\Controllers\Distributor\DistributorsController;

/*
|--------------------------------------------------------------------------
| Web Routes For Distributors
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Distributor Routes
Route::middleware(['auth:distributor', 'authCheck'])->name('distributor.')->prefix('distributor')->group(function () {
    Profile::routes();
    Route::get('/', [DistributorsController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DistributorsController::class, 'index'])->name('dashboard');

    // ----------------------- Retailer Routes ----------------------------------------------------
    Route::get('retailers', [RetailerController::class, 'index'])->name('retailers');
    Route::get('retailers/add', [RetailerController::class, 'add'])->name('retailers.add');
    Route::post('retailers/add', [RetailerController::class, 'save'])->name('retailers.add');
    Route::get('retailers/{id}', [RetailerController::class, 'edit'])->name('retailers.edit');
    Route::post('retailers/{id}', [RetailerController::class, 'update'])->name('retailers.edit');
    Route::delete('retailers', [RetailerController::class, 'delete'])->name('retailers.delete');
    Route::get('retailers/services/{slug}', [RetailerController::class, 'services'])->name('retailers.services');
    Route::post('retailers/services/{slug}', [RetailerController::class, 'services_update'])->name('retailers.services');
    Route::get('retailers/ledger/{slug}', [RetailerController::class, 'ledger'])->name('retailers.ledger');
    Route::post('retailers/ledger/{slug}', [RetailerController::class, 'ledger_add'])->name('retailers.ledger');
});
