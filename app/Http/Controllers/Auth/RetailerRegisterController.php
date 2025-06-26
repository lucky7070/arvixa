<?php

namespace App\Http\Controllers\Auth;

use App\Models\Retailer;
use App\Rules\CheckUnique;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RetailerRegisterController extends Controller
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
        $this->middleware(['guest', 'guest:distributor', 'guest:customer', 'guest:main_distributor', 'guest:retailer', 'guest:employee']);
    }

    public function showRegistrationForm()
    {
        return view('front.auth.retailer-register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

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
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', new CheckUnique('retailers')],
            'mobile'        => ['required', 'digits:10', new CheckUnique('retailers'), 'regex:' . config('constant.phoneRegExp')],
            'password'      => ['required', 'string', 'min:8', 'confirmed']
        ], [
            'mobile.regex'  => "Please enter valid indian mobile number."
        ]);
    }

    protected function create(array $data)
    {
        return Retailer::create([
            'slug'                  => Str::uuid(),
            'registor_from'         => 2,
            'main_distributor_id'   => null,
            'distributor_id'        => null,
            'name'                  => $data['name'],
            'email'                 => $data['email'],
            'mobile'                => $data['mobile'],
            'status'                => 1,
            'image'                 => 'admin/avatar.png',
            'password'              => Hash::make($data['password']),
        ]);
    }

    protected function registered(Request $request, $user)
    {
        SendWelComeEmail::dispatch($user, $request->site_settings);
        return redirect()->route('loginPage', ['guard' => 'retailer'])->with('success', 'Registration complete, Please login.');
    }
}
