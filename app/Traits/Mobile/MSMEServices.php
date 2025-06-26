<?php

namespace App\Traits\Mobile;

use App\Models\City;
use App\Models\State;
use App\Models\Customer;
use App\Models\ServicesLog;
use App\Models\CustomerBank;
use Illuminate\Http\Request;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use App\Models\MSMECertificate;
use App\Models\CustomerDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\LedgerController;

trait MSMEServices
{
    public function msme_dropdowns(Request $request)
    {
        $data = [
            'gender_list'           => collect(config('constant.gender_list', []))->map(fn ($row, $key) => ['id' => $key, 'name' => $row])->values(),
            'social_category_list'  => collect(config('constant.social_category_list', []))->map(fn ($row, $key) => ['id' => $key, 'name' => $row])->values(),
            'pancard_type_list'     => collect(config('constant.pancard_type_list', []))->map(fn ($row, $key) => ['id' => $key, 'name' => $row])->values(),
            'unit_activity'         => collect([
                'Manufacturing', 'Services', 'Trading'
            ])->map(fn ($row) => ['id' => $row, 'name' => $row])->values(),

            'bank_account_type'         => collect(config('constant.bank_account_type', []))->map(fn ($row, $key) => ['id' => $key, 'name' => $row])->values(),
            'bank_account_holder_type'  => collect(config('constant.bank_account_holder_type', []))->map(fn ($row, $key) => ['id' => $key, 'name' => $row])->values(),
            'employeer_types'           => collect(config('constant.employeer_types', []))->map(fn ($row, $key) => ['id' => $key, 'name' => $row])->values(),
            'rented_house_type'         => collect(config('constant.rented_house_type', []))->map(fn ($row, $key) => ['id' => $key, 'name' => $row])->values(),
            'capital_gain_asset_type'   => collect(config('constant.capital_gain_asset_type', []))->map(fn ($row, $key) => ['id' => $key, 'name' => $row])->values(),
            'business_type_list'        => collect(config('constant.business_type_list', []))->map(fn ($row, $key) => ['id' => $key, 'name' => $row])->values(),

        ];

        return response()->json([
            'status'    => true,
            'message'   => "Success..!!",
            'data'      => $data
        ]);
    }

