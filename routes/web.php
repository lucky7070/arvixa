<?php



use App\Routes\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CityController;

use App\Http\Controllers\CustomerController;

use App\Http\Controllers\RetailerController;

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Common\CommonController;

use App\Http\Controllers\Common\LedgerController;

use App\Http\Controllers\Common\RazorPayController;

use App\Http\Controllers\Retailer\PanCardController;

use App\Http\Controllers\Retailer\RetailersController;

use App\Http\Controllers\Auth\ForgotPasswordController;

use App\Http\Controllers\Api\ServiceController as ApiServiceController;

use App\Http\Controllers\FrontController;

use App\Http\Controllers\Webhook\MintraGatewayController;
use App\Http\Controllers\Retailer\ElectricityController;
use App\Http\Controllers\Retailer\WaterController;
use App\Http\Controllers\Retailer\GasController;
use App\Http\Controllers\Retailer\LicController;



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/


Route::get('/check-gas', function (Request $request) {
    $apiKey = 'ba0fa41bee5146ebe30f8f7e3c10c68b';

    $tel = $request->query('tel'); // Customer number
    $operator = $request->query('operator'); // e.g., BPCL, IGL, etc.

    if (!$tel || !$operator) {
        return response()->json(['error' => 'Missing required parameters: tel or operator'], 400);
    }

    // Clean operator in case of extra quotes
    $operator = trim($operator, '"');

    $url = "https://www.mplan.in/api/Gas.php?apikey={$apiKey}&offer=roffer&tel={$tel}&operator={$operator}";

    $response = Http::get($url);

    if ($response->successful()) {
        return response()->json($response->json());
    }

    return response()->json(['error' => 'Failed to fetch gas bill details'], 500);
});


Route::get('/check-insurance', function (Request $request) {
    // Get values from query string
    $apiKey = 'ba0fa41bee5146ebe30f8f7e3c10c68b';

    $policy = $request->query('policyNumber');
    $mobile = $request->query('mobile');
    $operator = $request->query('operator');

    if (!$policy || !$mobile || !$operator) {
        return response()->json(['error' => 'Missing required parameters'], 400);
    }

    // Clean operator in case it has quotes
    $operator = trim($operator, '"');

    $url = "https://www.mplan.in/api/insurance.php?apikey={$apiKey}&offer=roffer&tel={$policy}&mob={$mobile}&operator={$operator}";

    $response = Http::get($url);

    if ($response->successful()) {
        return response()->json($response->json());
    }

    return response()->json(['error' => 'Failed to fetch insurance details'], 500);
});


Route::get('/water-bill-details', function (Request $request) {
    $url = 'https://www.mplan.in/api/water.php';

    $operator = $request->query('operator');
    $offer = $request->query('offer');
    $tel = $request->query('tel');

    if (!$operator || !$offer || !$tel) {
        return response()->json(['error' => 'Missing required parameters.'], 400);
    }

    $queryParams = [
        'apikey'   => 'ba0fa41bee5146ebe30f8f7e3c10c68b',
        'offer'    => $offer,
        'tel'      => $tel,
        'operator' => $operator,
    ];

    $response = Http::get($url, $queryParams);
    $data = $response->json();

    if (!$response->successful() || empty($data) || empty($data['records'])) {
        return response()->json(['error' => 'Failed to fetch bill details.'], 500);
    }

    $record = $data['records'][0];

    return response()->json([
        'biller_name'   => $record['CustomerName'] ?? 'N/A',
        'consumer_no'   => $record['CustomerId'] ?? $tel,
        'amount_due'    => $record['Billamount'] ?? 'N/A',
        'status'        => $record['Status'] ?? 'N/A',
        'due_date'      => $record['Duedate'] ?? 'N/A',
        'bill_number'   => $record['BillNumber'] ?? 'N/A',
        'raw_response'  => $data // if you want to debug full response
    ]);
});

// Route::redirect('/', 'login')->name('home');

// Route definition in Laravel (assuming in routes/web.php or routes/api.php)
Route::get('/check_payment_status/{orderId}',  [App\Http\Controllers\PaymentProfileController::class, 'checkPaymentStatus'])->name('check_payment_status');
Route::get('/check_payment_qkqr_status/{orderId}',  [App\Http\Controllers\PaymentProfileController::class, 'checkPaymentQkqrStatus'])->name('check_payment_qkqr_status');


// Open WebSite Routes

Route::get('/', [FrontController::class, 'home'])->name('home');

Route::get('/home', [FrontController::class, 'home'])->name('home');

Route::get('/about-us', [FrontController::class, 'about'])->name('about');

Route::get('/terms-and-condition', [FrontController::class, 'terms_and_condition'])->name('terms_and_condition');

Route::get('/privacy-policy', [FrontController::class, 'privacy_policy'])->name('privacy_policy');

Route::get('/refund-policy', [FrontController::class, 'refund_policy'])->name('refund_policy');

Route::get('/contact-us', [FrontController::class, 'contact'])->name('contact');

Route::post('/contact-us', [FrontController::class, 'contact_save'])->name('contact');

Route::get('/join-us', [FrontController::class, 'join_us'])->name('join_us');

Route::post('/join-us', [FrontController::class, 'join_us_save'])->name('join_us');

Route::get('/testimonial', [FrontController::class, 'testimonial'])->name('testimonial');

Route::get('/our-services', [FrontController::class, 'services'])->name('our-services');



// Authentication Routes

Auth::routes();

Route::get('/{guard}/login', [LoginController::class, 'showLoginForm'])->whereIn('guard', ['admin', 'main_distributor', 'distributor', 'retailer', 'employee'])
    ->name('loginPage');



