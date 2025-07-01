<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\BannerAdmin;
use App\Models\Retailer;
use App\Models\ServicesLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetailersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retailer');
    }

    public function index()
    {
        $servicesLog = ServicesLog::select('services_logs.service_id', 'services.name as service_name', 'services_logs.sale_rate as sale_rate')
            ->leftJoin('services', 'services.id', '=', 'services_logs.service_id')
            ->where('user_id', auth()->guard('retailer')->id())
            ->where('services_logs.status', 1)
            ->where('user_type', 4)
            ->orderBy('services.id', 'asc')
            ->get();

        $banners = BannerAdmin::where('status', 1)->whereRaw("find_in_set('4',banner_for)")->get();
        $services = $servicesLog->pluck('service_id')->toArray();

        return view('retailer.home', compact('services', 'servicesLog', 'banners'));
    }

    public function commission(Request $request)
    {
        $servicesLog = ServicesLog::select('services_logs.service_id', 'services.name as service_name', 'services_logs.sale_rate as sale_rate', 'services_logs.retailer_commission as retailer_commission')
            ->leftJoin('services', 'services.id', '=', 'services_logs.service_id')
            ->where('user_id', auth()->guard('retailer')->id())
            ->where('services_logs.status', 1)
            ->where('user_type', 4)
            ->orderBy('services.id', 'asc')
            ->get();

        $user = $request->user();
        $providers = DB::table('rproviders')->get()->groupBy('sertype');
        return view('retailer.commission', compact('servicesLog', 'user', 'providers'));
    }


    public function default_board_save(Request $request)
    {
        $validated = $request->validate([
            'default_water_board'           => ['required', 'integer', 'min:1'],
            'default_gas_board'             => ['required', 'integer', 'min:1'],
            'default_lic_board'             => ['required', 'integer', 'min:1'],
            'default_electricity_board'     => ['required', 'integer', 'min:1'],
        ]);

        Retailer::where('id', auth('retailer')->id())->update($validated);
        return to_route('retailer.my-commission')->withSuccess('Default Board updated successfully..!!');
    }
}
