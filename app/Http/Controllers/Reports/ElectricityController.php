<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Common\LedgerController;
use Illuminate\Http\Request;
use App\Models\ElectricityBill;
use Illuminate\Support\Carbon;
use \Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\ServiceUsesLog;
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

            $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'electricity_bills.commission', 'electricity_bills.tds', 'electricity_bills.bu_code', 'electricity_bills.status', 'rproviders.name as provider_name', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
            $query->where('electricity_bills.bill_type', 'electricity');
            $query->join('retailers', 'retailers.id', 'electricity_bills.user_id');
            $query->join('rproviders', 'rproviders.id', 'electricity_bills.board_id');

            if (request('start_date') && request('end_date')) {
                $startDate = Carbon::parse(request('start_date'));
                $endDate = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->eq($endDate)) {
                    $startDateStr = $startDate->format('Y-m-d');
                    $query->whereDate('electricity_bills.created_at', $startDateStr);
                } else {
                    $query->whereBetween('electricity_bills.created_at', [$startDate, $endDate]);
                }
            }

            if ($request->filled('is_refunded')) {
                $query->where('electricity_bills.is_refunded', $request->get('is_refunded'));
            }

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('retailer_name', function ($row) {
                    return '<b>' . $row['retailer_name'] . '</b><br><b>' . $row['retailer_userId'] . '</b>';
                })
                ->editColumn('transaction_id', function ($row) {
                    return '<b>' . $row['transaction_id'] . '</b>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->addColumn('consumer_name', function ($row) {
                    return '<b>' . trim($row->consumer_name) . '</b>';
                })
                ->editColumn('bill_amount', function ($row) {
                    return  '<div class="text-primary">₹ ' . $row['bill_amount'] . '</div><small class="text-success">₹ ' . $row['commission'] . ' </small> | <small class="text-primary">₹ ' . $row['tds'] . ' </small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row['status'] == 1) {
                        $btn .= '<span class="badge badge-light-success mb-2 me-4">Paid</span>';
                    } else if ($row['status'] == 2) {
                        $btn .= '<span class="badge badge-light-danger">Cancelled</span>';
                        if ($row['is_refunded'] == 1)   $btn .= '<span class="badge badge-light-warning ms-1">Refunded</span>';
                    } else {
                        $btn .= '<button class="btn btn-sm btn-outline-success update-status me-1" data-id="' . $row['id'] . '" data-type="1">Paid</button>';
                        $btn .= '<button class="btn btn-sm btn-outline-danger update-status" data-id="' . $row['id'] . '" data-type="2">Cancel</button>';
                    }

                    return $btn;
                })
                ->editColumn('bu_code', function ($row) {
                    return  $row['bu_code'] ?? '--';
                })
                ->rawColumns(['transaction_id', 'consumer_name', 'bill_amount', 'action', 'retailer_name'])
                ->make(true);
        }

        return view('reports.electricity-bill.index');
    }

    public function waterbill(Request $request)
    {
        if ($request->ajax()) {

            $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'electricity_bills.commission', 'electricity_bills.tds', 'electricity_bills.status', 'rproviders.name as provider_name', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
            $query->where('electricity_bills.bill_type', 'water');
            $query->join('retailers', 'retailers.id', 'electricity_bills.user_id');
            $query->join('rproviders', 'rproviders.id', 'electricity_bills.board_id');

            if (request('start_date') && request('end_date')) {
                $startDate = Carbon::parse(request('start_date'));
                $endDate = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->eq($endDate)) {
                    $startDateStr = $startDate->format('Y-m-d');
                    $query->whereDate('electricity_bills.created_at', $startDateStr);
                } else {
                    $query->whereBetween('electricity_bills.created_at', [$startDate, $endDate]);
                }
            }

            if ($request->filled('is_refunded')) {
                $query->where('electricity_bills.is_refunded', $request->get('is_refunded'));
            }
            // dd($query->get()->toArray());
            return Datatables::of($query)->addIndexColumn()
                ->editColumn('retailer_name', function ($row) {
                    return '<b>' . $row['retailer_name'] . '</b><br><b>' . $row['retailer_userId'] . '</b>';
                })
                ->editColumn('transaction_id', function ($row) {
                    return '<b>' . $row['transaction_id'] . '</b>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->addColumn('consumer_name', function ($row) {
                    return '<div>' . trim($row->consumer_name) . '</div>';
                })
                ->editColumn('bill_amount', function ($row) {
                    return  '<div class="text-primary">₹ ' . $row['bill_amount'] . '</div><small class="text-success">₹ ' . $row['commission'] . ' </small> | <small class="text-primary">₹ ' . $row['tds'] . ' </small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row['status'] == 1) {
                        $btn .= '<span class="badge badge-light-success mb-2 me-4">Paid</span>';
                    } else if ($row['status'] == 2) {
                        $btn .= '<span class="badge badge-light-danger">Cancelled</span>';
                        if ($row['is_refunded'] == 1)   $btn .= '<span class="badge badge-light-warning ms-1">Refunded</span>';
                    } else {
                        $btn .= '<button class="btn btn-sm btn-outline-success update-status me-1" data-id="' . $row['id'] . '" data-type="1">Paid</button>';
                        $btn .= '<button class="btn btn-sm btn-outline-danger update-status" data-id="' . $row['id'] . '" data-type="2">Cancel</button>';
                    }

                    return $btn;
                })
                ->rawColumns(['transaction_id', 'consumer_name', 'bill_amount', 'action', 'retailer_name'])
                ->make(true);
        }
        $bills = [];
        return view('reports.water-bill.index', compact('bills'));
    }

    public function gasbill(Request $request)
    {
        if ($request->ajax()) {

            $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'electricity_bills.commission', 'electricity_bills.tds', 'electricity_bills.status', 'rproviders.name as provider_name', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
            $query->where('electricity_bills.bill_type', 'gas');
            $query->join('retailers', 'retailers.id', 'electricity_bills.user_id');
            $query->join('rproviders', 'rproviders.id', 'electricity_bills.board_id');

            if (request('start_date') && request('end_date')) {
                $startDate = Carbon::parse(request('start_date'));
                $endDate = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->eq($endDate)) {
                    $startDateStr = $startDate->format('Y-m-d');
                    $query->whereDate('electricity_bills.created_at', $startDateStr);
                } else {
                    $query->whereBetween('electricity_bills.created_at', [$startDate, $endDate]);
                }
            }

            if ($request->filled('is_refunded')) {
                $query->where('electricity_bills.is_refunded', $request->get('is_refunded'));
            }

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('retailer_name', function ($row) {
                    return '<b>' . $row['retailer_name'] . '</b><br><b>' . $row['retailer_userId'] . '</b>';
                })
                ->editColumn('transaction_id', function ($row) {
                    return '<b>' . $row['transaction_id'] . '</b>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->addColumn('consumer_name', function ($row) {
                    return '<b>' . trim($row->consumer_name) . '</b>';
                })
                ->editColumn('bill_amount', function ($row) {
                    return  '<div class="text-primary">₹ ' . $row['bill_amount'] . '</div><small class="text-success">₹ ' . $row['commission'] . ' </small> | <small class="text-primary">₹ ' . $row['tds'] . ' </small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row['status'] == 1) {
                        $btn .= '<span class="badge badge-light-success mb-2 me-4">Paid</span>';
                    } else if ($row['status'] == 2) {
                        $btn .= '<span class="badge badge-light-danger">Cancelled</span>';
                        if ($row['is_refunded'] == 1)   $btn .= '<span class="badge badge-light-warning ms-1">Refunded</span>';
                    } else {
                        $btn .= '<button class="btn btn-sm btn-outline-success update-status me-1" data-id="' . $row['id'] . '" data-type="1">Paid</button>';
                        $btn .= '<button class="btn btn-sm btn-outline-danger update-status" data-id="' . $row['id'] . '" data-type="2">Cancel</button>';
                    }

                    return $btn;
                })
                ->rawColumns(['transaction_id', 'consumer_name', 'bill_amount', 'action', 'retailer_name'])
                ->make(true);
        }

        return view('reports.gas-bill.index');
    }

    public function licbill(Request $request)
    {
        if ($request->ajax()) {

            $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'electricity_bills.commission', 'electricity_bills.tds', 'electricity_bills.status', 'rproviders.name as provider_name', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
            $query->where('electricity_bills.bill_type', 'lic');
            $query->join('retailers', 'retailers.id', 'electricity_bills.user_id');
            $query->join('rproviders', 'rproviders.id', 'electricity_bills.board_id');

            if (request('start_date') && request('end_date')) {
                $startDate = Carbon::parse(request('start_date'));
                $endDate = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->eq($endDate)) {
                    $startDateStr = $startDate->format('Y-m-d');
                    $query->whereDate('electricity_bills.created_at', $startDateStr);
                } else {
                    $query->whereBetween('electricity_bills.created_at', [$startDate, $endDate]);
                }
            }

            if ($request->filled('is_refunded')) {
                $query->where('electricity_bills.is_refunded', $request->get('is_refunded'));
            }

            return Datatables::of($query)->addIndexColumn()
                ->editColumn('retailer_name', function ($row) {
                    return '<b>' . $row['retailer_name'] . '</b><br><b>' . $row['retailer_userId'] . '</b>';
                })
                ->editColumn('transaction_id', function ($row) {
                    return '<b>' . $row['transaction_id'] . '</b>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->addColumn('consumer_name', function ($row) {
                    return '<b>' . trim($row->consumer_name) . '</b>';
                })
                ->editColumn('bill_amount', function ($row) {
                    return  '<div class="text-primary">₹ ' . $row['bill_amount'] . '</div><small class="text-success">₹ ' . $row['commission'] . ' </small> | <small class="text-primary">₹ ' . $row['tds'] . ' </small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row['status'] == 1) {
                        $btn .= '<span class="badge badge-light-success mb-2 me-4">Paid</span>';
                    } else if ($row['status'] == 2) {
                        $btn .= '<span class="badge badge-light-danger">Cancelled</span>';
                        if ($row['is_refunded'] == 1)   $btn .= '<span class="badge badge-light-warning ms-1">Refunded</span>';
                    } else {
                        $btn .= '<button class="btn btn-sm btn-outline-success update-status me-1" data-id="' . $row['id'] . '" data-type="1">Paid</button>';
                        $btn .= '<button class="btn btn-sm btn-outline-danger update-status" data-id="' . $row['id'] . '" data-type="2">Cancel</button>';
                    }

                    return $btn;
                })
                ->rawColumns(['transaction_id', 'consumer_name', 'bill_amount', 'action', 'retailer_name'])
                ->make(true);
        }

        return view('reports.lic-bill.index');
    }

    public function export(Request $request, $type)
    {
        $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.due_date', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'electricity_bills.commission', 'electricity_bills.tds', 'electricity_bills.bu_code', 'electricity_bills.status', 'rproviders.name as provider_name', 'rproviders.code1 as board_id', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
        $query->where('electricity_bills.bill_type', $type);
        $query->join('retailers', 'retailers.id', 'electricity_bills.user_id');
        $query->join('rproviders', 'rproviders.id', 'electricity_bills.board_id');

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
            $query->whereDate('electricity_bills.created_at', $startDateStr);
        } else {
            $query->whereBetween('electricity_bills.created_at', [$startDate, $endDate]);
        }

        if ($request->filled('is_refunded')) {
            $query->where('electricity_bills.is_refunded', $request->get('is_refunded'));
        }

        // Start Building Excel Sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Water Bill List');

        $sheet->setCellValue('A1', 'Transaction Id');
        $sheet->setCellValue('B1', 'Customer Name');
        $sheet->setCellValue('C1', 'Customer No');
        $sheet->setCellValue('D1', 'Board');
        $sheet->setCellValue('E1', 'Bill No');
        $sheet->setCellValue('F1', 'Bill Amount');
        $sheet->setCellValue('G1', 'Commission Amount');
        $sheet->setCellValue('H1', 'TDS Amount');
        $sheet->setCellValue('I1', 'Due Date');
        $sheet->setCellValue('J1', 'Created Date');
        $sheet->setCellValue('K1', 'Status');
        $sheet->setCellValue('L1', 'Is Refunded');
        $sheet->setCellValue('M1', 'Provider Name');
        $sheet->setCellValue('N1', 'BU Code');


        $rows = 2;
        foreach ($query->get() as $row) {
            $sheet->setCellValue('A' . $rows, $row->transaction_id);
            $sheet->setCellValue('B' . $rows, trim($row->consumer_name));
            $sheet->setCellValue('C' . $rows, $row->consumer_no);
            $sheet->setCellValue('D' . $rows, $row->board_id);
            $sheet->setCellValue('E' . $rows, $row->bill_no);
            $sheet->setCellValue('F' . $rows, $row->bill_amount);
            $sheet->setCellValue('G' . $rows, $row->commission);
            $sheet->setCellValue('H' . $rows, $row->tds);
            $sheet->setCellValue('I' . $rows, Date::PHPToExcel($row->due_date));
            $sheet->setCellValue('J' . $rows, Date::PHPToExcel($row->created_at));
            $sheet->setCellValue('K' . $rows, $row->status == 1 ? 'Paid' : 'Pending');
            $sheet->setCellValue('L' . $rows, $row->is_refunded == 1 ? 'Yes' : 'No');
            $sheet->setCellValue('M' . $rows, $row->provider_name);
            $sheet->setCellValue('N' . $rows, $row->bu_code ?? '--');
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

        $sheet->getStyle('A1:N' . $rows)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C1:E' . $rows)->getNumberFormat()->setFormatCode('#');
        $sheet->getStyle('F1:H' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        $sheet->getStyle('I1:J' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $spreadsheet->setActiveSheetIndex(0);
        $fileName = "Bill Export.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . ($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }

    public function submit(Request $request)
    {
        if (!$request->filled('id'))     return response()->json(['status'    => false, 'message'   => "Please provide record id."]);
        if (!$request->filled('type'))   return response()->json(['status'    => false, 'message'   => "Please provide submit type."]);

        $bill = ElectricityBill::find($request->get('id'));
        if (!$bill) {
            return response()->json([
                'status'    => false,
                'message'   => "Invalid Bill id..!!",
            ]);
        }

        if ($request->get('type') == 1) {
            $bill->update(['status' => 1]);
            return response()->json([
                'status'    => true,
                'message'   => "Bill Updated Successfully Type..!!",
            ]);
        }

        if ($request->get('type') == 2) {

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
            return response()->json([
                'status'    => true,
                'message'   => "Bill Updated Successfully Type..!!",
            ]);
        }

        return response()->json([
            'status'    => false,
            'message'   => "Invalid Submit Type..!!",
        ]);
    }
}