    public function msme_certificate_save(Request $request)
    {
        $serviceLog = ServicesLog::where([
            'user_id'       => $this->user_id,
            'user_type'     => $this->user_type,
            'service_id'    => config('constant.service_ids.msme_certificate'),
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return response()->json([
                'status'    => false,
                'message'   => "Service Can't be used..!!",
                'data'      => []
            ]);

        if ($serviceLog->sale_rate > $this->user->user_balance)
            return response()->json([
                'status'    => false,
                'message'   => "Insufficient Balance to use this service..!!",
                'data'      => []
            ]);

        $validation = Validator::make(request()->all(), [
            'name'                      => ['required', 'string', 'max:50'],
            'email'                     => ['required', 'string', 'max:50', 'email'], //, 'regex:' . config('constant.emailRegExp')
            'phone'                     => ['required', 'digits:10', 'integer', 'regex:' . config('constant.phoneRegExp')],
            'aadharcard'                => ['required', 'digits:12', 'integer', 'regex:' . config('constant.aadhaarRegExp')],
            'aadhar_file'               => ['required', 'max:1000', 'mimes:png,jpg,jpeg,pdf'],
            'pancard_type'              => ['required', 'integer'],
            'pancard'                   => ['required', 'string', 'alpha_num', 'size:10', 'regex:' . config('constant.pancardRegExp')],
            'pancard_file'              => ['required', 'max:1000', 'mimes:png,jpg,jpeg,pdf'],
            'category'                  => ['required', 'integer'],
            'gender'                    => ['required', 'integer'],
            'special_abled'             => ['required', 'boolean'],
            'name_enterprise'           => ['required', 'string', 'max:100'],
            'name_plant'                => ['required', 'string', 'max:100'],
            'flat_plant'                => ['required', 'string', 'max:100'],
            'building_plant'            => ['required', 'string', 'max:100'],
            'block_plant'               => ['required', 'string', 'max:100'],
            'street_plant'              => ['required', 'string', 'max:100'],
            'village_plant'             => ['required', 'string', 'max:100'],
            'city'                      => ['required', 'integer'],
            'state'                     => ['required', 'integer'],
            'pincode'                   => ['required', 'digits:6', 'integer'],
            'enterprise_registration'   => ['required', 'date', 'date_format:Y-m-d'],
            'enterprise_date'           => ['required', 'date', 'date_format:Y-m-d'],
            'bank_name'                 => ['required', 'string', 'max:100'],
            'bank_ifsc'                 => ['required', 'string', 'max:100'],
            'bank_account'              => ['required', 'string', 'max:100'],
            'unit_type'                 => ['required', 'string', 'max:100'],
            'nic_description'           => ['required', 'string', 'max:500'],
            'emp_male'                  => ['required', 'integer', 'min:0'],
            'emp_female'                => ['required', 'integer', 'min:0'],
            'emp_other'                 => ['required', 'integer', 'min:0'],
            'emp_total'                 => ['required', 'integer', 'min:0'],
            'inv_wdv_a'                 => ['required', 'numeric', 'min:0'],
            'turnover_a'                => ['required', 'numeric', 'min:0'],
        ]);

        if ($validation->fails()) {
            return CommonController::validationFails($validation);
        }

        DB::transaction(function () use ($request, $serviceLog) {

            $path = 'documents';
            if ($file = $request->file('aadhar_file')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $adhaar_file        = $path . '/' . $uploadImage;
            }

            if ($file = $request->file('pancard_file')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $pancard_file       = $path . '/' . $uploadImage;
            }

            $customer = Customer::firstOrNew(['mobile' =>  request('phone')]);
            $customer->first_name   = request('name');
            $customer->email        = request('email');
            $customer->gender       = request('gender');
            $customer->state_id     = request('state');
            $customer->city_id      = request('city');
            $customer->save();

            // Customer Bank Data Save
            $customerBank   = CustomerBank::firstOrNew(['customer_id' => $customer->id, 'account_number' => $request->bank_account]);
            $customerBank->account_name     = $request->name;
            $customerBank->account_bank     = $request->bank_name;
            $customerBank->account_ifsc     = $request->bank_ifsc;
            $customerBank->save();

            // Customer Aadhaar Data Save
            $customerAadhar = CustomerDocument::firstOrNew(['customer_id' => $customer->id, 'doc_type' => 1]);
            $customerAadhar->doc_number     = $request->aadharcard;
            if ($adhaar_file) $customerAadhar->doc_img_front  = $adhaar_file;
            $customerAadhar->save();

            // Customer PanCard Data Save
            $customerPan    = CustomerDocument::firstOrNew(['customer_id' => $customer->id, 'doc_type' => 4]);
            $customerPan->doc_number        = $request->pancard;
            if ($pancard_file) $customerPan->doc_img_front     = $pancard_file;
            $customerPan->save();

            $toSave = [
                'useFrom'                   => 1,
                'txn_id'                    => "AYT" . rand(10000000, 99999999),
                'customer_id'               => $customer->id,
                'user_id'                   => $this->user_id,
                'user_type'                 => $this->user_type,
                'name'                      => $request->name,
                'email'                     => $request->email,
                'phone'                     => $request->phone,
                'aadharcard'                => $request->aadharcard,
                'aadhar_file'               => $adhaar_file,
                'pancard_type'              => $request->pancard_type,
                'pancard'                   => $request->pancard,
                'pancard_file'              => $pancard_file,
                'category'                  => $request->category,
                'gender'                    => $request->gender,
                'special_abled'             => $request->special_abled,
                'name_enterprise'           => $request->name_enterprise,
                'name_plant'                => $request->name_plant,
                'flat_plant'                => $request->flat_plant,
                'building_plant'            => $request->building_plant,
                'block_plant'               => $request->block_plant,
                'street_plant'              => $request->street_plant,
                'village_plant'             => $request->village_plant,
                'city'                      => City::find($request->city)?->name,
                'state'                     => State::find($request->state)?->name,
                'country'                   => 'India',
                'pincode'                   => $request->pincode,
                'enterprise_registration'   => $request->enterprise_registration,
                'enterprise_date'           => $request->enterprise_date,
                'bank_name'                 => $request->bank_name,
                'bank_ifsc'                 => $request->bank_ifsc,
                'bank_account'              => $request->bank_account,
                'unit_type'                 => $request->unit_type,
                'nic_description'           => $request->nic_description,
                'emp_male'                  => $request->emp_male,
                'emp_female'                => $request->emp_female,
                'emp_other'                 => $request->emp_other,
                'emp_total'                 => $request->emp_total,
                'inv_wdv_a'                 => $request->inv_wdv_a,
                'turnover_a'                => $request->turnover_a,
                'certificate'               => null,
                'comment'                   => null,
                'is_refunded'               => 0,
                'error_message'             => null,
                'status'                    => 0
            ];



            $certificate = MSMECertificate::create($toSave);
            ServiceUsesLog::create([
                'user_id'                       => $this->user_id,
                'user_type'                     => $this->user_type,
                'service_id'                    => config('constant.service_ids.msme_certificate'),
                'customer_id'                   => $customer->id,
                'request_id'                    => $certificate->id,
                'used_in'                       => 1,
                'purchase_rate'                 => $serviceLog->purchase_rate,
                'sale_rate'                     => $serviceLog->sale_rate,
                'main_distributor_id'           => $serviceLog->main_distributor_id,
                'distributor_id'                => $serviceLog->distributor_id,
                'main_distributor_commission'   => $serviceLog->main_distributor_commission,
                'distributor_commission'        => $serviceLog->distributor_commission,
                'agent_commission'              => 0,
                'is_refunded'                   => 0,
                'created_at'                    => Carbon::now(),
            ]);

            LedgerController::chargeMSMEService($certificate, $serviceLog);
        });

        return response()->json([
            'status'    => true,
            'message'   => "Request Submitted Successfully..!!",
            'data'      => []
        ]);
    }

    public function msme_certificate_list(Request $request)
    {
        $pageNo = request('pageNo', 1);
        $limit  = request('limit', 10);
        $limit  = $limit <= 50 ? $limit : 50;

        $query = MSMECertificate::query();

        $query->select('*');
        $query->where('user_type', $this->user_type);
        $query->where('user_id', $this->user_id);

        if (request('start_date') && request('end_date')) {
            $startDate = Carbon::parse(request('start_date'));
            $endDate = Carbon::parse(request('end_date'))->endOfDay();
            if ($startDate->eq($endDate)) {
                $startDateStr = $startDate->format('Y-m-d');
                $query->whereDate('created_at', $startDateStr);
            } else {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        $search = request('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
                $query->orWhere('email', 'LIKE', "%{$search}%");
                $query->orWhere('phone', 'LIKE', "%{$search}%");
                $query->orWhere('aadharcard', 'LIKE', "%{$search}%");
                $query->orWhere('txn_id', 'LIKE', "%{$search}%");
            });
        }

        // Ordering
        if (request('orderAs', 'desc') == 'desc') {
            $query->orderByDesc(request('orderBy', 'created_at'));
        } else {
            $query->orderBy(request('orderBy', 'created_at'));
        }

        $totalPage  = ceil($query->count() / $limit);

        // Set Offset
        $query->offset($limit * ($pageNo - 1));

        // Limiting
        $query->limit($limit);

        $panCards = $query->get()->map(function ($data) {
            $data->category         = config('constant.social_category_list.' . $data->category, '');
            $data->gender           = config('constant.gender_list.' . $data->gender, '');
            $data->pancard_has      = yesNo($data->pancard_has);
            $data->pancard_type     = config('constant.pancard_type_list.' . $data->pancard_type, '');
            $data->special_abled    = yesNo($data->special_abled);
            $data->get_register     = yesNo($data->get_register);
            $data->treds_rgister    = yesNo($data->treds_rgister);
            $data->is_refunded      = yesNo($data->is_refunded);
            $data->status           = $data->status == 0 ? "Pending" : ($data->status == 1 ? "Submitted" : "Rejected");
            return $data;
        });

        if ($panCards->count()) {
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

    public function msme_certificate_details($slug)
    {
        $data = MSMECertificate::select('*')
            ->where('txn_id', $slug)
            ->where('user_type', $this->user_type)
            ->where('user_id', $this->user_id)
            ->first();

        if (!$data) {
            return response()->json([
                'status'    => false,
                'message'   => "Details not found..!!",
                'data'      => []
            ]);
        }

        $data->category         = config('constant.social_category_list.' . $data->category, '');
        $data->gender           = config('constant.gender_list.' . $data->gender, '');
        $data->pancard_has      = yesNo($data->pancard_has);
        $data->pancard_type     = config('constant.pancard_type_list.' . $data->pancard_type, '');
        $data->special_abled    = yesNo($data->special_abled);
        $data->get_register     = yesNo($data->get_register);
        $data->treds_rgister    = yesNo($data->treds_rgister);
        $data->is_refunded      = yesNo($data->is_refunded);
        $data->status           = $data->status == 0 ? "Pending" : ($data->status == 1 ? "Submitted" : "Rejected");

        return response()->json([
            'status'    => true,
            'message'   => "Success..!!",
            'data'      => $data
        ]);
    }
}
