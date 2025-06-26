<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('get_cities');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = City::select('cities.id', 'cities.name', 'cities.state_id', 'cities.status', 'states.name as state_name')
                ->leftJoin('states', 'states.id', '=', 'cities.state_id');
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(111, 'can_edit')) {
                        $btn .= '<button class="dropdown-item edit" data-all="' . htmlspecialchars(json_encode($row))  . '">Edit</button>';
                    }
                    if (userCan(111, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(111)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $states = State::where('status', 1)->get();
        return view('cities.index', compact('states'));
    }

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'max:100', 'unique:cities,name'],
            'state_id'  => ['required', 'integer'],
            'status'    => ['required', 'integer'],
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();
            return response()->json([
                'success'   => false,
                'message'   => $errors->first('name'),
                'data'      => ''
            ]);
        } else {
            City::create([
                'name'      =>  $request->name,
                'state_id'  =>  $request->state_id,
                'status'    =>  $request->status
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'City Added Successfully',
                'data'      => ''
            ]);
        }
    }

    public function update(Request $request)
    {
        $city = City::firstWhere('id', $request->id);
        if ($city == null) {
            return response()->json([
                'success'   => false,
                'message'   => 'City Not Found!!',
            ]);
        }

        $validation = Validator::make($request->all(), [
            'id'        => ['required'],
            'name'      => ['required', 'string', 'max:100', 'unique:cities,name,' . $city['id']],
            'state_id'  => ['required', 'integer'],
            'status'    => ['required', 'integer'],
        ]);

        if ($validation->fails()) {
            $err = array();
            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ]);
        } else {
            $city->update([
                'name'      =>  $request->name,
                'state_id'  =>  $request->state_id,
                'status'    =>  $request->status
            ]);
            return response()->json([
                'success'   => true,
                'message'   => 'City Added Successfully',
            ]);
        }
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $city = City::where('id', $request->id)->first();
            if ($city == null) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'City Not Found.',
                ]);
            }

            $city->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'City deleted Successfully',
            ]);
        }
    }

    public function get_cities(Request $request)
    {
        $state_id     = $request->state_id;
        $city_id     = $request->city_id;
        $cities = City::where('state_id', $state_id)->where('status', 1)->get();

        $html = '<option value="">Select City</option>';
        foreach ($cities as $city) {
            $html .= '<option value="' . $city['id'] . '" ' . ($city_id == $city['id'] ? 'selected' : '') . '>' . $city['name'] . '</option > ';
        }

        echo $html;
        return;
    }
}
