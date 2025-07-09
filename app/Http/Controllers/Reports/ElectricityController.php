<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Common\LedgerController;
use Illuminate\Http\Request;
use App\Models\Bill;
use Illuminate\Support\Carbon;
use \Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class ElectricityController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '512M');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Bill::select('bills.id', 'bills.transaction_id', 'bills.consumer_name', 'bills.consumer_no', 'bills.bill_no', 'due_date', 'bills.created_at', 'bills.bill_amount', 'bills.commission', 'bills.tds', 'bills.bu_code', 'bills.status', 'bills.remark', 'providers.name as provider_name', 'retailers.name as retailer_name', 'retailers.mobile as retailer_userId');
            $query->where('bills.bill_type', 'electricity');
            $query->join('retailers', 'retailers.id', 'bills.user_id');
            $query->join('providers', 'providers.id', 'bills.board_id');

            if (request('start_date') && request('end_date')) {
                $startDate = Carbon::parse(request('start_date'));
                $endDate = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->eq($endDate)) {
                    $startDateStr = $startDate->format('Y-m-d');
                    $query->whereDate('bills.created_at', $startDateStr);
                } else {
                    $query->whereBetween('bills.created_at', [$startDate, $endDate]);
                }
            }

            if ($request->filled('status')) $query->where('bills.status', $request->get('status'));
            if ($request->filled('provider')) $query->where('bills.board_id', $request->get('provider'));

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('transaction_id', function ($row) {
                    return '<div class="fw-bold">' . $row['transaction_id'] . '</div><small class="text-info">' . $row['created_at']->format('d M, Y') . '</small>';
                })
                ->editColumn('retailer_name', function ($row) {
                    return '<div class="fw-bold">' . $row['retailer_name'] . '</div><small  class="text-primary">' . $row['retailer_userId'] . '</small>';
                })
                ->addColumn('provider_name', function ($row) {
                    return '<div class="td-table small fw-bold">' . trim($row->provider_name) . '</div>';
                })
                ->editColumn('consumer_no', function ($row) {
                    return '<div class="fw-bold">KNo : ' . $row['consumer_no'] . '</div><div  class="text-success fw-bold small">Due Date : ' . date('d F, Y', strtotime($row['due_date'])) . '</div><div  class="text-primary fw-bold small">Bill Amount : ₹' . $row['bill_amount'] . '</div>';
                })
                ->editColumn('commission', function ($row) {
                    return  '<div class="text-success small fw-semibold">Commission : ₹ ' . $row['commission'] . ' </div><div class="text-primary small fw-semibold">TDS : ₹ ' . $row['tds'] . ' </div>';
                })
                ->editColumn('status', function ($row) {
                    $btn = '';
                    if ($row['status'] == 2) {
                        $btn .= '<span class="badge badge-light-danger">Cancelled</span>';
                    } else if ($row['status'] == 1) {
                        $btn .= '<span class="badge badge-light-success">Success</span>';
                    } else {
                        $btn .= '<span class="badge badge-light-primary">Pending</span>';
                    }

                    $btn .= '<p class="small"> Remark : ' . str($row['remark'] ?? 'N/A')->limit(20) . '</p>';
                    return  $btn;
                })
                ->addColumn('action', function ($row) {
                    return '<button id="btndefault" type="button" class="btn btn-outline-dark action" data-all="' . htmlspecialchars(json_encode($row))  . '">Action</button>';
                })
                ->editColumn('bu_code', function ($row) {
                    return  $row['bu_code'] ?? '--';
                })
                ->rawColumns(['transaction_id', 'provider_name', 'consumer_no', 'commission', 'status', 'action', 'retailer_name'])
                ->make(true);
        }

        $providers = Provider::where('type', 'electricity')->get();
        return view('reports.electricity-bill.index', compact('providers'));
    }

    public function waterbill(Request $request)
    {
        if ($request->ajax()) {

            $query = Bill::select('bills.id', 'bills.transaction_id', 'bills.consumer_name', 'bills.consumer_no', 'bills.bill_no', 'due_date', 'bills.created_at', 'bills.bill_amount', 'bills.commission', 'bills.tds', 'bills.bu_code', 'bills.status', 'bills.remark', 'providers.name as provider_name', 'retailers.name as retailer_name', 'retailers.mobile as retailer_userId');
            $query->where('bills.bill_type', 'water');
            $query->join('retailers', 'retailers.id', 'bills.user_id');
            $query->join('providers', 'providers.id', 'bills.board_id');

            if (request('start_date') && request('end_date')) {
                $startDate = Carbon::parse(request('start_date'));
                $endDate = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->eq($endDate)) {
                    $startDateStr = $startDate->format('Y-m-d');
                    $query->whereDate('bills.created_at', $startDateStr);
                } else {
                    $query->whereBetween('bills.created_at', [$startDate, $endDate]);
                }
            }

            if ($request->filled('status')) $query->where('bills.status', $request->get('status'));
            if ($request->filled('provider')) $query->where('bills.board_id', $request->get('provider'));

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('transaction_id', function ($row) {
                    return '<div class="fw-bold">' . $row['transaction_id'] . '</div><small class="text-info">' . $row['created_at']->format('d M, Y') . '</small>';
                })
                ->editColumn('retailer_name', function ($row) {
                    return '<div class="fw-bold">' . $row['retailer_name'] . '</div><small  class="text-primary">' . $row['retailer_userId'] . '</small>';
                })
                ->addColumn('provider_name', function ($row) {
                    return '<div class="td-table small fw-bold">' . trim($row->provider_name) . '</div>';
                })
                ->editColumn('consumer_no', function ($row) {
                    return '<div class="fw-bold">KNo : ' . $row['consumer_no'] . '</div><div  class="text-success fw-bold small">Due Date : ' . date('d F, Y', strtotime($row['due_date'])) . '</div><div  class="text-primary fw-bold small">Bill Amount : ₹' . $row['bill_amount'] . '</div>';
                })
                ->editColumn('commission', function ($row) {
                    return  '<div class="text-success small fw-semibold">Commission : ₹ ' . $row['commission'] . ' </div><div class="text-primary small fw-semibold">TDS : ₹ ' . $row['tds'] . ' </div>';
                })
                ->editColumn('status', function ($row) {
                    $btn = '';
                    if ($row['status'] == 2) {
                        $btn .= '<span class="badge badge-light-danger">Cancelled</span>';
                    } else if ($row['status'] == 1) {
                        $btn .= '<span class="badge badge-light-success">Success</span>';
                    } else {
                        $btn .= '<span class="badge badge-light-primary">Pending</span>';
                    }

                    $btn .= '<p class="small"> Remark : ' . str($row['remark'] ?? 'N/A')->limit(20) . '</p>';
                    return  $btn;
                })
                ->addColumn('action', function ($row) {
                    return '<button id="btndefault" type="button" class="btn btn-outline-dark action" data-all="' . htmlspecialchars(json_encode($row))  . '">Action</button>';
                })
                ->rawColumns(['transaction_id', 'provider_name', 'consumer_no', 'commission', 'status', 'action', 'retailer_name'])
                ->make(true);
        }

        $providers = Provider::where('type', 'water')->get();
        return view('reports.water-bill.index', compact('providers'));
    }

    public function gasbill(Request $request)
    {
        if ($request->ajax()) {

            $query = Bill::select('bills.id', 'bills.transaction_id', 'bills.consumer_name', 'bills.consumer_no', 'bills.bill_no', 'due_date', 'bills.created_at', 'bills.bill_amount', 'bills.commission', 'bills.tds', 'bills.bu_code', 'bills.status', 'bills.remark', 'providers.name as provider_name', 'retailers.name as retailer_name', 'retailers.mobile as retailer_userId');
            $query->where('bills.bill_type', 'gas');
            $query->join('retailers', 'retailers.id', 'bills.user_id');
            $query->join('providers', 'providers.id', 'bills.board_id');

            if (request('start_date') && request('end_date')) {
                $startDate = Carbon::parse(request('start_date'));
                $endDate = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->eq($endDate)) {
                    $startDateStr = $startDate->format('Y-m-d');
                    $query->whereDate('bills.created_at', $startDateStr);
                } else {
                    $query->whereBetween('bills.created_at', [$startDate, $endDate]);
                }
            }

            if ($request->filled('status')) $query->where('bills.status', $request->get('status'));
            if ($request->filled('provider')) $query->where('bills.board_id', $request->get('provider'));

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('transaction_id', function ($row) {
                    return '<div class="fw-bold">' . $row['transaction_id'] . '</div><small class="text-info">' . $row['created_at']->format('d M, Y') . '</small>';
                })
                ->editColumn('retailer_name', function ($row) {
                    return '<div class="fw-bold">' . $row['retailer_name'] . '</div><small  class="text-primary">' . $row['retailer_userId'] . '</small>';
                })
                ->addColumn('provider_name', function ($row) {
                    return '<div class="td-table small fw-bold">' . trim($row->provider_name) . '</div>';
                })
                ->editColumn('consumer_no', function ($row) {
                    return '<div class="fw-bold">KNo : ' . $row['consumer_no'] . '</div><div  class="text-success fw-bold small">Due Date : ' . date('d F, Y', strtotime($row['due_date'])) . '</div><div  class="text-primary fw-bold small">Bill Amount : ₹' . $row['bill_amount'] . '</div>';
                })
                ->editColumn('commission', function ($row) {
                    return  '<div class="text-success small fw-semibold">Commission : ₹ ' . $row['commission'] . ' </div><div class="text-primary small fw-semibold">TDS : ₹ ' . $row['tds'] . ' </div>';
                })
                ->editColumn('status', function ($row) {
                    $btn = '';
                    if ($row['status'] == 2) {
                        $btn .= '<span class="badge badge-light-danger">Cancelled</span>';
                    } else if ($row['status'] == 1) {
                        $btn .= '<span class="badge badge-light-success">Success</span>';
                    } else {
                        $btn .= '<span class="badge badge-light-primary">Pending</span>';
                    }

                    $btn .= '<p class="small"> Remark : ' . str($row['remark'] ?? 'N/A')->limit(20) . '</p>';
                    return  $btn;
                })
                ->addColumn('action', function ($row) {
                    return '<button id="btndefault" type="button" class="btn btn-outline-dark action" data-all="' . htmlspecialchars(json_encode($row))  . '">Action</button>';
                })
                ->rawColumns(['transaction_id', 'provider_name', 'consumer_no', 'commission', 'status', 'action', 'retailer_name'])
                ->make(true);
        }

        $providers = Provider::where('type', 'gas')->get();
        return view('reports.gas-bill.index', compact('providers'));
    }

    public function licbill(Request $request)
    {
        if ($request->ajax()) {

            $query = Bill::select('bills.id', 'bills.transaction_id', 'bills.consumer_name', 'bills.consumer_no', 'bills.bill_no', 'due_date', 'bills.created_at', 'bills.bill_amount', 'bills.commission', 'bills.tds', 'bills.bu_code', 'bills.status', 'bills.remark', 'providers.name as provider_name', 'retailers.name as retailer_name', 'retailers.mobile as retailer_userId');
            $query->where('bills.bill_type', 'lic');
            $query->join('retailers', 'retailers.id', 'bills.user_id');
            $query->join('providers', 'providers.id', 'bills.board_id');

            if (request('start_date') && request('end_date')) {
                $startDate = Carbon::parse(request('start_date'));
                $endDate = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->eq($endDate)) {
                    $startDateStr = $startDate->format('Y-m-d');
                    $query->whereDate('bills.created_at', $startDateStr);
                } else {
                    $query->whereBetween('bills.created_at', [$startDate, $endDate]);
                }
            }

            if ($request->filled('status')) $query->where('bills.status', $request->get('status'));
            if ($request->filled('provider')) $query->where('bills.board_id', $request->get('provider'));

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('transaction_id', function ($row) {
                    return '<div class="fw-bold">' . $row['transaction_id'] . '</div><small class="text-info">' . $row['created_at']->format('d M, Y') . '</small>';
                })
                ->editColumn('retailer_name', function ($row) {
                    return '<div class="fw-bold">' . $row['retailer_name'] . '</div><small  class="text-primary">' . $row['retailer_userId'] . '</small>';
                })
                ->addColumn('provider_name', function ($row) {
                    return '<div class="td-table small fw-bold">' . trim($row->provider_name) . '</div>';
                })
                ->editColumn('consumer_no', function ($row) {
                    return '<div class="fw-bold">KNo : ' . $row['consumer_no'] . '</div><div  class="text-success fw-bold small">Due Date : ' . date('d F, Y', strtotime($row['due_date'])) . '</div><div  class="text-primary fw-bold small">Bill Amount : ₹' . $row['bill_amount'] . '</div>';
                })
                ->editColumn('commission', function ($row) {
                    return  '<div class="text-success small fw-semibold">Commission : ₹ ' . $row['commission'] . ' </div><div class="text-primary small fw-semibold">TDS : ₹ ' . $row['tds'] . ' </div>';
                })
                ->editColumn('status', function ($row) {
                    $btn = '';
                    if ($row['status'] == 2) {
                        $btn .= '<span class="badge badge-light-danger">Cancelled</span>';
                    } else if ($row['status'] == 1) {
                        $btn .= '<span class="badge badge-light-success">Success</span>';
                    } else {
                        $btn .= '<span class="badge badge-light-primary">Pending</span>';
                    }

                    $btn .= '<p class="small"> Remark : ' . str($row['remark'] ?? 'N/A')->limit(20) . '</p>';
                    return  $btn;
                })
                ->addColumn('action', function ($row) {
                    return '<button id="btndefault" type="button" class="btn btn-outline-dark action" data-all="' . htmlspecialchars(json_encode($row))  . '">Action</button>';
                })
                ->rawColumns(['transaction_id', 'provider_name', 'consumer_no', 'commission', 'status', 'action', 'retailer_name'])
                ->make(true);
        }

        $providers = Provider::where('type', 'lic')->get();
        return view('reports.lic-bill.index', compact('providers'));
    }

    public function export(Request $request, $type)
    {
        $query = Bill::select('bills.id', 'bills.transaction_id', 'bills.consumer_name', 'bills.consumer_no', 'bills.bill_no', 'due_date', 'bills.created_at', 'bills.bill_amount', 'bills.commission', 'bills.tds', 'bills.bu_code', 'bills.status', 'bills.remark', 'providers.name as provider_name', 'retailers.name as retailer_name', 'retailers.mobile as retailer_userId');
        $query->where('bills.bill_type', $type);
        $query->join('retailers', 'retailers.id', 'bills.user_id');
        $query->join('providers', 'providers.id', 'bills.board_id');

        if (request('start_date') && request('end_date')) {
            $startDate = Carbon::parse(request('start_date'));
            $endDate = Carbon::parse(request('end_date'))->endOfDay();
            if ($startDate->diffInDays($endDate) > 90) {
                return back()->withInput()->with('error', "Report can be exported for max 90 Days.");
            }
        } else {
            $startDate  = Carbon::now()->startOfDay()->subDays(7);
            $endDate    = Carbon::now();
        }

        if ($startDate->eq($endDate)) {
            $startDateStr = $startDate->format('Y-m-d');
            $query->whereDate('bills.created_at', $startDateStr);
        } else {
            $query->whereBetween('bills.created_at', [$startDate, $endDate]);
        }

        if ($request->filled('status')) $query->where('bills.status', $request->get('status'));
        if ($request->filled('provider')) $query->where('bills.board_id', $request->get('provider'));

        // Start Building Excel Sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(ucfirst($type) . ' Bill List');

        $sheet->setCellValue('A1', 'Transaction Id');
        $sheet->setCellValue('B1', 'Transaction Date');
        $sheet->setCellValue('C1', 'Retailer Name');
        $sheet->setCellValue('D1', 'Retailer Mobile');
        $sheet->setCellValue('E1', 'Provider');
        $sheet->setCellValue('F1', 'Bill No/K.No');
        $sheet->setCellValue('G1', 'Due Date');
        $sheet->setCellValue('H1', 'Bill Amount');
        $sheet->setCellValue('I1', 'Profit');
        $sheet->setCellValue('J1', 'TDS');
        $sheet->setCellValue('K1', 'Status ');
        $sheet->setCellValue('L1', 'Remark');

        $rows = 2;
        foreach ($query->get() as $row) {
            $bu_code = $row->bu_code ? " (BU : $row->bu_code)" : '';

            $sheet->setCellValue('A' . $rows, $row->transaction_id);
            $sheet->setCellValue('B' . $rows, Date::PHPToExcel($row->created_at));
            $sheet->setCellValue('C' . $rows, $row->retailer_name);
            $sheet->setCellValue('D' . $rows, $row->retailer_userId);
            $sheet->setCellValue('E' . $rows, $row->provider_name . $bu_code);
            $sheet->setCellValue('F' . $rows, $row->consumer_no);
            $sheet->setCellValue('G' . $rows, Date::PHPToExcel($row->due_date));
            $sheet->setCellValue('H' . $rows, $row->bill_amount);
            $sheet->setCellValue('I' . $rows, $row->commission);
            $sheet->setCellValue('J' . $rows, $row->tds);
            $sheet->setCellValue('K' . $rows, $row->status == 1 ? 'Success' : ($row->status == 2 ? "Cancelled" : 'Pending'));
            $sheet->setCellValue('L' . $rows, $row->remark ?? '--');
            $rows++;
        }

        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('bbbbbb');

        // AutoWidth Column
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $sheet->getStyle('A1:L' . $rows)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('D1:F' . $rows)->getNumberFormat()->setFormatCode('#');
        $sheet->getStyle('H1:J' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        $sheet->getStyle('B1:B' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('G1:G' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $spreadsheet->setActiveSheetIndex(0);
        $fileName = ucfirst($type) . " Bill Export.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . ($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }

    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status'    => ['required', 'integer', 'in:1,2'],
            'remark'    => ['required', 'string', 'min:2', 'max:100'],
        ]);

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
        } else {

            $bill = Bill::find($request->get('id'));
            if (!$bill)  return response()->json([
                'status'    => false,
                'message'   => "Invalid Bill id..!!",
            ]);

            $bill->update(['remark' => $request->remark]);
            if (in_array($bill->status, [1, 2]))  return response()->json([
                'status'    => false,
                'message'   => "Bill already submitted..!!",
            ]);

            if ($request->get('status') == 1) {
                $bill->update($validator->validated());
            }

            if ($request->get('status') == 2) {
                $service_id = 0;
                if ($bill->bill_type === 'electricity') {
                    $service_id = config('constant.service_ids.electricity_bill');
                } else if ($bill->bill_type === 'water') {
                    $service_id = config('constant.service_ids.water_bill');
                } else if ($bill->bill_type === 'lic') {
                    $service_id = config('constant.service_ids.lic_premium');
                } else if ($bill->bill_type === 'gas') {
                    $service_id = config('constant.service_ids.gas_payment');
                }

                $serviceLog = ServiceUsesLog::where([
                    'request_id'                    => $bill->id,
                    'user_id'                       => $bill->user_id,
                    'user_type'                     => 4,
                    'customer_id'                   => 0,
                    'is_refunded'                   => 0,
                    'service_id'                    => $service_id,
                ])->first();

                if (!$serviceLog) {
                    return response()->json([
                        'status'    => false,
                        'message'   => "Invalid Transaction id..!!",
                    ]);
                }

                LedgerController::refundForBillPayment($bill, $serviceLog);
            }

            return response()->json([
                'status'    => true,
                'message'   => "Bill Updated Successfully..!!",
            ]);
        }
    }
}
