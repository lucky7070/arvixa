<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Retailer;
use App\Models\BannerAdmin;
use App\Models\Distributor;
use Illuminate\Http\Request;
use App\Models\PaymentRequest;
use Illuminate\Support\Carbon;
use App\Models\MainDistributor;
use App\Library\PanCard;
use Illuminate\Support\Facades\Session;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $banners = BannerAdmin::where('status', 1)->whereRaw("find_in_set('1',banner_for)")->get();

        $main_distributor = MainDistributor::selectRaw('COUNT(DISTINCT id) as count,  SUM(user_balance) as user_balance')->first();
        $distributor = Distributor::selectRaw('COUNT(DISTINCT id) as count,  SUM(user_balance) as user_balance')->first();
        $retailer = Retailer::selectRaw('COUNT(DISTINCT id) as count,  SUM(user_balance) as user_balance')->first();

        $main_distributor_today = MainDistributor::whereDate('created_at', Carbon::today())->count();
        $distributor_today = Distributor::whereDate('created_at', Carbon::today())->count();
        $retailer_today = Retailer::whereDate('created_at', Carbon::today())->count();

        $payment = PaymentRequest::where('status', 2)->whereDate('created_at', Carbon::today())->get();
        $payment_sum['main_distributor']    = $payment->where('user_type', 2)->sum('amount');
        $payment_sum['distributor']         = $payment->where('user_type', 3)->sum('amount');
        $payment_sum['retailer']            = $payment->where('user_type', 4)->sum('amount');

        try {
            $profile = PanCard::getProfile();
        } catch (\Throwable $th) {
            Session::flash('error', $th->getMessage());
            $profile = false;
        }

        return view('home', compact('banners', 'profile', 'main_distributor', 'distributor', 'retailer', 'main_distributor_today', 'distributor_today', 'retailer_today', 'payment_sum'));
    }

    public function user_chart(Request $request)
    {
        if ($request->method() == 'GET') {

            $admin = User::select('id', 'name', 'image')->firstWhere('id', 1);
            $main_distributors = MainDistributor::select('id', 'name', 'image')->where('status', 1)->get();
            return view('user_chart.index', compact('admin', 'main_distributors'));
        }

        if ($request->method() == 'POST') {
            $type = $request->type;
            $id = $request->id;
            $data = null;
            if ($type == 'main_distributor' && $id) {
                $data = Distributor::where(['main_distributor_id' =>  $id, 'status'    => 1])->get();
            }

            if ($type == 'distributor' && $id) {
                $data = Retailer::where(['distributor_id' =>  $id, 'status'    => 1])->get();
            }

            $html = "";
            if (!empty($data) && count($data) > 0) {
                $html .= '<ul class="active">';
                foreach ($data as $key => $value) {
                    if ($type == 'main_distributor') {
                        $role = "distributor";
                    } elseif ($type == 'distributor') {
                        $role = "retailer";
                    } else {
                        $role = "";
                    }

                    $html .= '
                        <li data-id="' . $value['id'] . '" data-type="' . $role . '" data-details="' . htmlspecialchars(json_encode($value)) . '">
                            <a href="javascript:void(0);">
                                <div class="member-view-box">
                                    <div class="member-image">
                                        <img  class="border border-4" src="' . asset('storage/' . $value['image']) . '" alt="' . ucfirst($role) . '">
                                    </div>
                                    <div class="member-details">
                                        <h3 class="text-white">' . $value['name'] . '</h3>
                                        <p class="text-white">(' . ucfirst($role) . ')</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    ';
                }

                $html .= "</ul>";

                return response()->json([
                    'success'   => true,
                    'message'   => 'Success',
                    'html'      => $html
                ]);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Not Found.',
                    'html'      => ""
                ]);
            }
        }
    }
}
