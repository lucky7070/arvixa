<?php

namespace App\Traits\Mobile;

use App\Models\Customer;
use App\Models\ItReturn;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use App\Models\CustomerBank;
use Illuminate\Http\Request;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\CustomerDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Common\LedgerController;

trait ITRServices
{
    public function save(Request $request, $step = null, $slug = null)
    {
        if (in_array($step, ['income-sources', 'tax-saving', 'tax-summary'])) {
            $itr = ItReturn::firstWhere('slug', $slug);
            if (!$itr) {
                return self::responce('error', "Token Not valid..!!");
            }
        } else {
            $itr = ItReturn::firstOrNew(['slug' => $slug]);
        }

        if ($itr->status > 0 && $itr->status != 4) {
            return self::responce('error', "This ITR File can't be updated..!!");
        }

        $request->merge(['itr' => $itr]);
        switch ($step) {
            case 'personal-info':
                $step_res = self::stepOne($request, $this->user_id, $this->user_type);
                break;
            case 'income-sources':
                $step_res = self::stepTwo($request, $this->user_id, $this->user_type);
                break;
            case 'tax-saving':
                $step_res = self::stepThree($request, $this->user_id, $this->user_type);
                break;
            case 'tax-summary':
                $step_res = self::stepFour($request, $this->user_id, $this->user_type);
                break;
            default:
                $step_res = self::stepOne($request, $this->user_id, $this->user_type);
                break;
        }

        return $step_res;
    }

