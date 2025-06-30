<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\RazorPayController;
use App\Traits\Mobile\ITRServices;
use App\Traits\Mobile\MSMEServices;
use App\Traits\Mobile\PanCardServices;
use App\Traits\Mobile\StoreServices;

class AccountController extends Controller
{
    use StoreServices, ITRServices, MSMEServices, PanCardServices;

    protected $user         = null;
    protected $user_id      = null;
    protected $user_type    = null;

    public function __construct()
    {
        $this->middleware(['auth:api', function ($request, $next) {
            $this->user_id   = auth()->id();
            $this->user      = auth('api')->user();
            return $next($request);
        }]);

        $this->user_type    = 4;
    }

    public function razorpay(Request $request)
    {
        $validation = Validator::make(request()->all(), [
            'amount'     => ['required'],
        ]);

        if ($validation->fails()) {
            return CommonController::validationFails($validation);
        }

        return RazorPayController::razorpay($request);
    }

    public function verify(Request $request)
    {
        $validation = Validator::make(request()->all(), [
            'razorpay_order_id'     => ['required'],
            'razorpay_payment_id'   => ['required'],
            'razorpay_signature'    => ['required'],
        ]);

        if ($validation->fails()) {
            return CommonController::validationFails($validation);
        }

        $request->merge([
            "user_id"   => $this->user_id,
            "user_type" => $this->user_type
        ]);

        return RazorPayController::verify($request);
    }
    

}
