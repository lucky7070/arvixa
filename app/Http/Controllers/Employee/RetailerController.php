<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\PanCard;
use App\Models\Retailer;
use App\Models\Services;
use App\Rules\CheckUnique;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use App\Models\EmployeeNote;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use App\Models\MainDistributor;
use Illuminate\Validation\Rule;
use \Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Common\LedgerController;
use App\Http\Controllers\Common\ServicesLogController;

class RetailerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:employee');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Retailer::query();
            $data->with(['main_distributor', 'distributor']);
            $data->select('retailers.id', 'retailers.name', 'retailers.registor_from', 'retailers.mobile', 'retailers.userId', 'retailers.slug', 'retailers.mobile', 'retailers.image', 'retailers.status', 'retailers.user_balance', 'retailers.distributor_id', 'retailers.main_distributor_id', 'retailers.created_at');
            $data->where('employee_id', Auth::guard('employee')->id());
            if ($request->distributor)      $data->where('distributor_id', $request->distributor);
            if ($request->main_distributor) $data->where('main_distributor_id', $request->main_distributor);

            return Datatables::of($data)->addIndexColumn()
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('name', function ($row) {
                    return '<b class="text-dark">' . $row['name'] . '</b><br /> <span class="text-secondary">( Balance : ₹ ' . $row['user_balance'] . ')<span>';
                })
                ->addColumn('main_distributor', function ($row) {
                    return '<b class="text-primary">' . (!empty($row->main_distributor->name) ? $row->main_distributor->name : '--') . '</b><br /><b  class="text-danger">' . (!empty($row->distributor->name) ? $row->distributor->name : '--') . '</b>';
                })
                ->addColumn('distributor', function ($row) {
                    return !empty($row->distributor->name) ? $row->distributor->name : '--';
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
                        <a class="dropdown-item" href="' . route('employee.retailers.edit', $row['slug']) . '">Edit</a>
                        <a class="dropdown-item" href="' . route('employee.retailers.services', $row['slug']) . '">Services</a>
                        <a class="dropdown-item" href="' . route('employee.retailers.ledger', $row['slug']) . '">Ledger</a>
                        <a class="dropdown-item" href="' . route('employee.retailers.notes', $row['slug']) . '">Notes</a>
                        <a class="dropdown-item" href="' . route('employee.retailers.pancards', $row['slug']) . '">PanCards</a>
                    </div>';
                    return $btn;
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'main_distributor', 'name', 'userId', 'status'])
                ->make(true);
        }
        return view('employee.retailers.index');
    }

    public function add()
    {
        $main_distributors = MainDistributor::select('id', 'name')->where('status', 1)->get();
        return view('employee.retailers.add', compact('main_distributors'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'main_distributor_id'   => ['integer', 'nullable'],
            'distributor_id'        => ['integer', 'nullable', Rule::requiredIf($request->get('main_distributor_id') != null)],
            'name'                  => ['required', 'string', 'max:255'],
            'status'                => ['required', 'integer'],
            'email'                 => ['required', new CheckUnique('retailers')],
            'mobile'                => ['required', 'digits:10', new CheckUnique('retailers'), 'regex:' . config('constant.phoneRegExp')],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'image'                 => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $data = [
            'slug'                  => Str::uuid(),
            'main_distributor_id'   => $request->main_distributor_id,
            'distributor_id'        => $request->distributor_id,
            'name'                  => $request->name,
            'employee_id'           => auth()->guard('employee')->id(),
            'email'                 => $request->email,
            'mobile'                => $request->mobile,
            'status'                => $request->status,
            'image'                 => 'admin/avatar.png',
            'password'              => Hash::make($request['password']),
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
        return redirect(route('employee.retailers'))->with('success', 'Retailer Added Successfully!!');
    }

    public function edit($id)
    {
        $retailer = Retailer::select('retailers.*', 'distributors.main_distributor_id as main_distributor_id')
            ->where('retailers.slug', $id)
            ->leftJoin('distributors', 'distributors.id', '=', 'retailers.distributor_id')
            ->first();

        if ($retailer == null) {
            return redirect(route('employee.retailers'))->with('error', 'Retailer Not Found!!');
        }

        $main_distributors = MainDistributor::select('id', 'name')->where('status', 1)->get();
        return view('employee.retailers.edit', compact('retailer', 'main_distributors'));
    }

    public function update(Request $request, $id)
    {
        $retailer = Retailer::firstWhere('id', $id);
        if ($retailer == null) {
            return redirect(route('employee.retailers'))->with('error', 'Retailer Not Found!!');
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
        return redirect(route('employee.retailers'))->with('success', 'Retailer Updated Successfully!!');
    }

    public function services(Request $request, $slug)
    {
        $user = Retailer::with(['main_distributor:id,name', 'distributor:id,name'])->where('employee_id', Auth::guard('employee')->id())->firstWhere('slug', $slug);
        if ($user == null) {
            return redirect(route('retailers'))->with('error', 'Retailer Not Found!!');
        }

        if ($request->isMethod('get') && $request->ajax()) {
            $query = Services::query();
            $query->select(
                'services.id',
                'services.name',
                'assign_date',
                'default_d_commission',
                'default_md_commission',
                'services.sale_rate as sale_rate_all',
                'main_distributor_commission',
                'distributor_commission',
                'services_logs.sale_rate as sale_rate_unique',
                'services_logs.id as services_log_id',
                'services_logs.purchase_rate',
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
                ->rawColumns(['check'])
                ->make(true);
        }

        return view('employee.retailers.services', compact('user'));
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
        $user = Retailer::with(['main_distributor:id,name', 'distributor:id,name'])->where('employee_id', Auth::guard('employee')->id())->firstWhere('slug', $slug);
        if ($user == null) {
            return redirect(route('retailers'))->with('error', 'Retailer Not Found!!');
        }

        if ($request->ajax()) {
            $data = Ledger::select('id', 'voucher_no', 'particulars', 'amount', 'current_balance',  'updated_balance', 'payment_type', 'payment_method', 'created_at')
                ->where('user_id', $user['id'])
                ->where('user_type', 4);

            return LedgerController::getDataTable($data);
        }

        return view('employee.retailers.ledger', compact('user'));
    }

    public function pancards(Request $request, $slug)
    {
        $user = Retailer::with(['main_distributor:id,name', 'distributor:id,name'])->where('employee_id', Auth::guard('employee')->id())->firstWhere('slug', $slug);
        if ($user == null) {
            return redirect(route('retailers'))->with('error', 'Retailer Not Found!!');
        }

        if ($request->ajax()) {
            $data = PanCard::select('id', 'name', 'type', 'middle_name', 'is_physical_card', 'is_refunded', 'last_name', 'email', 'slug', 'phone', 'doc', 'nsdl_ack_no', 'nsdl_txn_id', 'nsdl_complete', 'created_at')
                ->where('user_type', 4)
                ->where('user_id', $user->id);

            return Datatables::of($data)->addIndexColumn()
                ->editColumn('nsdl_complete', function ($row) {
                    $status = '';
                    if ($row['nsdl_complete'] == 1 && $row['nsdl_ack_no'] != null) {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Submitted</small>';
                    } elseif ($row['nsdl_complete'] == 1 && $row['is_refunded'] == 1) {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-dark"> Not Submitted</small>';
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Refunded</small>';
                    } else {
                        $status .= '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> InComplete</small>';
                    }

                    return $status;
                })
                ->editColumn('nsdl_txn_id', function ($row) {
                    return '<b>' . $row['nsdl_txn_id'] . '</b>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->editColumn('type', function ($row) {
                    return $row['type'] == 1 ? '<span class="badge badge-light-primary">New</span>' : '<span class="badge badge-light-secondary">Update</span>';
                })
                ->addColumn('full_name', function ($row) {
                    return '<b>' . trim($row->fname . " " . $row->middle_name . " " . $row->last_name) . '</b><br /> <span>(' . $row->phone . ')</span>';
                })
                ->editColumn('is_physical_card', function ($row) {
                    return  $row->is_physical_card == 'Y' ? '<span class="badge badge-light-secondary">Physical</span>' : '<span class="badge badge-light-info">Digital</span>';
                })
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw('CONCAT(name, " ", middle_name, " ", last_name) like ?', ["%{$keyword}%"]);
                })
                ->rawColumns(['is_physical_card', 'nsdl_complete', 'nsdl_txn_id', 'full_name', 'type'])
                ->make(true);
        }

        return view('employee.retailers.pancards', compact('user'));
    }

    public function notes(Request $request, $slug = null)
    {
        $retailers  = Retailer::where('employee_id', Auth::guard('employee')->id())->get();
        $user       = $retailers->first(fn($row) => $row->slug ==  $slug);

        if ($request->isMethod('post')) {
            $request->validate([
                'message'       => ['required', 'string', 'max:1000'],
                'date'          => ['required', 'date', 'date_format:Y-m-d', 'before:tomorrow', 'after:1900-01-01'],
                'retailer_id'   => ['required', 'integer']
            ]);

            EmployeeNote::create([
                'retailer_id'   => !empty($user->id) ? $user->id  : $request->retailer_id,
                'employee_id'   => auth()->guard('employee')->id(),
                'date'          => Carbon::parse($request->date)->toDateTimeString(),
                'message'       => $request->message
            ]);

            if (empty($user->id) && !in_array($request->retailer_id, $retailers->pluck('id')->toArray())) {
                return back()->withInput()->with('error', 'Invalid Retailer..!!');
            }

            return redirect()->route('employee.retailers.notes', $slug)->with('success', 'Comment Saved Successfully..!!');
        }

        if ($request->isMethod('get')) {

            $query  = EmployeeNote::query();
            $query->with('retailer');
            $query->where('employee_id', Auth::guard('employee')->id());
            $query->whereIn('retailer_id', $retailers->pluck('id')->toArray());
            if ($user != null) $query->where('retailer_id', $user->id);
            $query->orderBy('date', 'desc');
            $notes = $query->paginate(20);


            return view('employee.retailers.notes', compact('user', 'notes', 'retailers'));
        }
    }

    public function export()
    {
        $query = Retailer::query();
        $query->with(['main_distributor', 'distributor']);
        $query->where('employee_id', Auth::guard('employee')->id());

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
        $sheet->getStyle('A1:J' . $rows)->getAlignment()->setHorizontal('center');
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
}
