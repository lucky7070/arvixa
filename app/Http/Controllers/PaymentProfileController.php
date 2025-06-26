<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Retailer;
use App\Models\UpiPayments;
use Illuminate\Http\Request;

class PaymentProfileController extends Controller
{
    public function checkPaymentStatus($orderId)
    {
        // Initialize CURL session for the first payment gateway
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://mitragateway.com/order/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(array(
                "token" => "29de93-f39f06-c41d43-aaf431-e3712a",
                "orderId" => $orderId
            )),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        // Check for CURL execution errors
        if ($response === false) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment status from gateway',
                'data' => null
            ]);
        }

        curl_close($curl);

        // Decode the JSON response from the respective payment gateway
        $response_data = json_decode($response, true);

        // Check if response contains valid data and status is "COMPLETED"
        if (isset($response_data['status']) && $response_data['status'] === 'COMPLETED') {

            // Update UpiPayments table status to "Completed"
            $upi_payment = UpiPayments::where('voucher_no', $orderId)->first();

            if ($upi_payment) {
                $upi_payment->status = 'Completed';
                $upi_payment->save();

                // Clone data to Ledger table
                $ledger = new Ledger();
                $ledger->voucher_no = $upi_payment->voucher_no;
                $ledger->user_id = $upi_payment->user_id;
                $ledger->user_type = $upi_payment->user_type;
                $ledger->amount = $upi_payment->amount;
                $ledger->payment_type = 1; // Example: Payment type logic
                $ledger->payment_type_by_mintra = 'Yes'; // Example: Indicator for Mintra payment
                $ledger->save();

                // Update Ledger table status to "Approved"
                $ledger->status = 'Approved';
                $ledger->voucher_no = $response_data['result']['txnId'];
                $ledger->json_response = json_encode($response_data);
                $ledger->particulars = "UPI Payment Credited :: " . $response_data['result']['orderId'];
                $ledger->save();

                // Find retailer by user_id from the ledger
                $retailer = Retailer::find($ledger->user_id);

                if ($retailer) {
                    // Update retailer's balance with the amount credited
                    $retailer->user_balance += $upi_payment->amount;
                    $retailer->save();

                    return response()->json([
                        'success' => "Yes",
                        'message' => 'Payment Approved and Ledger Updated Successfully',
                        'data' => [
                            'updated_balance' => $retailer->user_balance,
                            'ledger_status' => $ledger->status,
                            'redirect_url' => route('retailer.wallet'), // Example: Redirect URL after processing
                        ]
                    ]);
                } else {
                    // If retailer not found, handle accordingly
                    return response()->json([
                        'success' => "No",
                        'message' => 'Retailer not found for UPI payment with ID: ' . $upi_payment->id,
                        'data' => null
                    ]);
                }
            } else {
                // If UpiPayment record not found, handle accordingly
                return response()->json([
                    'success' => "No",
                    'message' => 'UpiPayment record not found for orderId: ' . $orderId,
                    'data' => null
                ]);
            }
        } else {
            // If payment status is not "COMPLETED", handle accordingly
            return response()->json([
                'success' => "Yes", // Adjust as per your requirement
                'message' => 'Payment is not completed yet',
                'data' => null
            ]);
        }
    }

    public function checkPaymentQkqrStatus($orderId)
    {
        // Retrieve the UpiPayments record for the given orderId
        $upi_payment = UpiPayments::where('voucher_no', $orderId)->first();

        if (!$upi_payment) {
            return response()->json([
                'success' => false,
                'message' => 'UpiPayment record not found for orderId: ' . $orderId,
                'data' => null
            ]);
        }

        // Get the created_at date from the UpiPayments record
        $txn_date = $upi_payment->created_at->format('d-m-Y');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.ekqr.in/api/check_order_status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(array(
                "key" => env('EQRK_UPI_GATEWAY_API_KEY'),
                "client_txn_id" => $orderId,
                "txn_date" => $txn_date // Adjust the date format if necessary
            )),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        // Check for CURL execution errors
        if ($response === false) {
            $error_message = curl_error($curl);
            curl_close($curl);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment status from gateway: ' . $error_message,
                'data' => null
            ]);
        }

        curl_close($curl);

        // Decode the JSON response from the respective payment gateway
        $response_data = json_decode($response, true);

        // Check if response contains valid data and status is "true"
        if ((isset($response_data['status']) && $response_data['status'] == true) && (isset($response_data['data']['status']) && $response_data['data']['status'] != 'failure') ) {


            // Update UpiPayments table status to "true"
            $upi_payment = UpiPayments::where('voucher_no', $orderId)->first();


            if ($upi_payment) {
                $upi_payment->status = 'Completed';
                $upi_payment->save();

                $userbalance = 0;

                 // Find retailer by user_id from the ledger
                 $retailer = Retailer::find($upi_payment->user_id);
                if ($retailer) {
                    $userbalance = $retailer->user_balance;
                }

                // Clone data to Ledger table
                $ledger = new Ledger();
                $ledger->voucher_no = $upi_payment->voucher_no;
                $ledger->user_id = $upi_payment->user_id;
                $ledger->user_type = $upi_payment->user_type;
                $ledger->amount = $upi_payment->amount;
                $ledger->payment_type = 1; // Example: Payment type logic
                $ledger->payment_type_by_mintra = 'Yes'; // Example: Indicator for Mintra payment

                $ledger->current_balance = $userbalance;
                $ledger->updated_balance = $userbalance + $upi_payment->amount;


                $ledger->save();

                // Update Ledger table status to "Approved"
                $ledger->status = 'Approved';
                $ledger->voucher_no = $response_data['data']['client_txn_id'];
                $ledger->json_response = json_encode($response_data);
                $ledger->particulars = "UPI Payment Credited :: " . $response_data['data']['client_txn_id'];
                $ledger->save();

                // Find retailer by user_id from the ledger
                $retailer = Retailer::find($ledger->user_id);

                if ($retailer) {
                    // Update retailer's balance with the amount credited
                    $retailer->user_balance += $upi_payment->amount;
                    $retailer->save();

                    // Delete the completed UpiPayments entry
                    $upi_payment->delete();

                    return redirect()->route('retailer.wallet')->with('success', 'Payment Approved and Ledger Updated Successfully');
                } else {
                    // If retailer not found, handle accordingly
                    return redirect()->route('retailer.wallet')->with('error', 'Retailer not found for UPI payment with ID: ' . $upi_payment->id);
                }
            } else {
                // If payment status is not "COMPLETED", handle accordingly
                return redirect()->route('retailer.wallet')->with('error', 'Payment is not completed yet');
            }
        } else {
            // If payment status is not "COMPLETED", handle accordingly
            return redirect()->route('retailer.wallet')->with('error', 'Payment is not completed yet due to cancel by user');
        }
    }
}
