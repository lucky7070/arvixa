<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Cms;
use App\Models\City;
use App\Models\State;
use App\Models\Ledger;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Retailer;
use App\Models\Services;
use App\Library\TextLocal;
use App\Rules\CheckUnique;
use App\Models\BannerAdmin;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use App\Models\PaymentRequest;
use App\Models\RegistrationOtp;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Common\ServicesLogController;

class AuthController extends Controller
{
    public function send_otp(Request $request)
    {
        if ($request->is_register)
            $rule    = ['required', 'numeric', 'min:10', 'regex:' . config('constant.phoneRegExp'), new CheckUnique('retailers')];
        else
            $rule    = ['required', 'numeric', 'min:10', 'regex:' . config('constant.phoneRegExp'), 'exists:retailers,mobile'];

        $validation = Validator::make(
            $request->all(),
            ['mobile'    => $rule],
            [
                'mobile.exists'     => "Account doesn't exist",
                'mobile.regex'      => "Please enter valid indian mobile number."
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validation->errors()->first('mobile'),
                "data"      => [
                    'mobile'    => $validation->errors()->first('mobile'),
                ]
            ]);
        } else {

            $old = RegistrationOtp::where('mobile', $request->mobile)->where('expire_at', '>', Carbon::now())->first();
            if ($old) {
                $otp        = $old->otp;
            } else {
                $otp        = random_int(100000, 999999);
                RegistrationOtp::where('mobile', $request->mobile)->delete();
                RegistrationOtp::create([
                    'mobile'        =>  $request->mobile,
                    'otp'           =>  $otp,
                    'expire_at'     =>  Carbon::now()->addMinutes(10)
                ]);
            }

            if (TextLocal::sendSms(
                ['+91' . $request->mobile],
                "Hello User Your Login Verification Code is " . $otp . " Thanks AYT"
            )) {
                $response = array(
                    'status'    => true,
                    'message'   => 'Enter OTP recived on your mobile.!!',
                    'data'      => ''
                );
                return response()->json($response, 200);
            } else {
                $response = array(
                    'status'    => false,
                    'message'   => "OTP can't be send, Please retry after some time.",
                    'data'      => ''
                );
                return response()->json($response, 422);
            }
        }
    }

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'string', 'email', 'max:100', new CheckUnique('retailers')],
            'mobile'    => ['required', 'numeric', 'min:10', new CheckUnique('retailers'), 'regex:' . config('constant.phoneRegExp')],
            'password'  => ['required', 'string', 'min:8'],
            'otp'       => ['required', 'numeric', 'min:6'],
        ], [
            'mobile.regex'      => "Please enter valid indian mobile number."
        ]);

        if ($validation->fails()) {
            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ], 422);
        } else {

            $checkOtp = RegistrationOtp::where('mobile', $request->mobile)
                ->where('otp', $request->otp)
                ->first();

            if (!$checkOtp) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Incorrect OTP..!!",
                    'data'      => [
                        'otp'   => "Incorrect OTP..!!",
                    ]
                ], 422);
            }

            if ($checkOtp && Carbon::now()->isAfter($checkOtp->expire_at)) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Your OTP has been expired',
                    'data'      => [
                        'otp'   => 'Your OTP has been expired',
                    ]
                ], 422);
            }

            $retailer =  Retailer::create([
                'slug'          => Str::uuid(),
                'name'          => $request->name,
                'email'         => $request->email,
                'mobile'        => $request->mobile,
                'status'        => 1,
                'registor_from' => 2,
                'image'         => 'admin/avatar.png',
                'password'      => Hash::make($request->password),
            ]);

            RegistrationOtp::where('mobile', $request->mobile)->delete();
            $services = Services::where('default_assign', 1)->where('status', 1)->get();
            foreach ($services as $key => $value) {
                ServicesLogController::update($value->id, $retailer->id, 4);
            }

            SendWelComeEmail::dispatch($retailer, $request->site_settings);
            return response()->json([
                'status'    => true,
                'message'   => 'Registration complete, Please login.',
                'data'      => []
            ], 200);
        }
    }

    public function login(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'mobile'    => ['required'],
                'password'  => ['required', 'string', 'min:6']
            ]
        );

        if ($validation->fails()) {
            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            if ($validation->fails()) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Invalid Input values.",
                    "data"      => $err
                ], 422);
            }
        } else {

            if (is_numeric($request->mobile)) {
                $userName = 'mobile';
                $request->merge(['mobile' => $request->mobile]);
            } elseif (filter_var($request->mobile, FILTER_VALIDATE_EMAIL)) {
                $userName = 'email';
                $request->merge(['email' => $request->mobile]);
            } else {
                $userName = 'userId';
                $request->merge(['userId' => $request->mobile]);
            }

            if (Auth::guard('retailer')->attempt($request->only([$userName, 'password']))) {
                $user = Auth::guard('retailer')->user();

                if ($request->device_id && $request->fcm_id) {
                    $user->update([
                        'device_id' => $request->device_id,
                        'fcm_id'    => $request->fcm_id,
                    ]);
                }

                $token =  $user->createToken('AuthLogin')->accessToken;
                $user['image'] = imageexist($user['image']);
                $response = array(
                    'status'    => true,
                    'message'   => 'Logged in successfully',
                    'data'      => [
                        'token' => $token,
                        'user'  => $user
                    ]
                );

                return response()->json($response);
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => "Your have entered wrong password, Please try again.",
                    "data"      => []
                ], 200);
            }
        }
    }

    public function reset_password(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'mobile'                => ['required', 'numeric', 'min:10', 'exists:retailers,mobile,deleted_at,NULL,status,1', 'regex:' . config('constant.phoneRegExp')],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'otp'                   => ['required', 'string'],
        ]);

        if ($validation->fails()) {

            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ], 422);
        } else {

            $checkOtp = RegistrationOtp::where('mobile', $request->mobile)
                ->where('otp', $request->otp)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$checkOtp) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Incorrect OTP..!!",
                    'data'      => []
                ], 422);
            }

            if ($checkOtp && Carbon::now()->isAfter($checkOtp->expire_at)) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Your OTP has been expired',
                    'data'      => []
                ], 422);
            }

            $retailer['password']  = Hash::make($request->password);
            Retailer::where('mobile', $request->mobile)->update($retailer);
            RegistrationOtp::where('mobile', $request->mobile)->delete();

            return response()->json([
                'status'    => true,
                'message'   => 'Your password has been updated successfully',
                'data'      => []
            ], 200);
        }
    }

    public function change_password(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password'          => ['required', 'string'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        if ($validation->fails()) {
            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ], 422);
        } else {

            if (!Hash::check($request->old_password, auth()->user()->password)) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Incorrect old password",
                    'data'      => []
                ], 422);
            }

            if (Hash::check($request->password, auth()->user()->password)) {
                return response()->json([
                    'status'    => false,
                    'message'   => "New password cannot be same as old password",
                    'data'      => []
                ], 422);
            } else {

                $retailer_data['password']  = Hash::make($request->password);
                Retailer::where('id', auth()->id())->update($retailer_data);

                return response()->json([
                    'status'    => true,
                    'message'   => 'Your password has been changed successfully',
                    'data'      => []
                ], 200);
            }
        }
    }

    public function dashboard()
    {
        $user = Auth::user();
        $services = ServicesLog::where('user_id', $user->id)
            ->where('status', 1)
            ->where('user_type', 4)
            ->get()
            ->pluck('service')
            ->map(function ($row) {
                return [
                    'id'    => $row->id,
                    'name'  => $row->name,
                    'image' => imageexist($row->image),
                ];
            });

        $sliders_all = BannerAdmin::where('status', 1)->whereRaw("find_in_set('4',banner_for)")->get();

        $sliders = $sliders_all->filter(fn ($row) => $row->is_special ==  0)
            ->map(function ($row) {
                return [
                    'image'         => imageexist($row->image),
                    'url'           => $row->url,
                ];
            })->values();

        $sliders_2 = $sliders_all->filter(fn ($row) => $row->is_special ==  1)
            ->map(function ($row) {
                return [
                    'image'         => imageexist($row->image),
                    'url'           => $row->url,
                ];
            })->values();

        $products = Product::with(['main_image'])->where('is_feature', 1)->where('status', 1)->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Data fetched successfully',
            'data'      => ['services' => $services, 'balance' => $user->user_balance, 'banners' => $sliders, 'banners_2' => $sliders_2, 'products' => $products]
        ], 200);
    }

    public function profile()
    {
        $retailer = Retailer::select('retailers.name', 'email', 'mobile', 'retailers.state_id', 'retailers.city_id', 'user_balance', 'image', 'states.name as state_name', 'cities.name as city_name')->where('retailers.id', auth()->id())
            ->leftJoin('states', 'states.id', '=', 'retailers.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'retailers.city_id')
            ->first()->toArray();

        $retailer['image'] = imageexist($retailer['image']);

        return response()->json([
            'status'    => true,
            'message'   => 'Data fetched successfully',
            'data'      => $retailer
        ], 200);
    }

    public function wallet(Request $request)
    {
        $pageNo = request('pageNo', 1);
        $limit  = request('limit', 10);
        $limit  = $limit <= 50 ? $limit : 50;

        $query  = Ledger::query();
        $query->select('voucher_no', 'date', 'amount', 'payment_type', 'particulars as discription');
        $query->where('user_id', auth()->id());
        $search = request('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('amount', 'like', '%' . $search . '%');
                $query->orWhere('voucher_no', 'like', '%' . $search . '%');
                $query->orWhere('particulars', 'like', '%' . $search . '%');
                $query->orWhere('date', 'like', '%' . $search . '%');
            });
        }

        $totalPage  = ceil($query->count() / $limit);
        // Ordering
        if (request('orderAs', 'desc') == 'desc') {
            $query->orderByDesc(request('orderBy', 'created_at'));
        } else {
            $query->orderBy(request('orderBy', 'created_at'));
        }

        // Set Offset
        $query->offset($limit * ($pageNo - 1));

        // Limiting
        $query->limit($limit);

        $wallet_data = $query->get()->toArray();

        return response()->json([
            'status'    => true,
            'message'   => 'Data fetched successfully',
            'balance'   => auth()->user()->user_balance,
            'data'      => $wallet_data,
            'totalPage' => $totalPage,
        ], 200);
    }

    public function update_profile(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', new CheckUnique('retailers', auth()->id())],
            'mobile'    => ['required', 'digits:10', new CheckUnique('retailers', auth()->id()), 'regex:' . config('constant.phoneRegExp')],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'state_id'  => ['nullable', 'integer'],
            'city_id'   => ['nullable', 'integer']
        ]);

        if ($validation->fails()) {

            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ], 422);
        } else {

            $retailer = Retailer::find(auth()->id());

            $retailer->name     = $request->name;
            $retailer->email    = $request->email;
            $retailer->mobile   = $request->mobile;
            $retailer->state_id = $request->state_id;
            $retailer->city_id  = $request->city_id;

            $path = 'admin';
            if ($file = $request->file('image')) {
                removeFile($retailer->image);
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $retailer->image        = $path . '/' . $uploadImage;
            }

            $retailer->save();

            return response()->json([
                'status'    => true,
                'message'   => 'Profile updated successfully',
                'data'      => []
            ], 200);
        }
    }

    public function request_money(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'amount'        => ['required', 'numeric'],
            'title'         => ['required', 'string', 'max:100'],
            'description'   => ['required', 'string', 'max:500'],
            'attachment'    => ['mimes:jpg,png,jpeg,pdf', 'max:2048'],
        ]);

        if ($validation->fails()) {
            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ], 422);
        } else {

            $retailer = Retailer::find(auth()->id());
            $data = [
                'request_number' => Str::uuid(),
                'user_id'       => auth()->id(),
                'user_type'     => 4,
                'amount'        => $request->amount,
                'title'         => filter_var($request->title, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                'description'   => filter_var($request->description, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                'status'        => 0,
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
                    'status'    => true,
                    'message'   => 'Payment Request Submitted Successfully',
                    'data'      => []
                ], 200);
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => "Oops..!! There is some error.",
                    "data"      => []
                ], 422);
            }
        }
    }

    public function money_requests(Request $request)
    {
        $query = PaymentRequest::query();
        $query->select(
            'request_number',
            'amount',
            'title',
            'reason',
            'description',
            'payment_requests.status',
            'payment_requests.created_at',
            'retailers.name as r_name',
        )->join('retailers', 'retailers.id', '=', 'payment_requests.user_id')
            ->where('user_id', auth()->id())
            ->where('user_type', 4);

        $requests_data = $query->get()->toArray();

        $request = [];

        if (!empty($requests_data)) {
            foreach ($requests_data  as $key => $value) {
                $request[] = [
                    'request_number' => $value['request_number'],
                    'amount' => $value['amount'],
                    'title' => $value['title'],
                    'reason' => $value['reason'],
                    'description' => $value['description'],
                    'status' => $value['status'],
                    'status_text' => $value['status'] == '0' ? 'Pending' : ($value['status'] == '1' ? 'Approved' : 'Rejected'),
                    'created_at' => date('d M Y', strtotime($value['created_at'])),
                ];
            }
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Data fetched successfully',
            'data'      => $request
        ], 200);
    }

    public function states()
    {
        $states = State::select('id', 'name')->where('status', 1)->get();
        if (count($states)) {
            return response()->json([
                'status'    => true,
                'message'   => 'Success',
                'data'      => $states
            ], 200);
        } else {
            return response()->json([
                'status'    => true,
                'message'   => 'No Data Found.',
                'data'      => []
            ], 404);
        }
    }

    public function cities(Request $request)
    {
        $query = City::query();
        if ($request->state_id) {
            $query->where('state_id', $request->state_id);
        }

        $query->select('id', 'name');
        $query->where('status', 1);
        $cities = $query->get();
        if (count($cities)) {
            return response()->json([
                'status'    => true,
                'message'   => 'Success',
                'data'      => $cities
            ], 200);
        } else {
            return response()->json([
                'status'    => true,
                'message'   => 'No Data Found.',
                'data'      => []
            ], 404);
        }
    }

    public function settings()
    {
        $setting = Setting::whereIn('setting_type', [1, 2, 7])->get()->pluck('filed_value', 'setting_name');
        $setting['base_path'] = asset('storage') . '/';
        $response = array(
            'status'    => true,
            'message'   => 'Successfully!',
            'data'      => array(
                'settings' => $setting
            )
        );
        return response()->json($response, 200);
    }

    public function cms($id)
    {
        $cms_data = Cms::select('title', 'description', 'image')
            ->where('status', 1)
            ->where('id', $id)
            ->first();

        if (!empty($cms_data)) {
            $cms = [
                'title'         => $cms_data->title,
                'description'   => $cms_data->description,
                'image'         => asset('storage/' . $cms_data->image)
            ];

            $response = array(
                'status'    => true,
                'message'   => 'Success',
                'data'      => $cms
            );
        } else {
            $response = array(
                'status'    => false,
                'message'   => 'Invalid request!',
                'data'      => array()
            );
        }
        return response()->json($response, 200);
    }
}
