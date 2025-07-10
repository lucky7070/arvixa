<?php

namespace App\Http\Controllers\MainDistributor;

use App\Models\Ledger;
use App\Models\Services;
use App\Rules\CheckUnique;
use App\Models\Distributor;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use \Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\LedgerController;
use App\Http\Controllers\Common\ServicesLogController;

class DistributorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:main_distributor');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Distributor::select('id', 'name', 'userId', 'slug', 'mobile', 'image', 'user_balance', 'main_distributor_id', 'status', 'created_at')
                ->where('main_distributor_id', Auth::guard('main_distributor')->id());
            return Datatables::of($data)->addIndexColumn()
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('name', function ($row) {
                    return '<b class="text-dark">' . $row['name'] . '</b><br /> <span class="text-secondary">( Balance : ₹ ' . $row['user_balance'] . ')<span>';
                })
                ->editColumn('userId', function ($row) {
                    return '<b class="text-danger">' . $row['userId'] . '</b><br /> <b class="text-dark">' . $row['mobile'] . '<span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {
                    $btn =
                        '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="fas fa-ellipsis-h fs--1"></span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="drop">
                        <a class="dropdown-item" href="' . route('main_distributor.distributors.edit', $row['slug']) . '">Edit</a>
                        <a class="dropdown-item" href="' . route('main_distributor.distributors.services', $row['slug']) . '">Services</a>
                        <a class="dropdown-item" href="' . route('main_distributor.distributors.ledger', $row['slug']) . '">Ledger</a>
                        
                        <button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>
                    </div>';
                    return $btn;
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'name', 'userId', 'status'])
                ->make(true);
        }
        return view('main_distributor.distributors.index');
    }

    public function add()
    {
        return view('main_distributor.distributors.add');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'status'                => ['required', 'integer'],
            'email'                 => ['required', new CheckUnique('distributors')],
            'mobile'                => ['required', 'digits:10', new CheckUnique('distributors'), 'regex:' . config('constant.phoneRegExp')],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'image'                 => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'date_of_birth'         => ['required', 'date'],
            'gender'                => ['required', 'string', 'in:male,female,other'],
            'address'               => ['required', 'string', 'max:500'],
            'shop_name'             => ['required', 'string', 'max:255'],
            'shop_address'          => ['required', 'string', 'max:500'],
            'aadhar_no'             => ['required', 'string', 'digits:12'],
            'pan_no'                => ['required', 'string', 'regex:/[A-Z]{5}[0-9]{4}[A-Z]{1}/'],
            'aadhar_doc'            => ['required', 'mimes:jpg,png,jpeg,pdf', 'max:2048'],
            'pan_doc'               => ['required', 'mimes:jpg,png,jpeg,pdf', 'max:2048'],
            'bank_proof_doc'        => ['required', 'mimes:jpg,png,jpeg,pdf', 'max:2048'],
            'bank_name'             => ['required', 'string', 'max:255'],
            'bank_account_number'   => ['required', 'string', 'max:20'],
            'bank_ifsc_code'        => ['required', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        ]);

        $data = [
            'slug'                  => Str::uuid(),
            'main_distributor_id'   => Auth::guard('main_distributor')->id(),
            'name'                  => $request->name,
            'email'                 => $request->email,
            'mobile'                => $request->mobile,
            'status'                => $request->status,
            'image'                 => 'admin/avatar.png',
            'password'              => Hash::make($request['password']),
            'date_of_birth'         => $request->date_of_birth,
            'gender'                => $request->gender,
            'address'               => $request->address,
            'shop_name'             => $request->shop_name,
            'shop_address'          => $request->shop_address,
            'aadhar_no'             => $request->aadhar_no,
            'pan_no'                => $request->pan_no,
            'bank_name'             => $request->bank_name,
            'bank_account_number'   => $request->bank_account_number,
            'bank_ifsc_code'        => $request->bank_ifsc_code,
        ];

        $path = 'admin';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        // Handle document uploads
        $documentFields = ['aadhar_doc', 'pan_doc', 'bank_proof_doc'];
        foreach ($documentFields as $field) {
            if ($file = $request->file($field)) {
                $destinationPath = 'public\\admin\\documents';
                $uploadDoc = time() . '_' . $field . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadDoc, file_get_contents($file));
                $data[$field] = 'admin/documents/' . $uploadDoc;
            }
        }

        $data = Distributor::create($data);
        SendWelComeEmail::dispatch($data, $request->site_settings);
        return to_route('main_distributor.distributors')->with('success', 'Distributor Added Successfully!!');
    }

    public function edit($id)
    {
        $distributor = Distributor::where('slug', $id)
            ->where('main_distributor_id', Auth::guard('main_distributor')->id())
            ->first();
        if ($distributor == null) {
            return to_route('main_distributor.distributors')->with('error', 'Distributor Not Found!!');
        }

        return view('main_distributor.distributors.edit', compact('distributor'));
    }

    public function update(Request $request, $id)
    {
        $distributor = Distributor::where('id', $id)
            ->where('main_distributor_id', Auth::guard('main_distributor')->id())
            ->first();

        if ($distributor == null) {
            return to_route('main_distributor.distributors')->with('error', 'Distributor Not Found!!');
        }

        $validated = [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', new CheckUnique('distributors', $distributor->id)],
            'mobile'                => ['required', 'digits:10', new CheckUnique('distributors', $distributor->id), 'regex:' . config('constant.phoneRegExp')],
            'image'                 => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'status'                => ['required', 'integer'],
            'date_of_birth'         => ['required', 'date'],
            'gender'                => ['required', 'string', 'in:male,female,other'],
            'address'               => ['required', 'string', 'max:500'],
            'shop_name'             => ['required', 'string', 'max:255'],
            'shop_address'          => ['required', 'string', 'max:500'],
            'aadhar_no'             => ['required', 'string', 'digits:12'],
            'pan_no'                => ['required', 'string', 'regex:/[A-Z]{5}[0-9]{4}[A-Z]{1}/'],
            'aadhar_doc'            => ['nullable', 'mimes:jpg,png,jpeg,pdf', 'max:2048'],
            'pan_doc'               => ['nullable', 'mimes:jpg,png,jpeg,pdf', 'max:2048'],
            'bank_proof_doc'        => ['nullable', 'mimes:jpg,png,jpeg,pdf', 'max:2048'],
            'bank_name'             => ['required', 'string', 'max:255'],
            'bank_account_number'   => ['required', 'string', 'max:20'],
            'bank_ifsc_code'        => ['required', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        ];

        if ($request['password']) {
            $validated['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $request->validate($validated);
        $data = [
            'name'                  => $request->name,
            'email'                 => $request->email,
            'mobile'                => $request->mobile,
            'status'                => $request->status,
            'date_of_birth'         => $request->date_of_birth,
            'gender'                => $request->gender,
            'address'               => $request->address,
            'shop_name'             => $request->shop_name,
            'shop_address'          => $request->shop_address,
            'aadhar_no'             => $request->aadhar_no,
            'pan_no'                => $request->pan_no,
            'bank_name'             => $request->bank_name,
            'bank_account_number'   => $request->bank_account_number,
            'bank_ifsc_code'        => $request->bank_ifsc_code,
        ];

        if ($request['password']) {
            $data['password'] = Hash::make($request['password']);
        }

        $path = 'admin';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        // Handle document uploads
        $documentFields = ['aadhar_doc', 'pan_doc', 'bank_proof_doc'];
        foreach ($documentFields as $field) {
            if ($file = $request->file($field)) {
                $destinationPath = 'public\\admin\\documents';
                $uploadDoc = time() . '_' . $field . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadDoc, file_get_contents($file));
                $data[$field] = 'admin/documents/' . $uploadDoc;

                // Delete old document if exists
                if ($distributor->$field && Storage::disk('local')->exists('public/' . $distributor->$field)) {
                    Storage::disk('local')->delete('public/' . $distributor->$field);
                }
            }
        }

        $distributor->update($data);
        return to_route('main_distributor.distributors')->with('success', 'Distributor Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $distributor = Distributor::where('id', $request->id)
                ->where('main_distributor_id', Auth::guard('main_distributor')->id())
                ->first();
            if ($distributor == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Distributor Not Found.',
                ]);
            }

            $distributor->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Distributor deleted Successfully',
            ]);
        }
    }

    public function services($slug)
    {
        $distributor = Distributor::firstWhere('slug', $slug);
        if ($distributor == null) {
            return to_route('distributors')->with('error', 'Distributor Not Found!!');
        }

        $serviceIds = ServicesLog::select('service_id')
            ->where('user_id', Auth::guard('main_distributor')->id())
            ->where('user_type', 2)
            ->where('status', 1)
            ->get()
            ->pluck('service_id')
            ->toArray();

        $query = Services::query();
        $query->select('services.id', 'services.name', 'assign_date');
        $query->leftJoin('services_logs', function ($join) use ($distributor) {
            $join->on('services.id', '=', 'services_logs.service_id')
                ->where('services_logs.status', 1)
                ->where('services_logs.user_type', 3)
                ->where('services_logs.user_id', $distributor['id']);
        });

        $query->whereIn('services.id', $serviceIds);
        $query->where('services.status', 1);
        $query->orderBy('services.id', 'asc');
        $services = $query->get();

        return view('main_distributor.distributors.services', compact(['distributor', 'services']));
    }

    public function services_update(Request $request, $slug)
    {
        $distributor = Distributor::firstWhere('slug', $slug);
        if (!$distributor) {
            return response()->json([
                'status'   => false,
                'message'   => 'Distributor Not Found.',
            ]);
        }

        $serviceIds = ServicesLog::select('service_id')
            ->where('user_id', Auth::guard('main_distributor')->id())
            ->where('user_type', 2)
            ->where('status', 1)
            ->get()
            ->pluck('service_id')
            ->toArray();

        $query = Services::query();
        $query->select('id');
        $query->where('status', 1);
        $query->whereIn('id', $serviceIds);
        $services = $query->get()->pluck('id')->toArray();

        if (in_array($request->service_id, $services)) {
            if (in_array($request->service_id, ['on', 'off'])) {
                foreach ($services as $key => $value) {
                    ServicesLogController::update($value,  $distributor['id'],  3, $request->service_id);
                }
            } else {
                ServicesLogController::update($request->service_id,  $distributor['id'],  3);
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Service Updated Successfully',
                'data'      => []
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => "This Service Cannot assign to Selected User.",
                'data'      => []
            ]);
        }
    }

    public function ledger(Request $request, $slug)
    {
        $user = Distributor::where('slug', $slug)->where('main_distributor_id', Auth::guard('main_distributor')->id())->first();
        if ($user == null) {
            return to_route('main_distributor.distributor')->with('error', 'Distributor Not Found!!');
        }

        if ($request->ajax()) {
            $data = Ledger::select('id', 'voucher_no', 'particulars', 'amount', 'current_balance',  'updated_balance', 'payment_type', 'payment_method', 'created_at')
                ->where('user_id', $user['id'])
                ->where('user_type', 3);

            return LedgerController::getDataTable($data);
        }

        return view('main_distributor.distributors.ledger', compact('user'));
    }

    public function ledger_add(Request $request, $slug)
    {
        $user = Distributor::where('slug', $slug)->where('main_distributor_id', Auth::guard('main_distributor')->id())->first();
        if (!$user) {
            return response()->json([
                'status'   => false,
                'message'   => 'Distributor Not Found.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'amount'        => 'required|numeric|min:1|max:1000000',
            'payment_type'  => 'required|integer|in:1,2',
            'particulars'   => 'required|string|min:2|max:150',
        ]);

        if ($validator->fails()) {
            return CommonController::validationFails($validator);
        }

        if ($request->payment_type == 2 && $request->amount > $user['user_balance']) {
            return response()->json([
                'status'    => false,
                'message'   => "Debit amount is greater than balance, you can max debit amount is " . $user['user_balance'] . ".",
                "data"      => []
            ]);
        }

        if ($request->payment_type == 1 && Auth::guard('main_distributor')->user()->user_balance < $request->amount) {
            return response()->json([
                'status'    => false,
                'message'   => "Your Balance is less than credit amount. Your balance is " . Auth::guard('main_distributor')->user()->user_balance . ".",
                "data"      => []
            ]);
        }

        $credit = LedgerController::add($user['id'], 3, [
            'amount'        => $request->amount,
            'payment_type'  => $request->payment_type,
            'payment_method' => 1,
            'particulars'   => getTransactionDetails('Main Distributor', $request->all()),
        ]);

        $debit =  LedgerController::add(Auth::guard('main_distributor')->id(), 2, [
            'amount'        => $request->amount,
            'payment_type'  => $request->payment_type == 1 ? 2 : 1,
            'payment_method' => 1,
            'particulars'   => getTransactionDetails('Distributor', $request->all(), 2),
        ]);

        if ($credit && $debit) {
            return response()->json([
                'status'   => true,
                'message'   => 'Ledger Entry Added Successfully',
            ]);
        } else {
            return response()->json([
                'status'   => false,
                'message'   => 'Oops..!! There is some error.',
            ]);
        }
    }
}
