<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Retailer;
use App\Models\Services;
use App\Rules\CheckUnique;
use App\Models\Distributor;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use App\Models\MainDistributor;
use Illuminate\Validation\Rule;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Common\LedgerController;
use App\Http\Controllers\Common\ServicesLogController;

class RetailerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['distributors_list']);
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = Retailer::query();
            $query->with(['main_distributor', 'distributor']);
            $query->select('retailers.id', 'retailers.name', 'retailers.registor_from', 'retailers.mobile', 'retailers.userId', 'retailers.slug', 'retailers.mobile', 'retailers.image', 'retailers.status', 'retailers.user_balance', 'retailers.distributor_id', 'retailers.main_distributor_id', 'retailers.created_at');

            if ($request->distributor) $query->where('distributor_id', $request->distributor);
            if ($request->main_distributor) $query->where('main_distributor_id', $request->main_distributor);

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->addColumn('main_distributor', function ($row) {
                    return '<b class="text-primary">' . (!empty($row->main_distributor->name) ? $row->main_distributor->name : '--') . '</b><br /><b  class="text-danger">' . (!empty($row->distributor->name) ? $row->distributor->name : '--') . '</b>';
                })
                ->editColumn('name', function ($row) {
                    return '<b class="text-dark">' . $row['name'] . '</b><br /> <span class="text-secondary">( Balance : ₹ ' . $row['user_balance'] . ')<span>';
                })
                ->editColumn('userId', function ($row) {
                    return '<b class="text-danger">' . $row['userId'] . '</b><br /> <b class="text-dark">' . $row['mobile'] . '<span>';
                })
                ->editColumn('user_balance', function ($row) {
                    return '<b> ₹ ' . $row['user_balance'] . '</b>';
                })
                ->editColumn('created_at', function ($row) {
                    return '<b>' . ($row['registor_from'] == 1 ? "Portal" : "Front Website") . '</b><br />' . ($row['created_at'] ? $row['created_at']->format('d M, Y h:i A') : '');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(106, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('retailers.edit', $row['slug']) . '">Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('retailers.services', $row['slug']) . '">Services</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('retailers.ledger', $row['slug']) . '">Ledger</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('retailers.customers_list', $row['slug']) . '">Customers</a>';
                    }
                    if (userCan(106, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(106)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'name', 'userId', 'main_distributor', 'created_at', 'status', 'user_balance'])
                ->make(true);
        }
        return view('retailers.index');
    }

    public function add()
    {
        $main_distributors = MainDistributor::select('id', 'name')->where('status', 1)->get();
        return view('retailers.add', compact('main_distributors'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'main_distributor_id'   => ['integer', 'nullable'],
            'distributor_id'        => ['integer', 'nullable', Rule::requiredIf($request->get('main_distributor_id') != null)],
            'name'      => ['required', 'string', 'max:255'],
            'status'    => ['required', 'integer'],
            'email'     => ['required', new CheckUnique('retailers')],
            'mobile'    => ['required', 'digits:10', new CheckUnique('retailers'), 'regex:' . config('constant.phoneRegExp')],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $data = [
            'slug'      => Str::uuid(),
            'main_distributor_id'   => $request->main_distributor_id,
            'distributor_id'        => $request->distributor_id,
            'name'      => $request->name,
            'email'     => $request->email,
            'mobile'    => $request->mobile,
            'status'    => $request->status,
            'image'     => 'admin/avatar.png',
            'password'  => Hash::make($request['password']),
        ];

        $path = 'admin';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        $data = Retailer::create($data);
        SendWelComeEmail::dispatch($data, $request->site_settings);
        return to_route('retailers')->with('success', 'Retailer Added Successfully!!');
    }

    public function edit($id)
    {
        $retailer = Retailer::select('retailers.*', 'distributors.main_distributor_id as main_distributor_id')
            ->where('retailers.slug', $id)
            ->leftJoin('distributors', 'distributors.id', '=', 'retailers.distributor_id')
            ->first();
        if ($retailer == null) {
            return to_route('retailers')->with('error', 'Retailer Not Found!!');
        }

        $main_distributors = MainDistributor::select('id', 'name')->where('status', 1)->get();
        return view('retailers.edit', compact('retailer', 'main_distributors'));
    }

    public function update(Request $request, $id)
    {
        $retailer = Retailer::firstWhere('id', $id);
        if ($retailer == null) {
            return to_route('retailers')->with('error', 'Retailer Not Found!!');
        }

        $validated = [
            'main_distributor_id'   => ['integer', 'nullable'],
            'distributor_id'        => ['integer', 'nullable', Rule::requiredIf($request->get('main_distributor_id') != null)],
            'status'    => ['required', 'integer'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', new CheckUnique('retailers', $retailer->id)],
            'mobile'    => ['required', 'digits:10', new CheckUnique('retailers', $retailer->id), 'regex:' . config('constant.phoneRegExp')],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ];

        if ($request['password']) {
            $validated['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $request->validate($validated);
        $data = [
            'main_distributor_id'   => $request->main_distributor_id,
            'distributor_id'        => $request->distributor_id,
            'name'                  => $request->name,
            'email'                 => $request->email,
            'mobile'                => $request->mobile,
            'status'                => $request->status,
        ];

        if ($request['password']) {
            $data['password'] = Hash::make($request['password']);
        }

        if ($retailer['distributor_id'] != $request->distributor_id) {
            ServicesLog::where('user_id',  $retailer['id'])
                ->where('user_type', 4)
                ->where('status', 1)
                ->update([
                    'status'        => 0,
                    'decline_date'  => Carbon::now()
                ]);
        }

        $path = 'admin';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        $retailer->update($data);
        return to_route('retailers')->with('success', 'Retailer Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $retailer = Retailer::where('id', $request->id)->first();
            if ($retailer == null) {
                return response()->json([
                    'success'   => false,
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

    public function distributors_list(Request $request)
    {
        $main_distributor_id = $request->main_distributor_id;
        $query = Distributor::query();
        $query->select('id', 'name');
        if ($main_distributor_id) {
            $query->where('main_distributor_id', $main_distributor_id);
        } else {
            $query->where('main_distributor_id', null);
        }

        $retailer = $query->get();
        return response()->json($retailer);
    }

    public function services(Request $request, $slug)
    {
        $user = Retailer::with(['main_distributor:id,name', 'distributor:id,name'])->firstWhere('slug', $slug);
        if ($user == null) {
            return to_route('retailers')->with('error', 'Retailer Not Found!!');
        }

        if ($request->ajax()) {
            $query = Services::query();
            $query->select(
                'services.id',
                'services.name',
                'assign_date',
                'default_d_commission',
                'default_md_commission',
                'default_r_commission',
                'services.sale_rate as sale_rate_all',
                'main_distributor_commission',
                'distributor_commission',
                'retailer_commission',
                'services_logs.sale_rate as sale_rate_unique',
                'services_logs.id as services_log_id',
                'services_logs.purchase_rate',
                'services_logs.commission_slots',
            );

            $query->leftJoin('services_logs', function ($join) use ($user) {
                $join->on('services.id', '=', 'services_logs.service_id')
                    ->where('services_logs.status', 1)
                    ->where('services_logs.user_type', 4)
                    ->where('services_logs.user_id', $user['id']);
            });

            if ($user->distributor_id != null) {
                $serviceIds = ServicesLog::select('service_id',)
                    ->where('user_id', $user->distributor_id)
                    ->where('user_type', 3)
                    ->where('status', 1)
                    ->get();

                $query->whereIn('services.id', $serviceIds->pluck('service_id')->toArray());
            }
            $query->where('services.status', 1);
            $query->orderBy('services.id', 'asc');

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('check', function ($row) {
                    $btn = '<div class="form-group">
                            <div class="switch form-switch-custom form-switch-secondary">
                                <input data-service-id="' . $row['id'] . '" class="switch-input" type="checkbox"
                                    role="switch" ' . (!empty($row['assign_date']) ? 'checked' : '') . ' />
                            </div>
                        </div>';
                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    if (empty($row['assign_date'])) {
                        return "";
                    } else {
                        if (in_array($row->id, config('constant.commission-slab-services', []))) {
                            $row->openSlots = true;
                        } else {
                            $row->openSlots = false;
                        }

                        return '<button data-all="' . htmlentities(json_encode($row)) . '" class="btn btn-sm btn-outline-secondary py-1 edit">
                            <i class="fa fa-edit"></i>
                        </button>';
                    }
                })
                ->rawColumns(['check', 'action'])
                ->make(true);
        }

        return view('retailers.services', compact('user'));
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
        $user = Retailer::with(['main_distributor:id,name', 'distributor:id,name'])->firstWhere('slug', $slug);
        if ($user == null) {
            return to_route('retailers')->with('error', 'Retailer Not Found!!');
        }

        if ($request->ajax()) {
            $data = Ledger::select('id', 'voucher_no', 'particulars', 'amount', 'current_balance',  'updated_balance', 'payment_type', 'payment_method', 'created_at')
                ->where('user_id', $user['id'])
                ->where('user_type', 4);

            return LedgerController::getDataTable($data);
        }

        return view('retailers.ledger', compact('user'));
    }

    public function ledger_add(Request $request, $slug)
    {
        $user = Retailer::firstWhere('slug', $slug);
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

        if ($request->payment_type == 2 && $request->amount > $user['user_balance']) {
            return response()->json([
                'status'    => false,
                'message'   => "Debit amount is greater than balance, you can max debit amount is " . $user['user_balance'] . ".",
                "data"      => []
            ]);
        }

        $data = [
            'amount'        => $request->amount,
            'payment_type'  => $request->payment_type,
            'payment_method' => 1,
            'particulars'   => getTransactionDetails('Admin', $request->all()),
        ];

        if (LedgerController::add($user['id'], 4, $data)) {
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

    public function services_commission_update(Request $request)
    {
        $service_log = ServicesLog::where('id', $request->id)
            ->where('status', 1)
            ->where('user_type', 4)
            ->whereNull('decline_date')
            ->first();

        if (!$service_log)
            return response()->json([
                'status'    => false,
                'message'   => "Can't Update commission for this service.",
                "data"      => ""
            ]);

        $service = Services::find($service_log->service_id);
        if ($service == null) {
            return response()->json([
                'status'    => false,
                'message'   => "Service Not Found..!!",
                "data"      => ""
            ]);
        }

        if (in_array($service->id, config('constant.commission-slab-services', []))) {
            $validator = Validator::make($request->all(), [
                'commission_slots.*.start'                          => ['required', 'numeric', 'min:1', 'max:10000000'],
                'commission_slots.*.end'                            => ['required', 'numeric', 'min:1', 'max:10000000'],
                'commission_slots.*.commission'                     => ['required', 'numeric', 'min:0', 'max:100'],
                'commission_slots.*.commission_distributor'         => ['required', 'numeric', 'min:0', 'max:100'],
                'commission_slots.*.commission_main_distributor'    => ['required', 'numeric', 'min:0', 'max:100'],
                'commission_slots.*.total_commission'               => ['required', 'numeric', 'min:0', 'max:100'],
            ]);

            $validator->after(function ($validator) use ($request) {
                foreach ($request->commission_slots as $index => $slot) {
                    $sum = $slot['commission'] + $slot['commission_distributor'] + $slot['commission_main_distributor'];

                    // Option 1: Validate against fixed 100% limit
                    if ($sum > 100) {
                        $validator->errors()->add(
                            "commission_slots.$index.total_commission",
                            "The sum of all commissions (currently {$sum}%) must not exceed 100%"
                        );
                    }

                    // Option 2: Validate against total_commission value
                    if (abs($sum - $slot['total_commission']) > 0.01) {
                        $validator->errors()->add(
                            "commission_slots.$index.total_commission",
                            "The sum of individual commissions (currently {$sum}%) exceeds the total commission value ({$slot['total_commission']}%)"
                        );
                    }
                }
            });

            if ($validator->fails()) {

                $err = array();
                foreach ($validator->errors()->toArray() as $key => $value) {
                    $err[$key] = $value[0];
                }

                return response()->json([
                    'status'    => false,
                    'message'   => "Invalid Input values.",
                    "data"      => $err
                ]);
            }

            $isSame = true;
            $validated = $validator->validate();
            foreach (config('constant.bill-slab', []) as $key => $value) {
                if (@$validated['commission_slots'][$key]['start']  != $value['start']  || @$validated['commission_slots'][$key]['end']  != $value['end']) {
                    $isSame = false;
                    break;
                }
            }

            if (!$isSame) return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => []
            ]);

            if ($validated['commission_slots'] == $service->commission_slots) {
                return response()->json([
                    'status'    => false,
                    'message'   => "No changes..!!",
                    "data"      => []
                ]);
            }


            ServicesLog::where('id', $request->id)->update([
                'status'        => 0,
                'decline_date'  => Carbon::now()
            ]);

            $service = ServicesLog::create([
                'user_id'                       => $service_log->user_id,
                'service_id'                    => $service_log->service_id,
                'user_type'                     => $service_log->user_type,
                'status'                        => 1,
                'assign_date'                   => Carbon::now(),
                'decline_date'                  => null,
                'purchase_rate'                 => $service_log->purchase_rate,
                'sale_rate'                     => $service_log->sale_rate,
                'main_distributor_commission'   => $service_log->main_distributor_commission,
                'distributor_commission'        => $service_log->distributor_commission,
                'retailer_commission'           => $service_log->retailer_commission,
                'commission_slots'              => $validated['commission_slots'],
                'main_distributor_id'           => $service_log->main_distributor_id,
                'distributor_id'                => $service_log->distributor_id,
                'created_at'                    => Carbon::now(),
                'updated_at'                    => Carbon::now(),
            ]);

            return response()->json([
                'status'    => true,
                'message'   => "Commission Updated.",
                "data"      => ''
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'sale_rate'                     => 'required|numeric|min:0|max:100000000',
                'distributor_commission'        => 'required|numeric|min:0|max:100000000',
                'main_distributor_commission'   => 'required|numeric|min:0|max:100000000',
                'retailer_commission'           => 'required|numeric|min:0|max:100000000',
            ]);

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

            if (floatval($request->sale_rate) < (floatval($service_log->purchase_rate) + floatval($request->main_distributor_commission) + floatval($request->distributor_commission))) {
                return response()->json([
                    'status'    => false,
                    'message'   => "Sale Rate can't be less then sum of 'Distributor commission', 'MainDistributor commission' and 'Purchase Rate'.",
                    "data"      => ""
                ]);
            }

            if (
                $service_log['sale_rate']                   != $request->sale_rate              ||
                $service_log['distributor_commission']      != $request->distributor_commission ||
                $service_log['retailer_commission']         != $request->retailer_commission    ||
                $service_log['commission_slots']            != $request->commission_slots       ||
                $service_log['main_distributor_commission'] != $request->main_distributor_commission
            ) {
                ServicesLog::where('id', $request->id)->update([
                    'status'        => 0,
                    'decline_date'  => Carbon::now()
                ]);

                $service = ServicesLog::create([
                    'user_id'                       => $service_log->user_id,
                    'service_id'                    => $service_log->service_id,
                    'user_type'                     => $service_log->user_type,
                    'status'                        => 1,
                    'assign_date'                   => Carbon::now(),
                    'decline_date'                  => null,
                    'purchase_rate'                 => $service_log->purchase_rate,
                    'sale_rate'                     => $request->sale_rate,
                    'main_distributor_commission'   => $request->main_distributor_commission,
                    'distributor_commission'        => $request->distributor_commission,
                    'retailer_commission'           => $request->retailer_commission,
                    'commission_slots'              => $request->commission_slots,
                    'main_distributor_id'           => $service_log->main_distributor_id,
                    'distributor_id'                => $service_log->distributor_id,
                    'created_at'                    => Carbon::now(),
                    'updated_at'                    => Carbon::now(),
                ]);
            }

            return response()->json([
                'status'    => true,
                'message'   => "Commission Updated.",
                "data"      => ''
            ]);
        }
    }

    public function customers_list(Request $request, $slug)
    {
        $user = Retailer::firstWhere('slug', $slug);
        if ($user == null) {
            return to_route('retailers')->with('error', 'Retailer Not Found!!');
        }

        if ($request->ajax()) {
            $query = ServiceUsesLog::select('customer_id', 'user_id')
                ->with('customer')
                ->groupBy(['customer_id', 'user_id'])
                ->where('user_type', 4)
                ->where('user_id', $user->id);

            return Datatables::of($query)->addIndexColumn()
                ->addColumn('image', function (ServiceUsesLog $log) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $log->customer->image) . '" alt=""></div>';
                    return $btn;
                })
                ->addColumn('name', function (ServiceUsesLog $log) {
                    return $log->customer->name;
                })
                ->addColumn('email', function (ServiceUsesLog $log) {
                    return $log->customer->email;
                })
                ->addColumn('status', function (ServiceUsesLog $log) {
                    return $log->customer->status == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })

                ->addColumn('created_at', function (ServiceUsesLog $log) {
                    return $log->customer->created_at->format('d M, Y');
                })
                ->addColumn('action', function (ServiceUsesLog $log) {
                    return '<a href="' . route('customers.service_used', ['id' => $log->customer->slug, 'user_type' => 4, 'user_id' => $log->user_id]) . '" class="btn btn-secondary btn-sm">Used Services</a>';
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }

        return view('retailers.customer_list', compact('user'));
    }

    public function export()
    {
        $query = Retailer::query();
        $query->with(['main_distributor', 'distributor']);

        if (request('main_distributor'))  $query->where('main_distributor_id', request('main_distributor'));
        if (request('distributor'))  $query->where('distributor_id', request('distributor'));

        $retailer = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Retailer List', true);

        $sheet->setCellValue('A1', 'User Id');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Main Distributor');
        $sheet->setCellValue('D1', 'Distributor');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'Mobile');
        $sheet->setCellValue('G1', 'User Balance');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Register Date');
        $sheet->setCellValue('J1', 'Register From');

        $rows = 2;
        foreach ($retailer as $row) {
            $sheet->setCellValue('A' . $rows, $row->userId);
            $sheet->setCellValue('B' . $rows, $row->name);
            $sheet->setCellValue('C' . $rows, isset($row->main_distributor->name) ? $row->main_distributor->name : '--');
            $sheet->setCellValue('D' . $rows, isset($row->distributor->name) ? $row->distributor->name : '--');
            $sheet->setCellValue('E' . $rows, $row->email);
            $sheet->setCellValue('F' . $rows, $row->mobile);
            $sheet->setCellValue('G' . $rows, $row->user_balance);
            $sheet->setCellValue('H' . $rows, $row->status == 1 ? "Active" : "InActive");
            $sheet->setCellValue('I' . $rows,  Date::PHPToExcel($row->created_at));
            $sheet->setCellValue('J' . $rows, $row->registor_from == 1 ? "Portal" : "Front Website");
            $rows++;
        }

        $sheet->getStyle('G1:G' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        $sheet->getStyle('I1:I' . $rows)->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm AM/PM');
        $sheet->getStyle('F1:F' . $rows)->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('A1:H' . $rows)->getAlignment()->setHorizontal('center');
        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);

        // AutoWidth Column
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $fileName = "Retailers.xlsx";
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }

    public function not_loaded(Request $request)
    {
        if ($request->ajax()) {
            $query = Retailer::query();
            $query->select('retailers.id', 'retailers.name', 'retailers.registor_from', 'retailers.mobile', 'retailers.userId', 'retailers.slug', 'retailers.mobile', 'retailers.image', 'retailers.status', 'retailers.user_balance', 'retailers.distributor_id', 'retailers.main_distributor_id', 'retailers.created_at');
            $query->with(['main_distributor', 'distributor']);
            $query->has('ledgers',  0);
            $query->where('retailers.user_balance', 0);

            if ($request->distributor) $query->where('distributor_id', $request->distributor);
            if ($request->main_distributor) $query->where('main_distributor_id', $request->main_distributor);

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->addColumn('main_distributor', function ($row) {
                    return '<b class="text-primary">' . (!empty($row->main_distributor->name) ? $row->main_distributor->name : '--') . '</b><br /><b  class="text-danger">' . (!empty($row->distributor->name) ? $row->distributor->name : '--') . '</b>';
                })
                ->editColumn('name', function ($row) {
                    return '<b class="text-dark">' . $row['name'] . '</b><br /> <span class="text-secondary">( Balance : ₹ ' . $row['user_balance'] . ')<span>';
                })
                ->editColumn('userId', function ($row) {
                    return '<b class="text-danger">' . $row['userId'] . '</b><br /> <b class="text-dark">' . $row['mobile'] . '<span>';
                })
                ->editColumn('user_balance', function ($row) {
                    return '<b> ₹ ' . $row['user_balance'] . '</b>';
                })
                ->editColumn('created_at', function ($row) {
                    return '<b>' . ($row['registor_from'] == 1 ? "Portal" : "Front Website") . '</b><br />' . ($row['created_at'] ? $row['created_at']->format('d M, Y h:i A') : '');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(106, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('retailers.edit', $row['slug']) . '">Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('retailers.services', $row['slug']) . '">Services</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('retailers.ledger', $row['slug']) . '">Ledger</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('retailers.customers_list', $row['slug']) . '">Customers</a>';
                    }
                    if (userCan(106, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(106)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'name', 'userId', 'main_distributor', 'created_at', 'status', 'user_balance'])
                ->make(true);
        }
        return view('retailers.not-loaded');
    }

    public function not_loaded_export(Request $request)
    {
        $query = Retailer::query();
        $query->with(['main_distributor', 'distributor']);
        $query->has('ledgers',  0);
        $query->where('retailers.user_balance', 0);

        if (request('main_distributor'))  $query->where('main_distributor_id', request('main_distributor'));
        if (request('distributor'))  $query->where('distributor_id', request('distributor'));

        $retailer = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Retailer List', true);

        $sheet->setCellValue('A1', 'User Id');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Main Distributor');
        $sheet->setCellValue('D1', 'Distributor');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'Mobile');
        $sheet->setCellValue('G1', 'User Balance');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Register Date');
        $sheet->setCellValue('J1', 'Register From');

        $rows = 2;
        foreach ($retailer as $row) {
            $sheet->setCellValue('A' . $rows, $row->userId);
            $sheet->setCellValue('B' . $rows, $row->name);
            $sheet->setCellValue('C' . $rows, isset($row->main_distributor->name) ? $row->main_distributor->name : '--');
            $sheet->setCellValue('D' . $rows, isset($row->distributor->name) ? $row->distributor->name : '--');
            $sheet->setCellValue('E' . $rows, $row->email);
            $sheet->setCellValue('F' . $rows, $row->mobile);
            $sheet->setCellValue('G' . $rows, $row->user_balance);
            $sheet->setCellValue('H' . $rows, $row->status == 1 ? "Active" : "InActive");
            $sheet->setCellValue('I' . $rows,  Date::PHPToExcel($row->created_at));
            $sheet->setCellValue('J' . $rows, $row->registor_from == 1 ? "Portal" : "Front Website");
            $rows++;
        }

        $sheet->getStyle('G1:G' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        $sheet->getStyle('I1:I' . $rows)->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm AM/PM');
        $sheet->getStyle('F1:F' . $rows)->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('A1:H' . $rows)->getAlignment()->setHorizontal('center');
        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);

        // AutoWidth Column
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $fileName = "RetailersNotLoadedBalance.xlsx";
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }
}
