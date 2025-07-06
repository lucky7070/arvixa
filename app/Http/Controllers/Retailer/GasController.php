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
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Yajra\Datatables\Datatables;

class GasController extends Controller
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
        $this->service_id = config('constant.service_ids.gas_payment');
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

        $providers = DB::table('rproviders')->where('sertype', 'gas')->get();

        $receipt = route('retailer.gas-download.receipt', '') . '/';
        $resent = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.due_date', 'electricity_bills.bill_amount', 'electricity_bills.bu_code', 'electricity_bills.commission', 'electricity_bills.tds', 'rproviders.name as provider_name')
            ->where('electricity_bills.bill_type', 'gas')
            ->where('electricity_bills.user_id', $this->user_id)
            ->join('rproviders', 'rproviders.id', 'electricity_bills.board_id')
            ->limit(5)
            ->latest()
            ->get();

        return view('my_services.gas.create', compact('providers', 'resent', 'receipt'));
    }

    public function getDetails(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'operator'      => ['required', 'numeric', 'min:1'],
            'consumer_no'   => ['required', 'string', 'min:2', 'max:50'],
        ]);

        if ($validation->fails()) {

            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ]);
        } else {

            $provider = DB::table('rproviders')->where('id', $request->operator)->first();
            if (!$provider) {
                return response()->json(['error' => 'Invalid provider selected.'], 400);
            }

            $record = BillPay::getGasBill($request->consumer_no, $provider->code1);
            if (!empty($record['CustomerName'])) {
                $fetch =   FetchBill::create([
                    'transaction_id'    => (string) Str::uuid(),
                    'service_id'        => $this->service_id,
                    'user_id'           => $this->user_id,
                    'board_id'          => $request->operator,
                    'consumer_no'       => $request->consumer_no,
                    'consumer_name'     => @$record['userName'],
                    'bill_no'           => '',
                    'bill_amount'       => @$record['billnetamount'] ?? @$record['billAmount'] ?? '',
                    'due_date'          => Carbon::parse($record['dueDate'])->format('Y-m-d')
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

            $fetch =   FetchBill::create([
                'transaction_id'    => (string) Str::uuid(),
                'service_id'        => $this->service_id,
                'user_id'           => $this->user_id,
                'board_id'          => $request->operator,
                'consumer_no'       => $request->consumer_no,
                'consumer_name'     => '',
                'bill_no'           => '',
                'bill_amount'       => null,
                'due_date'          => null,
            ]);

            return response()->json([
                'status'    => true,
                'message'   => 'Bill details fetched successfully.',
                'data'      => $fetch
            ]);
        }
    }

    public function paymentSubmit(Request $request)
    {
        $request->validate([
            'transaction_id'        => ['required', 'string', 'max:255'],
            'consumer_name'         => ['required', 'string', 'min:3', 'max:100'],
            'bill_amount'           => ['required', 'numeric', 'min:1', 'max:9999999'],
            'due_date'              => ['required', 'date', 'after_or_equal:today'],
        ]);

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
        $amountDue = (float) $request->bill_amount;

        if ($amountDue == 0)  return back()->with('error', "No bill amount pending..!!");

        // Check if user has enough wallet balance
        if ($balance < $amountDue) return back()->with('error', "Insufficient Balance, please recharge your wallet..!!");

        $commission = 0;
        $tds_amount = 0;
        $commission_distributor = 0;
        $tds_distributor = 0;
        $commission_main_distributor = 0;
        $tds_main_distributor = 0;

        if ($serviceLog->retailer_commission > 0) {
            $commission = round((float) $amountDue * $serviceLog->retailer_commission / 100, 4);
            $tds_amount = round($commission * (float) $request->site_settings['tds_percent'] / 100, 4);
        }

        if ($serviceLog->distributor_commission > 0) {
            $commission_distributor = round((float) $amountDue * $serviceLog->distributor_commission / 100, 4);
            $tds_distributor = round($commission_distributor * (float) $request->site_settings['tds_percent'] / 100, 4);
        }

        if ($serviceLog->main_distributor_commission > 0) {
            $commission_main_distributor = round((float) $amountDue * $serviceLog->main_distributor_commission / 100, 4);
            $tds_main_distributor = round($commission_main_distributor * (float) $request->site_settings['tds_percent'] / 100, 4);
        }

        $bill =   ElectricityBill::create([
            'transaction_id'                => 'TXN' . str()->upper(str()->random(10)),
            'user_id'                       => $data->user_id,
            'board_id'                      => $data->board_id,
            'consumer_no'                   => $data->consumer_no,
            'consumer_name'                 => $data->consumer_name,
            'bill_no'                       => '',
            'bill_amount'                   => $amountDue,
            'bill_type'                     => 'gas',
            'due_date'                      => $data->due_date,
            'commission'                    => $commission,
            'tds'                           => $tds_amount,
            'status'                        => 0,
            'commission_distributor'        => $commission_distributor,
            'tds_distributor'               => $tds_distributor,
            'commission_main_distributor'   => $commission_main_distributor,
            'tds_main_distributor'          => $tds_main_distributor,
        ]);

        $state =  LedgerController::chargeForBillPayment($bill, $serviceLog);
        if ($state) {
            return to_route('retailer.gas-bill-list')->with('success', "Bill Submitted successfully..!!");
        } else {
            $bill->delete();
            return back()->withError('Something went wrong..!!');
        }
    }

    public function list(Request $request)
    {
        $service = Services::find($this->service_id);
        if ($request->ajax()) {

            $data = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'due_date', 'electricity_bills.created_at', 'electricity_bills.bill_amount', 'electricity_bills.commission', 'electricity_bills.tds', 'electricity_bills.bu_code', 'electricity_bills.status', 'electricity_bills.remark', 'rproviders.name as provider_name')
                ->where('electricity_bills.bill_type', 'gas')
                ->where('electricity_bills.user_id', $this->user_id)
                ->join('rproviders', 'rproviders.id', 'electricity_bills.board_id');

            return Datatables::of($data)->addIndexColumn()
                ->editColumn('transaction_id', function ($row) {
                    return '<div class="fw-bold">' . $row['transaction_id'] . '</div><small class="text-info">' . $row['created_at']->format('d M, Y') . '</small>';
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
                    $row->receipt = route('retailer.water-download.receipt', $row->id);
                    return '<button class="btn btn-sm btn-primary view" data-all="' . htmlspecialchars(json_encode($row))  . '">View</button>';
                })
                ->rawColumns(['transaction_id', 'provider_name', 'consumer_no', 'commission', 'status', 'action', 'retailer_name'])
                ->make(true);
        }
        return view('my_services.gas.list', compact('service'));
    }

    public function export()
    {
        $data = ElectricityBill::select('electricity_bills.id', 'electricity_bills.transaction_id', 'electricity_bills.consumer_name', 'electricity_bills.consumer_no', 'electricity_bills.bill_no', 'electricity_bills.created_at', 'electricity_bills.due_date', 'electricity_bills.bill_amount', 'electricity_bills.commission', 'electricity_bills.tds', 'rproviders.name as provider_name', 'rproviders.code1 as board_id')
            ->where('electricity_bills.bill_type', 'gas')
            ->where('electricity_bills.user_id', $this->user_id)
            ->join('rproviders', 'rproviders.id', 'electricity_bills.board_id');

        // Start Building Excel Sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('LIC Premium List');

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

        $rows = 2;
        foreach ($data->get() as $row) {
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

        $sheet->getStyle('A1:M' . $rows)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C1:E' . $rows)->getNumberFormat()->setFormatCode('#');
        $sheet->getStyle('F1:H' . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        $sheet->getStyle('I1:J' . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $spreadsheet->setActiveSheetIndex(0);
        $fileName = "Gas Bill Export.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . ($fileName) . '"');
        header('Cache-Control: max-age=0');
        exit($writer->save('php://output'));
    }

    public function downloadReceipt($id)
    {
        $bill = ElectricityBill::with(['retailer', 'board'])->findOrFail($id);
        $service = 'Gas Bill';
        $pdf = PDF::loadView('retailer.receipts.receipt', compact('bill', 'service'));
        return $pdf->download('Gas_Receipt_' . $bill->bill_no . '.pdf');
    }
}