Route::get('/{guard}/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])

    ->whereIn('guard', ['admin', 'main_distributor', 'distributor', 'retailer', 'employee'])

    ->name('forget.password');



Route::any('/user-search', [CommonController::class, 'user_search'])->name('user-search');

Route::get('/login', function () {

    return redirect()->to(url('/retailer/login'));

})->name('login');



Route::get('/{guard}', function ($guard) {

    if ($guard == 'admin')

        return redirect()->to(url('/admin/login'));

    else

        return redirect()->to(url('/' . $guard . '/dashboard'));

})->whereIn('guard', ['admin', 'main_distributor', 'distributor', 'retailer', 'employee']);



// Retailer Routes

Route::middleware(['auth:retailer', 'authCheck'])->group(function () {
    Route::name('retailer.')->prefix('retailer')->group(function () {

        Profile::routes();

        Route::get('/', [RetailersController::class, 'index'])->name('dashboard');

        Route::get('dashboard', [RetailersController::class, 'index'])->name('dashboard');
        
        Route::get('electricity-bill', [ElectricityController::class, 'view'])->name('electricity-bill');
        Route::get('electricity-bill-details', [ElectricityController::class, 'electricityGetDetails'])->name('electricity-bill-details');
        Route::post('electricity-payment-submit', [ElectricityController::class, 'electricityPaymentSubmit'])->name('electricity-payment-submit');
        Route::get('download-receipt/{id}', [ElectricityController::class, 'downloadReceipt'])->name('download.receipt');
        
        
        Route::get('water-bill', [WaterController::class, 'view'])->name('water-bill');
        Route::get('water-bill-details', [WaterController::class, 'waterGetDetails'])->name('water-bill-details');
        Route::post('water-payment-submit', [WaterController::class, 'waterPaymentSubmit'])->name('water-payment-submit');
        Route::get('water-download-receipt/{id}', [WaterController::class, 'downloadReceipt'])->name('water-download.receipt');
        
        Route::get('gas-bill', [GasController::class, 'view'])->name('gas-bill');
        Route::get('gas-bill-details', [GasController::class, 'gasGetDetails'])->name('gas-bill-details');
        Route::post('gas-payment-submit', [GasController::class, 'gasPaymentSubmit'])->name('gas-payment-submit');
        Route::get('gas-download-receipt/{id}', [GasController::class, 'downloadReceipt'])->name('gas-download.receipt');
        
        Route::get('lic-bill', [LicController::class, 'view'])->name('lic-bill');
        Route::get('lic-bill-details', [LicController::class, 'licGetDetails'])->name('lic-bill-details');
        Route::post('lic-payment-submit', [LicController::class, 'licPaymentSubmit'])->name('lic-payment-submit');
        Route::get('lic-download-receipt/{id}', [LicController::class, 'downloadReceipt'])->name('lic-download.receipt');
    });



    // Pancard Routes

    Route::prefix('service')->group(function () {

        Route::get('pan-card/{card_type?}', [PanCardController::class, 'pan_card'])->name('pan-card')->whereIn('card_type', ['physical', 'digital']);

        Route::get('create-pan-card/{card_type?}', [PanCardController::class, 'create_pan_card'])->name('create-pan-card')->whereIn('card_type', ['physical', 'digital']);

        Route::post('create-pan-card/{card_type?}', [PanCardController::class, 'pan_card_save'])->name('create-pan-card')->whereIn('card_type', ['physical', 'digital']);

        Route::post('pan-card-status', [PanCardController::class, 'pan_card_status'])->name('pan-card-status');

        Route::get('update-pan-card/{card_type?}', [PanCardController::class, 'update_pan_card'])->name('update-pan-card')->whereIn('card_type', ['physical', 'digital']);

        Route::post('update-pan-card/{card_type?}', [PanCardController::class, 'update_pan_card_save'])->name('update-pan-card')->whereIn('card_type', ['physical', 'digital']);

        Route::post('incomplete-pan-card', [PanCardController::class, 'incomplete_pan_card'])->name('incomplete-pan-card');

        Route::get('pan-card-export/{card_type?}', [PanCardController::class, 'export'])->name('pan-card-export')->whereIn('card_type', ['physical', 'digital']);

    });

});





Route::middleware(['authCheck'])->group(function () {

    Route::get('ledger/export', [LedgerController::class, 'export'])->name('ledger.export');

    Route::post('create-order', [RazorPayController::class, 'razorpay'])->name('razorpay');

    Route::post('update-wallet', [RazorPayController::class, 'verify'])->name('update-wallet');

    Route::post('distributors_list', [RetailerController::class, 'distributors_list'])->name('retailers.distributors_list');

});



// Open Routes

Route::post('get_cities', [CityController::class, 'get_cities'])->name('cities.list');

Route::post('customer-find', [CustomerController::class, 'customer_find'])->name('customer.find');

Route::post('upload-image', [CommonController::class, 'upload_image'])->name('upload_image');

Route::get('get-user-list', [CommonController::class, 'get_user_list'])->name('get_user_list');

Route::get('get-user-list-filter', [CommonController::class, 'get_user_list_filter'])->name('get_user_list_filter');

Route::any('change-pan-card-status', [PanCardController::class, 'change_pan_card_status'])->name('change-pan-card-status');

// Route::post('change-pan-card-status-api', [ApiServiceController::class, 'change_pan_card_status_api'])->name('change-pan-card-status-api');

Route::post('webhook/mintra-gateway-response', [MintraGatewayController::class, 'mintra_gateway_response']);

Route::fallback(function () {

    abort(404);

});

