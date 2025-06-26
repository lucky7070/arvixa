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
            
        $bills = ElectricityBill::with(['retailer', 'board'])
        ->where('bill_type', 'electricity')
        ->latest() // Order by created_at descending
        ->take(5)  // Limit to 5 records
        ->get();
        
        $waterbills = ElectricityBill::with(['retailer', 'board'])
        ->where('bill_type', 'water')
        ->latest() // Order by created_at descending
        ->take(5)  // Limit to 5 records
        ->get();
        
         $gasbills = ElectricityBill::with(['retailer', 'board'])
        ->where('bill_type', 'gas')
        ->latest() // Order by created_at descending
        ->take(5)  // Limit to 5 records
        ->get();
        
         $licbills = ElectricityBill::with(['retailer', 'board'])
        ->where('bill_type', 'lic')
        ->latest() // Order by created_at descending
        ->take(5)  // Limit to 5 records
        ->get();

        $banners = BannerAdmin::where('status', 1)->whereRaw("find_in_set('4',banner_for)")->get();
        $services = $servicesLog->pluck('service_id')->toArray();

        return view('retailer.home', compact('services', 'servicesLog', 'banners', 'waterbills','gasbills','licbills','bills'));
    }
}
