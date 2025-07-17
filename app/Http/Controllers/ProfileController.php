<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Ledger;
use App\Rules\CheckUnique;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentRequest;
use \Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Common\LedgerController;
use App\Models\PaymentMode;
use App\Models\Provider;
use App\Models\UpiPayments;

class ProfileController extends Controller
{
    protected $route;
    protected $role;
    protected $user_type;

    public function __construct(Request $request)
    {
        $this->middleware('auth:' . $this->route);
        $gaurd      = $this->route = getGuardFromURL($request);
        $this->role = ucfirst($gaurd);
        if ($gaurd == "web") {
            $this->role =   "Admin";
        }

        switch ($gaurd) {
            case 'main_distributor':
                $user_type = 2;
                break;
            case 'distributor':
                $user_type = 3;
                break;
            case 'retailer':
                $user_type = 4;
                break;
            case 'employee':
                $user_type = 5;
                break;
            case 'customer':
                $user_type = 7;
                break;
            default:
                $user_type = 0;
                break;
        }

        $this->user_type = $user_type;
    }

    public function profile(Request $request)
    {
        $user = Auth::guard($this->route)->user();
        if ($user == null) {
            return redirect()->route($this->route . '/dashboard')->with('error', 'Path not Valid.');
        }

        $role           = $this->role;
        $user['route']  = $this->route;
        $states         = State::where('status', 1)->get();
        $providers      = Provider::get()->groupBy('type');
        $user           = $request->user();
        return view('profile.update', compact('user', 'role', 'states', 'user', 'providers'));
    }

    public function update_password(Request $request)
    {
        $request->validate([
            'old_password' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (Hash::check($request->old_password, Auth::guard($this->route)->user()->password)) {
            $user = Auth::guard($this->route)->user();
            $user->password = Hash::make($request['password']);
            $user->save();

            Auth::logout();
            return redirect()->route('login')->with('success', 'Password updated successfully..!! Please login again.');
        }

        return back()->with('error', 'Credentials not Valid.');
    }

    public function update(Request $request)
    {
        if (in_array($this->user_type, [2, 3, 4])) {
            return back()->with('error', "Profile can't be update..!!");
        }

        $table = getTableFromURL($request);
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', new CheckUnique($table, Auth::guard($this->route)->id())],
            'mobile'    => ['required', 'digits:10', new CheckUnique($table, Auth::guard($this->route)->id()), 'regex:' . config('constant.phoneRegExp')],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'state_id'  => ['nullable', 'integer'],
            'city_id'   => ['nullable', 'integer']
        ]);

        $user = Auth::guard($this->route)->user();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->mobile   = $request->mobile;
        $user->state_id = $request->state_id;
        $user->city_id  = $request->city_id;
        $path = 'admin';
        if ($file = $request->file('image')) {
            removeFile(Auth::guard($this->route)->user()->image);
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $user->image        = $path . '/' . $uploadImage;
        }

        $user->save();
        return back()->with('success', 'Profile Upated Successfully!!');
    }

