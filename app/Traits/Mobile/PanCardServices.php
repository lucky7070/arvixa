<?php

namespace App\Traits\Mobile;

use App\Models\PanCard;
use App\Models\Customer;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Library\PanCard as LibraryPanCard;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\LedgerController;
use App\Http\Controllers\Retailer\PanCardController;

trait PanCardServices
{
    public function add_pan_card(Request $request)
    {
        $IsPhyPan   = $request->IsPhyPan == 'N' ? 'N' : 'Y';
        $service_id = $IsPhyPan == 'N' ? config('constant.service_ids.pan_cards_add_digital') : config('constant.service_ids.pan_cards_add');
        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => $service_id,
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return response()->json([
                'status'    => false,
                'message'   => "Service Can't be used..!!",
                'data'      => []
            ]);

        if ($serviceLog->sale_rate > auth()->user()->user_balance)
            return response()->json([
                'status'    => false,
                'message'   => "Insufficient Balance to use this service..!!",
                'data'      => []
            ]);

        $validator = Validator::make(request()->all(), [
            'name'          => ['nullable', 'string', 'max:200'],
            'middle_name'   => ['nullable', 'string', 'max:200'],
            'last_name'     => ['required', 'string', 'max:200'],
            'email'         => ['required', 'email'],
            'phone'         => ['required', 'digits:10', 'regex:' . config('constant.phoneRegExp')],
            'gender'        => ['required', 'string'],
            'dob'           => ['required', 'date', 'date_format:Y-m-d', 'before:1 years ago'],
        ]);

        if ($validator->fails()) {
            $err = array();
            foreach ($validator->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ]);
        }

        $user = [
            'last_name'     => $request->last_name,
            'name'          => $request->name,
            'middle_name'   => $request->middle_name,
            'dob'           => $request->dob,
            'gender'        => $request->gender,
            'kyc_type'      => $request->kyc_type,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'card_type'     => $IsPhyPan
        ];

