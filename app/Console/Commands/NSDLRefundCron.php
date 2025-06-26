<?php

namespace App\Console\Commands;

use App\Models\PanCard;
use App\Models\ServicesLog;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Library\PanCard as LibraryPanCard;
use App\Http\Controllers\Common\LedgerController;

class NSDLRefundCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nsdl:refund';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'NSDL PanCard Refund';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $panCards = PanCard::select('id', 'user_id', 'user_type', 'nsdl_txn_id', 'is_physical_card', 'type')
            ->where('nsdl_complete', 0)
            ->where('created_at', '<=', Carbon::now()->subMinute(config('ayt.nsdl.refund_after_time_minutes', 60))->toDateTimeString())
            ->get();

        $out = array();
        foreach ($panCards as $key => $value) {
            $data = $out[$key] = LibraryPanCard::getInfo($value->nsdl_txn_id);
            if ($data) {

                // Successfully Completed Case
                if ($data['nsdl_complete'] == 1 && $data['is_refunded'] == 0 && $data['nsdl_ack_no'] != null) {
                    $value->update([
                        'nsdl_ack_no'       => $data['nsdl_ack_no'],
                        'nsdl_complete'     => 1,
                        'error_message'     => 'PanCard Request Submitted Successfully..!!'
                    ]);
                }

                // Refund Case
                if ($data['nsdl_complete'] == 1 && $data['is_refunded'] == 1 && $data['nsdl_ack_no'] == null) {
                    if ($value->type == 1) {
                        $service_id = $value->is_physical_card == 'N' ? config('constant.service_ids.pan_cards_add_digital') : config('constant.service_ids.pan_cards_add');
                    } else {
                        $service_id = $value->is_physical_card == 'N' ? config('constant.service_ids.pan_cards_edit_digital') : config('constant.service_ids.pan_cards_edit');
                    }

                    $serviceLog = ServiceUsesLog::where([
                        'user_id'       => $value->user_id,
                        'user_type'     => $value->user_type,
                        'service_id'    => $service_id,
                        'request_id'    => $value->id,
                    ])->first();

                    if ($serviceLog) {
                        LedgerController::panCardRefund($value, $serviceLog, json_encode($data['error_message']));
                    }
                }
            }
        }

        Log::info("Refund Cron Run Successfully." . Carbon::now());
        return Command::SUCCESS;
    }
}
