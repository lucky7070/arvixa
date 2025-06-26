<?php

use App\Http\Controllers\Employee\DistributorController;
use App\Routes\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\RetailerController;
use App\Http\Controllers\Employee\MainDistributorController;


/*
|--------------------------------------------------------------------------
| Web Routes For Employee
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Employee Routes
Route::middleware(['auth:employee', 'authCheck'])->name('employee.')->prefix('employee')->group(function () {
    Profile::routes();
    Route::get('/', [EmployeeController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [EmployeeController::class, 'index'])->name('dashboard');
    Route::get('toggle-online', [EmployeeController::class, 'toggle_online'])->name('toggle-online');

    // ----------------------- Retailer Routes ----------------------------------------------------
    Route::get('retailers', [RetailerController::class, 'index'])->name('retailers');
    Route::get('retailers/add', [RetailerController::class, 'add'])->name('retailers.add');
    Route::post('retailers/add', [RetailerController::class, 'save'])->name('retailers.add');
    Route::get('retailers/export', [RetailerController::class, 'export'])->name('retailers.export');
    Route::get('retailers/{slug}/edit', [RetailerController::class, 'edit'])->name('retailers.edit');
    Route::post('retailers/{slug}/edit', [RetailerController::class, 'update'])->name('retailers.edit');
    Route::get('retailers/{slug}/services', [RetailerController::class, 'services'])->name('retailers.services');
    Route::post('retailers/{slug}/services', [RetailerController::class, 'services_update'])->name('retailers.services');
    Route::match(['get', 'post'], 'retailers/notes/{slug?}', [RetailerController::class, 'notes'])->name('retailers.notes');
    Route::get('retailers/{slug}/ledger', [RetailerController::class, 'ledger'])->name('retailers.ledger');
    Route::get('retailers/{slug}/pancards', [RetailerController::class, 'pancards'])->name('retailers.pancards');

    // ----------------------- Main Distributor Routes ----------------------------------------------------
    Route::get('main_distributors', [MainDistributorController::class, 'index'])->name('main_distributors');
    Route::get('main_distributors/add', [MainDistributorController::class, 'add'])->name('main_distributors.add');
    Route::post('main_distributors/add', [MainDistributorController::class, 'save'])->name('main_distributors.add');
    Route::get('main_distributors/export', [MainDistributorController::class, 'export'])->name('main_distributors.export');
    Route::get('main_distributors/{id}', [MainDistributorController::class, 'edit'])->name('main_distributors.edit');
    Route::post('main_distributors/{id}', [MainDistributorController::class, 'update'])->name('main_distributors.edit');
    Route::delete('main_distributors', [MainDistributorController::class, 'delete'])->name('main_distributors.delete');
    Route::get('main_distributors/services/{slug}', [MainDistributorController::class, 'services'])->name('main_distributors.services');
    Route::post('main_distributors/services/{slug}', [MainDistributorController::class, 'services_update'])->name('main_distributors.services');
    Route::get('main_distributors/ledger/{slug}', [MainDistributorController::class, 'ledger'])->name('main_distributors.ledger');

    // ----------------------- Distributor Routes ----------------------------------------------------
    Route::get('distributors', [DistributorController::class, 'index'])->name('distributors');
    Route::get('distributors/add', [DistributorController::class, 'add'])->name('distributors.add');
    Route::post('distributors/add', [DistributorController::class, 'save'])->name('distributors.add');
    Route::get('distributors/export', [DistributorController::class, 'export'])->name('distributors.export');
    Route::get('distributors/{id}', [DistributorController::class, 'edit'])->name('distributors.edit');
    Route::post('distributors/{id}', [DistributorController::class, 'update'])->name('distributors.edit');
    Route::delete('distributors', [DistributorController::class, 'delete'])->name('distributors.delete');
    Route::get('distributors/services/{slug}', [DistributorController::class, 'services'])->name('distributors.services');
    Route::post('distributors/services/{slug}', [DistributorController::class, 'services_update'])->name('distributors.services');
    Route::get('distributors/ledger/{slug}', [DistributorController::class, 'ledger'])->name('distributors.ledger');
    Route::post('distributors/ledger/{slug}', [DistributorController::class, 'ledger_add'])->name('distributors.ledger');
});
