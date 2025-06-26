<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Retailer;
use App\Models\Distributor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\MainDistributor;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');
        $token_data = DB::table('password_resets')->where([
            'token'         => $token,
        ])->first();

        $loginUrl = route('loginPage', $token_data->guard == 'web' ? 'admin' : $token_data->guard);

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email, 'loginUrl' => $loginUrl]
        );
    }

    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $token_data = DB::table('password_resets')->where([
            'email'         => $request->email,
            'token'         => $request->token,
        ])->first();

        if ($token_data == null) {
            return redirect()->back()->withErrors(['email' => trans('User does not exist')]);
        }

        switch ($token_data->guard) {
            case 'main_distributor':
                $user = MainDistributor::where('email', $request->email)->first();
                break;
            case 'distributor':
                $user = Distributor::where('email', $request->email)->first();
                break;
            case 'retailer':
                $user = Retailer::where('email', $request->email)->first();
                break;
            case 'employee':
                $user = Employee::where('email', $request->email)->first();
                break;
            case 'customer':
                $user = Customer::where('email', $request->email)->first();
                break;
            default:
                $user = User::where('email', $request->email)->first();
        }

        if ($user == null) {
            return redirect()->back()->withErrors(['email' => trans('User does not exist')]);
        }

        if ($this->resetPassword($user, $request->password)) {
            DB::table('password_resets')->where('email', $user->email)->delete();
            return redirect()
                ->to(route('loginPage', $token_data->guard == 'web' ? 'admin' : $token_data->guard))->with('success',  trans('Password Updated Successfully, Please login with new password.'))
                ->withInput(['email' => $request->email]);
        } else {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans('An Error Occurred Please Try Again Later.')]);
        }
    }

    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);
        $user->remember_token = Str::random(60);
        $user->save();

        event(new PasswordReset($user));

        return true;
    }
}
