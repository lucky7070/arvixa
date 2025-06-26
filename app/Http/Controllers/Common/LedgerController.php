<?php

namespace App\Http\Controllers\Common;

use App\Models\Ledger;
use App\Models\PanCard;
use App\Models\Customer;
use App\Models\ItReturn;
use App\Models\Retailer;
use App\Library\Firebase;
use App\Models\Distributor;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\LedgerExport;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use App\Models\MainDistributor;
use App\Models\MSMECertificate;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LedgerController extends Controller
{
    //=============================================================================
    // Add Ledger Entry
    //=============================================================================
    // ******************** Mendatory Keys ********************************
    // ◙ amount
    // ◙ payment_type
    // ◙ payment_method
    // ◙ particulars
    // ******************** Optional Keys ********************************
    // ◙ trans_details_json
    // ◙ service_id
    // ◙ request_id
    // ◙ paid_by
    //=============================================================================

    public static function add(int $user_id = null, int $user_type = 1, array $data = []): bool
    {
        switch ($user_type) {
            case 2:
                $user =  MainDistributor::firstWhere('id', $user_id);
                break;
            case 3:
                $user =  Distributor::firstWhere('id', $user_id);
                break;
            case 4:
                $user =  Retailer::firstWhere('id', $user_id);
                break;
            case 7:
                $user =  Customer::firstWhere('id', $user_id);
                break;
            default:
                $user = null;
                break;
        }

        if ($user != null) {
            DB::transaction(function () use ($data, $user, $user_id, $user_type) {

                $json = $request_id = $paid_by = $service_id = null;
                if (!empty($data['trans_details_json']))    $json           = $data['trans_details_json'];
                if (!empty($data['service_id']))            $service_id     = $data['service_id'];
                if (!empty($data['request_id']))            $request_id     = $data['request_id'];
                if (!empty($data['paid_by']))               $paid_by        = $data['paid_by'];

                $data = [
                    'voucher_no'            => Str::uuid(),
                    'user_id'               => $user_id,
                    'user_type'             => $user_type,
                    'amount'                => $data['amount'],
                    'payment_type'          => $data['payment_type'],
                    'payment_method'        => $data['payment_method'],
                    'particulars'           => $data['particulars'],
                    'date'                  => Carbon::now(),
                    'trans_details_json'    => $json,
                    'service_id'            => $service_id,
                    'request_id'            => $request_id,
                    'paid_by'               => $paid_by
                ];

                $data['current_balance']   = $user['user_balance'];
                $data['updated_balance']   = $user['user_balance'];

                if ($user && $data['payment_type'] == 1) {
                    $data['updated_balance']   = $user['user_balance'] + $data['amount'];
                    $user->increment('user_balance', $data['amount']);
                }

                if ($user && $data['payment_type'] == 2) {
                    $data['updated_balance']   = $user['user_balance'] - $data['amount'];
                    $user->decrement('user_balance', $data['amount']);
                }

                Ledger::create($data);
            });

            if ($user && $user->fcm_id) {
                $title      = $data['payment_type'] == 1 ? "Amount Credited :: ₹ " . $data['amount'] : "Amount Debited :: ₹ " . $data['amount'];
                $message    = $data['particulars'];
                Firebase::sendMessage([$user->fcm_id], $title, $message);
            }

            return true;
        } else {
            return false;
        }
    }

    public static function getDataTable($data)
    { 
        return Datatables::of($data)->addIndexColumn()
            ->editColumn('voucher_no', function ($row) {
                return "<span class='fw-bold text-primary'>" . $row['voucher_no'] . "</span>";
            })
            ->editColumn('created_at', function ($row) {
                return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
            })
            ->editColumn('amount', function ($row) {
                $str = '';
                if ($row['payment_type'] == 1) {
                    $str = '<b class="text-success"> + ₹' . $row['amount'] . '</b>';
                } else {
                    $str = '<b class="text-danger"> - ₹' . $row['amount'] . '</b>';
                }
                return  $str;
            })
            ->editColumn('updated_balance', function ($row) {
                return  '<b class="text-primary">₹ ' . $row['updated_balance'] . '</b>';
            })
            ->editColumn('particulars', function ($row) {
                return Str::limit($row['particulars'], 50, '...');
            })
            ->editColumn('status', function ($row) {
                // Determine status and apply appropriate styling
                $statusClass = '';
                switch (strtolower($row['status'])) {
                    case 'approved':
                        $statusClass = 'text-success';
                        break;
                    case 'pending':
                        $statusClass = 'text-danger';
                        break;
                    default:
                        $statusClass = 'text-warning';
                        break;
                }
                return '<span class="' . $statusClass . '">' . ucfirst($row['status']) . '</span>';
            })
        
            ->orderColumn('created_at', function ($query, $order) {
                $query->orderBy('created_at', $order);
            })
            ->rawColumns(['voucher_no', 'amount', 'updated_balance','status'])
            ->make(true);
    }

    public function export(Request $request)
    {
        switch ($request->user_type) {
            case 2:
                $user =  MainDistributor::firstWhere('slug', $request->user);
                break;

            case 3:
                $user =  Distributor::firstWhere('slug', $request->user);
                break;

            case 4:
                $user =  Retailer::firstWhere('slug', $request->user);
                break;

            case 7:
                $user =  Customer::firstWhere('slug', $request->user);
                break;

            default:
                $user = null;
                break;
        }

        if ($user != null) {
            $filter = ['user_id' => $user['id']];
            if (!empty($request->user_type)) $filter['user_type'] = $request->user_type;
            if (!empty($request->payment_type)) $filter['payment_type'] = $request->payment_type;
            if (!empty($request->payment_method)) $filter['payment_method'] = $request->payment_method;
            if (!empty($request->status)) $filter['status'] = $request->status;

            return (new LedgerExport)->where($filter)->download('Ledger.xlsx');
        }

        return back()->with(['error' => 'Invalid Request.']);
    }

    public static function panCardRefund(PanCard $pan_card, ServicesLog | ServiceUsesLog $serviceLog, string $errorMsg = "")
    {
        if ($pan_card->is_refunded == 0) {

            DB::transaction(function () use ($pan_card, $serviceLog, $errorMsg) {
                self::add($serviceLog->user_id, $serviceLog->user_type, [
                    'amount'            => $serviceLog->sale_rate,
                    'payment_type'      => 1,
                    'payment_method'    => 5,
                    'particulars'       => "Refund For PanCard Service : " . $pan_card->nsdl_txn_id,
                    'service_id'        => $serviceLog->service_id,
                    'request_id'        => $pan_card->id,
                ]);

                if ($serviceLog->main_distributor_id && $serviceLog->main_distributor_commission > 0)
                    self::add($serviceLog->main_distributor_id, 2, [
                        'amount'            => $serviceLog->main_distributor_commission,
                        'payment_type'      => 2,
                        'payment_method'    => 5,
                        'particulars'       => "PanCard Service Commission Refund : " . $pan_card->nsdl_txn_id,
                        'service_id'        => $serviceLog->service_id,
                        'request_id'        => $pan_card->id,
                    ]);

                if ($serviceLog->distributor_id && $serviceLog->distributor_commission > 0)
                    self::add($serviceLog->distributor_id, 3, [
                        'amount'            => $serviceLog->distributor_commission,
                        'payment_type'      => 2,
                        'payment_method'    => 5,
                        'particulars'       => "PanCard Service Commission Refund :: " . $pan_card->nsdl_txn_id,
                        'service_id'        => $serviceLog->service_id,
                        'request_id'        => $pan_card->id,
                    ]);

                ServiceUsesLog::where(['used_in' => 1, 'request_id' => $pan_card->id, 'service_id' => $serviceLog->service_id])->update(['is_refunded' => 1]);
                $pan_card->update(['nsdl_complete' => 1, 'is_refunded' => 1, 'error_message' => $errorMsg]);
            });
        }
    }

    public static function chargePanCardService(PanCard $pan_card, ServicesLog $serviceLog)
    {
        DB::transaction(function () use ($pan_card, $serviceLog) {
            self::add($serviceLog->user_id, $serviceLog->user_type, [
                'amount'            => $serviceLog->sale_rate,
                'payment_type'      => 2,
                'payment_method'    => 5,
                'particulars'       => "Charged For PanCard Service : " . $pan_card->nsdl_txn_id,
                'service_id'        => $serviceLog->service_id,
                'request_id'        => $pan_card->id,
            ]);

            if ($serviceLog->main_distributor_id && $serviceLog->main_distributor_commission > 0)
                self::add($serviceLog->main_distributor_id, 2, [
                    'amount'            => $serviceLog->main_distributor_commission,
                    'payment_type'      => 1,
                    'payment_method'    => 5,
                    'particulars'       => "PanCard Service Commission : " . $pan_card->nsdl_txn_id,
                    'service_id'        => $serviceLog->service_id,
                    'request_id'        => $pan_card->id,
                ]);

            if ($serviceLog->distributor_id && $serviceLog->distributor_commission > 0)
                self::add($serviceLog->distributor_id, 3, [
                    'amount'            => $serviceLog->distributor_commission,
                    'payment_type'      => 1,
                    'payment_method'    => 5,
                    'particulars'       => "PanCard Service Commission : " . $pan_card->nsdl_txn_id,
                    'service_id'        => $serviceLog->service_id,
                    'request_id'        => $pan_card->id,
                ]);
        });
    }

    public static function chargeMSMEService(MSMECertificate $certificate, ServicesLog $serviceLog)
    {
        DB::transaction(function () use ($serviceLog, $certificate) {
            self::add($serviceLog->user_id, $serviceLog->user_type, [
                'amount'            => $serviceLog->sale_rate,
                'payment_type'      => 2,
                'payment_method'    => 5,
                'particulars'       => "Charged For MSME Certificate Service : " . $certificate->txn_id,
                'service_id'        => $serviceLog->service_id,
                'request_id'        => $certificate->id,
            ]);

            if ($serviceLog->main_distributor_id && $serviceLog->main_distributor_commission > 0)
                self::add($serviceLog->main_distributor_id, 2, [
                    'amount'            => $serviceLog->main_distributor_commission,
                    'payment_type'      => 1,
                    'payment_method'    => 5,
                    'particulars'       => "MSME Certificate Service Commission : " . $certificate->txn_id,
                    'service_id'        => $serviceLog->service_id,
                    'request_id'        => $certificate->id,
                ]);

            if ($serviceLog->distributor_id && $serviceLog->distributor_commission > 0)
                self::add($serviceLog->distributor_id, 3, [
                    'amount'            => $serviceLog->distributor_commission,
                    'payment_type'      => 1,
                    'payment_method'    => 5,
                    'particulars'       => "MSME Certificate Service Commission : " . $certificate->txn_id,
                    'service_id'        => $serviceLog->service_id,
                    'request_id'        => $certificate->id,
                ]);
        });
    }

    public static function refundMSMEService(MSMECertificate $certificate, ServicesLog $serviceLog, string $errorMsg = "")
    {
        if ($certificate->is_refunded == 0) {
            DB::transaction(function () use ($serviceLog, $certificate, $errorMsg) {
                self::add($serviceLog->user_id, $serviceLog->user_type, [
                    'amount'            => $serviceLog->sale_rate,
                    'payment_type'      => 1,
                    'payment_method'    => 5,
                    'particulars'       => "Refund For MSME Certificate Service : " . $certificate->txn_id,
                    'service_id'        => $serviceLog->service_id,
                    'request_id'        => $certificate->id,
                ]);

                if ($serviceLog->main_distributor_id && $serviceLog->main_distributor_commission > 0)
                    self::add($serviceLog->main_distributor_id, 2, [
                        'amount'            => $serviceLog->main_distributor_commission,
                        'payment_type'      => 2,
                        'payment_method'    => 5,
                        'particulars'       => "MSME Certificate Service Commission Refund : " . $certificate->txn_id,
                        'service_id'        => $serviceLog->service_id,
                        'request_id'        => $certificate->id,
                    ]);

                if ($serviceLog->distributor_id && $serviceLog->distributor_commission > 0)
                    self::add($serviceLog->distributor_id, 3, [
                        'amount'            => $serviceLog->distributor_commission,
                        'payment_type'      => 2,
                        'payment_method'    => 5,
                        'particulars'       => "MSME Certificate Service Commission Refund :: " . $certificate->txn_id,
                        'service_id'        => $serviceLog->service_id,
                        'request_id'        => $certificate->id,
                    ]);

                ServiceUsesLog::where(['request_id' => $certificate->id, 'service_id' => $serviceLog->service_id])->update(['is_refunded' => 1]);
                $certificate->update(['status' => 2, 'is_refunded' => 1, 'error_message' => $errorMsg]);
            });
        }
    }

    public static function chargeItrService(ItReturn $itr, ServicesLog $serviceLog)
    {
        DB::transaction(function () use ($serviceLog, $itr) {
            self::add($serviceLog->user_id, $serviceLog->user_type, [
                'amount'            => $serviceLog->sale_rate,
                'payment_type'      => 2,
                'payment_method'    => 5,
                'particulars'       => "Charged For ITR Service : " . $itr->token,
                'service_id'        => $serviceLog->service_id,
                'request_id'        => $itr->id,
            ]);

            if ($serviceLog->main_distributor_id && $serviceLog->main_distributor_commission > 0)
                self::add($serviceLog->main_distributor_id, 2, [
                    'amount'            => $serviceLog->main_distributor_commission,
                    'payment_type'      => 1,
                    'payment_method'    => 5,
                    'particulars'       => "ITR Service Commission : " . $itr->token,
                    'service_id'        => $serviceLog->service_id,
                    'request_id'        => $itr->id,
                ]);

            if ($serviceLog->distributor_id && $serviceLog->distributor_commission > 0)
                self::add($serviceLog->distributor_id, 3, [
                    'amount'            => $serviceLog->distributor_commission,
                    'payment_type'      => 1,
                    'payment_method'    => 5,
                    'particulars'       => "ITR Service Commission : " . $itr->token,
                    'service_id'        => $serviceLog->service_id,
                    'request_id'        => $itr->id,
                ]);
        });
    }

    public static function refundItrService(ItReturn $itr, ServicesLog $serviceLog, string $errorMsg = "")
    {
        if ($itr->is_refunded == 0) {
            DB::transaction(function () use ($serviceLog, $itr, $errorMsg) {
                self::add($serviceLog->user_id, $serviceLog->user_type, [
                    'amount'            => $serviceLog->sale_rate,
                    'payment_type'      => 1,
                    'payment_method'    => 5,
                    'particulars'       => "Refund For ITR Service : " . $itr->token,
                    'service_id'        => $serviceLog->service_id,
                    'request_id'        => $itr->id,
                ]);

                if ($serviceLog->main_distributor_id && $serviceLog->main_distributor_commission > 0)
                    self::add($serviceLog->main_distributor_id, 2, [
                        'amount'            => $serviceLog->main_distributor_commission,
                        'payment_type'      => 2,
                        'payment_method'    => 5,
                        'particulars'       => "ITR Service Commission Refund : " . $itr->token,
                        'service_id'        => $serviceLog->service_id,
                        'request_id'        => $itr->id,
                    ]);

                if ($serviceLog->distributor_id && $serviceLog->distributor_commission > 0)
                    self::add($serviceLog->distributor_id, 3, [
                        'amount'            => $serviceLog->distributor_commission,
                        'payment_type'      => 2,
                        'payment_method'    => 5,
                        'particulars'       => "ITR Service Commission Refund :: " . $itr->token,
                        'service_id'        => $serviceLog->service_id,
                        'request_id'        => $itr->id,
                    ]);

                ServiceUsesLog::where(['request_id' => $itr->id, 'service_id' => $serviceLog->service_id])->update(['is_refunded' => 1]);
                $itr->update(['status' => 3, 'is_refunded' => 1, 'completed_date' => now(), 'comments' => $errorMsg]);
            });
        }
    }
}
