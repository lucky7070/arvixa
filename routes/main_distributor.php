<?php

use App\Routes\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainDistributor\RetailerController;
use App\Http\Controllers\MainDistributor\DistributorController;
use App\Http\Controllers\MainDistributor\MainDistributorsController;

/*
|--------------------------------------------------------------------------
| Web Routes For MainDistributors
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Main Distributor Routes
Route::middleware(['auth:main_distributor', 'authCheck'])->name('main_distributor.')->prefix('main_distributor')->group(function () {
    Profile::routes();
    Route::get('/', [MainDistributorsController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [MainDistributorsController::class, 'index'])->name('dashboard');

    // ----------------------- Distributor Routes ----------------------------------------------------
    Route::get('distributors', [DistributorController::class, 'index'])->name('distributors');
    Route::get('distributors/add', [DistributorController::class, 'add'])->name('distributors.add');
    Route::post('distributors/add', [DistributorController::class, 'save'])->name('distributors.add');
    Route::get('distributors/{id}', [DistributorController::class, 'edit'])->name('distributors.edit');
    Route::post('distributors/{id}', [DistributorController::class, 'update'])->name('distributors.edit');
    Route::delete('distributors', [DistributorController::class, 'delete'])->name('distributors.delete');
    Route::get('distributors/services/{slug}', [DistributorController::class, 'services'])->name('distributors.services');
    Route::post('distributors/services/{slug}', [DistributorController::class, 'services_update'])->name('distributors.services');
    Route::get('distributors/ledger/{slug}', [DistributorController::class, 'ledger'])->name('distributors.ledger');
    Route::post('distributors/ledger/{slug}', [DistributorController::class, 'ledger_add'])->name('distributors.ledger');

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
