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
use App\Models\Bill;

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

    public static function add(int $user_id, int $user_type = 1, array $data = []): bool
    {
        if ($data['amount'] === 0) return true;

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
            ->rawColumns(['voucher_no', 'amount', 'updated_balance', 'status'])
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

    public static function chargeForBillPayment(Bill $bill, ServicesLog $serviceLog)
    {
        DB::beginTransaction();
        try {

            $current    = now();
            $serviceName = 'Bill';
            if (config('constant.service_ids.electricity_bill') === $serviceLog->service_id) {
                $serviceName = 'Electricity Bill';
            } else if (config('constant.service_ids.water_bill') === $serviceLog->service_id) {
                $serviceName = 'Water Bill';
            } else if (config('constant.service_ids.lic_premium') === $serviceLog->service_id) {
                $serviceName = 'LIC Premium';
            } else if (config('constant.service_ids.gas_payment') === $serviceLog->service_id) {
                $serviceName = 'GAS Bill';
            }

            $balanceChange = 0;
            $balance = Retailer::where('id', $serviceLog->user_id)->value('user_balance');
            // Retailer Bill Amount
            if ($bill->bill_amount && $bill->bill_amount > 0) {
                $balanceChange += $bill->bill_amount;
                Ledger::create([
                    'voucher_no'                => Str::uuid(),
                    'user_id'                   => $serviceLog->user_id,
                    'user_type'                 => 4,
                    'amount'                    => $bill->bill_amount,
                    'payment_type'              => 2,
                    'payment_method'            => 5,
                    'particulars'               => $serviceName . " Charge for Transaction : " . $bill->transaction_id,
                    'date'                      => $current,
                    'trans_details_json'        => null,
                    'service_id'                => $serviceLog->service_id,
                    'request_id'                => $bill->id,
                    'paid_by'                   => null,
                    'current_balance'           => $balance,
                    'updated_balance'           => $balance - $bill->bill_amount,
                ]);
            }

            // Retailer Commission
            if ($bill->commission && $bill->commission > 0) {
                $balanceChange -= $bill->commission;
                Ledger::create([
                    'voucher_no'                => Str::uuid(),
                    'user_id'                   => $serviceLog->user_id,
                    'user_type'                 => 4,
                    'amount'                    => $bill->commission,
                    'payment_type'              => 1,
                    'payment_method'            => 5,
                    'particulars'               => $serviceName . " Commission for Transaction : " . $bill->transaction_id,
                    'date'                      => $current,
                    'trans_details_json'        => null,
                    'service_id'                => $serviceLog->service_id,
                    'request_id'                => $bill->id,
                    'paid_by'                   => null,
                    'current_balance'           => $balance - $bill->bill_amount,
                    'updated_balance'           => $balance - $bill->bill_amount + $bill->commission,
                ]);
            }

            // Retailer TDS
            if ($bill->tds && $bill->tds > 0) {
                $balanceChange += $bill->tds;
                Ledger::create([
                    'voucher_no'                => Str::uuid(),
                    'user_id'                   => $serviceLog->user_id,
                    'user_type'                 => 4,
                    'amount'                    => $bill->tds,
                    'payment_type'              => 2,
                    'payment_method'            => 5,
                    'particulars'               => $serviceName . " TDS Charge for Transaction : " . $bill->transaction_id,
                    'date'                      => $current,
                    'trans_details_json'        => null,
                    'service_id'                => $serviceLog->service_id,
                    'request_id'                => $bill->id,
                    'paid_by'                   => null,
                    'current_balance'           => $balance - $bill->bill_amount + $bill->commission,
                    'updated_balance'           => $balance - $bill->bill_amount + $bill->commission - $bill->tds,
                ]);
            }

            // For retailer: decrement (balance reduces by net charge)
            $netCharge = ($bill->bill_amount + $bill->tds) - $bill->commission;
            Retailer::where('id', $bill->user_id)->decrement('user_balance', $netCharge);

            if ($serviceLog->distributor_id) {
                $balance = Distributor::where('id', $serviceLog->distributor_id)->value('user_balance');
                // Distributor Commission
                $balanceChange = 0;
                if ($bill->commission_distributor && $bill->commission_distributor > 0) {
                    $balanceChange  += $bill->commission_distributor;
                    Ledger::create([
                        'voucher_no'                => Str::uuid(),
                        'user_id'                   => $serviceLog->distributor_id,
                        'user_type'                 => 3,
                        'amount'                    => $bill->commission_distributor,
                        'payment_type'              => 1,
                        'payment_method'            => 5,
                        'particulars'               => $serviceName . " Commission for Transaction : " . $bill->transaction_id,
                        'date'                      => $current,
                        'trans_details_json'        => null,
                        'service_id'                => $serviceLog->service_id,
                        'request_id'                => $bill->id,
                        'paid_by'                   => null,
                        'current_balance'           => $balance,
                        'updated_balance'           => $balance + $bill->commission_distributor,
                    ]);
                }

                // Distributor TDS
                if ($bill->tds_distributor && $bill->tds_distributor > 0) {
                    $balanceChange  -= $bill->tds_distributor;
                    Ledger::create([
                        'voucher_no'                => Str::uuid(),
                        'user_id'                   => $serviceLog->distributor_id,
                        'user_type'                 => 3,
                        'amount'                    => $bill->tds_distributor,
                        'payment_type'              => 2,
                        'payment_method'            => 5,
                        'particulars'               => $serviceName . " TDS Charge for Transaction : " . $bill->transaction_id,
                        'date'                      => $current,
                        'trans_details_json'        => null,
                        'service_id'                => $serviceLog->service_id,
                        'request_id'                => $bill->id,
                        'paid_by'                   => null,
                        'current_balance'           => $balance + $bill->commission_distributor,
                        'updated_balance'           => $balance + $bill->commission_distributor - $bill->tds_distributor,
                    ]);
                }

                // For distributors: increment (balance increases by net commission)
                if ($balanceChange != 0) Distributor::where('id', $serviceLog->distributor_id)->increment('user_balance', $balanceChange);
            }

            if ($serviceLog->main_distributor_id) {
                $balance = MainDistributor::where('id', $serviceLog->main_distributor_id)->value('user_balance');
                // Main Distributor Commission
                $balanceChange = 0;
                if ($bill->commission_main_distributor && $bill->commission_main_distributor > 0) {
                    $balanceChange  += $bill->commission_main_distributor;
                    Ledger::create([
                        'voucher_no'                => Str::uuid(),
                        'user_id'                   => $serviceLog->main_distributor_id,
                        'user_type'                 => 2,
                        'amount'                    => $bill->commission_main_distributor,
                        'payment_type'              => 1,
                        'payment_method'            => 5,
                        'particulars'               => $serviceName . " Commission for Transaction : " . $bill->transaction_id,
                        'date'                      => $current,
                        'trans_details_json'        => null,
                        'service_id'                => $serviceLog->service_id,
                        'request_id'                => $bill->id,
                        'paid_by'                   => null,
                        'current_balance'           => $balance,
                        'updated_balance'           => $balance + $bill->commission_main_distributor,
                    ]);
                }

                // Main Distributor TDS
                if ($bill->tds_main_distributor && $bill->tds_main_distributor > 0) {
                    $balanceChange  -= $bill->tds_main_distributor;
                    Ledger::create([
                        'voucher_no'                => Str::uuid(),
                        'user_id'                   => $serviceLog->main_distributor_id,
                        'user_type'                 => 2,
                        'amount'                    => $bill->tds_main_distributor,
                        'payment_type'              => 2,
                        'payment_method'            => 5,
                        'particulars'               => $serviceName . " TDS Charge for Transaction : " . $bill->transaction_id,
                        'date'                      => $current,
                        'trans_details_json'        => null,
                        'service_id'                => $serviceLog->service_id,
                        'request_id'                => $bill->id,
                        'paid_by'                   => null,
                        'current_balance'           => $balance + $bill->commission_main_distributor,
                        'updated_balance'           => $balance + $bill->commission_main_distributor + $bill->tds_main_distributor,
                    ]);
                }

                // For main distributors: increment (balance increases by net commission)
                if ($balanceChange != 0)  MainDistributor::where('id', $serviceLog->main_distributor_id)->increment('user_balance', $balanceChange);
            }


            ServiceUsesLog::create([
                'user_id'                       => $serviceLog->user_id,
                'user_type'                     => 4,
                'customer_id'                   => 0,
                'service_id'                    => $serviceLog->service_id,
                'request_id'                    => $bill->id,
                'used_in'                       => 1,
                'purchase_rate'                 => $serviceLog->purchase_rate,
                'sale_rate'                     => $serviceLog->sale_rate,
                'main_distributor_id'           => $serviceLog->main_distributor_id,
                'distributor_id'                => $serviceLog->distributor_id,
                'main_distributor_commission'   => $bill->commission_main_distributor,
                'distributor_commission'        => $bill->commission_distributor,
                'retailer_commission'           => $serviceLog->retailer_commission,
                'commission_slots'              => $serviceLog->commission_slots,
                'is_refunded'                   => 0,
                'created_at'                    => $current,
            ]);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }
    }

    public static function refundForBillPayment(Bill $bill, ServiceUsesLog $serviceLog)
    {
        DB::beginTransaction();
        try {

            $current = now();
            $serviceName = 'Bill';
            if (config('constant.service_ids.electricity_bill') === $serviceLog->service_id) {
                $serviceName = 'Electricity Bill';
            } else if (config('constant.service_ids.water_bill') === $serviceLog->service_id) {
                $serviceName = 'Water Bill';
            } else if (config('constant.service_ids.lic_premium') === $serviceLog->service_id) {
                $serviceName = 'LIC Premium';
            } else if (config('constant.service_ids.gas_payment') === $serviceLog->service_id) {
                $serviceName = 'GAS Bill';
            }

            $balanceChange = 0;
            $balance = Retailer::where('id', $serviceLog->user_id)->value('user_balance');
            // Refund Bill Amount (credit to retailer)
            if ($bill->bill_amount && $bill->bill_amount > 0) {
                $balanceChange += $bill->bill_amount;
                Ledger::create([
                    'voucher_no'            => Str::uuid(),
                    'user_id'               => $serviceLog->user_id,
                    'user_type'             => 4,
                    'amount'                => $bill->bill_amount,
                    'payment_type'          => 1, // Credit
                    'payment_method'        => 5,
                    'particulars'           => $serviceName . " Refund for Transaction: " . $bill->transaction_id,
                    'date'                  => $current,
                    'trans_details_json'    => null,
                    'service_id'            => $serviceLog->service_id,
                    'request_id'            => $bill->id,
                    'paid_by'               => null,
                    'current_balance'       => $balance,
                    'updated_balance'       => $balance + $bill->bill_amount,
                ]);
            }

            // Reverse Commission (debit from retailer)
            if ($bill->commission && $bill->commission > 0) {
                $balanceChange -= $bill->commission;
                Ledger::create([
                    'voucher_no'            => Str::uuid(),
                    'user_id'               => $serviceLog->user_id,
                    'user_type'             => 4,
                    'amount'                => $bill->commission,
                    'payment_type'          => 2, // Debit
                    'payment_method'        => 5,
                    'particulars'           => $serviceName . " Commission Reversal for Transaction: " . $bill->transaction_id,
                    'date'                  => $current,
                    'trans_details_json'    => null,
                    'service_id'            => $serviceLog->service_id,
                    'request_id'            => $bill->id,
                    'paid_by'               => null,
                    'current_balance'       => $balance + $bill->bill_amount,
                    'updated_balance'       => $balance + $bill->bill_amount - $bill->commission,
                ]);
            }

            // Refund TDS (credit to retailer)
            if ($bill->tds && $bill->tds > 0) {
                $balanceChange += $bill->tds;
                Ledger::create([
                    'voucher_no'            => Str::uuid(),
                    'user_id'               => $serviceLog->user_id,
                    'user_type'             => 4,
                    'amount'                => $bill->tds,
                    'payment_type'          => 1, // Credit
                    'payment_method'        => 5,
                    'particulars'           => $serviceName . " TDS Refund for Transaction: " . $bill->transaction_id,
                    'date'                  => $current,
                    'trans_details_json'    => null,
                    'service_id'            => $serviceLog->service_id,
                    'request_id'            => $bill->id,
                    'paid_by'               => null,
                    'current_balance'       => $balance + $bill->bill_amount - $bill->commission,
                    'updated_balance'       => $balance + $bill->bill_amount - $bill->commission + $bill->tds,
                ]);
            }

            // Update retailer balance
            $netRefund = ($bill->bill_amount + $bill->tds) - $bill->commission;
            if ($netRefund != 0) {
                Retailer::where('id', $serviceLog->user_id)->increment('user_balance', $netRefund);
            }

            // Handle distributor refunds if applicable
            if ($serviceLog->distributor_id) {
                $distributorBalanceChange = 0;
                $balance = Distributor::where('id', $serviceLog->distributor_id)->value('user_balance');
                // Reverse Distributor Commission
                if ($bill->commission_distributor && $bill->commission_distributor > 0) {
                    $distributorBalanceChange -= $bill->commission_distributor;
                    Ledger::create([
                        'voucher_no'            => Str::uuid(),
                        'user_id'               => $serviceLog->distributor_id,
                        'user_type'             => 3,
                        'amount'                => $bill->commission_distributor,
                        'payment_type'          => 2, // Debit
                        'payment_method'        => 5,
                        'particulars'           => $serviceName . " Commission Reversal for Transaction: " . $bill->transaction_id,
                        'date'                  => $current,
                        'trans_details_json'    => null,
                        'service_id'            => $serviceLog->service_id,
                        'request_id'            => $bill->id,
                        'paid_by'               => null,
                        'current_balance'       => $balance,
                        'updated_balance'       => $balance - $bill->commission_distributor,
                    ]);
                }

                // Refund Distributor TDS
                if ($bill->tds_distributor && $bill->tds_distributor > 0) {
                    $distributorBalanceChange += $bill->tds_distributor;
                    Ledger::create([
                        'voucher_no'            => Str::uuid(),
                        'user_id'               => $serviceLog->distributor_id,
                        'user_type'             => 3,
                        'amount'                => $bill->tds_distributor,
                        'payment_type'          => 1, // Credit
                        'payment_method'        => 5,
                        'particulars'           => $serviceName . " TDS Refund for Transaction: " . $bill->transaction_id,
                        'date'                  => $current,
                        'trans_details_json'    => null,
                        'service_id'            => $serviceLog->service_id,
                        'request_id'            => $bill->id,
                        'paid_by'               => null,
                        'current_balance'       => $balance - $bill->commission_distributor,
                        'updated_balance'       => $balance - $bill->commission_distributor + $bill->tds_distributor,
                    ]);
                }

                if ($distributorBalanceChange != 0) {
                    Distributor::where('id', $serviceLog->distributor_id)->increment('user_balance', $distributorBalanceChange);
                }
            }

            // Handle main distributor refunds if applicable
            if ($serviceLog->main_distributor_id) {
                $mainDistributorBalanceChange = 0;
                $balance = MainDistributor::where('id', $serviceLog->main_distributor_id)->value('user_balance');
                // Reverse Main Distributor Commission
                if ($bill->commission_main_distributor && $bill->commission_main_distributor > 0) {
                    $mainDistributorBalanceChange -= $bill->commission_main_distributor;
                    Ledger::create([
                        'voucher_no'            => Str::uuid(),
                        'user_id'               => $serviceLog->main_distributor_id,
                        'user_type'             => 2,
                        'amount'                => $bill->commission_main_distributor,
                        'payment_type'          => 2, // Debit
                        'payment_method'        => 5,
                        'particulars'           => $serviceName . " Commission Reversal for Transaction: " . $bill->transaction_id,
                        'date'                  => $current,
                        'trans_details_json'    => null,
                        'service_id'            => $serviceLog->service_id,
                        'request_id'            => $bill->id,
                        'paid_by'               => null,
                        'current_balance'       => $balance,
                        'updated_balance'       => $balance - $bill->commission_main_distributor,
                    ]);
                }

                // Refund Main Distributor TDS
                if ($bill->tds_main_distributor && $bill->tds_main_distributor > 0) {
                    $mainDistributorBalanceChange += $bill->tds_main_distributor;
                    Ledger::create([
                        'voucher_no'            => Str::uuid(),
                        'user_id'               => $serviceLog->main_distributor_id,
                        'user_type'             => 2,
                        'amount'                => $bill->tds_main_distributor,
                        'payment_type'          => 1, // Credit
                        'payment_method'        => 5,
                        'particulars'           => $serviceName . " TDS Refund for Transaction: " . $bill->transaction_id,
                        'date'                  => $current,
                        'trans_details_json'    => null,
                        'service_id'            => $serviceLog->service_id,
                        'request_id'            => $bill->id,
                        'paid_by'               => null,
                        'current_balance'       => $balance - $bill->commission_main_distributor,
                        'updated_balance'       => $balance - $bill->commission_main_distributor + $bill->tds_main_distributor,
                    ]);
                }

                if ($mainDistributorBalanceChange != 0) {
                    MainDistributor::where('id', $serviceLog->main_distributor_id)->increment('user_balance', $mainDistributorBalanceChange);
                }
            }

            // Update service log and bill status
            $serviceLog->update(['is_refunded' => 1]);
            $bill->update(['status' => 2, 'is_refunded' => 1]);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }
    }
}