    protected static function stepOne(Request $request, $user_id, $user_type)
    {
        $validation = Validator::make($request->all(), [
            'first_name'            => ['nullable', 'string', 'min:2', 'max:100'],
            'middle_name'           => ['nullable', 'string', 'min:2', 'max:100'],
            'last_name'             => ['required', 'string', 'min:2', 'max:100'],
            'date_of_birth'         => ['required', 'date',],
            'father_first_name'     => ['nullable', 'string', 'min:2', 'max:100'],
            'father_middle_name'    => ['nullable', 'string', 'min:2', 'max:100'],
            'father_last_name'      => ['required', 'string', 'min:2', 'max:100'],
            'gender'                => ['required', 'integer'],
            'email'                 => ['required', 'string', 'email', 'max:50'],
            'phone'                 => ['required', 'digits:10', 'integer', 'regex:' . config('constant.phoneRegExp')],
            'itr_password'          => ['nullable', 'string', 'min:2', 'max:100'],
            'pancard_number'        => ['required', 'string', 'alpha_num', 'max:15', 'regex:' . config('constant.pancardRegExp')],
            'pancard_file'          => [Rule::requiredIf($request->itr->pancard_file == null && $request->pancard_file_old == null),  'max:2048', 'mimes:png,jpg,jpeg,pdf'],
            'adhaar_number'         => ['required', 'digits:12', 'integer', 'regex:' . config('constant.aadhaarRegExp')],
            'adhaar_file'           => [Rule::requiredIf($request->itr->adhaar_file == null && $request->adhaar_file_old == null), 'max:2048', 'mimes:png,jpg,jpeg,pdf'],
            'phone_2'               => ['nullable', 'digits:10', 'integer', 'regex:' . config('constant.phoneRegExp')],
            'flat_number'           => ['required', 'string', 'min:2', 'max:100'],
            'address'               => ['required', 'string', 'min:2', 'max:100'],
            'city'                  => ['required', 'integer'],
            'state'                 => ['required', 'integer'],
            'pincode'               => ['required', 'digits:6', 'integer'],
            'bank_ifsc'             => ['required', 'string', 'max:15', 'alpha_num'],
            'bank_name'             => ['required', 'string', 'max:100'],
            'account_type'          => ['required', 'integer'],
            'bank_account_type'     => ['required', 'integer'],
            'bank_account_no'       => ['required', 'string', 'max:50'],
            'bank_statment_file'    => ['nullable', 'max:2048', 'mimes:png,jpg,jpeg,pdf']
        ]);

        if ($validation->fails()) {
            return CommonController::validationFails($validation);
        } else {
            $date   = now();
            $year_1 = $date->format('Y');
            $year_2 = $date->subYear()->format('Y');
            $year_3 = $date->subYear()->format('Y');

            $request->itr->token                 = 'ITR' . Str::upper(Str::random(7));
            $request->itr->slug                  = Str::uuid();
            $request->itr->user_id               = $user_id;
            $request->itr->user_type             = $user_type;
            $request->itr->assessment_year       = $year_1 . '-' . $year_2;
            $request->itr->financial_year        = $year_2 . '-' . $year_3;
            $request->itr->is_step_1_complete    = 1;
            $request->itr->first_name            = $request->first_name;
            $request->itr->middle_name           = $request->middle_name;
            $request->itr->last_name             = $request->last_name;
            $request->itr->date_of_birth         = $request->date_of_birth;
            $request->itr->father_first_name     = $request->father_first_name;
            $request->itr->father_middle_name    = $request->father_middle_name;
            $request->itr->father_last_name      = $request->father_last_name;
            $request->itr->gender                = $request->gender;
            $request->itr->email                 = $request->email;
            $request->itr->phone                 = $request->phone;
            $request->itr->itr_password          = $request->itr_password;
            $request->itr->pancard_number        = $request->pancard_number;
            $request->itr->adhaar_number         = $request->adhaar_number;
            $request->itr->phone_2               = $request->phone_2;
            $request->itr->flat_number           = $request->flat_number;
            $request->itr->address               = $request->address;
            $request->itr->city                  = $request->city;
            $request->itr->state                 = $request->state;
            $request->itr->pincode               = $request->pincode;
            $request->itr->country               = "India";
            $request->itr->bank_ifsc             = $request->bank_ifsc;
            $request->itr->bank_name             = $request->bank_name;
            $request->itr->account_type          = $request->account_type;
            $request->itr->bank_account_type     = $request->bank_account_type;
            $request->itr->bank_account_no       = $request->bank_account_no;

            $pancard_file   = $request->itr->pancard_file;
            $adhaar_file    = $request->itr->adhaar_file;
            $path = 'customer-documents';
            if ($file = $request->file('pancard_file')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $pancard_file        = $path . '/' . $uploadImage;
            }

            if ($file = $request->file('adhaar_file')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $adhaar_file        = $path . '/' . $uploadImage;
            }

            if ($file = $request->file('bank_statment_file')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $request->itr->bank_statment_file    = $path . '/' . $uploadImage;
            }

            DB::transaction(function () use ($request, $pancard_file, $adhaar_file) {

                // Customer Profile Data Save
                $customer = Customer::firstOrNew(['mobile' =>  request('phone')]);
                $customer->first_name           = $request->first_name;
                $customer->middle_name          = $request->middle_name;
                $customer->last_name            = $request->last_name;
                $customer->email                = $request->email;
                $customer->mobile               = $request->phone;
                $customer->dob                  = $request->date_of_birth;
                $customer->state_id             = $request->state;
                $customer->city_id              = $request->city;
                $customer->gender               = $request->gender;
                $customer->father_first_name    = $request->father_first_name;
                $customer->father_middle_name   = $request->father_middle_name;
                $customer->father_last_name     = $request->father_last_name;
                $customer->itr_password         = $request->itr_password;
                $customer->address              = $request->flat_number . ', ' . $request->address;
                $customer->pincode              = $request->pincode;
                $customer->save();

                // Customer Bank Data Save
                $customerBank   = CustomerBank::firstOrNew(['customer_id' => $customer->id, 'account_number' => $request->bank_account_no]);
                $customerBank->account_bank     = $request->bank_name;
                $customerBank->account_name     = trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name);
                $customerBank->account_ifsc     = $request->bank_ifsc;
                $customerBank->save();

                // Customer Aadhaar Data Save
                $customerAadhar = CustomerDocument::firstOrNew(['customer_id' => $customer->id, 'doc_type' => 1]);
                $customerAadhar->doc_number     = $request->adhaar_number;
                if ($adhaar_file) $customerAadhar->doc_img_front  = $adhaar_file;
                $customerAadhar->save();

                // Customer PanCard Data Save
                $customerPan    = CustomerDocument::firstOrNew(['customer_id' => $customer->id, 'doc_type' => 4]);
                $customerPan->doc_number        = $request->pancard_number;
                if ($pancard_file) $customerPan->doc_img_front     = $pancard_file;
                $customerPan->save();

                // ITR Data Save
                $request->itr->customer_id      = $customer->id;
                $request->itr->adhaar_file      = $adhaar_file;
                $request->itr->pancard_file     = $pancard_file;
                $request->itr->save();
            });

            return self::responce('success', 'Information has been saved.', $request->itr->toArray());
        }
    }

    protected static function stepTwo(Request $request, $user_id, $user_type)
    {
        $validation = Validator::make($request->all(), [
            'assessment_year'                                   => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'tax_regime'                                        => ['required', 'in:N,O'],
            'is_salary_income'                                  => ['nullable', 'boolean'],
            'employer_name'                                     => ['required_if:is_salary_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'employer_tan'                                      => ['required_if:is_salary_income,1', 'nullable', 'alpha_num', 'max:15'],
            'employer_flat_number'                              => ['required_if:is_salary_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'employer_address'                                  => ['required_if:is_salary_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'employer_city'                                     => ['required_if:is_salary_income,1', 'nullable', 'integer'],
            'employer_state'                                    => ['required_if:is_salary_income,1', 'nullable', 'integer'],
            'employer_pincode'                                  => ['required_if:is_salary_income,1', 'nullable', 'string', 'digits:6'],
            'employer_type'                                     => ['required_if:is_salary_income,1', 'nullable', 'integer'],
            'salary'                                            => ['required_if:is_salary_income,1', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'dearness_allowances'                               => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'bonus_commission'                                  => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'other_amount_head'                                 => ['nullable', 'string', 'min:6', 'max:100'],
            'other_amount'                                      => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'form_16_file'                                      => ['nullable', 'max:2048', 'mimes:png,jpg,jpeg,pdf'],
            'is_house_income'                                   => ['nullable', 'boolean'],
            'income_house_type'                                 => ['required_if:is_house_income,1', 'nullable', 'integer'],
            'income_house_flat_number'                          => ['required_if:is_house_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'income_house_address'                              => ['required_if:is_house_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'income_house_city'                                 => ['required_if:is_house_income,1', 'nullable', 'integer'],
            'income_house_state'                                => ['required_if:is_house_income,1', 'nullable', 'integer'],
            'income_house_pincode'                              => ['required_if:is_house_income,1', 'nullable', 'string', 'digits:6'],
            'income_house_rent_received'                        => ['required_if:is_house_income,1', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'interest_paid_on_home_loan'                        => ['required_if:is_house_income,1', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'principal_paid_on_home_loan'                       => ['required_if:is_house_income,1', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'rent_agreement'                                    => ['nullable', 'max:2048', 'mimes:png,jpg,jpeg,pdf'],
            'is_business_income'                                => ['nullable', 'boolean'],
            'business_name'                                     => ['nullable', 'string', 'max:100'],
            'business_type'                                     => ['nullable', 'integer'],
            'turnover'                                          => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'net_profit'                                        => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'description'                                       => ['nullable', 'string', 'max:500'],
            'partners_own_capital'                              => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'liabilities_secured_loans'                         => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'liabilities_unsecured_loans'                       => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'liabilities_advances'                              => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'liabilities_sundry_creditors'                      => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'liabilities_other_liabilities'                     => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'assets_fixed_assets'                               => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'assets_inventories'                                => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'assets_sundry_debtors'                             => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'assets_balance_with_banks'                         => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'assets_cash-in_hand'                               => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'assets_loans_and_advances'                         => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'assets_other_assets'                               => ['nullable', 'numeric', 'min:0', 'max:100000000', 'decimal:0,2'],
            'is_capital_gain_income'                            => ['nullable', 'boolean'],
            'capital_gains_type_1'                              => ['required', 'integer'],
            'capital_gains_purchase_date_1'                     => ['required_unless:capital_gains_type_1,0', 'nullable', 'date', 'date_format:Y-m-d'],
            'capital_gains_purchase_amount_1'                   => ['required_unless:capital_gains_type_1,0', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'capital_gains_sale_date_1'                         => ['required_unless:capital_gains_type_1,0', 'nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'capital_gains_sale_amount_1'                       => ['required_unless:capital_gains_type_1,0', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'capital_gains_type_2'                              => ['required', 'integer'],
            'capital_gains_purchase_date_2'                     => ['required_unless:capital_gains_type_2,0', 'nullable', 'date', 'date_format:Y-m-d'],
            'capital_gains_purchase_amount_2'                   => ['required_unless:capital_gains_type_2,0', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'capital_gains_sale_date_2'                         => ['required_unless:capital_gains_type_2,0', 'nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'capital_gains_sale_amount_2'                       => ['required_unless:capital_gains_type_2,0', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'capital_gains_type_3'                              => ['required', 'integer'],
            'capital_gains_purchase_date_3'                     => ['required_unless:capital_gains_type_3,0', 'nullable', 'date', 'date_format:Y-m-d'],
            'capital_gains_purchase_amount_3'                   => ['required_unless:capital_gains_type_3,0', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'capital_gains_sale_date_3'                         => ['required_unless:capital_gains_type_3,0', 'nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'capital_gains_sale_amount_3'                       => ['required_unless:capital_gains_type_3,0', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'capital_gains_type_4'                              => ['required', 'integer'],
            'capital_gains_purchase_date_4'                     => ['required_unless:capital_gains_type_4,0', 'nullable', 'date', 'date_format:Y-m-d'],
            'capital_gains_purchase_amount_4'                   => ['required_unless:capital_gains_type_4,0', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'capital_gains_sale_date_4'                         => ['required_unless:capital_gains_type_4,0', 'nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'capital_gains_sale_amount_4'                       => ['required_unless:capital_gains_type_4,0', 'nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'investment_sale_amount_in_house'                   => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'investment_sale_amount_in_securities'              => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'investment_sale_amount_in_capital_gain_bank_a_c'   => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'is_other_income'                                   => ['nullable', 'boolean'],
            'commission'                                        => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'brokerage'                                         => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'interest_from_saving_bank'                         => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'interest_from_fixed_deposit'                       => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'dividend'                                          => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'family_pension'                                    => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'other_rent'                                        => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'other_interest'                                    => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'mutual_fund'                                       => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'uti_income'                                        => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'agricultural_gross_income'                         => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
            'agricultural_expenses'                             => ['nullable', 'numeric', 'min:0', 'max:10000000', 'decimal:0,2'],
        ]);

        if ($validation->fails()) {
            return CommonController::validationFails($validation);
        } else {
            $array_1 = $array_2 = $array_3 = $array_4 = $array_5 = [];
            if ($request->boolean('is_salary_income')) {
                $array_1 = [
                    'is_salary_income'                                  => $request->boolean('is_salary_income'),
                    'employer_name'                                     => $request->employer_name,
                    'employer_tan'                                      => $request->employer_tan,
                    'employer_flat_number'                              => $request->employer_flat_number,
                    'employer_address'                                  => $request->employer_address,
                    'employer_city'                                     => $request->employer_city,
                    'employer_state'                                    => $request->employer_state,
                    'employer_pincode'                                  => $request->employer_pincode,
                    'employer_type'                                     => $request->employer_type,
                    'salary'                                            => $request->salary,
                    'dearness_allowances'                               => $request->dearness_allowances,
                    'bonus_commission'                                  => $request->bonus_commission,
                    'other_amount_head'                                 => $request->other_amount_head,
                    'other_amount'                                      => $request->other_amount,
                ];
            }

            if ($request->boolean('is_house_income')) {
                $array_2 = [
                    'is_house_income'                                   => $request->boolean('is_house_income'),
                    'income_house_type'                                 => $request->income_house_type,
                    'income_house_flat_number'                          => $request->income_house_flat_number,
                    'income_house_address'                              => $request->income_house_address,
                    'income_house_city'                                 => $request->income_house_city,
                    'income_house_state'                                => $request->income_house_state,
                    'income_house_pincode'                              => $request->income_house_pincode,
                    'income_house_rent_received'                        => $request->income_house_rent_received,
                    'interest_paid_on_home_loan'                        => $request->interest_paid_on_home_loan,
                    'principal_paid_on_home_loan'                       => $request->principal_paid_on_home_loan,
                ];
            }

            if ($request->boolean('is_business_income')) {
                $array_3 = [
                    'is_business_income'                                => $request->boolean('is_business_income'),
                    'business_name'                                     => $request->business_name,
                    'business_type'                                     => $request->business_type,
                    'turnover'                                          => $request->turnover,
                    'net_profit'                                        => $request->net_profit,
                    'description'                                       => $request->description,
                    'partners_own_capital'                              => $request->partners_own_capital,
                    'liabilities_secured_loans'                         => $request->liabilities_secured_loans,
                    'liabilities_unsecured_loans'                       => $request->liabilities_unsecured_loans,
                    'liabilities_advances'                              => $request->liabilities_advances,
                    'liabilities_sundry_creditors'                      => $request->liabilities_sundry_creditors,
                    'liabilities_other_liabilities'                     => $request->liabilities_other_liabilities,
                    'assets_fixed_assets'                               => $request->assets_fixed_assets,
                    'assets_inventories'                                => $request->assets_inventories,
                    'assets_sundry_debtors'                             => $request->assets_sundry_debtors,
                    'assets_balance_with_banks'                         => $request->assets_balance_with_banks,
                    'assets_cash_in_hand'                               => $request->assets_cash_in_hand,
                    'assets_loans_and_advances'                         => $request->assets_loans_and_advances,
                    'assets_other_assets'                               => $request->assets_other_assets,
                ];
            }

            if ($request->boolean('is_capital_gain_income')) {
                $array_4 = [
                    'is_capital_gain_income'                            => $request->boolean('is_capital_gain_income'),
                    'capital_gains_type_1'                              => $request->capital_gains_type_1,
                    'capital_gains_purchase_date_1'                     => $request->capital_gains_purchase_date_1,
                    'capital_gains_purchase_amount_1'                   => $request->capital_gains_purchase_amount_1,
                    'capital_gains_sale_date_1'                         => $request->capital_gains_sale_date_1,
                    'capital_gains_sale_amount_1'                       => $request->capital_gains_sale_amount_1,
                    'capital_gains_type_2'                              => $request->capital_gains_type_2,
                    'capital_gains_purchase_date_2'                     => $request->capital_gains_purchase_date_2,
                    'capital_gains_purchase_amount_2'                   => $request->capital_gains_purchase_amount_2,
                    'capital_gains_sale_date_2'                         => $request->capital_gains_sale_date_2,
                    'capital_gains_sale_amount_2'                       => $request->capital_gains_sale_amount_2,
                    'capital_gains_type_3'                              => $request->capital_gains_type_3,
                    'capital_gains_purchase_date_3'                     => $request->capital_gains_purchase_date_3,
                    'capital_gains_purchase_amount_3'                   => $request->capital_gains_purchase_amount_3,
                    'capital_gains_sale_date_3'                         => $request->capital_gains_sale_date_3,
                    'capital_gains_sale_amount_3'                       => $request->capital_gains_sale_amount_3,
                    'capital_gains_type_4'                              => $request->capital_gains_type_4,
                    'capital_gains_purchase_date_4'                     => $request->capital_gains_purchase_date_4,
                    'capital_gains_purchase_amount_4'                   => $request->capital_gains_purchase_amount_4,
                    'capital_gains_sale_date_4'                         => $request->capital_gains_sale_date_4,
                    'capital_gains_sale_amount_4'                       => $request->capital_gains_sale_amount_4,
                    'investment_sale_amount_in_house'                   => $request->investment_sale_amount_in_house,
                    'investment_sale_amount_in_securities'              => $request->investment_sale_amount_in_securities,
                    'investment_sale_amount_in_capital_gain_bank_a_c'   => $request->investment_sale_amount_in_capital_gain_bank_a_c,
                ];
            }

            if ($request->boolean('is_other_income')) {
                $array_5 = [
                    'is_other_income'                                   => $request->boolean('is_other_income'),
                    'commission'                                        => $request->commission,
                    'brokerage'                                         => $request->brokerage,
                    'interest_from_saving_bank'                         => $request->interest_from_saving_bank,
                    'interest_from_fixed_deposit'                       => $request->interest_from_fixed_deposit,
                    'dividend'                                          => $request->dividend,
                    'family_pension'                                    => $request->family_pension,
                    'other_rent'                                        => $request->other_rent,
                    'other_interest'                                    => $request->other_interest,
                    'mutual_fund'                                       => $request->mutual_fund,
                    'uti_income'                                        => $request->uti_income,
                    'agricultural_gross_income'                         => $request->agricultural_gross_income,
                    'agricultural_expenses'                             => $request->agricultural_expenses,
                ];
            }

            $yearsArr = explode('-', $request->assessment_year);
            $data = [
                ...$array_1, ...$array_2, ...$array_3, ...$array_4, ...$array_5,
                'is_step_2_complete'    => 1,
                'assessment_year'       => $request->assessment_year,
                'financial_year'        => ($yearsArr[0] - 1) . '-' . $yearsArr[0],
                'tax_regime'            => $request->tax_regime,
            ];

            if (count($data) == 4) {
                return back()->withInput()->withError('Please select at least one income source.');
            }

            $path = 'customer-documents';
            if ($file = $request->file('form_16_file')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $data['form_16_file']        = $path . '/' . $uploadImage;
            }

            if ($file = $request->file('rent_agreement')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $data['rent_agreement']        = $path . '/' . $uploadImage;
            }

            $request->itr->update($data);

            return self::responce('success', 'Information has been saved.', $request->itr->toArray());
        }
    }

    protected static function stepThree(Request $request, $user_id, $user_type)
    {
        $validation = Validator::make($request->all(), [
            '80c_life_insurance_premium_paid'                   => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_gpf_ppf'                                       => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_ulip'                                          => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_provident_fund'                                => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_mutual_fund'                                   => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_principal_on_home_loan'                        => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_tuition_fees_upto_2_children'                  => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_fixed_deposit'                                 => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_tax_saving_bonds'                              => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80d_checkup_fee_for_self'                          => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80d_checkup_fee_for_parents'                       => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80d_medical_expenditures_for_self'                 => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80d_medical_expenditures_for_parents'              => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80tta_interest_earned_saving_banks'                => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80ccc_pension_annuity_fund'                        => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80ccd_own_contribution_nps'                        => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80ccd_employer_contribution_nps'                   => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80u_disablity'                                     => ['required', 'integer', 'in:0,40,80'],
            '80ee_interest_on_home_loan'                        => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80eeb_electric_vehicle_loan'                       => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'tds_certificates_form_26as'                        => ['nullable', 'max:2048', 'mimes:png,jpg,jpeg,pdf'],
            'is_make_donation'                                  => ['nullable', 'boolean'],
            '80g_donee_name'                                    => ['required_if:is_make_donation,1', 'nullable', 'string', 'min:2', 'max:100'],
            '80g_donee_address'                                 => ['required_if:is_make_donation,1', 'nullable', 'string', 'min:2', 'max:100'],
            '80g_donee_city'                                    => ['required_if:is_make_donation,1', 'nullable', 'integer'],
            '80g_donee_state'                                   => ['required_if:is_make_donation,1', 'nullable', 'integer'],
            '80g_donee_pincode'                                 => ['required_if:is_make_donation,1', 'nullable', 'string', 'digits:6'],
            '80g_donee_pancard'                                 => ['required_if:is_make_donation,1', 'nullable', 'alpha_num', 'max:15'],
            '80g_donation_amount_cash'                          => ['required_if:is_make_donation,1', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            '80g_donation_amount_no_cash'                       => ['required_if:is_make_donation,1', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            '80g_donee_qualifying_percentage'                   => ['required_if:is_make_donation,1', 'nullable', 'integer'],
        ]);

        if ($validation->fails()) {
            return CommonController::validationFails($validation);
        } else {

            $data = [
                '80c_life_insurance_premium_paid'                   => $request['80c_life_insurance_premium_paid'],
                '80c_gpf_ppf'                                       => $request['80c_gpf_ppf'],
                '80c_ulip'                                          => $request['80c_ulip'],
                '80c_provident_fund'                                => $request['80c_provident_fund'],
                '80c_mutual_fund'                                   => $request['80c_mutual_fund'],
                '80c_principal_on_home_loan'                        => $request['80c_principal_on_home_loan'],
                '80c_tuition_fees_upto_2_children'                  => $request['80c_tuition_fees_upto_2_children'],
                '80c_fixed_deposit'                                 => $request['80c_fixed_deposit'],
                '80c_tax_saving_bonds'                              => $request['80c_tax_saving_bonds'],
                '80d_checkup_fee_for_self'                          => $request['80d_checkup_fee_for_self'],
                '80d_checkup_fee_for_parents'                       => $request['80d_checkup_fee_for_parents'],
                '80d_medical_expenditures_for_self'                 => $request['80d_medical_expenditures_for_self'],
                '80d_medical_expenditures_for_parents'              => $request['80d_medical_expenditures_for_parents'],
                '80tta_interest_earned_saving_banks'                => $request['80tta_interest_earned_saving_banks'],
                '80ccc_pension_annuity_fund'                        => $request['80ccc_pension_annuity_fund'],
                '80ccd_own_contribution_nps'                        => $request['80ccd_own_contribution_nps'],
                '80ccd_employer_contribution_nps'                   => $request['80ccd_employer_contribution_nps'],
                '80u_disablity'                                     => $request['80u_disablity'],
                '80ee_interest_on_home_loan'                        => $request['80ee_interest_on_home_loan'],
                '80eeb_electric_vehicle_loan'                       => $request['80eeb_electric_vehicle_loan'],
                'is_step_3_complete'                                => 1,
            ];

            if ($request->boolean('is_make_donation')) {
                $data  = [
                    ...$data,
                    'is_make_donation'                              => $request->boolean('is_make_donation'),
                    '80g_donee_name'                                => $request['80g_donee_name'],
                    '80g_donee_address'                             => $request['80g_donee_address'],
                    '80g_donee_city'                                => $request['80g_donee_city'],
                    '80g_donee_state'                               => $request['80g_donee_state'],
                    '80g_donee_pincode'                             => $request['80g_donee_pincode'],
                    '80g_donee_country'                             => "India",
                    '80g_donee_pancard'                             => $request['80g_donee_pancard'],
                    '80g_donation_amount_cash'                      => $request['80g_donation_amount_cash'],
                    '80g_donation_amount_no_cash'                   => $request['80g_donation_amount_no_cash'],
                    '80g_donee_qualifying_percentage'               => $request['80g_donee_qualifying_percentage'],
                ];
            }

            $path = 'customer-documents';
            if ($file = $request->file('tds_certificates_form_26as')) {
                $destinationPath    = 'public\\' . $path;
                $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
                $data['tds_certificates_form_26as']        = $path . '/' . $uploadImage;
            }

            $request->itr->update($data);
            return self::responce('success', 'Information has been saved.', $request->itr->toArray());
        }
    }

    protected static function stepFour(Request $request, $user_id, $user_type)
    {
        if (!$request->confirmation)
            return self::responce('error', "Please check on confirmation first..!!", [
                'confirmation' => "Please check on confirmation first..!!"
            ]);

        $serviceLog = ServicesLog::where([
            'user_id'       => $user_id,
            'user_type'     => $user_type,
            'service_id'    => config('constant.service_ids.income_tax_return'),
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return self::responce('error', "Service Can't be used..!!");

        if ($serviceLog->sale_rate > $request->user()->user_balance)
            return self::responce('error', "Insufficient Balance to use this service..!!");

        DB::transaction(function () use ($request, $serviceLog) {

            if ($request->itr->is_step_4_complete == 0 && $request->itr->status == 0) {
                ServiceUsesLog::create([
                    'user_id'                       => $serviceLog->user_id,
                    'user_type'                     => $serviceLog->user_type,
                    'service_id'                    => $serviceLog->service_id,
                    'customer_id'                   => $request->itr->customer_id,
                    'request_id'                    => $request->itr->id,
                    'used_in'                       => 2,
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

                LedgerController::chargeItrService($request->itr, $serviceLog);
            }

            $request->itr->update(['is_step_4_complete' => 1, 'status' => 1]);
        });

        return self::responce('success', 'Information has been saved.', $request->itr->toArray());
    }

    protected static function responce(string $type, string $message = "", array $data = [])
    {
        if ($type == 'error') {
            return response()->json([
                'status'    => false,
                'message'   => $message,
                'data'      => $data
            ]);
        } else if ($type == 'success') {
            return response()->json([
                'status'    => true,
                'message'   => $message,
                'data'      => $data
            ]);
        } else {
            return response()->json([
                'status'    => true,
                'message'   => $message,
                'data'      => $data
            ]);
        }
    }

    public function itr_list(Request $request)
    {
        $pageNo = request('pageNo', 1);
        $limit  = request('limit', 10);
        $limit  = $limit <= 50 ? $limit : 50;

        $query = ItReturn::query();
        $query->select('token', 'slug', 'assessment_year', 'financial_year', 'tax_regime', 'first_name', 'middle_name', 'last_name', 'gender', 'email', 'phone', 'pancard_number', 'adhaar_number', 'is_refunded', 'status', 'created_at');
        $query->where('user_type', $this->user_type);
        $query->where('user_id', $this->user_id);

        $search = request('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%{$search}%");
                $query->orWhere('middle_name', 'LIKE', "%{$search}%");
                $query->orWhere('last_name', 'LIKE', "%{$search}%");
                $query->orWhere('email', 'LIKE', "%{$search}%");
                $query->orWhere('phone', 'LIKE', "%{$search}%");
                $query->orWhere('token', 'LIKE', "%{$search}%");
                $query->orWhere('pancard_number', 'LIKE', "%{$search}%");
                $query->orWhere('adhaar_number', 'LIKE', "%{$search}%");
                $query->orWhere('assessment_year', 'LIKE', "%{$search}%");
            });
        }

        $query->where('user_type', $this->user_type);

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

        $data = $query->get();
        if (count($data) > 0) {
            return response()->json([
                'status'    => true,
                'message'   => 'Success',
                'data'      => $data,
                'totalPage' => $totalPage,
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => "No Data Found.",
                'data'      => []
            ], 404);
        }
    }

    public function itr_details(Request $request, $slug)
    {
        $query = ItReturn::query();
        $query->select('*');
        $query->where('user_type', $this->user_type);
        $query->where('user_id', $this->user_id);
        $query->where('slug', $slug);
        $data = $query->first();
        if ($data) {
            return response()->json([
                'status'    => true,
                'message'   => 'Success',
                'data'      => $data,
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => "No Data Found.",
                'data'      => []
            ], 404);
        }
    }
}
