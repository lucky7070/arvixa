<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;

class MintraGatewayController extends Controller
{
    public function mintra_gateway_response(Request $request)
    {
        \Log::info("RESPONSE FROM MINTRA START");
        \Log::info($request->all());
        \Log::info("RESPONSE FROM MINTRA END");

        try {
            
            // Get the encrypted hash from the request
 
            $encrypted_hash = $request->input('hash');
            // $encrypted_hash = 'uwbj13rSqP0ozKfBJZQ025wBu\/r2dU=';

            // Decrypt the hash using the secret key
            $secret_key = env('MITRAGETWAY_SECRET_KEY');

            $decrypted_hash = hash_decrypt($encrypted_hash, $secret_key);

            // Check if decryption was successful
            if (empty($decrypted_hash)) {
                throw new \Exception("Decryption failed. Check the Data.");
            }

            // Now, $decrypted_hash contains the decrypted hash

            // Parse the JSON data from the decrypted hash
            $request_data = json_decode($decrypted_hash, true);

            // Check if the required fields are present in the request data
            $requiredFields = ['txnStatus', 'resultInfo', 'orderId', 'txnAmount', 'txnId', 'bankTxnId', 'paymentMode', 'txnDate', 'utr', 'customerName', 'customerEmail', 'customerMobile', 'customerUpi', 'txnNote', 'payee_vpa'];
            foreach ($requiredFields as $field) {
                if (!isset($request_data[$field])) {
                    throw new \Exception("Missing required field: $field");
                }
            }

            // Extract data from the request
            $txnStatus      = $request_data['txnStatus'];
            $resultInfo     = $request_data['resultInfo'];
            $orderId        = $request_data['orderId'];
            $txnAmount      = $request_data['txnAmount'];
            $txnId          = $request_data['txnId'];
            $bankTxnId      = $request_data['bankTxnId'];
            $paymentMode    = $request_data['paymentMode'];
            $txnDate        = $request_data['txnDate'];
            $utr            = $request_data['utr'];
            $customerName   = $request_data['customerName'];
            $customerEmail  = $request_data['customerEmail'];
            $customerMobile = $request_data['customerMobile'];
            $customerUpi    = $request_data['customerUpi'];
            $txnNote        = $request_data['txnNote'];
            $payee_vpa      = $request_data['payee_vpa'];

            // Construct the response array
            $response = [
                'txnStatus'         => $txnStatus,
                'resultInfo'        => $resultInfo,
                'orderId'           => $orderId,
                'txnAmount'         => $txnAmount,
                'txnId'             => $txnId,
                'bankTxnId'         => $bankTxnId,
                'paymentMode'       => $paymentMode,
                'txnDate'           => $txnDate,
                'utr'               => $utr,
                'customerName'      => $customerName,
                'customerEmail'     => $customerEmail,
                'customerMobile'    => $customerMobile,
                'customerUpi'       => $customerUpi,
                'txnNote'           => $txnNote,
                'payee_vpa'         => $payee_vpa,
                // Add other fields as needed...
            ];

            // Return the response as JSON
            return response()->json($response);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Mintra Gateway processing error: ' . $e->getMessage());

            // Return an error response
            return response()->json([
                'error'     => 'Mintra Gateway processing error',
                'message'   => $e->getMessage(),
            ], 500);
        }
    }
}
