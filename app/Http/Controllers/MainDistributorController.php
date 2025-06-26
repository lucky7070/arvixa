<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Services;
use App\Rules\CheckUnique;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use App\Models\MainDistributor;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Common\LedgerController;
use App\Http\Controllers\Common\ServicesLogController;

class MainDistributorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MainDistributor::select('id', 'name', 'email', 'slug', 'userId', 'mobile', 'image', 'status', 'user_balance', 'created_at');
            return Datatables::of($data)->addIndexColumn()
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('name', function ($row) {
                    return '<b class="text-dark">' . $row['name'] . '</b><br /> <span class="text-secondary">( Balance : ₹ ' . $row['user_balance'] . ')<span>';
                })
                ->editColumn('email', function ($row) {
                    return '<b class="text-dark">' . $row['email'] . '</b><br /> <span class="text-danger">' . $row['userId'] . '<span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('main_distributors.edit', $row['slug']) . '">Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('main_distributors.services', $row['slug']) . '">Services</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('distributors', ['main_distributor' => $row->id]) . '">Distributors</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('retailers', ['main_distributor' => $row->id]) . '">Retailers</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('main_distributors.ledger', $row['slug']) . '">Ledger</a>';
                    }
                    if (userCan(104, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(104)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'name', 'email', 'status', 'user_balance'])
                ->make(true);
        }
        return view('main_distributors.index');
    }

    public function add()
    {
        return view('main_distributors.add');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'status'    => ['required', 'integer'],
            'email'     => ['required', new CheckUnique('main_distributors')],
            'mobile'    => ['required', 'digits:10', new CheckUnique('main_distributors'),  'regex:' . config('constant.phoneRegExp')],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $data = [
            'slug'      => Str::uuid(),
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

        $data = MainDistributor::create($data);
        SendWelComeEmail::dispatch($data, $request->site_settings);
        return redirect(route('main_distributors'))->with('success', 'Main Distributor Added Successfully!!');
    }

    public function edit($id)
    {
        $main_distributor = MainDistributor::firstWhere('slug', $id);
        if ($main_distributor == null) {
            return redirect(route('main_distributors'))->with('error', 'Main Distributor Not Found!!');
        }
        return view('main_distributors.edit', compact(['main_distributor']));
    }

    public function update(Request $request, $id)
    {
        $main_distributor = MainDistributor::firstWhere('id', $id);
        if ($main_distributor == null) {
            return redirect(route('main_distributors'))->with('error', 'Main Distributor Not Found!!');
        }

        $validated = [
            'status'    => ['required', 'integer'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required',  new CheckUnique('main_distributors', $main_distributor['id'])],
            'mobile'    => ['required', 'digits:10', new CheckUnique('main_distributors', $main_distributor['id']), 'regex:' . config('constant.phoneRegExp')],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ];

        if ($request['password']) {
            $validated['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $request->validate($validated);
        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'mobile'    => $request->mobile,
            'status'    => $request->status,
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

        $main_distributor->update($data);
        return redirect(route('main_distributors'))->with('success', 'Main Distributor Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $main_distributor = MainDistributor::where('id', $request->id)->first();
            if ($main_distributor == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Main Distributor Not Found.',
                ]);
            }

            $main_distributor->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Main Distributor deleted Successfully',
            ]);
        }
    }

    public function services($slug)
    {
        $main_distributor = MainDistributor::firstWhere('slug', $slug);
        if ($main_distributor == null) {
            return redirect(route('main_distributors'))->with('error', 'Main Distributor Not Found!!');
        }

        $services = Services::select('services.id', 'services.name', 'assign_date')
            ->leftJoin('services_logs', function ($join) use ($main_distributor) {
                $join->on('services.id', '=', 'services_logs.service_id')
                    ->where('services_logs.status', 1)
                    ->where('services_logs.user_type', 2)
                    ->where('services_logs.user_id', $main_distributor['id']);
            })
            ->where('services.status', 1)
            ->orderBy('services.id', 'asc')
            ->get();

        return view('main_distributors.services', compact(['main_distributor', 'services']));
    }

    public function services_update(Request $request, $slug)
    {
        $main_distributor = MainDistributor::firstWhere('slug', $slug);
        if (!$main_distributor) {
            return response()->json([
                'status'   => false,
                'message'   => 'Main Distributor Not Found.',
            ]);
        }

        if (in_array($request->service_id, ['on', 'off'])) {
            $services = Services::select('id')->where('services.status', 1)->get();
            foreach ($services as $key => $value) {
                ServicesLogController::update($value['id'],  $main_distributor['id'],  2, $request->service_id);
            }
        } else {
            ServicesLogController::update($request->service_id,  $main_distributor['id'],  2);
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Service Updated Successfully',
            'data'      => []
        ]);
    }

    public function ledger(Request $request, $slug)
    {
        $user = MainDistributor::firstWhere('slug', $slug);
        if ($user == null) {
            return redirect(route('main_distributors'))->with('error', 'Main Distributor Not Found!!');
        }

        if ($request->ajax()) {
            $data = Ledger::select('id', 'voucher_no', 'particulars', 'amount', 'current_balance',  'updated_balance', 'payment_type', 'payment_method', 'created_at')
                ->where('user_id', $user['id'])
                ->where('user_type', 2);

            return LedgerController::getDataTable($data);
        }

        return view('main_distributors.ledger', compact('user'));
    }

    public function ledger_add(Request $request, $slug)
    {
        $user = MainDistributor::firstWhere('slug', $slug);
        if (!$user) {
            return response()->json([
                'status'    => false,
                'message'   => 'Main Distributor Not Found.',
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

        if (LedgerController::add($user['id'], 2, $data)) {
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
        $main_distributor = MainDistributor::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('MainDistributor List', true);

        $sheet->setCellValue('A1', 'User Id');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Mobile');
        $sheet->setCellValue('E1', 'User Balance');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Register Date');

        $rows = 2;
        foreach ($main_distributor as $row) {
            $sheet->setCellValue('A' . $rows, $row->userId);
            $sheet->setCellValue('B' . $rows, $row->name);
            $sheet->setCellValue('C' . $rows, $row->email);
            $sheet->setCellValue('D' . $rows, $row->mobile);
            $sheet->setCellValue('E' . $rows, $row->user_balance);
            $sheet->setCellValue('F' . $rows, $row->status == 1 ? "Active" : "InActive");
            $sheet->setCellValue('G' . $rows, $row->created_at->format('d F, Y'));
            $rows++;
        }

        $sheet->getStyle('D1:D' . $rows)->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('E1:E' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        $sheet->getStyle('A1:E' . $rows)->getAlignment()->setHorizontal('center');

        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);

        // AutoWidth Column
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $fileName = "MainDistributors.xlsx";
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }
}