    public function upload_image(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => "Allow Types : .jpg, .png, .jpeg and Max Size : 2MB ",
                'image'     => ''
            ]);
        } else {

            $user = Auth::guard($this->route)->user();
            $path = 'admin';
            if ($file = $request->file('image')) {
                removeFile(Auth::guard($this->route)->user()->image);
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                $user->image        = $path . '/' . $uploadImage;
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            }

            $user->save();

            return response()->json([
                'success'   => true,
                'message'   => 'Image Updated Successfully',
                'image'     => asset('storage/' . $user->image)
            ]);
        }
    }

    public function logout()
    {
        Auth::guard('distributor')->logout();
        Auth::guard('main_distributor')->logout();
        Auth::guard('retailer')->logout();
        Auth::guard('web')->logout();
        Auth::logout();
        Session::forget('locked');
        return redirect()->to(config('ayt.front_url', route('login')));
    }

    public function wallet(Request $request)
    {
        $user = Auth::guard($this->route)->user();
        if ($user == null) {
            return redirect()->route($this->route . '/.')->with('error', 'Path not Valid.');
        }

        $role           = $this->role;
        $user['route']  = $this->route;
        $user_type      = $this->user_type;

        $route_name =  Route::getCurrentRoute()->getName();
        if ($request->ajax()) {

            $data = Ledger::select('id', 'voucher_no', 'particulars', 'amount', 'current_balance',  'updated_balance', 'payment_type', 'payment_method', 'created_at', 'status')
                ->where('user_id', $user['id'])
                ->where('user_type', $user_type);

            return LedgerController::getDataTable($data);
        }

        return view('profile.wallet', compact('user', 'role', 'route_name', 'user_type'));
    }

    public function request_money(Request $request)
    {
        $user = Auth::guard($this->route)->user();

        $role           = $this->role;
        $user['route']  = $this->route;
        $user_type      = $this->user_type;

        $route_name =  Route::getCurrentRoute()->getName();
        if ($request->ajax()) {
            $query = PaymentRequest::select('payment_requests.id', 'payment_requests.request_number',  'payment_requests.amount', 'payment_requests.title', 'payment_requests.reason', 'payment_requests.description', 'payment_requests.attachment', 'payment_requests.status', 'payment_requests.created_at', 'payment_modes.name as payment_mode_name')
                ->where('user_id', $user['id'])
                ->where('user_type', $user_type)
                ->join('payment_modes', 'payment_modes.id', 'payment_requests.payment_mode_id');

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('request_number', function ($row) {
                    return "<span data-data='" . htmlentities(json_encode($row)) . "' class='fw-bold text-primary viewDetails pointer'>" . $row['request_number'] . "</span>";
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->editColumn('amount', function ($row) {
                    return  '<b class="text-primary">₹ ' . $row['amount'] . '</b>';
                })
                ->editColumn('status', function ($row) {
                    $html = '';
                    if ($row['status'] == 0)  $html = '<span class="badge badge-light-secondary">Pending</span>';
                    if ($row['status'] == 1)  $html = '<span class="badge badge-light-success">Approved</span>';
                    if ($row['status'] == 2)  $html = '<span class="badge badge-light-danger">Rejected</span>';
                    return $html;
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['request_number', 'status', 'amount'])
                ->make(true);
        }

        $paymodesAll = PaymentMode::where('status', 1)->get();
        $paymodes =  $paymodesAll->groupBy('type')->toArray();
        ksort($paymodes);

        $indianBanks = [
            [
                'name' => "State Bank of India (SBI)",
                'link' => "https://retail.onlinesbi.sbi/retail/login.htm"
            ],
            [
                'name' => "HDFC Bank",
                'link' => "https://www.hdfcbank.com/personal/ways-to-bank/online-banking/net-banking"
            ],
            [
                'name' => "ICICI Bank",
                'link' => "https://infinity.icicibank.com/corp/Login.jsp"
            ],
            [
                'name' => "Bank of Baroda",
                'link' => "https://www.bobibanking.com/"
            ],
            [
                'name' => "Punjab National Bank (PNB)",
                'link' => "https://www.netpnb.com/"
            ],
            [
                'name' => "Axis Bank",
                'link' => "https://omni.axisbank.co.in/"
            ],
        ];

        return view('profile.request_money', compact('user', 'role', 'route_name', 'user_type', 'paymodes', 'paymodesAll', 'indianBanks'));
    }

    public function request_money_save(Request $request)
    {
        $user = Auth::guard($this->route)->user();
        if ($user == null) {
            return response()->json([
                'status'    => false,
                'message'   => "Not Allowed.",
                "data"      => ""
            ]);
        }

        $user_type = $this->user_type;
        $validator = Validator::make($request->all(), [
            'payment_mode_id'   => ['required', 'numeric', 'min:1', 'exists:payment_modes,id'],
            'amount'            => ['required', 'numeric', 'min:1', 'max:100000'],
            'description'       => ['required', 'string', 'max:500'],
            'attachment'        => ['mimes:jpg,png,jpeg,pdf', 'max:2048'],
        ]);

        if ($validator->fails()) {
            $err = array();
            foreach ($validator->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Invalid Input values.",
                    "data"      => $err
                ]);
            }
        } else {

            $data = [
                'request_number'    => Str::uuid(),
                'payment_mode_id'   => $request->payment_mode_id,
                'user_id'           => $user['id'],
                'user_type'         => $user_type,
                'amount'            => $request->amount,
                'title'             => "Load Money :: " . $user->mobile . " (" . $user->userId . ")",
                'description'       => filter_var($request->description, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                'status'            => 0,
            ];

            $path = 'payment-request';
            if ($file = $request->file('attachment')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $data['attachment'] = $path . '/' . $uploadImage;
            }

            $res = PaymentRequest::create($data);
            if ($res) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'Payment Request Submitted Successfully',
                    'data'      => '',
                ]);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Oops..!! There is some error.',
                    'data'      => ''
                ]);
            }
        }
    }

    // public function upi_payment(Request $request)
    // {
    //     $user = Auth::guard($this->route)->user();
    //     if ($user == null) {
    //         return response()->json([
    //             'status'    => false,
    //             'message'   => "Not Allowed.",
    //             "data"      => ""
    //         ]);
    //     }

    //     $ledger                         = new Ledger();
    //     $ledger->voucher_no             = Str::uuid();
    //     $ledger->user_id                = $user->id;
    //     $ledger->user_type              = $this->user_type;
    //     $ledger->amount                 = $request->amount;
    //     $ledger->payment_type           = 1;
    //     $ledger->payment_type_by_mintra = 'Yes';

    //     // Save the ledger entry
    //     $ledger->save();

    //     $ledger->voucher_no             = Str::uuid() . '_' . rand(00000, 99999);
    //     $ledger->save();

    //     // Construct the cURL request payload
    //     $payload = [
    //         "token"             => env('MITRAGETWAY_API_TOKEN'),
    //         "orderId"           => $ledger->voucher_no,
    //         "txnAmount"         => $request->amount,
    //         "txnNote"           => "UPI Payment",
    //         "customerName"      => $user->name,
    //         "customerEmail"     => $user->email,
    //         "customerMobile"    => $user->mobile
    //     ];

    //     // Initiate cURL request
    //     $curl = curl_init();

    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL             => 'https://mitragateway.com/order/qrcode',
    //         CURLOPT_RETURNTRANSFER  => true,
    //         CURLOPT_ENCODING        => '',
    //         CURLOPT_MAXREDIRS       => 10,
    //         CURLOPT_TIMEOUT         => 0,
    //         CURLOPT_FOLLOWLOCATION  => true,
    //         CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST   => 'POST',
    //         CURLOPT_POSTFIELDS      => json_encode($payload),
    //         CURLOPT_HTTPHEADER      => array(
    //             'token: ' . env('MITRAGETWAY_API_TOKEN'),
    //             'Content-Type: application/json'
    //         ),
    //     ));

    //     // Execute cURL request
    //     $response = curl_exec($curl);

    //     // Check for cURL execution errors
    //     if ($response === false) {
    //         $error_message = curl_error($curl);
    //         curl_close($curl);
    //         return response()->json([
    //             'success'   => false,
    //             'message'   => 'cURL error: ' . $error_message,
    //             'data'      => ''
    //         ]);
    //     }

    //     // Close cURL session
    //     curl_close($curl);

    //     // Decode the JSON response
    //     $response_data = json_decode($response, true);

    //     $orderId = $ledger->voucher_no; // Assuming this is your order ID
    //     $checkUrl = route('check_payment_status', ['orderId' => $orderId]);

    //     if (isset($response_data['status']) && $response_data['status'] === true) {
    //         $ledger->status = "Pending"; // Initial status is Pending
    //     } else {
    //         $ledger->status = "Pending"; // Initial status is Pending
    //     }
    //     $ledger->save();

    //     // Check if the response contains necessary data for successful payment
    //     if (isset($response_data['status']) && $response_data['status'] === true) {
    //         // Check if orderId exists and is not empty
    //         if (isset($response_data['result']['orderId']) && !empty($response_data['result']['orderId'])) {

    //             return response()->json([
    //                 'success'       => true,
    //                 'message'       => 'Payment Request Submitted Successfully',
    //                 'data'          => $response_data,
    //                 'orderId'       => $ledger->voucher_no,
    //                 'status'        => $ledger->status,
    //                 'check_url'     => $checkUrl,
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'success'   => false,
    //                 'message'   => 'Error in payment gateway response: Order ID missing or empty.',
    //                 'data'      => ''
    //             ]);
    //         }
    //     } else {
    //         return response()->json([
    //             'success'   => false,
    //             'message'   => 'Error in payment gateway response: Payment failed or status not true.',
    //             'data'      => ''
    //         ]);
    //     }
    // }


    public function upi_payment(Request $request)
    {
        $user = Auth::guard($this->route)->user();
        if ($user == null) {
            return response()->json([
                'status'    => false,
                'message'   => "Not Allowed.",
                "data"      => ""
            ]);
        }

        $upi_payment                         = new UpiPayments();
        $upi_payment->voucher_no             = Str::uuid();
        $upi_payment->user_id                = $user->id;
        $upi_payment->user_type              = $this->user_type;
        $upi_payment->amount                 = $request->amount;

        // Save the upi_payment entry
        $upi_payment->save();

        // Check if UPI gateway (MITRAGETWAY) is enabled
        if (env('PAYMENT_GATEWAY_NAME') != 'qkqr_upi_gateway') {
            // UPI gateway (MITRAGETWAY) API integration
            $payload = [
                "token"             => env('MITRAGETWAY_API_TOKEN'),
                "orderId"           => $upi_payment->voucher_no,
                "txnAmount"         => $request->amount,
                "txnNote"           => "UPI Payment",
                "customerName"      => $user->name,
                "customerEmail"     => $user->email,
                "customerMobile"    => $user->mobile
            ];

            // Initiate cURL request to UPI gateway (MITRAGETWAY) API
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL             => 'https://mitragateway.com/order/qrcode',
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_ENCODING        => '',
                CURLOPT_MAXREDIRS       => 10,
                CURLOPT_TIMEOUT         => 0,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST   => 'POST',
                CURLOPT_POSTFIELDS      => json_encode($payload),
                CURLOPT_HTTPHEADER      => array(
                    'token: ' . env('MITRAGETWAY_API_TOKEN'),
                    'Content-Type: application/json'
                ),
            ));

            // Execute cURL request
            $response = curl_exec($curl);

            // Check for cURL execution errors
            if ($response === false) {
                $error_message = curl_error($curl);
                curl_close($curl);
                return response()->json([
                    'success'   => false,
                    'message'   => 'cURL error: ' . $error_message,
                    'data'      => ''
                ]);
            }

            // Close cURL session
            curl_close($curl);

            // Decode the JSON response
            $response_data = json_decode($response, true);

            // Update ledger status based on UPI gateway (MITRAGETWAY) response
            if (isset($response_data['status']) && $response_data['status'] === true) {
                $upi_payment->status = "Pending"; // Initial status is Pending
            } else {
                $upi_payment->status = "Pending"; // Initial status is Pending
            }
            $upi_payment->save();

            // Check if the response contains necessary data for successful payment
            if (isset($response_data['status']) && $response_data['status'] === true) {
                // Check if orderId exists and is not empty
                if (isset($response_data['result']['orderId']) && !empty($response_data['result']['orderId'])) {

                    return response()->json([
                        'success'       => true,
                        'message'       => 'Payment Request Submitted Successfully',
                        'data'          => $response_data,
                        'orderId'       => $upi_payment->voucher_no,
                        'status'        => $upi_payment->status,
                        'check_url'     => route('check_payment_status', ['orderId' => $upi_payment->voucher_no]),
                    ]);
                } else {
                    return response()->json([
                        'success'   => false,
                        'message'   => 'Error in payment gateway response: Order ID missing or empty.',
                        'data'      => ''
                    ]);
                }
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Error in payment gateway response: Payment failed or status not true.',
                    'data'      => ''
                ]);
            }
        } else {
            $url = route('check_payment_qkqr_status', ['orderId' => $upi_payment->voucher_no]);
            // EQRK UPI gateway API integration
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL             => 'https://api.ekqr.in/api/create_order',
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_ENCODING        => '',
                CURLOPT_MAXREDIRS       => 10,
                CURLOPT_TIMEOUT         => 0,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST   => 'POST',
                CURLOPT_POSTFIELDS      => json_encode([
                    "key"               => env('EQRK_UPI_GATEWAY_API_KEY'),
                    "client_txn_id"     => $upi_payment->voucher_no,
                    "amount"            => $request->amount,
                    "p_info"            => "Product Name",
                    "customer_name"     => $user->name,
                    "customer_email"    => $user->email,
                    "customer_mobile"   => $user->mobile,
                    "redirect_url"      => $url, // Dynamic redirect URL
                ]),
                CURLOPT_HTTPHEADER      => array(
                    'Content-Type: application/json'
                ),
            ));

            // Execute cURL request
            $response = curl_exec($curl);

            // Check for cURL execution errors
            if ($response === false) {
                $error_message = curl_error($curl);
                curl_close($curl);
                return response()->json([
                    'success'   => false,
                    'message'   => 'cURL error: ' . $error_message,
                    'data'      => ''
                ]);
            }

            // Close cURL session
            curl_close($curl);

            // Handle response from EQRK UPI gateway
            $response_data = json_decode($response, true);

            // Check if the response contains necessary data for successful payment
            if (isset($response_data['status']) && $response_data['status'] === true) {
                // Check if orderId exists and is not empty
                if (isset($response_data['msg']) && $response_data['msg'] === 'Order Created') {
                    // Assuming 'data' contains the details
                    $data = $response_data['data'];

                    // Construct your response to send back to the client
                    return response()->json([
                        'success'       => true,
                        'message'       => 'Order Created Successfully',
                        'data'          => [
                            'order_id'      => $data['order_id'],
                            'redirect_url'  => $url,
                            'payment_url'   => $data['payment_url'],
                            'upi_id_hash'   => $data['upi_id_hash'],
                            'upi_intent'    => isset($data['upi_intent']) ? $data['upi_intent'] : null,
                        ],
                    ]);
                } else {
                    return response()->json([
                        'success'   => false,
                        'message'   => 'Error in payment gateway response: Order not created.',
                        'data'      => ''
                    ]);
                }
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Error in payment gateway response: Payment failed or status not true.',
                    'data'      => ''
                ]);
            }
        }
    }


    public function lock(Request $request)
    {
        // only if user is logged in
        if (Auth::guard($this->route)->check()) {
            Session::put('locked', true);
            $user = Auth::guard($this->route)->user();
            $path = $this->route == "web" ? '' : $this->route;
            return view('profile.lockscreen', compact('user', 'path'));
        }
        return redirect('/login');
    }

    public function unlock(Request $request)
    {
        // if user in not logged in
        if (!Auth::guard($this->route)->check())
            return redirect('/login');

        $password = $request->password;
        if (Hash::check($password, Auth::guard($this->route)->user()->password)) {
            Session::forget('locked');
            return redirect(($this->route == 'web' ? '' : $this->route) . '/dashboard')->with('success', "Profile Unlocked Sussessfully.");
        } else {
            return redirect(($this->route == 'web' ? '' : $this->route) . '/lock')->with('error', "Invalid Password.");
        }
    }
}
