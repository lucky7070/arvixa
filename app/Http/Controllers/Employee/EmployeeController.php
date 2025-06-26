<?php

namespace App\Http\Controllers\Employee;

use App\Models\Retailer;
use App\Models\BannerAdmin;
use App\Models\Distributor;
use App\Models\MainDistributor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:employee');
    }

    public function index()
    {
        $banners                = BannerAdmin::where('status', 1)->whereRaw("find_in_set('5',banner_for)")->get();
        $retailers_all          = Retailer::where('employee_id', auth()->guard('employee')->id())->get();
        $main_distributor_all   = MainDistributor::where('employee_id', auth()->guard('employee')->id())->get();
        $distributor_all        = Distributor::where('employee_id', auth()->guard('employee')->id())->get();

        $retailers = [
            'today' => $retailers_all->filter(fn ($row) => $row->created_at->isSameDay(Carbon::now()))->count(),
            'total' => $retailers_all->count(),
        ];

        $main_distributor = [
            'today' => $main_distributor_all->filter(fn ($row) => $row->created_at->isSameDay(Carbon::now()))->count(),
            'total' => $main_distributor_all->count(),
        ];

        $distributor = [
            'today' => $distributor_all->filter(fn ($row) => $row->created_at->isSameDay(Carbon::now()))->count(),
            'total' => $distributor_all->count(),
        ];

        return view('employee.home', compact('banners', 'retailers', 'main_distributor', 'distributor'));
    }

    public function toggle_online()
    {
        $user = Auth::guard('employee')->user();
        $user->is_active = 1 - $user->is_active;
        $user->save();

        return response()->json([
            'status'   => true,
            'message'   => 'Online Status Updated Successfully',
            'data'      => ''
        ]);
    }
}
