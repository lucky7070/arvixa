<?php

namespace App\Http\Controllers\Distributor;

use App\Models\BannerAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DistributorsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:distributor');
    }

    public function index()
    {
        $banners = BannerAdmin::where('status', 1)->whereRaw("find_in_set('3',banner_for)")->get();
        return view('distributor.home', compact('banners'));
    }
}
