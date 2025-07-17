<?php

namespace App\Http\Controllers\Retailer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BannerAdmin;
use App\Models\Provider;
use App\Models\ServicesLog;
use App\Models\Retailer;
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
        $servicesLog = ServicesLog::select('services_logs.service_id', 'services.name as service_name', 'services_logs.sale_rate as sale_rate', 'services_logs.retailer_commission as retailer_commission', 'services_logs.commission_slots as commission_slots')
            ->leftJoin('services', 'services.id', '=', 'services_logs.service_id')
            ->where('user_id', auth()->guard('retailer')->id())
            ->where('services_logs.status', 1)
            ->where('user_type', 4)
            ->orderBy('services.id', 'asc')
            ->get();

        return view('retailer.commission', compact('servicesLog'));
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
        return to_route('retailer.profile')->withSuccess('Default Board updated successfully..!!');
    }

    public function update_board(Request $request)
    {
        $provider = Provider::where('id', $request->board_id)->first();
        if (!$provider) {
            return response()->json(['status' => false, 'message' => 'Invalid provider selected.'], 400);
        }

        if (!$request->filled('type')) {
            return response()->json(['status' => false, 'message' => 'Invalid Type.'], 400);
        }

        if (!in_array($request->filled('type'), ['water', 'lic', 'gas', 'electricity'])) {
            return response()->json(['status' => false, 'message' => 'Invalid Type.'], 400);
        }

        if ($request->filled('type') != $provider->type) {
            return response()->json(['status' => false, 'message' => 'Invalid Type.'], 400);
        }

        if ($request->get('type')  === 'water') {
            $request->user()->update(['default_water_board'         => $provider->id]);
        } else if ($request->get('type')  === 'lic') {
            $request->user()->update(['default_lic_board'           => $provider->id]);
        } else if ($request->get('type')  === 'gas') {
            $request->user()->update(['default_gas_board'           => $provider->id]);
        } else if ($request->get('type')  === 'electricity') {
            $request->user()->update(['default_electricity_board'   => $provider->id]);
        }

        return response()->json(['status' => true, 'message' => 'Board updated successfully..!!'], 200);
    }
}
