<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $maxAttempts = 5;
    protected $decayMinutes = 1;

    use ThrottlesLogins, AuthenticatesUsers {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['guest', 'guest:distributor',  'guest:main_distributor', 'guest:retailer', 'guest:employee'])->except('logout');
    }

    public function credentials(Request $request)
    {
        $credentials = $request->only($this->email(), 'password');
        $credentials = array_merge($credentials, ['status' => '1']);
        return $credentials;
    }

    public function showLoginForm($guard = 'web')
    {
        return view('auth.login', ['guard' => $guard]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required',
            'login_as'  => 'required',
            'password'  => 'required|min:6'
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // For Login with Mobile and Email Both
        $redirectTo = null;
        if (is_numeric($request->email)) {
            $userName = 'mobile';
            $request->merge(['mobile' => $request->email]);
        } elseif (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $userName = 'email';
        } else {
            $userName = 'userId';
            $request->merge(['userId' => $request->email]);
        }

        switch ($request->login_as) {
            case 'distributor':
                if (Auth::guard('distributor')->attempt($request->only([$userName, 'password']), $request->get('remember'))) {
                    $redirectTo = 'distributor/dashboard';
                }
                break;
            case 'main_distributor':
                if (Auth::guard('main_distributor')->attempt($request->only([$userName, 'password']), $request->get('remember'))) {
                    $redirectTo = 'main_distributor/dashboard';
                }
                break;
            case 'retailer':
                if (Auth::guard('retailer')->attempt($request->only([$userName, 'password']), $request->get('remember'))) {
                    $redirectTo = 'retailer/dashboard';
                }
                break;
            case 'employee':
                if (Auth::guard('employee')->attempt($request->only([$userName, 'password']), $request->get('remember'))) {
                    $redirectTo = 'employee/dashboard';
                }
                break;
            default:
                if (Auth::guard('web')->attempt($request->only([$userName, 'password']), $request->get('remember'))) {
                    $redirectTo = '/dashboard';
                }
        }

        if ($redirectTo != null) {
            if (Auth::guard($request->login_as)->user()->status == 0) {
                Auth::guard($request->login_as)->logout();
                throw ValidationException::withMessages([
                    $this->username() => "Your Account is blocked by Admin.",
                ]);
            }

            $this->clearLoginAttempts($request);
            return redirect()->intended($redirectTo);
        }

        $this->incrementLoginAttempts($request);
        return back()->with('error', "Invalid Login Credential..!!")->withInput($request->only('email', 'login_as', 'remember'));
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);
        Auth::guard('distributor')->logout();
        Auth::guard('main_distributor')->logout();
        Auth::guard('retailer')->logout();
        Auth::guard('web')->logout();
        Auth::logout();
        Session::forget('locked');
        return redirect()->to(config('ayt.front_url', route('login')));
    }
}
