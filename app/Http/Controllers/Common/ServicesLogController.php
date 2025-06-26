<?php

namespace App\Http\Controllers\Common;

use App\Models\Retailer;
use App\Models\Distributor;
use App\Models\ServicesLog;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Services;

class ServicesLogController extends Controller
{
    public static function update(int $service_id = null, int $user_id = null, int $user_type = 1,  $force = false): bool
    {
        $data = [
            'user_id'       => $user_id,
            'service_id'    => $service_id,
            'user_type'     => $user_type
        ];

        $service_log = ServicesLog::where(array_merge($data, ['status'    => 1]))->first();
        if ($force == 'on') {
            if (!$service_log) {
                ServicesLogController::start($data);
            }
        } elseif ($force == 'off') {
            if ($service_log) {
                ServicesLogController::stop($service_log, $data);
            }
        } else {
            if ($service_log) {
                ServicesLogController::stop($service_log, $data);
            } else {
                ServicesLogController::start($data);
            }
        }
        return true;
    }

    // For Assign Service to User
    protected static function start($data)
    {
        $data = [
            'user_id'       => $data['user_id'],
            'service_id'    => $data['service_id'],
            'user_type'     => $data['user_type'],
            'status'        => 1,
            'assign_date'   => Carbon::now(),
            'decline_date'  => null
        ];

        if ($data['user_type'] != 4) {
            $data = array_merge($data, [
                'purchase_rate'                 => 0,
                'sale_rate'                     => 0,
                'main_distributor_id'           => null,
                'distributor_id'                => null,
                'main_distributor_commission'   => 0,
                'distributor_commission'        => 0,
            ]);
        } else {
            $service    = Services::find($data['service_id']);
            $retailer   = Retailer::find($data['user_id']);
            if ($retailer && $retailer->distributor_id && $retailer->main_distributor_id) {
                $data = array_merge($data, [
                    'purchase_rate'                 => $service->purchase_rate,
                    'sale_rate'                     => $service->sale_rate,
                    'main_distributor_id'           => $retailer->main_distributor_id,
                    'distributor_id'                => $retailer->distributor_id,
                    'main_distributor_commission'   => $service->default_md_commission,
                    'distributor_commission'        => $service->default_d_commission,
                ]);
            } else if ($retailer && $retailer->distributor_id && $retailer->main_distributor_id == null) {
                $data = array_merge($data, [
                    'purchase_rate'                 => $service->purchase_rate,
                    'sale_rate'                     => $service->sale_rate,
                    'main_distributor_id'           => null,
                    'distributor_id'                => $retailer->distributor_id,
                    'main_distributor_commission'   => 0,
                    'distributor_commission'        => $service->default_d_commission,
                ]);
            } else {
                $data = array_merge($data, [
                    'purchase_rate'                 => $service->purchase_rate,
                    'sale_rate'                     => $service->sale_rate,
                    'main_distributor_id'           => null,
                    'distributor_id'                => null,
                    'main_distributor_commission'   => 0,
                    'distributor_commission'        => 0,
                ]);
            }
        }

        ServicesLog::create($data);
    }

    // For Revoke Service to User
    protected static function stop(ServicesLog $service_log,  $data)
    {
        $service_log->update([
            'status'        => 0,
            'decline_date'  => Carbon::now()
        ]);

        // For Main Distributors
        if ($data['user_type'] == 2) {
            $reated_distributor = Distributor::select('id')
                ->where('main_distributor_id', $data['user_id'])
                ->get()
                ->pluck('id')
                ->toArray();

            $related_retailer = Retailer::select('id')
                ->where('main_distributor_id', $data['user_id'])
                ->whereIn('distributor_id',  $reated_distributor, 'or')
                ->get()
                ->pluck('id')
                ->toArray();

            ServicesLog::where(function ($query) use ($reated_distributor) {
                $query->whereIn('user_id',  $reated_distributor);
                $query->where('user_type', 3);
                $query->where('status', 1);
            })->orWhere(function ($query) use ($related_retailer) {
                $query->whereIn('user_id', $related_retailer);
                $query->where('user_type', 4);
                $query->where('status', 1);
            })->update([
                'status'        => 0,
                'decline_date'  => Carbon::now()
            ]);
        }

        // For Distributors
        if ($data['user_type'] == 3) {
            $related_retailer = Retailer::select('id')
                ->where('distributor_id', $data['user_id'])
                ->get()
                ->pluck('id')
                ->toArray();

            ServicesLog::whereIn('user_id', $related_retailer)
                ->where('user_type', 4)
                ->where('status', 1)
                ->update([
                    'status'        => 0,
                    'decline_date'  => Carbon::now()
                ]);
        }
    }
}
