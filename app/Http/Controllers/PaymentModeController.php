<?php

namespace App\Http\Controllers;

use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;

class PaymentModeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = PaymentMode::select('id', 'type', 'name', 'beneficiary_name', 'account_number', 'ifsc_code', 'note', 'logo', 'upi', 'status', 'created_at');
            return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(124, 'can_edit')) {
                        $btn .= '<button class="dropdown-item edit" data-all="' . htmlspecialchars(json_encode($row))  . '">Edit</button>';
                    }

                    if (userCan(124, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(124)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->editColumn('type-label', function ($row) {
                    switch ($row['type']) {
                        case 1:
                            return 'Bank Details';
                        case 2:
                            return 'UPI Handel';
                        default:
                            return '--';
                    };
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y h:i A') : '';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('payment-modes.index');
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => ['required', 'string', 'min:2', 'max:100'],
            'logo'              => ['required', 'image', 'mimes:jpg,png,jpeg,pdf', 'max:2048'],
            'status'            => ['required', 'integer', 'in:0,1'],
            'type'              => ['required', 'integer', 'in:1,2'], // 1 for Bank, 2 for UPI
            'note'              => ['nullable', 'string', 'max:100'],

            'beneficiary_name'  => ['required_if:type,1', 'nullable', 'string', 'min:2', 'max:100'],
            'account_number'    => ['required_if:type,1', 'nullable', 'string', 'min:2', 'max:20', 'regex:/^[0-9]+$/'],
            'ifsc_code'         => ['required_if:type,1', 'nullable', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'upi'               => ['required_if:type,2', 'nullable', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+$/'],
        ], [
            'type.in'       => 'Invalid payment type selected',
            'logo.required' => 'Please upload a logo',
        ]);

        if ($validator->fails()) {
            $err = array();
            foreach ($validator->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Invalid Input values.",
                    "data"      => $err
                ]);
            }
        } else {

            $path = 'pay-modes';
            $data = $validator->validated();
            if ($file = $request->file('logo')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $data['logo']        = $path . '/' . $uploadImage;
            }

            PaymentMode::create($data);
            return response()->json([
                'success'   => true,
                'message'   => 'PaymentMode Added Successfully',
                'data'      => ''
            ]);
        }
    }

    public function update(Request $request)
    {
        $role = PaymentMode::firstWhere('id', $request->id);
        if ($role == null) {
            return response()->json([
                'success'   => false,
                'message'   => 'PaymentMode Not Found!!',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name'              => ['required', 'string', 'min:2', 'max:100'],
            'logo'              => ['nullable', 'image', 'mimes:jpg,png,jpeg,pdf', 'max:2048'],
            'status'            => ['required', 'integer', 'in:0,1'],
            'type'              => ['required', 'integer', 'in:1,2'], // 1 for Bank, 2 for UPI
            'note'              => ['nullable', 'string', 'max:100'],

            'beneficiary_name'  => ['required_if:type,1', 'nullable', 'string', 'min:2', 'max:100'],
            'account_number'    => ['required_if:type,1', 'nullable', 'string', 'min:2', 'max:20', 'regex:/^[0-9]+$/'],
            'ifsc_code'         => ['required_if:type,1', 'nullable', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'upi'               => ['required_if:type,2', 'nullable', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+$/'],
        ], [
            'type.in'       => 'Invalid payment type selected',
            'logo.required' => 'Please upload a logo',
        ]);


        if ($validator->fails()) {
            $err = array();
            foreach ($validator->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Invalid Input values.",
                    "data"      => $err
                ]);
            }
        } else {

            $path = 'pay-modes';
            $data = $validator->validated();
            if ($file = $request->file('logo')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $data['logo']        = $path . '/' . $uploadImage;
            }

            $role->update($data);
            return response()->json([
                'success'   => true,
                'message'   => 'PaymentMode Added Successfully',
            ]);
        }
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $role = PaymentMode::where('id', $request->id)->first();
            if ($role == null) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'PaymentMode Not Found.',
                ]);
            }

            $role->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'PaymentMode deleted Successfully',
            ]);
        }
    }
}
