<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\State;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;

class StateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = State::select('id', 'name', 'status');
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(110, 'can_edit')) {
                        $btn .= '<button class="dropdown-item edit" data-all="' . htmlspecialchars(json_encode($row))  . '">Edit</button>';
                    }
                    if (userCan(110, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(110)) {
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
        return view('states.index');
    }

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'max:100', 'unique:states,name'],
            'status'    => ['required', 'integer'],
        ]);

        if ($validation->fails()) {
            $err = array();
            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            if ($validation->fails()) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Invalid Input values.",
                    "data"      => $err
                ]);
            }
        } else {
            $new_state = State::create([
                'name'      =>  $request->name,
                'status'    =>  $request->status
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'State Added Successfully',
                'data'      => ''
            ]);
        }
    }

    public function update(Request $request)
    {
        $state = State::firstWhere('id', $request->id);
        if ($state == null) {
            return response()->json([
                'success'   => false,
                'message'   => 'State Not Found!!',
            ]);
        }

        $validation = Validator::make($request->all(), [
            'id'        => ['required'],
            'name'      => ['required', 'string', 'max:100', 'unique:states,name,' . $state['id']],
            'status'    => ['required', 'integer'],
        ]);

        if ($validation->fails()) {
            $err = array();
            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            if ($validation->fails()) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Invalid Input values.",
                    "data"      => $err
                ]);
            }
        } else {
            $state->update(['name'      =>  $request->name, 'status' =>  $request->status]);
            return response()->json([
                'success'   => true,
                'message'   => 'State Added Successfully',
            ]);
        }
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $state = State::where('id', $request->id)->first();
            if ($state == null) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'State Not Found.',
                ]);
            }

            $state->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'State deleted Successfully',
            ]);
        }
    }
}