        $res = LibraryPanCard::newPan($user);
        if ($res) {
            if ($res['status']) {
                $customer = Customer::firstOrNew(['mobile' =>  request('phone')]);
                $customer->first_name    = request('name');
                $customer->middle_name   = request('middle_name');
                $customer->last_name     = request('last_name');
                $customer->dob           = Carbon::parse(request('dob'))->format('Y-m-d');
                $customer->email         = request('email');
                $customer->gender        = request('gender') == 'M' ? 1 : (request('gender') == 'F' ? 2 : (request('gender') == 'T' ? 3 : null));
                $customer->save();

                $apiData    = $res['data'];
                $data       = [
                    'slug'              => Str::uuid(),
                    'type'              => 1,
                    'user_id'           => $this->user_id,
                    'user_type'         => $this->user_type,
                    'name'              => $request->name,
                    'middle_name'       => $request->middle_name,
                    'last_name'         => $request->last_name,
                    'email'             => $request->email,
                    'phone'             => $request->phone,
                    'gender'            => $request->gender,
                    'doc'               => '',
                    'is_physical_card'  => $IsPhyPan,
                    'customer_id'       => $customer->id,
                    'nsdl_formdata'     => $apiData['req']['reqEntityData']['formData'],
                    'nsdl_txn_id'       => $apiData['req']['reqEntityData']['txnid'],
                    'useFrom'           => 2,
                ];

                $pan_card = PanCard::create($data);
                ServiceUsesLog::create([
                    'user_id'                       => $this->user_id,
                    'user_type'                     => $this->user_type,
                    'customer_id'                   => $customer->id,
                    'service_id'                    => $service_id,
                    'request_id'                    => $pan_card->id,
                    'used_in'                       => 1,
                    'purchase_rate'                 => $serviceLog->purchase_rate,
                    'sale_rate'                     => $serviceLog->sale_rate,
                    'main_distributor_id'           => $serviceLog->main_distributor_id,
                    'distributor_id'                => $serviceLog->distributor_id,
                    'main_distributor_commission'   => $serviceLog->main_distributor_commission,
                    'distributor_commission'        => $serviceLog->distributor_commission,
                    'is_refunded'                   => 0,
                    'created_at'                    => Carbon::now(),
                ]);

                LedgerController::chargePanCardService($pan_card, $serviceLog);
                $Output['req']          = $apiData['req'];
                $Output['submit_url']   = $apiData['submit_url'];
                return response()->json([
                    'status'    => true,
                    'message'   => 'Success.!!',
                    'data'      => $Output
                ], 200);
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => "validation error.",
                    'data'      => $res['data']
                ], 422);
            }
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Oops.. There is some error.!!',
                'data'      => ""
            ], 422);
        }
    }

    public function update_pan_card(Request $request)
    {
        $IsPhyPan   = $request->IsPhyPan == 'N' ? 'N' : 'Y';
        $service_id = $IsPhyPan == 'N' ? config('constant.service_ids.pan_cards_edit_digital') : config('constant.service_ids.pan_cards_edit');

        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => $service_id,
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return response()->json([
                'status'    => false,
                'message'   => "Service Can't be used..!!",
                'data'      => []
            ]);

        if ($serviceLog->sale_rate > auth()->user()->user_balance)
            return response()->json([
                'status'    => false,
                'message'   => "Insufficient Balance to use this service..!!",
                'data'      => []
            ]);

        $validator = Validator::make(request()->all(), [
            'name'          => ['nullable', 'string', 'max:255'],
            'middle_name'   => ['nullable', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email'],
            'phone'         => ['required', 'digits:10', 'regex:' . config('constant.phoneRegExp')],
            'gender'        => ['required', 'string'],
            'dob'           => ['required', 'date', 'date_format:Y-m-d', 'before:1 years ago'],
        ]);

        if ($validator->fails()) {
            $err = array();
            foreach ($validator->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ]);
        }

        $user = [
            'last_name'     => $request->last_name,
            'name'          => $request->name,
            'middle_name'   => $request->middle_name,
            'dob'           => $request->dob,
            'gender'        => $request->gender,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'card_type'     => $IsPhyPan == 'N' ? 'N' : 'Y'
        ];

        $res = LibraryPanCard::updatePan($user);
        if ($res) {

            if ($res['status']) {
                $customer = Customer::firstOrNew(['mobile' =>  request('phone')]);
                $customer->first_name    = request('name');
                $customer->middle_name   = request('middle_name');
                $customer->last_name     = request('last_name');
                $customer->dob           = request('dob');
                $customer->email         = request('email');
                $customer->gender        = request('gender') == 'M' ? 1 : (request('gender') == 'F' ? 2 : (request('gender') == 'T' ? 3 : null));
                $customer->save();

                $apiData = $res['data'];
                $data = [
                    'slug'              => Str::uuid(),
                    'type'              => 2,
                    'user_id'           => $this->user_id,
                    'user_type'         => $this->user_type,
                    'name'              => $request->name,
                    'middle_name'       => $request->middle_name,
                    'last_name'         => $request->last_name,
                    'email'             => $request->email,
                    'phone'             => $request->phone,
                    'gender'            => $request->gender,
                    'doc'               => '',
                    'is_physical_card'  => $IsPhyPan,
                    'customer_id'       => $customer->id,
                    'nsdl_formdata'     => $apiData['req']['reqEntityData']['formData'],
                    'nsdl_txn_id'       => $apiData['req']['reqEntityData']['txnid'],
                    'useFrom'           => 2,
                ];

                $pan_card = PanCard::create($data);
                ServiceUsesLog::create([
                    'user_id'                       => $this->user_id,
                    'user_type'                     => $this->user_type,
                    'customer_id'                   => $customer->id,
                    'service_id'                    => $service_id,
                    'request_id'                    => $pan_card->id,
                    'used_in'                       => 1,
                    'purchase_rate'                 => $serviceLog->purchase_rate,
                    'sale_rate'                     => $serviceLog->sale_rate,
                    'main_distributor_id'           => $serviceLog->main_distributor_id,
                    'distributor_id'                => $serviceLog->distributor_id,
                    'main_distributor_commission'   => $serviceLog->main_distributor_commission,
                    'distributor_commission'        => $serviceLog->distributor_commission,
                    'is_refunded'                   => 0,
                    'created_at'                    => Carbon::now(),
                ]);

                LedgerController::chargePanCardService($pan_card, $serviceLog);
                $Output['req']          = $apiData['req'];
                $Output['submit_url']   = $apiData['submit_url'];
                return response()->json([
                    'status'    => true,
                    'message'   => 'Success.!!',
                    'data'      => $Output
                ], 200);
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => "validation error.",
                    'data'      => $res['data']
                ], 422);
            }
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Oops.. There is some error.!!',
                'data'      => ""
            ], 422);
        }
    }

    public function list_pan()
    {
        $pageNo = request('pageNo', 1);
        $limit  = request('limit', 10);
        $limit  = $limit <= 50 ? $limit : 50;

        $query  = PanCard::query();
        $query->select('type', 'is_physical_card', 'name', 'middle_name', 'last_name', 'email', 'phone', 'gender', 'nsdl_txn_id', 'nsdl_ack_no', 'is_refunded',  'nsdl_complete', 'error_message', 'created_at');
        $query->where('user_id', $this->user_id);
        $query->where('user_type', $this->user_type);

        // Filtering
        if (request('nsdl_complete') || request('nsdl_complete') === '0') $query->where('nsdl_complete', request('nsdl_complete'));
        if (request('type') || request('type') === '0') $query->where('type', request('type'));
        if (request('nsdl_txn_id')) $query->where('nsdl_txn_id', request('nsdl_txn_id'));
        if (request('nsdl_ack_no')) $query->where('nsdl_ack_no', request('nsdl_ack_no'));
        if (request('is_physical_card')) $query->where('is_physical_card', request('is_physical_card'));

        $search = request('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
                $query->orWhere('middle_name', 'LIKE', "%{$search}%");
                $query->orWhere('last_name', 'LIKE', "%{$search}%");
                $query->orWhere('email', 'LIKE', "%{$search}%");
                $query->orWhere('phone', 'LIKE', "%{$search}%");
                $query->orWhere('nsdl_ack_no', 'LIKE', "%{$search}%");
                $query->orWhere('nsdl_txn_id', 'LIKE', "%{$search}%");
            });
        }

        $totalPage  = ceil($query->count() / $limit);

        // Ordering
        if (request('orderAs', 'desc') == 'desc') {
            $query->orderByDesc(request('orderBy', 'created_at'));
        } else {
            $query->orderBy(request('orderBy', 'created_at'));
        }

        // Set Offset
        $query->offset($limit * ($pageNo - 1));

        // Limiting
        $query->limit($limit);

        $panCards = $query->get();
        if (count($panCards) > 0) {
            return response()->json([
                'status'   => true,
                'message'   => 'Success',
                'data'      => $panCards,
                'totalPage' => $totalPage,
            ]);
        } else {
            return response()->json([
                'status'   => false,
                'message'   => "No Data Found.",
                'data'      => ""
            ], 404);
        }
    }

    public function pan_card_status(Request $request)
    {
        return PanCardController::pan_card_status($request);
    }
}
