<?php

namespace App\Http\Controllers\Retailer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BannerAdmin;
use App\Models\Services;
use App\Models\ServicesLog;
use App\Models\ElectricityBill;

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

    public function commission()
    {
        $servicesLog = ServicesLog::select('services_logs.service_id', 'services.name as service_name', 'services_logs.sale_rate as sale_rate', 'services_logs.retailer_commission as retailer_commission')
            ->leftJoin('services', 'services.id', '=', 'services_logs.service_id')
            ->where('user_id', auth()->guard('retailer')->id())
            ->where('services_logs.status', 1)
            ->where('user_type', 4)
            ->orderBy('services.id', 'asc')
            ->get();

        return view('retailer.commission', compact('servicesLog'));
    }
}
