<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Retailer;
use App\Models\Services;
use App\Rules\CheckUnique;
use App\Models\Distributor;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use App\Models\MainDistributor;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Common\LedgerController;
use App\Http\Controllers\Common\ServicesLogController;


class DistributorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Distributor::query();

            $query->with('main_distributor');
            $query->select('id', 'name', 'userId', 'slug', 'mobile', 'image', 'status', 'user_balance', 'main_distributor_id', 'created_at');

            if ($request->main_distributor) $query->where('main_distributor_id', $request->main_distributor);

            return Datatables::of($query)->addIndexColumn()
                ->addColumn('main_distributor', function ($row) {
                    return !empty($row->main_distributor->name) ? $row->main_distributor->name : '--';
                })
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('name', function ($row) {
                    return '<b class="text-dark">' . $row['name'] . '</b><br /> <span class="text-secondary">( Balance : ₹ ' . $row['user_balance'] . ')<span>';
                })
                ->addColumn('main_distributor', function ($row) {
                    return '<b class="text-primary">' . (!empty($row->main_distributor->name) ? $row->main_distributor->name : '--') . '</b>';
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
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(105, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('distributors.edit', $row['slug']) . '">Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('distributors.services', $row['slug']) . '">Services</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('retailers', ['distributor' => $row->id]) . '">Retailers</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('distributors.ledger', $row['slug']) . '">Ledger</a>';
                    }
                    if (userCan(105, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(105)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'name', 'userId', 'main_distributor', 'status', 'user_balance'])
                ->make(true);
        }
        return view('distributors.index');
    }

    public function add()
    {
        $main_distributors = MainDistributor::select('id', 'name')->where('status', 1)->get();
        return view('distributors.add', compact('main_distributors'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'main_distributor_id'   => ['integer', 'nullable'],
            'name'      => ['required', 'string', 'max:255'],
            'status'    => ['required', 'integer'],
            'email'     => ['required',  new CheckUnique('distributors')],
            'mobile'    => ['required', 'digits:10',  new CheckUnique('distributors'), 'regex:' . config('constant.phoneRegExp')],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $data = [
            'slug'      => Str::uuid(),
            'main_distributor_id'   => $request->main_distributor_id,
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

        $data = Distributor::create($data);
        SendWelComeEmail::dispatch($data, $request->site_settings);
        return redirect(route('distributors'))->with('success', 'Distributor Added Successfully!!');
    }

    public function edit($id)
    {
        $distributor = Distributor::firstWhere('slug', $id);
        if ($distributor == null) {
            return redirect(route('distributors'))->with('error', 'Distributor Not Found!!');
        }

        $main_distributors = MainDistributor::select('id', 'name')->where('status', 1)->get();
        return view('distributors.edit', compact('distributor', 'main_distributors'));
    }

    public function update(Request $request, $id)
    {
        $distributor = Distributor::firstWhere('id', $id);
        if ($distributor == null) {
            return redirect(route('distributors'))->with('error', 'Distributor Not Found!!');
        }

        $validated = [
            'main_distributor_id'   => ['integer', 'nullable'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', new CheckUnique('distributors', $distributor->id)],
            'mobile'    => ['required', 'digits:10', new CheckUnique('distributors', $distributor->id), 'regex:' . config('constant.phoneRegExp')],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
            'status'    => ['required', 'integer'],
        ];

        if ($request['password']) {
            $validated['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $request->validate($validated);
        $data = [
            'main_distributor_id'   => $request->main_distributor_id,
            'name'      => $request->name,
            'email'     => $request->email,
            'mobile'    => $request->mobile,
            'status'    => $request->status,
        ];

        if ($request['password']) {
            $data['password'] = Hash::make($request['password']);
        }

        if ($distributor['main_distributor_id'] != $request->main_distributor_id) {
            $retailers = Retailer::select('id')->where(['distributor_id' => $distributor['id']]);

            $retailers->update(['main_distributor_id' => $request->main_distributor_id]);
            $retailer_under_this_distributor = $retailers->get()->pluck('id')->toArray();

            ServicesLog::where(function ($query) use ($distributor) {
                $query->where('user_id',  $distributor['id']);
                $query->where('user_type', 3);
                $query->where('status', 1);
            })->orWhere(function ($query) use ($retailer_under_this_distributor) {
                $query->whereIn('user_id', $retailer_under_this_distributor);
                $query->where('user_type', 4);
                $query->where('status', 1);
            })->update([
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

        $distributor->update($data);
        return redirect(route('distributors'))->with('success', 'Distributor Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $distributor = Distributor::where('id', $request->id)->first();
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
        $user = Distributor::with(['main_distributor'])->firstWhere('slug', $slug);
        if ($user == null) {
            return redirect(route('distributors'))->with('error', 'Distributor Not Found!!');
        }

        $query = Services::query();
        $query->select('services.id', 'services.name', 'assign_date');
        $query->leftJoin('services_logs', function ($join) use ($user) {
            $join->on('services.id', '=', 'services_logs.service_id')
                ->where('services_logs.status', 1)
                ->where('services_logs.user_type', 3)
                ->where('services_logs.user_id', $user['id']);
        });

        if ($user->main_distributor_id != null) {
            $serviceIds = ServicesLog::select('service_id')
                ->where('user_id', $user->main_distributor_id)
                ->where('user_type', 2)
                ->where('status', 1)
                ->get()
                ->pluck('service_id')
                ->toArray();
            $query->whereIn('services.id', $serviceIds);
        }

        $query->where('services.status', 1);
        $query->orderBy('services.id', 'asc');
        $services = $query->get();

        return view('distributors.services', compact(['user', 'services']));
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

        $query = Services::query();
        $query->select('id');
        $query->where('status', 1);

        if ($distributor->main_distributor_id != null) {
            $serviceIds = ServicesLog::select('service_id')
                ->where('user_id', $distributor->main_distributor_id)
                ->where('user_type', 2)
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
        $user = Distributor::with(['main_distributor'])->firstWhere('slug', $slug);
        if ($user == null) {
            return redirect(route('distributors'))->with('error', 'Distributor Not Found!!');
        }

        if ($request->ajax()) {
            $data = Ledger::select('id', 'voucher_no', 'particulars', 'amount', 'current_balance',  'updated_balance', 'payment_type', 'payment_method', 'created_at')
                ->where('user_id', $user['id'])
                ->where('user_type', 3);

            return LedgerController::getDataTable($data);
        }

        return view('distributors.ledger', compact('user'));
    }

    public function ledger_add(Request $request, $slug)
    {
        $user = Distributor::firstWhere('slug', $slug);
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

        if (LedgerController::add($user['id'], 3, $data)) {
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

    public function export()
    {
        $query = Distributor::query();
        $query->with('main_distributor');

        if (request('main_distributor')) $query->where('main_distributor_id', request('main_distributor'));

        $main_distributor = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Distributor List', true);

        $sheet->setCellValue('A1', 'User Id');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Main Distributor');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Mobile');
        $sheet->setCellValue('F1', 'User Balance');
        $sheet->setCellValue('G1', 'Status');
        $sheet->setCellValue('H1', 'Register Date');

        $rows = 2;
        foreach ($main_distributor as $row) {
            $sheet->setCellValue('A' . $rows, $row->userId);
            $sheet->setCellValue('B' . $rows, $row->name);
            $sheet->setCellValue('C' . $rows, isset($row->main_distributor->name) ? $row->main_distributor->name : '--');
            $sheet->setCellValue('D' . $rows, $row->email);
            $sheet->setCellValue('E' . $rows, $row->mobile);
            $sheet->setCellValue('F' . $rows, $row->user_balance);
            $sheet->setCellValue('G' . $rows, $row->status == 1 ? "Active" : "InActive");
            $sheet->setCellValue('H' . $rows, $row->created_at->format('d F, Y'));
            $rows++;
        }

        $sheet->getStyle('E1:E' . $rows)->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('F1:F' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        $sheet->getStyle('A1:F' . $rows)->getAlignment()->setHorizontal('center');

        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);

        // AutoWidth Column
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $fileName = "Distributors.xlsx";
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }
}
