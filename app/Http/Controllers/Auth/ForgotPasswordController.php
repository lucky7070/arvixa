<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Employee;
use App\Models\Retailer;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\MainDistributor;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('mail');
    }

    public function showLinkRequestForm($guard = 'web')
    {
        return view('auth.passwords.email', ['guard' => $guard]);
    }

    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'login_as'  => 'required',
        ]);
    }

    protected function credentials(Request $request)
    {
        return $request->only(['email']);
    }

    public function sendResetLinkEmail(Request $request)
    {
        //validation request here
        $this->validateEmail($request);

        //Check if the user exists
        $user  = null;
        switch ($request->login_as) {
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
            default:
                $user = User::where('email', $request->email)->first();
        }

        if ($user == null) {
            return back()->withErrors(['email' => trans('User does not exist')]);
        }

        //Create Password Reset Token
        $token = generateRandomString(60);
        DB::table('password_resets')->insert([
            'email'         => $request->email,
            'guard'         => $request->login_as,
            'token'         => $token,
            'created_at'    => Carbon::now()
        ]);

        //Get the token just created above
        if ($this->sendResetEmail($user, $token)) {
            return back()->with('success', trans('A reset link has been sent to your email address.'));
        } else {
            return back()->with('error', trans('A Network Error occurred. Please try again.'));
        }
    }

    private function sendResetEmail($user, $token)
    {
        try {
            Mail::send('email.reset-password', [
                'name'      => $user->name,
                'reset_url'     => route('password.reset', ['token' => $token, 'email' => $user->email]),
            ], function ($message) use ($user) {
                $message->subject('Reset Password Request');
                $message->to($user->email);
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
