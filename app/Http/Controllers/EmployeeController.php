<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PanCard;
use App\Rules\CheckUnique;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Employee::select('id', 'name', 'email', 'designation_id', 'slug', 'mobile', 'image', 'status', 'created_at');
            return Datatables::of($data)->addIndexColumn()
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('name', function ($row) {
                    return '<b class="text-dark">' . $row['name'] . '</b><br /> <span class="text-secondary">(' . config('constant.designation_list.' . $row['designation_id'], '') . ')<span>';
                })
                ->editColumn('email', function ($row) {
                    return '<b class="text-dark">' . $row['email'] . '</b><br /> <b class="text-dark">' . $row['mobile'] . '<span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(116, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('employees.edit', $row['slug']) . '">Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('retailers', ['employee' => $row->id]) . '">Retailers</a>';
                    }
                    if (userCan(116, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(116)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'email', 'name', 'image', 'status'])
                ->make(true);
        }
        return view('employees.index');
    }

    public function add()
    {
        return view('employees.add');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'status'            => ['required', 'integer'],
            'designation_id'    => ['required', 'integer'],
            'email'             => ['required', new CheckUnique('employees')],
            'mobile'            => ['required', 'digits:10', new CheckUnique('employees'), 'regex:' . config('constant.phoneRegExp')],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'image'             => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $data = [
            'slug'              => Str::uuid(),
            'designation_id'    => $request->designation_id,
            'name'              => $request->name,
            'email'             => $request->email,
            'mobile'            => $request->mobile,
            'status'            => $request->status,
            'image'             => 'admin/avatar.png',
            'password'          => Hash::make($request['password']),
        ];

        $path = 'admin';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        Employee::create($data);
        return redirect(route('employees'))->with('success', 'Employee Added Successfully!!');
    }

    public function edit($id)
    {
        $employee = Employee::firstWhere('slug', $id);
        if ($employee == null) {
            return redirect(route('employees'))->with('error', 'Employee Not Found!!');
        }
        return view('employees.edit', compact(['employee']));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::firstWhere('id', $id);
        if ($employee == null) {
            return redirect(route('employees'))->with('error', 'Employee Not Found!!');
        }

        $validated = [
            'designation_id'   => ['required'],
            'status'    => ['required', 'integer'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', new CheckUnique('employees', $employee->id)],
            'mobile'    => ['required', 'digits:10', new CheckUnique('employees', $employee->id), 'regex:' . config('constant.phoneRegExp')],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ];

        if ($request['password']) {
            $validated['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $request->validate($validated);

        $data = [
            'designation_id'    => $request->designation_id,
            'name'              => $request->name,
            'email'             => $request->email,
            'mobile'            => $request->mobile,
            'status'            => $request->status,
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

        $employee->update($data);
        return redirect(route('employees'))->with('success', 'Employee Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $employee = Employee::where('id', $request->id)->first();
            if ($employee == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Employee Not Found.',
                ]);
            }

            $employee->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Employee deleted Successfully',
            ]);
        }
    }

    public function export()
    {
        $employees = Employee::with('retailer')->get();
        $panCards = PanCard::whereNotNull('nsdl_ack_no')->where('nsdl_complete', 1)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('MainDistributor List', true);

        $sheet->setCellValue('A1', 'User Id');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Mobile');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Register Date');
        $sheet->setCellValue('G1', "Retailer's Count");
        $sheet->setCellValue('H1', "Retailer's Total Balance");
        $sheet->setCellValue('I1', "Retailer's Total PanCards");

        $rows = 2;
        foreach ($employees as $row) {
            $sheet->setCellValue('A' . $rows, $row->userId);
            $sheet->setCellValue('B' . $rows, $row->name);
            $sheet->setCellValue('C' . $rows, $row->email);
            $sheet->setCellValue('D' . $rows, $row->mobile);
            $sheet->setCellValue('E' . $rows, $row->status == 1 ? "Active" : "InActive");
            $sheet->setCellValue('F' . $rows, $row->created_at->format('d F, Y'));
            $sheet->setCellValue('G' . $rows, $row->retailer->count());
            $sheet->setCellValue('H' . $rows, $row->retailer->sum('user_balance'));
            $sheet->setCellValue('I' . $rows, $panCards->whereIn('user_id', $row->retailer->pluck('id')->toArray())->where('user_type', 4)->count());
            $rows++;
        }

        $sheet->getStyle('D1:D' . $rows)->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('A1:I' . $rows)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('H1:H' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');

        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);

        // AutoWidth Column
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $fileName = "Employees.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }
}
