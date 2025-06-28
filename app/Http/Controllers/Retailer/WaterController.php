<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Common\LedgerController;
use App\Models\Services;
use App\Models\ElectricityBill;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Library\BillPay;
use App\Models\FetchBill;
use App\Models\ServiceUsesLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Yajra\Datatables\Datatables;

class WaterController extends Controller
{
    protected $user_id      = null;
    protected $service_id   = null;
    protected $user_type    = null;
    protected $user         = null;

    public function __construct()
    {
        $this->middleware(['auth:retailer', function ($request, $next) {
            $this->user_id      = auth()->guard('retailer')->id();
            $this->user         = auth()->guard('retailer')->user();
            return $next($request);
        }])->except([]);

        $this->user_type    = 4;
        $this->service_id = config('constant.service_ids.water_bill');
    }

    public function view()
    {
        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => $this->service_id,
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return to_route('retailer.dashboard')->with('error', "Service Can't be used..!!");

        $providers = DB::table('rproviders')->where('sertype', 'water')->get();
        return view('my_services.water.create', compact('providers'));
    }


    public function getDetails(Request $request)
    {
        $provider = DB::table('rproviders')->where('id', $request->operator)->first();
        if (!$provider) {
            return response()->json(['error' => 'Invalid provider selected.'], 400);
        }

        $record = BillPay::getWaterBill($request->consumer_no, $provider->code1);
        if (!empty($record['CustomerName'])) {
            $fetch =   FetchBill::create([
                'transaction_id'    => (string) Str::uuid(),
                'service_id'        => $this->service_id,
                'user_id'           => $this->user_id,
                'board_id'          => $request->operator,
                'consumer_no'       => $request->consumer_no,
                'consumer_name'     => @$record['CustomerName'],
                'bill_no'           => @$record['BillNumber'] ?? '',
                'bill_amount'       => @$record['Billamount'] ?? '',
                'due_date'          => Carbon::parse($record['Duedate'])->format('Y-m-d')
            ]);

            return response()->json([
                'status'    => true,
                'message'   => 'Bill details fetched successfully.',
                'data'      => $fetch
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'No bill amount pending.',
                'data'      => []
            ]);
        }
    }

    public function paymentSubmit(Request $request)
    {
        $data = FetchBill::where('transaction_id', $request->get('transaction_id'))->where('user_id', $this->user_id)->where('service_id', $this->service_id)->first();
        if (!$data)  return back()->with('error', "Invalid Request..!!");

        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => $this->service_id,
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return to_route('retailer.dashboard')->with('error', "Service Can't be used..!!");

        $balance = (float) $this->user->user_balance;
        $amountDue = (float) $data->bill_amount;

        if ($amountDue == 0)  return back()->with('error', "No bill amount pending..!!");

        // Check if user has enough wallet balance
        if ($balance < $amountDue) return back()->with('error', "Insufficient Balance, please recharge your wallet..!!");

        $bill =   ElectricityBill::create([
            'transaction_id'    => 'TXN' . str()->upper(str()->random(10)),
            'user_id'           => $data->user_id,
            'board_id'          => $data->board_id,
            'consumer_no'       => $data->consumer_no,
            'consumer_name'     => $data->consumer_name,
            'bill_no'           => $data->bill_no,
            'bill_amount'       => (float) $data->bill_amount,
            'bill_type'         => 'water',
            'due_date'          => $data->due_date,
        ]);

        LedgerController::chargeForBillPayment($bill, $serviceLog);
        ServiceUsesLog::create([
            'user_id'                       => $this->user_id,
            'user_type'                     => $this->user_type,
            'customer_id'                   => 0,
            'service_id'                    => $this->service_id,
            'request_id'                    => $bill->id,
            'used_in'                       => 1,
            'purchase_rate'                 => $serviceLog->purchase_rate,
            'sale_rate'                     => $serviceLog->sale_rate,
            'main_distributor_id'           => $serviceLog->main_distributor_id,
            'distributor_id'                => $serviceLog->distributor_id,
            'main_distributor_commission'   => $serviceLog->main_distributor_commission,
            'distributor_commission'        => $serviceLog->distributor_commission,
            'retailer_commission'           => $serviceLog->retailer_commission,
            'is_refunded'                   => 0,
            'created_at'                    => Carbon::now(),
        ]);

        return to_route('retailer.water-bill-list')->with('success', "Bill Submitted successfully..!!");
    }

    public function list(Request $request)
    {
        $service = Services::find($this->service_id);
        if ($request->ajax()) {

            $data = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'rproviders.name as provider_name', 'rproviders.code1 as board_id')
                ->where('electricity_bills.bill_type', 'water')
                ->where('electricity_bills.user_id', $this->user_id)
                ->join('rproviders', 'rproviders.id', 'electricity_bills.board_id');

            return Datatables::of($data)->addIndexColumn()
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
                ->rawColumns(['transaction_id', 'consumer_name', 'bill_amount', 'action'])
                ->make(true);
        }
        return view('my_services.water.list', compact('service'));
    }

    public function export()
    {
        $data = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'rproviders.name as provider_name', 'rproviders.code1 as board_id')
            ->where('electricity_bills.bill_type', 'water')
            ->where('electricity_bills.user_id', $this->user_id)
            ->join('rproviders', 'rproviders.id', 'electricity_bills.board_id');

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
        foreach ($data->get() as $row) {
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
        $fileName = "Water Bill Export.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . ($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }


    public function downloadReceipt($id)
    {
        $bill = ElectricityBill::with(['retailer', 'board'])->findOrFail($id);
        $pdf = PDF::loadView('retailer.receipts.electricity', compact('bill'));
        return $pdf->download('Water_Receipt_' . $bill->bill_no . '.pdf');
    }
}
