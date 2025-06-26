<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use Illuminate\Support\Carbon;
use App\Models\RegistrationOtp;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware(['guest', 'guest:distributor', 'guest:main_distributor', 'guest:retailer', 'guest:employee']);
    }

    public function showRegistrationForm()
    {
        abort(404);
        return view('front.auth.register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // $checkOtp = RegistrationOtp::firstWhere(['mobile' => $request->mobile, 'otp' => $request->otp]);
        // if (!$checkOtp) {
        //     return redirect()->back()->with('error', 'Incorrect OTP..!!')->withInput();
        // }

        // if ($checkOtp && Carbon::now()->isAfter($checkOtp->expire_at)) {
        //     return redirect()->back()->with('error', 'Your OTP has been expired')->withInput();
        // }

        event(new Registered($user = $this->create($request->all())));

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name'    => ['required', 'string', 'max:100'],
            'middle_name'   => ['nullable', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'email'         => ['required', 'string', 'email', 'max:100', 'unique:customers,email'],
            'mobile'        => ['required', 'numeric', 'min:10', 'unique:customers,mobile', 'regex:' . config('constant.phoneRegExp')],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            // 'otp'           => ['required', 'numeric', 'min:6'],
        ], [
            'mobile.regex'  => "Please enter valid indian mobile number."
        ]);
    }

    protected function create(array $data)
    {
        return Customer::create([
            'slug'          => Str::uuid(),
            'image'         => 'customer/avatar.png',
            'status'        => 1,
            'first_name'    => $data['first_name'],
            'middle_name'   => $data['middle_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'mobile'        => $data['mobile'],
            'password'      => Hash::make($data['password']),
        ]);
    }

    protected function registered(Request $request, $user)
    {
        // RegistrationOtp::where('mobile', $request->mobile)->delete();
        SendWelComeEmail::dispatch($user, $request->site_settings);

        return redirect()->route('loginPage', ['guard' => 'customer'])->with('success', 'Registration complete, Please login.');
    }
}
