<?php

namespace App\Http\Controllers\Common;

use App\Models\Retailer;
use App\Models\Distributor;
use Illuminate\Http\Request;
use App\Models\MainDistributor;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Validator;

class CommonController extends Controller
{
    public function upload_image(Request $request)
    {
        $path = 'images';
        if ($file = $request->file('file')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $image        = $path . '/' . $uploadImage;

            return asset('storage/' . $image);
        }
    }

    public function get_user_list(Request $request)
    {
        $user_type = $request->user_type;
        switch ($user_type) {
            case 1:
                $user = Retailer::select('id', 'name')->where('status', 1)->get();
                break;
            case 2:
                $user = MainDistributor::select('id', 'name')->where('status', 1)->get();
                break;
            case 3:
                $user = Distributor::select('id', 'name')->where('status', 1)->get();
                break;
            default:
                $user = Retailer::select('id', 'name')->where('status', 1)->get();
                break;
        }

        return response()->json($user);
    }

    public function get_user_list_filter(Request $request)
    {
        $user_type = $request->user_type;
        switch ($user_type) {
            case 1:
                $query = Retailer::select("id, CONCAT(name,' (', mobile,')') as name");
                break;
            case 2:
                $query = MainDistributor::selectRaw("id, CONCAT(name,' (', mobile,')') as name");
                break;
            case 3:
                $query = Distributor::selectRaw("id, CONCAT(name,' (', mobile,')') as name");
                break;
            case 4:
                $query = Retailer::selectRaw("id, CONCAT(name,' (', mobile,')') as name");
                break;
            default:
                $query = Retailer::select("id, CONCAT(name,' (', mobile,')') as name");
                break;
        }

        $query->where('status', 1);
        $query->limit(50);
        if ($request->has('filter')) {
            $query->where('name', 'like', '%' . $request->get('filter') . '%');
            $query->orWhere('mobile', 'like', '%' . $request->get('filter') . '%');
        }

        $user =   $query->get();
        return response()->json($user);
    }

    public static function validationFails(Validator $validation)
    {
        $err = array();
        foreach ($validation->errors()->toArray() as $key => $value) {
            $err[$key] = $value[0];
        }

        return response()->json([
            'status'    => false,
            'message'   => "Invalid Input values.",
            "data"      => $err
        ]);
    }

    public function user_search(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('open.user_search');
        }

        if ($request->isMethod('post')) {
            if (RateLimiter::tooManyAttempts('user_search', 5)) {
                return redirect()->route('user-search')->with('error', 'Too Many Requests. Try Again leter.');
            }

            $a = DB::table('main_distributors')->selectRaw("name, mobile, 'Main Distributor' as type")->where('mobile', $request->mobile);
            $b = DB::table('distributors')->selectRaw("name, mobile, 'Distributor' as type")->where('mobile', $request->mobile);
            $c = DB::table('retailers')->selectRaw("name, mobile, 'Retailer' as type")->where('mobile', $request->mobile);
            $data = $a->union($b)->union($c)->get();

            RateLimiter::hit('user_search');
            if (count($data) == 0) {
                return redirect()->route('user-search')->with('error', 'No user Found with this number.');
            }

            return view('open.user_search', compact('data'));
        }
    }

    public function update_user_ids(Request $request)
    {
        echo "Records updated Successfully..!!";
    }
}
