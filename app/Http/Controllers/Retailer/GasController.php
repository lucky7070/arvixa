<?php

namespace App\Http\Controllers\Retailer;

use App\Models\PanCard;
use App\Models\Customer;
use App\Models\Services;
use App\Models\Retailer;
use App\Models\waterBill;
use App\Models\ServicesLog;
use App\Models\ElectricityBill;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use \Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Library\PanCard as LibraryPanCard;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Http\Controllers\Common\LedgerController;
use Illuminate\Support\Facades\Http;
use DB;

class GasController extends Controller
{
    public function view(){
        // return view('retailer.gas-bill');
        
    $retailer = auth()->user(); // current logged-in retailer

    // Check if electricity service is active
    $commission = ServicesLog::where('user_id', $retailer->id)
        ->where('service_id', 9) // 8 is the electricity service ID
        ->whereNull('decline_date')
        ->first();


    // If not active, redirect back or show error
    if (!$commission) {
        return redirect()->back()->with('error', 'gas service is not active for your account.');
    }

    // If active, show bills
    $bills = ElectricityBill::with(['retailer', 'board'])
        ->where('bill_type', 'gas')
        ->orderBy('id', 'desc')
        ->paginate(5);

    return view('retailer.gas-bill', compact('bills'));
        
    }
    
    
    
    public function gasGetDetails(Request $request) {
        $url = 'https://www.mplan.in/api/gas.php';
    
        $provider = DB::table('rproviders')->where('id', $request->operator)->first();
        if (!$provider) {
            return response()->json(['error' => 'Invalid provider selected.'], 400);
        }
    
        $queryParams = [
            'apikey'   => 'ba0fa41bee5146ebe30f8f7e3c10c68b',
            'offer'    => $request->offer,
            'tel'      => $request->tel,
            'operator' => $provider->code1,
        ];
    
        $response = Http::get($url, $queryParams);
        $data = $response->json();
        dd($data);
        if (!$response->successful() || empty($data)) {
            return response()->json(['error' => 'Failed to fetch bill details.'], 500);
        }
        $record = $data['records'][0] ?? [];

        $customer_name = $record['CustomerName'] ?? 'Invalid Details';
        $amount_due = $record['Billamount'] ?? 'Invalid Details';
        $due_date = $record['Duedate'] ?? 'Invalid Details';
        $bill_number = $record['BillNumber'] ?? 'Invalid Details';
        return response()->json([
            'biller_name'  => $customer_name,
            'due_date'     => $due_date,
            'amount_due'   => $amount_due,
            'bill_number'  => $bill_number,
        ]);
    }
    
    
    
    // public function gasPaymentSubmit(Request $request)
    // {
    //     $retailer = auth()->user();
    
    //     $balance = (float) $retailer->user_balance;
    //     $amountDue = (float) $request->bill_amount;
    
    //     // Check if bill amount is zero
    //     if ($amountDue == 0) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No bill amount pending.'
    //         ], 400);
    //     }
    
    //     // Check if user has enough wallet balance
    //     if ($balance < $amountDue) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Insufficient Balance, please recharge your wallet.'
    //         ], 400);
    //     }
    
    //     try {
    //         $waterBill = new waterBill();
    //         $waterBill->user_id        = $retailer->id;
    //         $waterBill->board_id       = $request->board_id;
    //         $waterBill->consumer_no    = $request->consumer_no;
    //         $waterBill->consumer_name  = $request->consumer_name;
    //         $waterBill->bill_no        = $request->bill_no;
    //         $waterBill->bill_amount    = $amountDue;
    //         $waterBill->due_date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->due_date)->format('Y-m-d');
    //         $waterBill->save();
    
