<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Customer;
use Illuminate\Support\Str;
use App\Models\CustomerBank;
use Illuminate\Http\Request;
use App\Models\ServiceUsesLog;
use App\Models\CustomerDocument;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('customer_find');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::select('id', 'first_name', 'middle_name', 'last_name', 'email', 'slug', 'mobile', 'image', 'status', 'created_at');
            return Datatables::of($query)->addIndexColumn()
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $sql = "CONCAT(customers.first_name, ' ', customers.last_name) like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->editColumn('email', function ($row) {
                    return '<span class="text-dark">' . $row['email'] . '</span><br /> <span class="text-dark">' . $row['mobile'] . '<span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(112, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('customers.edit', $row['slug']) . '">Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('customers.documents', $row['slug']) . '">Documents</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('customers.banks', $row['slug']) . '">Banks</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('customers.service_used', $row['slug']) . '">Service Used</a>';
                    }

                    if (userCan(112, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(112)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->whereRaw('CONCAT(first_name, " ", middle_name, " ", last_name) like ?', ["%{$keyword}%"]);
                // })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'email', 'status'])
                ->make(true);
        }
        return view('customers.index');
    }

    public function add()
    {
        $states = State::where('status', 1)->where('id', '!=', 1)->get();
        return view('customers.add', compact('states'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => ['nullable', 'string', 'max:100'],
            'middle_name'   => ['nullable', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'status'        => ['required', 'integer'],
            'state_id'      => ['nullable', 'integer'],
            'city_id'       => ['nullable', 'integer'],
            'dob'           => ['nullable', 'date'],
            'gender'        => ['nullable', 'integer'],
            'email'         => ['required', 'unique:customers,email'],
            'mobile'        => ['required', 'digits:10', 'unique:customers,mobile', 'regex:' . config('constant.phoneRegExp')],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $data = [
            'first_name'    => $request->first_name,
            'middle_name'   => $request->middle_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'mobile'        => $request->mobile,
            'status'        => $request->status,
            'state_id'      => $request->state_id,
            'city_id'       => $request->city_id,
            'dob'           => $request->dob,
            'gender'        => $request->gender,
        ];

        $path = 'customer';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        $customer = Customer::create($data);
        return redirect(route('customers'))->with('success', 'Customer Added Successfully!!');
    }

    public function edit($id)
    {
        $customer = Customer::firstWhere('slug', $id);
        if ($customer == null) {
            return redirect(route('customers'))->with('error', 'Customer Not Found!!');
        }
        $states = State::where('status', 1)->where('id', '!=', 1)->get();
        return view('customers.edit', compact('customer', 'states'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::firstWhere('id', $id);
        if ($customer == null) {
            return redirect(route('customers'))->with('error', 'Customer Not Found!!');
        }

        $validated = [
            'status'        => ['required', 'integer'],
            'first_name'    => ['nullable', 'string', 'max:100'],
            'middle_name'   => ['nullable', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'state_id'      => ['nullable', 'integer'],
            'city_id'       => ['nullable', 'integer'],
            'dob'           => ['nullable', 'date'],
            'gender'        => ['nullable', 'integer'],
            'email'         => ['required', 'unique:customers,email,' . $customer['id']],
            'mobile'        => ['required', 'digits:10', 'unique:customers,mobile,' . $customer['id'], 'regex:' . config('constant.phoneRegExp')],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ];

        $request->validate($validated);
        $data = [
            'first_name'    => $request->first_name,
            'middle_name'   => $request->middle_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'mobile'        => $request->mobile,
            'state_id'      => $request->state_id,
            'city_id'       => $request->city_id,
            'dob'           => $request->dob,
            'gender'        => $request->gender,
            'status'        => $request->status,
        ];

        $path = 'customer';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        $customer->update($data);
        return redirect(route('customers'))->with('success', 'Customer Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $customer = Customer::where('id', $request->id)->first();
            if ($customer == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Customer Not Found.',
                ]);
            }

            $customer->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'Customer deleted Successfully',
            ]);
        }
    }

    public function documents(Request $request, $id = null)
    {
        $customer = Customer::where('slug', $id)->with('documents')->first();
        if ($customer == null) {
            return redirect(route('customers'))->with('error', 'Customer Not Found!!');
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'doc_type'          => ['required', 'integer'],
                'doc_number'        => ['required', 'string', 'max:200'],
                'doc_img_front'     => ['mimes:jpg,png,jpeg,pdf', 'max:2048'],
                'doc_img_back'      => ['mimes:jpg,png,jpeg,pdf', 'max:2048'],
            ]);

            $data = [
                'customer_id'   => $customer->id,
                'doc_type'      => $request->doc_type,
                'doc_number'    => $request->doc_number,
            ];

            $path = 'documents';
            if ($file = $request->file('doc_img_front')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $data['doc_img_front']        = $path . '/' . $uploadImage;
            }

            if ($file = $request->file('doc_img_back')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $data['doc_img_back']        = $path . '/' . $uploadImage;
            }

            $exist = $customer->documents->pluck('doc_type')->toArray();
            if (in_array($request->doc_type, $exist)) {
                $record = CustomerDocument::where('customer_id', $customer->id)->where('doc_type', $request->doc_type)->first();
                if ($record) {
                    if (!empty($data['doc_img_front'])) removeFile($record->doc_img_front);
                    if (!empty($data['doc_img_back'])) removeFile($record->doc_img_back);
                    $record->update($data);
                }
            } else {
                CustomerDocument::create($data);
            }

            return redirect(route('customers.documents', $customer->slug))->with('success', 'Document Uploaded Successfully!!');
        }

        if ($request->delete) {
            $record = CustomerDocument::where('id', $request->delete)->where('customer_id', $customer->id)->first();
            if ($record) {
                removeFile($record->doc_img_front);
                removeFile($record->doc_img_back);
                $record->delete();
            }
            return redirect(route('customers.documents', $customer->slug))->with('success', 'Document Uploaded Successfully!!');
        }

        return view('customers.documents', compact('customer'));
    }

    public function banks(Request $request, $id = null)
    {
        $customer = Customer::where('slug', $id)->with('documents')->first();
        if ($customer == null) {
            return redirect(route('customers'))->with('error', 'Customer Not Found!!');
        }

        if ($request->isMethod('get') && $request->ajax()) {
            $data = CustomerBank::select('id', 'account_bank', 'account_name', 'account_number', 'account_ifsc')->where('customer_id', $customer->id);
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    $btn .= '<button class="dropdown-item edit" data-all="' . htmlentities(json_encode($row)) . '">Edit</button>';
                    $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    return $btn;
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        if ($request->isMethod('post')) {
            $validation = Validator::make($request->all(), [
                'account_bank'      => ['required', 'string', 'max:255'],
                'account_name'      => ['required', 'string', 'max:255'],
                'account_number'    => ['required', 'string', 'max:255'],
                'account_ifsc'      => ['required', 'string', 'max:255'],
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
                CustomerBank::create([
                    'customer_id'       => $customer->id,
                    'account_bank'      => $request->account_bank,
                    'account_name'      => $request->account_name,
                    'account_number'    => $request->account_number,
                    'account_ifsc'      => $request->account_ifsc,
                ]);

                return response()->json([
                    'success'   => true,
                    'message'   => 'Customer Bank Added Successfully',
                    'data'      => ''
                ]);
            }
        }

        if ($request->isMethod('put')) {
            $bank = CustomerBank::find($request->id);
            if ($bank) {
                $validation = Validator::make($request->all(), [
                    'account_bank'      => ['required', 'string', 'max:255'],
                    'account_name'      => ['required', 'string', 'max:255'],
                    'account_number'    => ['required', 'string', 'max:255'],
                    'account_ifsc'      => ['required', 'string', 'max:255'],
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
                    $bank->update([
                        'customer_id'       => $customer->id,
                        'account_bank'      => $request->account_bank,
                        'account_name'      => $request->account_name,
                        'account_number'    => $request->account_number,
                        'account_ifsc'      => $request->account_ifsc,
                    ]);

                    return response()->json([
                        'success'   => true,
                        'message'   => 'Customer Bank Updated Successfully',
                        'data'      => ''
                    ]);
                }
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Customer Bank Not Found.',
                    'data'      => ''
                ]);
            }
        }

        if ($request->isMethod('delete')) {
            $bank = CustomerBank::find($request->id);
            if ($bank) {
                $bank->delete();
                return response()->json([
                    'success'   => true,
                    'message'   => 'Customer Bank Deleted Successfully',
                    'data'      => ''
                ]);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Customer Bank Not Found.',
                    'data'      => ''
                ]);
            }
        }

        return view('customers.banks', compact('customer'));
    }

    public function customer_find(Request $request)
    {
        if (!request('mobile'))
            return response()->json([
                'status'    => false,
                'message'   => 'Please provide mobile number..!!',
                'data'      => ''
            ], 422);

        $customer = Customer::where('mobile', request('mobile'))->with('documents')->first();
        if ($customer == null)
            return response()->json([
                'status'    => false,
                'message'   => 'Customer Not Found!!',
                'data'      => ''
            ]);


        return response()->json([
            'status'    => true,
            'message'   => 'Customer Found.',
            'data'      => $customer
        ]);
    }

    public function service_used(Request $request, $slug)
    {
        $customer = Customer::firstWhere('slug', $slug);
        if ($customer == null) {
            return redirect(route('customers'))->with('error', 'Customer Not Found!!');
        }

        $user_type  = request('user_type', null);
        $user_id    = request('user_id', null);

        if ($request->ajax()) {
            $query = ServiceUsesLog::query();

            $query->with(['service:id,name', 'retailer:id,name', 'distributor:id,name', 'main_distributor:id,name']);
            $query->select('*');
            $query->where('customer_id', $customer->id);

            if ($user_type)  $query->where('user_type', $user_type);

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->addColumn('service', function (ServiceUsesLog $log) {
                    return $log->service->name;
                })
                ->addColumn('name', function (ServiceUsesLog $log) {
                    $out = '';
                    switch ($log->user_type) {
                        case 2:
                            $out = @$log->main_distributor->name;
                            break;
                        case 3:
                            $out = @$log->distributor->name;
                            break;
                        case 4:
                            $out = @$log->retailer->name;
                            break;
                        default:
                            $out = '...';
                            break;
                    }

                    return $out;
                })
                ->addColumn('used_by', function (ServiceUsesLog $log) {
                    $out = '';
                    switch ($log->user_type) {
                        case 2:
                            $out = 'MainDistributor';
                            break;
                        case 3:
                            $out = 'Distributor';
                            break;
                        case 4:
                            $out = 'Retailer';
                            break;
                        default:
                            $out = '...';
                            break;
                    }

                    return $out;
                })
                ->filterColumn('used_in', function ($query, $keyword) {
                    $keyword = strtolower($keyword);
                    $filter = [];
                    if (strpos('admin', $keyword) !== false) $filter[] = 1;
                    if (strpos('maindistributor', $keyword) !== false) $filter[] = 2;
                    if (strpos('distributor', $keyword) !== false) $filter[] = 3;
                    if (strpos('retailer', $keyword) !== false) $filter[] = 4;
                    if ($filter != []) $query->whereIn('user_type', $filter);
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }

        return view('customers.service_used', compact('user_type', 'user_id', 'customer'));
    }

    public function export()
    {
        $main_distributor = Customer::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customers List', true);

        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Mobile');
        $sheet->setCellValue('D1', 'Date of Birth');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Register Date');

        $rows = 2;
        foreach ($main_distributor as $row) {
            $sheet->setCellValue('A' . $rows, $row->name);
            $sheet->setCellValue('B' . $rows, $row->email);
            $sheet->setCellValue('C' . $rows, $row->mobile);
            $sheet->setCellValue('D' . $rows, $row->date_of_birth->format('d M, Y'));
            $sheet->setCellValue('E' . $rows, $row->status == 1 ? "Active" : "InActive");
            $sheet->setCellValue('F' . $rows, $row->created_at->format('d F, Y'));
            $rows++;
        }

        $sheet->getStyle('C1:C' . $rows)->getNumberFormat()->setFormatCode('@');

        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);

        // AutoWidth Column
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $fileName = "Customers.xlsx";
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }
}
