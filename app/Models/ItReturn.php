<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table    = "service_itr";
    protected $fillable = [
        'token',
        'slug',
        'is_step_1_complete',
        'is_step_2_complete',
        'is_step_3_complete',
        'is_step_4_complete',
        'customer_id',
        'user_id',
        'user_type',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'father_first_name',
        'father_middle_name',
        'father_last_name',
        'gender',
        'email',
        'phone',
        'itr_password',
        'pancard_number',
        'pancard_file',
        'adhaar_number',
        'adhaar_file',
        'phone_2',
        'flat_number',
        'address',
        'city',
        'state',
        'pincode',
        'country',
        'bank_ifsc',
        'bank_name',
        'account_type',
        'bank_account_type',
        'bank_account_no',
        'bank_statment_file',
        'is_salary_income',
        'employer_name',
        'employer_tan',
        'employer_flat_number',
        'employer_address',
        'employer_city',
        'employer_state',
        'employer_pincode',
        'employer_type',
        'salary',
        'dearness_allowances',
        'bonus_commission',
        'other_amount_head',
        'other_amount',
        'form_16_file',
        'is_house_income',
        'income_house_type',
        'income_house_flat_number',
        'income_house_address',
        'income_house_city',
        'income_house_state',
        'income_house_pincode',
        'income_house_rent_received',
        'interest_paid_on_home_loan',
        'principal_paid_on_home_loan',
        'rent_agreement',
        'is_business_income',
        'business_name',
        'business_type',
        'turnover',
        'net_profit',
        'description',
        'partners_own_capital',
        'liabilities_secured_loans',
        'liabilities_unsecured_loans',
        'liabilities_advances',
        'liabilities_sundry_creditors',
        'liabilities_other_liabilities',
        'assets_fixed_assets',
        'assets_inventories',
        'assets_sundry_debtors',
        'assets_balance_with_banks',
        'assets_cash_in_hand',
        'assets_loans_and_advances',
        'assets_other_assets',
        'is_capital_gain_income',
        'capital_gains_type_1',
        'capital_gains_purchase_date_1',
        'capital_gains_purchase_amount_1',
        'capital_gains_sale_date_1',
        'capital_gains_sale_amount_1',
        'capital_gains_type_2',
        'capital_gains_purchase_date_2',
        'capital_gains_purchase_amount_2',
        'capital_gains_sale_date_2',
        'capital_gains_sale_amount_2',
        'capital_gains_type_3',
        'capital_gains_purchase_date_3',
        'capital_gains_purchase_amount_3',
        'capital_gains_sale_date_3',
        'capital_gains_sale_amount_3',
        'capital_gains_type_4',
        'capital_gains_purchase_date_4',
        'capital_gains_purchase_amount_4',
        'capital_gains_sale_date_4',
        'capital_gains_sale_amount_4',
        'investment_sale_amount_in_house',
        'investment_sale_amount_in_securities',
        'investment_sale_amount_in_capital_gain_bank_a_c',
        'is_other_income',
        'commission',
        'brokerage',
        'interest_from_saving_bank',
        'interest_from_fixed_deposit',
        'dividend',
        'family_pension',
        'other_rent',
        'other_interest',
        'mutual_fund',
        'uti_income',
        'agricultural_gross_income',
        'agricultural_expenses',
        '80c_life_insurance_premium_paid',
        '80c_gpf_ppf',
        '80c_ulip',
        '80c_provident_fund',
        '80c_mutual_fund',
        '80c_principal_on_home_loan',
        '80c_tuition_fees_upto_2_children',
        '80c_fixed_deposit',
        '80c_tax_saving_bonds',
        '80d_checkup_fee_for_self',
        '80d_checkup_fee_for_parents',
        '80d_medical_expenditures_for_self',
        '80d_medical_expenditures_for_parents',
        '80tta_interest_earned_saving_banks',
        '80ccc_pension_annuity_fund',
        '80ccd_own_contribution_nps',
        '80ccd_employer_contribution_nps',
        '80u_disablity',
        '80ee_interest_on_home_loan',
        '80eeb_electric_vehicle_loan',
        'tds_certificates_form_26as',
        'is_make_donation',
        '80g_donee_name',
        '80g_donee_address',
        '80g_donee_city',
        '80g_donee_state',
        '80g_donee_pincode',
        '80g_donee_country',
        '80g_donee_pancard',
        '80g_donation_amount_cash',
        '80g_donation_amount_no_cash',
        '80g_donee_qualifying_percentage',
        'assessment_year',
        'financial_year',
        'tax_regime',
        'is_refunded',
        'status',
        'completed_date',
        'itr_submit_file_1',
        'itr_submit_file_2',
        'itr_submit_file_3',
        'comments',
    ];

    protected $casts = [
        'completed_date'    => 'datetime',
        'date_of_birth'     => 'date'
    ];

    protected $appends = ['name'];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $row) => trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']),
            set: function ($value, $row) {
                $value              = explode(' ', $value);
                return [
                    'first_name'    => optional($value)[0],
                    'middle_name'   => optional($value)[1],
                    'last_name'     => optional($value)[2],
                ];
            },

        );
    }

    protected function fatherName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $row) => trim($row['father_first_name'] . ' ' . $row['father_middle_name'] . ' ' . $row['father_last_name']),
            set: function ($value, $row) {
                $value              = explode(' ', $value);
                return [
                    'father_first_name'    => optional($value)[0],
                    'father_middle_name'   => optional($value)[1],
                    'father_last_name'     => optional($value)[2],
                ];
            },

        );
    }

    public function city_name()
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }

    public function state_name()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    public function employer_city_name()
    {
        return $this->belongsTo(City::class, 'employer_city', 'id');
    }

    public function employer_state_name()
    {
        return $this->belongsTo(State::class, 'employer_state', 'id');
    }

    public function income_house_city_name()
    {
        return $this->belongsTo(City::class, 'income_house_city', 'id');
    }

    public function income_house_state_name()
    {
        return $this->belongsTo(State::class, 'income_house_state', 'id');
    }

    public function donee_city_name()
    {
        return $this->belongsTo(City::class, '80g_donee_city', 'id');
    }

    public function donee_state_name()
    {
        return $this->belongsTo(State::class, '80g_donee_state', 'id');
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'user_id', 'id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'user_id', 'id');
    }

    public function main_distributor()
    {
        return $this->belongsTo(MainDistributor::class, 'user_id', 'id');
    }
}
