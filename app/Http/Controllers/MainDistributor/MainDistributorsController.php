<?php

namespace App\Http\Controllers\MainDistributor;

use App\Models\BannerAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainDistributorsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:main_distributor');
    }

    public function index()
    {
        $banners = BannerAdmin::where('status', 1)->whereRaw("find_in_set('2',banner_for)")->get();
        return view('main_distributor.home', compact('banners'));
    }
}
