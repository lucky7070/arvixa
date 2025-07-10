<?php

namespace App\Http\Controllers\Distributor;

use App\Models\Ledger;
use App\Models\Retailer;
use App\Models\Services;
use App\Rules\CheckUnique;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\LedgerController;
use App\Http\Controllers\Common\ServicesLogController;

class RetailerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:distributor');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Retailer::select('id', 'name', 'userId', 'slug', 'mobile', 'image', 'status', 'user_balance', 'created_at')
                ->where('distributor_id', Auth::guard('distributor')->id());

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
                        <a class="dropdown-item" href="' . route('distributor.retailers.edit', $row['slug']) . '">Edit</a>
                        <a class="dropdown-item" href="' . route('distributor.retailers.services', $row['slug']) . '">Services</a>
                        <a class="dropdown-item" href="' . route('distributor.retailers.ledger', $row['slug']) . '">Ledger</a>
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
        return view('distributor.retailers.index');
    }

    public function add()
    {

        return view('distributor.retailers.add');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'status'                => ['required', 'integer'],
            'email'                 => ['required', new CheckUnique('retailers')],
            'mobile'                => ['required', 'digits:10', new CheckUnique('retailers'), 'regex:' . config('constant.phoneRegExp')],
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
            'slug'      => Str::uuid(),
            'main_distributor_id'   => Auth::guard('distributor')->user()->main_distributor_id,
            'distributor_id'        => Auth::guard('distributor')->id(),
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

        $data = Retailer::create($data);
        SendWelComeEmail::dispatch($data, $request->site_settings);
        return to_route('distributor.retailers')->with('success', 'Retailer Added Successfully!!');
    }

    public function edit($id)
    {
        $retailer = Retailer::select('*')
            ->where('retailers.slug', $id)
            ->where('retailers.distributor_id', Auth::guard('distributor')->id())
            ->first();

        if ($retailer == null) {
            return to_route('distributor.retailers')->with('error', 'Retailer Not Found!!');
        }

        return view('distributor.retailers.edit', compact('retailer'));
    }

    public function update(Request $request, $id)
    {
        $retailer = Retailer::where('id', $id)
            ->where('distributor_id', Auth::guard('distributor')->id())
            ->first();
        if ($retailer == null) {
            return to_route('distributor.retailers')->with('error', 'Retailer Not Found!!');
        }

        $validated = [
            'status'                => ['required', 'integer'],
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required',  new CheckUnique('retailers', $retailer['id'])],
            'mobile'                => ['required', 'digits:10', new CheckUnique('retailers', $retailer['id']), 'regex:' . config('constant.phoneRegExp')],
            'image'                 => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
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
                if ($retailer->$field && Storage::disk('local')->exists('public/' . $retailer->$field)) {
                    Storage::disk('local')->delete('public/' . $retailer->$field);
                }
            }
        }

        $retailer->update($data);
        return to_route('distributor.retailers')->with('success', 'Retailer Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $retailer = Retailer::where('id', $request->id)
                ->where('distributor_id', Auth::guard('distributor')->id())
                ->first();
            if ($retailer == null) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'Retailer Not Found.',
                ]);
            }

            $retailer->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Retailer deleted Successfully',
            ]);
        }
    }

    public function services($slug)
    {
        $retailer = Retailer::firstWhere('slug', $slug);
        if ($retailer == null) {
            return to_route('distributor.retailers')->with('error', 'Retailer Not Found!!');
        }

        $serviceIds = ServicesLog::select('service_id')
            ->where('user_id', Auth::guard('distributor')->id())
            ->where('user_type', 3)
            ->where('status', 1)
            ->get()
            ->pluck('service_id')
            ->toArray();

        $query = Services::query();
        $query->select('services.id', 'services.name', 'assign_date');
        $query->leftJoin('services_logs', function ($join) use ($retailer) {
            $join->on('services.id', '=', 'services_logs.service_id')
                ->where('services_logs.status', 1)
                ->where('services_logs.user_type', 4)
                ->where('services_logs.user_id', $retailer['id']);
        });

        $query->whereIn('services.id', $serviceIds);
        $query->where('services.status', 1);
        $query->orderBy('services.id', 'asc');
        $services = $query->get();


        return view('distributor.retailers.services', compact(['retailer', 'services']));
    }

    public function services_update(Request $request, $slug)
    {
        $retailer = Retailer::firstWhere('slug', $slug);
        if (!$retailer) {
            return response()->json([
                'status'   => false,
                'message'   => 'Retailer Not Found.',
            ]);
        }

        $query = Services::query();
        $query->select('id');
        $query->where('status', 1);

        if ($retailer->distributor_id != null) {
            $serviceIds = ServicesLog::select('service_id')
                ->where('user_id', $retailer->distributor_id)
                ->where('user_type', 3)
                ->where('status', 1)
                ->get()
                ->pluck('service_id')
                ->toArray();
            $query->whereIn('id', $serviceIds);
        }

        $services = $query->get()->pluck('id')->toArray();

        if (in_array($request->service_id, $services)) {
            if (in_array($request->service_id, ['on', 'off'])) {
                foreach ($services as $key => $value) {
                    ServicesLogController::update($value,  $retailer['id'],  4, $request->service_id);
                }
            } else {
                ServicesLogController::update($request->service_id,  $retailer['id'],  4);
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
        $user = Retailer::where('slug', $slug)->where('distributor_id', Auth::guard('distributor')->id())->first();
        if ($user == null) {
            return to_route('distributor.retailers')->with('error', 'Retailer Not Found!!');
        }

        if ($request->ajax()) {
            $data = Ledger::select('id', 'voucher_no', 'particulars', 'amount', 'current_balance',  'updated_balance', 'payment_type', 'payment_method', 'created_at')
                ->where('user_id', $user['id'])
                ->where('user_type', 4);

            return LedgerController::getDataTable($data);
        }

        return view('distributor.retailers.ledger', compact('user'));
    }

    public function ledger_add(Request $request, $slug)
    {
        $user = Retailer::where('slug', $slug)->where('distributor_id', Auth::guard('distributor')->id())->first();
        if (!$user) {
            return response()->json([
                'status'   => false,
                'message'   => 'Retailer Not Found.',
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

        if ($request->payment_type == 1 && Auth::guard('distributor')->user()->user_balance < $request->amount) {
            return response()->json([
                'status'    => false,
                'message'   => "Your Balance is less than credit amount. Your balance is " . Auth::guard('distributor')->user()->user_balance . ".",
                "data"      => []
            ]);
        }

        DB::beginTransaction();
        try {

            LedgerController::add($user['id'], 4, [
                'amount'            => $request->amount,
                'payment_type'      => $request->payment_type,
                'payment_method'    => 1,
                'particulars'       => getTransactionDetails('Distributor', $request->all()),
            ]);

            LedgerController::add(Auth::guard('distributor')->id(), 3, [
                'amount'            => $request->amount,
                'payment_type'      => $request->payment_type == 1 ? 2 : 1,
                'payment_method'    => 1,
                'particulars'       => getTransactionDetails('Retailer', $request->all(), 2),
            ]);

            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Ledger Entry Added Successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(),
            ]);
        }
    }
}
