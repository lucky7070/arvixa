<?php

namespace App\Http\Controllers\Reports;

use App\Models\PanCard;
use Illuminate\Http\Request;
use App\Models\ServiceUsesLog;
use App\Models\ElectricityBill;
use Illuminate\Support\Carbon;
use \Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
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

            $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'rproviders.name as provider_name', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
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
                    return  '<b class="text-primary">₹ ' . $row['bill_amount'] . '</b>';
                })
                ->addColumn('action', function ($row) {
                    return  '<a href="' . route('retailer.water-download.receipt', $row->id) . '" class="btn btn-sm btn-primary">Download</a>';
                })
                ->rawColumns(['transaction_id', 'consumer_name', 'bill_amount', 'action', 'retailer_name'])
                ->make(true);
        }

        return view('reports.electricity-bill.index');
    }

    public function waterbill(Request $request)
    {
        if ($request->ajax()) {

            $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'rproviders.name as provider_name', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
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
                    return  '<b class="text-primary">₹ ' . $row['bill_amount'] . '</b>';
                })
                ->addColumn('action', function ($row) {
                    return  '<a href="' . route('retailer.water-download.receipt', $row->id) . '" class="btn btn-sm btn-primary">Download</a>';
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

            $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'rproviders.name as provider_name', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
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
                    return  '<b class="text-primary">₹ ' . $row['bill_amount'] . '</b>';
                })
                ->addColumn('action', function ($row) {
                    return  '<a href="' . route('retailer.water-download.receipt', $row->id) . '" class="btn btn-sm btn-primary">Download</a>';
                })
                ->rawColumns(['transaction_id', 'consumer_name', 'bill_amount', 'action', 'retailer_name'])
                ->make(true);
        }

        return view('reports.gas-bill.index');
    }

    public function licbill(Request $request)
    {
        if ($request->ajax()) {

            $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'rproviders.name as provider_name', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
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
                    return  '<b class="text-primary">₹ ' . $row['bill_amount'] . '</b>';
                })
                ->addColumn('action', function ($row) {
                    return  '<a href="' . route('retailer.water-download.receipt', $row->id) . '" class="btn btn-sm btn-primary">Download</a>';
                })
                ->rawColumns(['transaction_id', 'consumer_name', 'bill_amount', 'action', 'retailer_name'])
                ->make(true);
        }

        return view('reports.lic-bill.index');
    }

    public function export(Request $request, $type)
    {
        $query = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'rproviders.name as provider_name', 'rproviders.code1 as board_id', 'retailers.name as retailer_name', 'retailers.userId as retailer_userId');
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
        $sheet->setCellValue('G1', 'Due Date');
        $sheet->setCellValue('H1', 'Created Date');
        $sheet->setCellValue('I1', 'Status');
        $sheet->setCellValue('J1', 'Is Refunded');
        $sheet->setCellValue('K1', 'Provider Name');

        $rows = 2;
        foreach ($query->get() as $row) {
            $sheet->setCellValue('A' . $rows, $row->transaction_id);
            $sheet->setCellValue('B' . $rows, trim($row->consumer_name));
            $sheet->setCellValue('C' . $rows, $row->consumer_no);
            $sheet->setCellValue('D' . $rows, $row->board_id);
            $sheet->setCellValue('E' . $rows, $row->bill_no);
            $sheet->setCellValue('F' . $rows, $row->bill_amount);
            $sheet->setCellValue('G' . $rows, Date::PHPToExcel($row->due_date));
            $sheet->setCellValue('H' . $rows, Date::PHPToExcel($row->created_at));
            $sheet->setCellValue('I' . $rows, $row->status == 1 ? 'Paid' : 'Pending');
            $sheet->setCellValue('J' . $rows, $row->is_refunded == 1 ? 'Yes' : 'No');
            $sheet->setCellValue('K' . $rows, $row->provider_name);
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

        $sheet->getStyle('A1:K' . $rows)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C1:E' . $rows)->getNumberFormat()->setFormatCode('#');
        $sheet->getStyle('F1:F' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        $sheet->getStyle('G1:H' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $spreadsheet->setActiveSheetIndex(0);
        $fileName = "Bill Export.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . ($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }
}