    //         $retailer->user_balance -= $amountDue;
    //         $retailer->save();
    
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Bill saved and payment processed successfully.',
    //             'data' => $waterBill
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to save bill: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    
    public function gasPaymentSubmit(Request $request)
    {
        $retailer = auth()->user();
    
        $balance = (float) $retailer->user_balance;
        $amountDue = (float) $request->bill_amount;
    
        // Check if bill amount is zero
        if ($amountDue == 0) {
            return response()->json([
                'status' => false,
                'message' => 'No bill amount pending.'
            ], 400);
        }
    
        // Check if user has enough wallet balance
        if ($balance < $amountDue) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient Balance, please recharge your wallet.'
            ], 400);
        }
        $transactionId = 'TXN' . Str::random(10);
        try {
            $electricityBill = new ElectricityBill();
            $electricityBill->transaction_id = $transactionId;
            $electricityBill->user_id        = $retailer->id;
            $electricityBill->board_id       = $request->board_id;
            $electricityBill->consumer_no    = $request->consumer_no;
            $electricityBill->consumer_name  = $request->consumer_name;
            $electricityBill->bill_no        = $request->bill_no;
            $electricityBill->bill_amount    = $amountDue;
            $electricityBill->bill_type    = 'gas';
            $electricityBill->due_date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->due_date)->format('Y-m-d');
            $electricityBill->save();
    
            
            // Add Transaction Ledger
            $voucherNo = $this->generateVoucherNo();

            $paymentTransaction = new Ledger();
            $paymentTransaction->voucher_no = $voucherNo;
            $paymentTransaction->user_id = $retailer->id;
            $paymentTransaction->user_type = 4;
            $paymentTransaction->amount = $amountDue;
            $paymentTransaction->current_balance = $retailer->user_balance;
            $retailer->user_balance -= $amountDue;
            $paymentTransaction->updated_balance = $retailer->user_balance;
            $paymentTransaction->payment_type = 2; // Debit
            $paymentTransaction->payment_method = 5;
            $paymentTransaction->particulars = "Gas bill paid for Consumer No.: " . $request->consumer_no;
            $paymentTransaction->created_at = now();
            $paymentTransaction->updated_at = now();
            $paymentTransaction->save();
    
            $retailer->save();
            
            $commission = ServicesLog::where('user_id', $retailer->id)
            ->where('service_id', 9)
            ->whereNull('decline_date')
            ->first();

            if ($commission) {
                $commissionAmount = ($amountDue * $commission->retailer_commission) / 100;
                $commissionVoucherNo = $this->generateVoucherNo();
    
                $commissionTransaction = new Ledger();
                $commissionTransaction->voucher_no = $commissionVoucherNo;
                $commissionTransaction->user_id = $retailer->id;
                $commissionTransaction->user_type = 4;
                $commissionTransaction->amount = $commissionAmount;
                $commissionTransaction->current_balance = $retailer->user_balance;
                $retailer->user_balance += $commissionAmount;
                $commissionTransaction->updated_balance = $retailer->user_balance;
                $commissionTransaction->payment_type = 1; // Credit
                $commissionTransaction->payment_method = 5;
                $commissionTransaction->particulars = "Gas Bill Service Commission credited for Consumer No.: " . $request->consumer_no;
                $commissionTransaction->created_at = now();
                $commissionTransaction->updated_at = now();
                $commissionTransaction->save();
    
                $retailer->save();
            }            
            return response()->json([
                'status' => true,
                'message' => 'Bill saved and payment processed successfully.',
                'data' => $electricityBill
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save bill: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function downloadReceipt($id)
    {
        // $bill = waterBill::findOrFail($id);
    
        // // Generate PDF or HTML receipt
        // $pdf = PDF::loadView('receipts.water', compact('bill'));
        // return $pdf->download('water_Receipt_' . $bill->bill_no . '.pdf');
        
         $bill = ElectricityBill::with(['retailer', 'board'])->findOrFail($id);
    
        // Generate PDF or HTML receipt
        $pdf = PDF::loadView('retailer.receipts.electricity', compact('bill'));
        return $pdf->download('gas_Receipt_' . $bill->bill_no . '.pdf');
    }
    
    private function generateVoucherNo()
    {
        $latest = Ledger::orderBy('id', 'desc')->first();
        $number = 1;
    
        if ($latest && preg_match('/TID(\d+)/', $latest->voucher_no, $matches)) {
            $number = (int)$matches[1] + 1;
        }
    
        return 'TID' . str_pad($number, 10, '0', STR_PAD_LEFT);
    }

}