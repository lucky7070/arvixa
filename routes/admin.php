<?php

use App\Routes\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\EnquiriesController;
use App\Http\Controllers\BannerAdminController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\UPIPaymentController;
use App\Http\Controllers\MainDistributorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Reports\PanCardController;
use App\Http\Controllers\Reports\ElectricityController;
use App\Http\Controllers\Retailer\PanCardController as RetailerPanCardController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TestimonialController;

/*
|--------------------------------------------------------------------------
| Web Routes For Admin
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Admin & Sub-Admin Routes
Route::middleware(['auth', 'permission', 'authCheck'])->group(function () {
    Profile::routes();
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::get('roles', [RolesController::class, 'index'])->name('roles')->middleware('isAllow:102,can_view');
    Route::post('roles', [RolesController::class, 'save'])->name('roles')->middleware('isAllow:102,can_add');
    Route::put('roles', [RolesController::class, 'update'])->name('roles')->middleware('isAllow:102,can_edit');
    Route::delete('roles', [RolesController::class, 'delete'])->name('roles.delete')->middleware('isAllow:102,can_delete');
    Route::get('roles/permission/{id}', [RolesController::class, 'permission'])->name('roles.permission.view')->middleware('isAllow:102,can_edit');
    Route::put('roles/permission', [RolesController::class, 'permission_update'])->name('roles.permission.update')->middleware('isAllow:102,can_edit');

    // ----------------------- Admin and Sub Admin Routes ----------------------------------------------------
    Route::get('users', [UsersController::class, 'index'])->name('users')->middleware('isAllow:103,can_view');
    Route::get('users/add', [UsersController::class, 'add'])->name('users.add')->middleware('isAllow:103,can_add');
    Route::post('users/add', [UsersController::class, 'save'])->name('users.add')->middleware('isAllow:103,can_add');
    Route::get('users/{id}', [UsersController::class, 'edit'])->name('users.edit')->middleware('isAllow:103,can_edit');
    Route::post('users/{id}', [UsersController::class, 'update'])->name('users.edit')->middleware('isAllow:103,can_edit');
    Route::delete('users', [UsersController::class, 'delete'])->name('users.delete')->middleware('isAllow:103,can_delete');
    Route::get('users/permission/{id}', [UsersController::class, 'permission'])->name('users.permission.view')->middleware('isAllow:103,can_edit');
    Route::put('users/permission', [UsersController::class, 'permission_update'])->name('users.permission.update')->middleware('isAllow:103,can_edit');

    // ----------------------- MainDistributor Routes ----------------------------------------------------
    Route::get('main_distributors', [MainDistributorController::class, 'index'])->name('main_distributors')->middleware('isAllow:104,can_view');
    Route::get('main_distributors/add', [MainDistributorController::class, 'add'])->name('main_distributors.add')->middleware('isAllow:104,can_add');
    Route::post('main_distributors/add', [MainDistributorController::class, 'save'])->name('main_distributors.add')->middleware('isAllow:104,can_add');
    Route::get('main_distributors/export', [MainDistributorController::class, 'export'])->name('main_distributors.export')->middleware('isAllow:104,can_view');
    Route::get('main_distributors/{id}', [MainDistributorController::class, 'edit'])->name('main_distributors.edit')->middleware('isAllow:104,can_edit');
    Route::post('main_distributors/{id}', [MainDistributorController::class, 'update'])->name('main_distributors.edit')->middleware('isAllow:104,can_edit');
    Route::delete('main_distributors', [MainDistributorController::class, 'delete'])->name('main_distributors.delete')->middleware('isAllow:104,can_delete');
    Route::get('main_distributors/services/{slug}', [MainDistributorController::class, 'services'])->name('main_distributors.services')->middleware('isAllow:104,can_edit');
    Route::post('main_distributors/services/{slug}', [MainDistributorController::class, 'services_update'])->name('main_distributors.services')->middleware('isAllow:104,can_edit');
    Route::get('main_distributors/ledger/{slug}', [MainDistributorController::class, 'ledger'])->name('main_distributors.ledger')->middleware('isAllow:104,can_edit');
    Route::post('main_distributors/ledger/{slug}', [MainDistributorController::class, 'ledger_add'])->name('main_distributors.ledger')->middleware('isAllow:104,can_edit');

    // ----------------------- Distributor Routes ----------------------------------------------------
    Route::get('distributors', [DistributorController::class, 'index'])->name('distributors')->middleware('isAllow:105,can_view');
    Route::get('distributors/add', [DistributorController::class, 'add'])->name('distributors.add')->middleware('isAllow:105,can_add');
    Route::post('distributors/add', [DistributorController::class, 'save'])->name('distributors.add')->middleware('isAllow:105,can_add');
    Route::get('distributors/export', [DistributorController::class, 'export'])->name('distributors.export')->middleware('isAllow:105,can_view');
    Route::get('distributors/{id}', [DistributorController::class, 'edit'])->name('distributors.edit')->middleware('isAllow:105,can_edit');
    Route::post('distributors/{id}', [DistributorController::class, 'update'])->name('distributors.edit')->middleware('isAllow:105,can_edit');
    Route::delete('distributors', [DistributorController::class, 'delete'])->name('distributors.delete')->middleware('isAllow:105,can_delete');
    Route::get('distributors/services/{slug}', [DistributorController::class, 'services'])->name('distributors.services')->middleware('isAllow:105,can_edit');
    Route::post('distributors/services/{slug}', [DistributorController::class, 'services_update'])->name('distributors.services')->middleware('isAllow:105,can_edit');
    Route::get('distributors/ledger/{slug}', [DistributorController::class, 'ledger'])->name('distributors.ledger')->middleware('isAllow:105,can_edit');
    Route::post('distributors/ledger/{slug}', [DistributorController::class, 'ledger_add'])->name('distributors.ledger')->middleware('isAllow:105,can_edit');

    // ----------------------- Retailer Routes ----------------------------------------------------
    Route::get('retailers', [RetailerController::class, 'index'])->name('retailers')->middleware('isAllow:106,can_view');
    Route::get('retailers/add', [RetailerController::class, 'add'])->name('retailers.add')->middleware('isAllow:106,can_add');
    Route::post('retailers/add', [RetailerController::class, 'save'])->name('retailers.add')->middleware('isAllow:106,can_add', 'mail');
    Route::get('retailers/not-loaded', [RetailerController::class, 'not_loaded'])->name('retailers.not_loaded')->middleware('isAllow:106,can_view');
    Route::get('retailers/not-loaded/export', [RetailerController::class, 'not_loaded_export'])->name('retailers.not_loaded.export')->middleware('isAllow:106,can_view');
    Route::get('retailers/export', [RetailerController::class, 'export'])->name('retailers.export')->middleware('isAllow:106,can_view');
    Route::get('retailers/{id}', [RetailerController::class, 'edit'])->name('retailers.edit')->middleware('isAllow:106,can_edit');
    Route::post('retailers/{id}', [RetailerController::class, 'update'])->name('retailers.edit')->middleware('isAllow:106,can_edit');
    Route::delete('retailers', [RetailerController::class, 'delete'])->name('retailers.delete')->middleware('isAllow:106,can_delete');
    Route::get('retailers/customers/{id}', [RetailerController::class, 'customers_list'])->name('retailers.customers_list')->middleware('isAllow:106,can_view');
    Route::get('retailers/services/{slug}', [RetailerController::class, 'services'])->name('retailers.services')->middleware('isAllow:106,can_edit');
    Route::post('retailers/services/{slug}', [RetailerController::class, 'services_update'])->name('retailers.services')->middleware('isAllow:106,can_edit');
    Route::put('retailers/services-commission', [RetailerController::class, 'services_commission_update'])->name('retailers.commission.services')->middleware('isAllow:106,can_edit');
    Route::get('retailers/ledger/{slug}', [RetailerController::class, 'ledger'])->name('retailers.ledger')->middleware('isAllow:106,can_edit');
    Route::post('retailers/ledger/{slug}', [RetailerController::class, 'ledger_add'])->name('retailers.ledger')->middleware('isAllow:106,can_edit');

    // ----------------------- Service Routes ----------------------------------------------------
    Route::get('services', [ServiceController::class, 'index'])->name('services')->middleware('isAllow:107,can_view');
    Route::get('services/add', [ServiceController::class, 'add'])->name('services.add')->middleware('isAllow:107,can_add');
    Route::post('services/add', [ServiceController::class, 'save'])->name('services.add')->middleware('isAllow:107,can_add');
    Route::get('services/{id}', [ServiceController::class, 'edit'])->name('services.edit')->middleware('isAllow:107,can_edit');
    Route::post('services/{id}', [ServiceController::class, 'update'])->name('services.edit')->middleware('isAllow:107,can_edit');
    Route::delete('services', [ServiceController::class, 'delete'])->name('services.delete')->middleware('isAllow:107,can_delete');

    // ----------------------- CMS Routes ----------------------------------------------------
    Route::get('cms', [CmsController::class, 'index'])->name('cms')->middleware('isAllow:108,can_view');
    Route::get('cms/add', [CmsController::class, 'add'])->name('cms.add')->middleware('isAllow:108,can_add');
    Route::post('cms/add', [CmsController::class, 'save'])->name('cms.add')->middleware('isAllow:108,can_add');
    Route::get('cms/{id}', [CmsController::class, 'edit'])->name('cms.edit')->middleware('isAllow:108,can_edit');
    Route::post('cms', [CmsController::class, 'slug'])->name('cms.slug')->middleware('isAllow:108,can_edit');
    Route::post('cms/{id}', [CmsController::class, 'update'])->name('cms.edit')->middleware('isAllow:108,can_edit');
    Route::delete('cms', [CmsController::class, 'delete'])->name('cms.delete')->middleware('isAllow:108,can_delete');

    // ----------------------- Payment Request Routes ----------------------------------------------------
    Route::get('payment-request', [PaymentRequestController::class, 'payment_request'])->name('payment-request')->middleware('isAllow:109,can_view');
    Route::post('payment-request', [PaymentRequestController::class, 'payment_request_update'])->name('payment-request')->middleware('isAllow:109,can_edit');
    Route::get('payment-request/export', [PaymentRequestController::class, 'payment_request_export'])->name('payment-request.export')->middleware('isAllow:109,can_view');


    // ----------------------- UPI Payment Routes ----------------------------------------------------
    Route::get('upi-payment', [UPIPaymentController::class, 'upi_payment'])->name('upi-payment')->middleware('isAllow:123,can_view');
    Route::post('upi-payment', [UPIPaymentController::class, 'upi_payment_update'])->name('upi-payment')->middleware('isAllow:123,can_edit');
    Route::get('upi-payment/export', [UPIPaymentController::class, 'upi_payment_export'])->name('upi-payment.export')->middleware('isAllow:123,can_view');

    // ----------------------- States Routes ----------------------------------------------------
    Route::get('states', [StateController::class, 'index'])->name('states')->middleware('isAllow:110,can_view');
    Route::post('states', [StateController::class, 'save'])->name('states')->middleware('isAllow:110,can_add');
    Route::put('states', [StateController::class, 'update'])->name('states')->middleware('isAllow:110,can_edit');
    Route::delete('states', [StateController::class, 'delete'])->name('states.delete')->middleware('isAllow:110,can_delete');

    // ----------------------- City Routes ----------------------------------------------------
    Route::get('cities', [CityController::class, 'index'])->name('cities')->middleware('isAllow:111,can_view');
    Route::post('cities', [CityController::class, 'save'])->name('cities')->middleware('isAllow:111,can_add');
    Route::put('cities', [CityController::class, 'update'])->name('cities')->middleware('isAllow:111,can_edit');
    Route::delete('cities', [CityController::class, 'delete'])->name('cities.delete')->middleware('isAllow:111,can_delete');

    // ----------------------- Customer Routes ----------------------------------------------------
    Route::get('customers', [CustomerController::class, 'index'])->name('customers')->middleware('isAllow:112,can_view');
    Route::get('customers/add', [CustomerController::class, 'add'])->name('customers.add')->middleware('isAllow:112,can_add');
    Route::post('customers/add', [CustomerController::class, 'save'])->name('customers.add')->middleware('isAllow:112,can_add');
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export')->middleware('isAllow:112,can_view');
    Route::get('customers/{id}', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('isAllow:112,can_edit');
    Route::post('customers/{id}', [CustomerController::class, 'update'])->name('customers.edit')->middleware('isAllow:112,can_edit');
    Route::delete('customers', [CustomerController::class, 'delete'])->name('customers.delete')->middleware('isAllow:112,can_delete');
    Route::any('customers/documents/{id}', [CustomerController::class, 'documents'])->name('customers.documents')->middleware('isAllow:112,can_edit');
    Route::any('customers/banks/{id}', [CustomerController::class, 'banks'])->name('customers.banks')->middleware('isAllow:112,can_edit');
    Route::any('customers/service-used/{id}', [CustomerController::class, 'service_used'])->name('customers.service_used')->middleware('isAllow:112,can_view');

    // ----------------------- Admin Banner Routes ----------------------------------------------------
    Route::get('admin-banners', [BannerAdminController::class, 'index'])->name('admin-banners')->middleware('isAllow:113,can_view');
    Route::post('admin-banners', [BannerAdminController::class, 'save'])->name('admin-banners')->middleware('isAllow:113,can_add');
    Route::put('admin-banners', [BannerAdminController::class, 'update'])->name('admin-banners')->middleware('isAllow:113,can_edit');
    Route::delete('admin-banners', [BannerAdminController::class, 'delete'])->name('admin-banners')->middleware('isAllow:113,can_delete');

    // ----------------------- Setting Routes ----------------------------------------------------
    Route::any('user-chart', [HomeController::class, 'user_chart'])->name('user-chart');
    Route::any('setting/{id}', [SettingController::class, 'setting'])->name('setting')->middleware('isAllow:101,can_view');
    Route::get('database-backup', [SettingController::class, 'database_backup'])->name('database_backup')->middleware('isAllow:101,can_view');

    // ----------------------- Send Email Routes ----------------------------------------------------
    Route::get('emails', [EmailController::class, 'index'])->name('emails')->middleware('isAllow:115,can_view');
    Route::post('emails', [EmailController::class, 'send'])->name('emails')->middleware('isAllow:115,can_add');

    // ----------------------- Employee Routes ----------------------------------------------------
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees')->middleware('isAllow:116,can_view');
    Route::get('employees/add', [EmployeeController::class, 'add'])->name('employees.add')->middleware('isAllow:116,can_add');
    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export')->middleware('isAllow:116,can_view');
    Route::post('employees/add', [EmployeeController::class, 'save'])->name('employees.add')->middleware('isAllow:116,can_add');
    Route::get('employees/{id}', [EmployeeController::class, 'edit'])->name('employees.edit')->middleware('isAllow:116,can_edit');
    Route::post('employees/{id}', [EmployeeController::class, 'update'])->name('employees.edit')->middleware('isAllow:116,can_edit');
    Route::delete('employees', [EmployeeController::class, 'delete'])->name('employees.delete')->middleware('isAllow:116,can_delete');

    // ----------------------- Send Notication Routes ----------------------------------------------------
    Route::get('notification', [NotificationController::class, 'index'])->name('notification')->middleware('isAllow:117,can_view');
    Route::post('notification', [NotificationController::class, 'send'])->name('notification')->middleware('isAllow:117,can_add');

    // ----------------------- Slider Routes ----------------------------------------------------
    Route::get('sliders', [SliderController::class, 'index'])->name('sliders')->middleware('isAllow:118,can_view');
    Route::get('sliders/add', [SliderController::class, 'add'])->name('sliders.add')->middleware('isAllow:118,can_add');
    Route::post('sliders/add', [SliderController::class, 'save'])->name('sliders.add')->middleware('isAllow:118,can_add');
    Route::get('sliders/{id}', [SliderController::class, 'edit'])->name('sliders.edit')->middleware('isAllow:118,can_edit');
    Route::post('sliders/{id}', [SliderController::class, 'update'])->name('sliders.edit')->middleware('isAllow:118,can_edit');
    Route::delete('sliders', [SliderController::class, 'delete'])->name('sliders.delete')->middleware('isAllow:118,can_delete');

    // ----------------------- Testimonial Routes ----------------------------------------------------
    Route::get('testimonials', [TestimonialController::class, 'index'])->name('testimonials')->middleware('isAllow:119,can_view');
    Route::get('testimonials/add', [TestimonialController::class, 'add'])->name('testimonials.add')->middleware('isAllow:119,can_add');
    Route::post('testimonials/add', [TestimonialController::class, 'save'])->name('testimonials.add')->middleware('isAllow:119,can_add');
    Route::get('testimonials/{id}', [TestimonialController::class, 'edit'])->name('testimonials.edit')->middleware('isAllow:119,can_edit');
    Route::post('testimonials/{id}', [TestimonialController::class, 'update'])->name('testimonials.edit')->middleware('isAllow:119,can_edit');
    Route::delete('testimonials', [TestimonialController::class, 'delete'])->name('testimonials.delete')->middleware('isAllow:119,can_delete');

    // ----------------------- FAQ Routes ----------------------------------------------------
    Route::get('faq', [FaqController::class, 'index'])->name('faq')->middleware('isAllow:120,can_view');
    Route::get('faq/add', [FaqController::class, 'add'])->name('faq.add')->middleware('isAllow:120,can_add');
    Route::post('faq/add', [FaqController::class, 'save'])->name('faq.add')->middleware('isAllow:120,can_add');
    Route::get('faq/{id}', [FaqController::class, 'edit'])->name('faq.edit')->middleware('isAllow:120,can_edit');
    Route::post('faq', [FaqController::class, 'slug'])->name('faq.slug')->middleware('isAllow:120,can_edit');
    Route::post('faq/{id}', [FaqController::class, 'update'])->name('faq.edit')->middleware('isAllow:120,can_edit');
    Route::delete('faq', [FaqController::class, 'delete'])->name('faq.delete')->middleware('isAllow:120,can_delete');

    // ----------------------- Enquiries Routes ----------------------------------------------------
    Route::get('enquiries', [EnquiriesController::class, 'index'])->name('enquiries')->middleware('isAllow:121,can_view');
    Route::delete('enquiries', [EnquiriesController::class, 'delete'])->name('enquiries.delete')->middleware('isAllow:121,can_delete');

    Route::get('join-requests', [EnquiriesController::class, 'join_requests'])->name('join-requests')->middleware('isAllow:121,can_view');
    Route::delete('join-requests', [EnquiriesController::class, 'join_request_delete'])->name('join-requests.delete')->middleware('isAllow:121,can_delete');

    // ----------------------- Reports Routes ----------------------------------------------------
    Route::name('reports.')->prefix('reports')->group(function () {
        Route::get('pan-cards', [PanCardController::class, 'index'])->name('pan-cards')->middleware('isAllow:114,can_view');
        Route::get('pan-cards/export', [PanCardController::class, 'export'])->name('pan-cards.export')->middleware('isAllow:114,can_view');
        Route::post('pan-cards/status', [RetailerPanCardController::class, 'pan_card_status'])->name('pan-cards.status');
        Route::post('pan-cards/statistics', [PanCardController::class, 'statistics'])->name('pan-cards.statistics');

        // ----------------------- ElectricityController ----------------------------------------------------
        Route::get('electricity-bill', [ElectricityController::class, 'index'])->name('electricity-bill')->middleware('isAllow:114,can_view');
        Route::get('water-bill', [ElectricityController::class, 'waterbill'])->name('water-bill')->middleware('isAllow:114,can_view');
        Route::get('bills/export/{type}', [ElectricityController::class, 'export'])->name('bills.export')->middleware('isAllow:114,can_view');
        Route::get('gas-bill', [ElectricityController::class, 'gasbill'])->name('gas-bill')->middleware('isAllow:114,can_view');
        Route::get('lic-bill', [ElectricityController::class, 'licbill'])->name('lic-bill')->middleware('isAllow:114,can_view');
        Route::post('bill/submit', [ElectricityController::class, 'submit'])->name('bill-submit')->middleware('isAllow:114,can_edit');
    });
});
