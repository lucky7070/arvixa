<?php

namespace App\Http\Controllers\Retailer;

use App\Models\State;
use App\Models\Customer;
use App\Models\ItReturn;
use App\Models\Services;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use App\Models\CustomerBank;
use Illuminate\Http\Request;
use App\Models\ServiceUsesLog;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\CustomerDocument;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\LedgerController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ITRController extends Controller
{
    protected $user_id = null;
    protected $user_type = null;
    protected $service_id = null;

    public function __construct()
    {
        $this->middleware([
            'auth:retailer',
            function ($request, $next) {
                $this->user_id = auth('retailer')->id();
                $this->user_type = 4;
                $this->service_id = config('constant.service_ids.income_tax_return');
                return $next($request);
            }
        ])->except('webhook');
    }

    public function itr_list(Request $request): View|JsonResponse
    {
        $service = Services::find(config('constant.service_ids.income_tax_return'));
        if ($request->ajax()) {

            $data = ItReturn::select('*')->where('user_type', $this->user_type)->where('user_id', $this->user_id);
            return Datatables::of($data)
                ->editColumn('status', function ($row) {
                    switch ($row['status']) {
                        case '0':
                            $status = '<small class="badge fw-semi-bold rounded-pill status badge-light-secondary"> Pending</small>';
                            break;
                        case '1':
                            $status = '<small class="badge fw-semi-bold rounded-pill status badge-light-info"> Submitted</small>';
                            break;
                        case '2':
                            $status = '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Completed</small>';
                            break;
                        case '3':
                            $status = '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Rejected</small>';
                            break;
                        case '4':
                            $status = '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Under Draft</small>';
                            break;
                        default:
                            $status = '';
                            break;
                    }
                    return $status;
                })
                ->editColumn('token', function ($row) {
                    return '<b>' . $row['token'] . '</b>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? $row['created_at']->format('d M, Y') : '';
                })
                ->editColumn('adhaar_number', function ($row) {
                    return '<b class="text-primary small mb-1">' . $row['adhaar_number'] . '</b><br /><b class="text-secondary small">' . $row['pancard_number'] . '</b>';
                })
                ->addColumn('name', function ($row) {
                    return '<b>' . trim($row->name) . '</b><br /> <span>(' . $row->phone . ')</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('file-itr', ['step' => 'personal-info', 'slug' => $row->slug]) . '" class="btn btn-outline-secondary py-1 px-2">View Details</a>';
                })
                ->rawColumns(['action', 'status', 'adhaar_number', 'name', 'token'])
                ->make(true);
        }

        return view('my_services.income_tax_return.list', compact('service'));
    }

    public function index(Request $request, $step = null, $slug = null): View|RedirectResponse
    {
        $service = Services::where('id', $this->service_id)->where('status', 1)->first();
        if (!$service)
            return to_route('retailer.dashboard')->withError("Service is Temporarily Inactive..!!");

        if (in_array($step, ['income-sources', 'tax-saving', 'tax-summary'])) {
            $itr = ItReturn::firstWhere('slug', $slug);
            if (!$itr) {
                return to_route('itr-list')->withError("Token Not valid..!!");
            }
        } else {
            $itr = ItReturn::firstOrNew(['slug' => $slug]);
        }

        if (($itr->status > 0 && $itr->status != 4) && in_array($step, ['personal-info', 'income-sources', 'tax-saving'])) {
            return to_route('file-itr', ['step' => 'tax-summary', 'slug' => $itr->slug]);
        }

        switch ($step) {
            case 'personal-info':
                $step_view = 'step1';
                break;
            case 'income-sources':
                $step_view = 'step2';
                break;
            case 'tax-saving':
                $step_view = 'step3';
                break;
            case 'tax-summary':
                $step_view = 'step4';
                break;
            default:
                $step_view = 'step1';
                break;
        }

        $assessment_year_list = [
            '0' => date('Y') . '-' . date('Y', strtotime('1 year')),
            '1' => date('Y', strtotime('-1 year')) . '-' . date('Y'),
            '2' => date('Y', strtotime('-2 year')) . '-' . date('Y', strtotime('-1 year')),
        ];

        $states = State::where('status', 1)->get();
        return view('my_services.income_tax_return.' . $step_view, compact('states', 'itr', 'assessment_year_list'));
    }

    public function save(Request $request, $step = null, $slug = null): RedirectResponse
    {
        $service = Services::where('id', $this->service_id)->where('status', 1)->first();
        if (!$service)
            return to_route('retailer.dashboard')->withError("Service is Temporarily Inactive..!!");

        if (in_array($step, ['income-sources', 'tax-saving', 'tax-summary'])) {
            $itr = ItReturn::firstWhere('slug', $slug);
            if (!$itr) {
                return to_route('itr-list')->withError("Token Not valid..!!");
            }
        } else {
            $itr = ItReturn::firstOrNew(['slug' => $slug]);
        }

        if ($itr->status > 0 && $itr->status != 4) {
            return to_route('itr-list')->withError("This ITR File can't be updated..!!");
        }

        $request->merge(['itr' => $itr]);
        switch ($step) {
            case 'personal-info':
                $step_view = self::stepOne($request, $this->user_id, $this->user_type);
                break;
            case 'income-sources':
                $step_view = self::stepTwo($request, $this->user_id, $this->user_type);
                break;
            case 'tax-saving':
                $step_view = self::stepThree($request, $this->user_id, $this->user_type);
                break;
            case 'tax-summary':
                $step_view = self::stepFour($request, $this->user_id, $this->user_type, $this->service_id);
                break;
            default:
                $step_view = self::stepOne($request, $this->user_id, $this->user_type);
                break;
        }

        return $step_view;
    }

    protected static function stepOne(Request $request, $user_id, $user_type): RedirectResponse
    {
        $request->validate([
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
            'pancard_number'        => ['required', 'string', 'alpha_num', 'max:15'],
            'pancard_file'          => [Rule::requiredIf(!$request->itr->pancard_file && !$request->pancard_file_old), 'max:2048', 'mimes:png,jpg,jpeg,pdf'],
            'adhaar_number'         => ['required', 'digits:12', 'integer'],
            'adhaar_file'           => [Rule::requiredIf(!$request->itr->adhaar_file && !$request->adhaar_file_old), 'max:2048', 'mimes:png,jpg,jpeg,pdf'],
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

        $year_1 = date('Y');
        $year_2 = date('Y') + 1;
        $year_3 = date('Y') - 1;

        $request->itr->token                = 'ITR' . Str::upper(Str::random(7));
        $request->itr->slug                 = Str::uuid();
        $request->itr->user_id              = $user_id;
        $request->itr->user_type            = $user_type;
        $request->itr->assessment_year      = $year_1 . '-' . $year_2;
        $request->itr->financial_year       = $year_3 . '-' . $year_1;
        $request->itr->is_step_1_complete   = 1;
        $request->itr->fill($request->only(['first_name', 'middle_name', 'last_name', 'date_of_birth', 'father_first_name', 'father_middle_name', 'father_last_name', 'gender', 'email', 'phone', 'itr_password', 'pancard_number', 'adhaar_number', 'phone_2', 'flat_number', 'address', 'city', 'state', 'pincode', 'country', 'bank_ifsc', 'bank_name', 'account_type', 'bank_account_type', 'bank_account_no']));

        $pancard_file                       = $request->itr->pancard_file;
        $adhaar_file                        = $request->itr->adhaar_file;

        if ($request->hasFile('pancard_file')) {
            $pancard_file = saveFile($request->file('pancard_file'), 'customer-documents');
        }

        if ($request->hasFile('adhaar_file')) {
            $adhaar_file = saveFile($request->file('adhaar_file'), 'customer-documents');
        }

        if ($request->hasFile('bank_statment_file')) {
            $request->itr->bank_statment_file = saveFile($request->file('bank_statment_file'), 'customer-documents');
        }

        DB::transaction(function () use ($request, $pancard_file, $adhaar_file) {

            // Customer Profile Data Save
            $customer = Customer::firstOrNew(['mobile' => request('phone')]);
            $customer->fill($request->only(['first_name', 'middle_name', 'last_name', 'email', 'mobile', 'dob', 'state_id', 'city_id', 'gender']));
            $customer->save();

            // Customer Bank Data Save
            $customerBank = CustomerBank::firstOrNew(['customer_id' => $customer->id, 'account_number' => $request->bank_account_no]);
            $customerBank->account_bank = $request->bank_name;
            $customerBank->account_name = trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name);
            $customerBank->account_ifsc = $request->bank_ifsc;
            $customerBank->save();

            // Customer Aadhaar Data Save
            $customerAadhar = CustomerDocument::firstOrNew(['customer_id' => $customer->id, 'doc_type' => 1]);
            $customerAadhar->doc_number = $request->adhaar_number;
            if ($adhaar_file) $customerAadhar->doc_img_front = $adhaar_file;
            $customerAadhar->save();

            // Customer PanCard Data Save
            $customerPan = CustomerDocument::firstOrNew(['customer_id' => $customer->id, 'doc_type' => 4]);
            $customerPan->doc_number    = $request->pancard_number;
            if ($pancard_file) $customerPan->doc_img_front = $pancard_file;
            $customerPan->save();

            // ITR Data Save
            $request->itr->customer_id  = $customer->id;
            $request->itr->adhaar_file  = $adhaar_file;
            $request->itr->pancard_file = $pancard_file;
            $request->itr->save();
        });

        return to_route('file-itr', ['step' => 'income-sources', 'slug' => $request->itr->slug])->withSuccess('Information has been saved.');
    }

    protected static function stepTwo(Request $request, $user_id, $user_type): RedirectResponse
    {
        $request->validate([
            'assessment_year'                               => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'tax_regime'                                    => ['required', 'in:N,O'],
            'is_salary_income'                              => ['nullable', 'boolean'],
            'employer_name'                                 => ['required_if:is_salary_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'employer_tan'                                  => ['required_if:is_salary_income,1', 'nullable', 'alpha_num', 'max:15'],
            'employer_flat_number'                          => ['required_if:is_salary_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'employer_address'                              => ['required_if:is_salary_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'employer_city'                                 => ['required_if:is_salary_income,1', 'nullable', 'integer'],
            'employer_state'                                => ['required_if:is_salary_income,1', 'nullable', 'integer'],
            'employer_pincode'                              => ['required_if:is_salary_income,1', 'nullable', 'string', 'digits:6'],
            'employer_type'                                 => ['required_if:is_salary_income,1', 'nullable', 'integer'],
            'salary'                                        => ['required_if:is_salary_income,1', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'dearness_allowances'                           => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'bonus_commission'                              => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'other_amount_head'                             => ['nullable', 'string', 'min:6', 'max:100'],
            'other_amount'                                  => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'form_16_file'                                  => ['nullable', 'max:2048', 'mimes:png,jpg,jpeg,pdf'],
            'is_house_income'                               => ['nullable', 'boolean'],
            'income_house_type'                             => ['required_if:is_house_income,1', 'nullable', 'integer'],
            'income_house_flat_number'                      => ['required_if:is_house_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'income_house_address'                          => ['required_if:is_house_income,1', 'nullable', 'string', 'min:2', 'max:100'],
            'income_house_city'                             => ['required_if:is_house_income,1', 'nullable', 'integer'],
            'income_house_state'                            => ['required_if:is_house_income,1', 'nullable', 'integer'],
            'income_house_pincode'                          => ['required_if:is_house_income,1', 'nullable', 'string', 'digits:6'],
            'income_house_rent_received'                    => ['required_if:is_house_income,1', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'interest_paid_on_home_loan'                    => ['required_if:is_house_income,1', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'principal_paid_on_home_loan'                   => ['required_if:is_house_income,1', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'rent_agreement'                                => ['nullable', 'max:2048', 'mimes:png,jpg,jpeg,pdf'],
            'is_business_income'                            => ['nullable', 'boolean'],
            'business_name'                                 => ['nullable', 'string', 'max:100'],
            'business_type'                                 => ['nullable', 'integer'],
            'turnover'                                      => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'net_profit'                                    => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'description'                                   => ['nullable', 'string', 'max:500'],
            'partners_own_capital'                          => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'liabilities_secured_loans'                     => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'liabilities_unsecured_loans'                   => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'liabilities_advances'                          => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'liabilities_sundry_creditors'                  => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'liabilities_other_liabilities'                 => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'assets_fixed_assets'                           => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'assets_inventories'                            => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'assets_sundry_debtors'                         => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'assets_balance_with_banks'                     => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'assets_cash-in_hand'                           => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'assets_loans_and_advances'                     => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'assets_other_assets'                           => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'is_capital_gain_income'                        => ['nullable', 'boolean'],
            'capital_gains_type_1'                          => ['required', 'integer'],
            'capital_gains_purchase_date_1'                 => ['required_unless:capital_gains_type_1,0', 'nullable', 'date', 'date_format:Y-m-d'],
            'capital_gains_purchase_amount_1'               => ['required_unless:capital_gains_type_1,0', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'capital_gains_sale_date_1'                     => ['required_unless:capital_gains_type_1,0', 'nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'capital_gains_sale_amount_1'                   => ['required_unless:capital_gains_type_1,0', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'capital_gains_type_2'                          => ['required', 'integer'],
            'capital_gains_purchase_date_2'                 => ['required_unless:capital_gains_type_2,0', 'nullable', 'date', 'date_format:Y-m-d'],
            'capital_gains_purchase_amount_2'               => ['required_unless:capital_gains_type_2,0', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'capital_gains_sale_date_2'                     => ['required_unless:capital_gains_type_2,0', 'nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'capital_gains_sale_amount_2'                   => ['required_unless:capital_gains_type_2,0', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'capital_gains_type_3'                          => ['required', 'integer'],
            'capital_gains_purchase_date_3'                 => ['required_unless:capital_gains_type_3,0', 'nullable', 'date', 'date_format:Y-m-d'],
            'capital_gains_purchase_amount_3'               => ['required_unless:capital_gains_type_3,0', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'capital_gains_sale_date_3'                     => ['required_unless:capital_gains_type_3,0', 'nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'capital_gains_sale_amount_3'                   => ['required_unless:capital_gains_type_3,0', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'capital_gains_type_4'                          => ['required', 'integer'],
            'capital_gains_purchase_date_4'                 => ['required_unless:capital_gains_type_4,0', 'nullable', 'date', 'date_format:Y-m-d'],
            'capital_gains_purchase_amount_4'               => ['required_unless:capital_gains_type_4,0', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'capital_gains_sale_date_4'                     => ['required_unless:capital_gains_type_4,0', 'nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'capital_gains_sale_amount_4'                   => ['required_unless:capital_gains_type_4,0', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            'investment_sale_amount_in_house'               => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'investment_sale_amount_in_securities'          => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'investment_sale_amount_in_capital_gain_bank_a_c'   => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'is_other_income'                               => ['nullable', 'boolean'],
            'commission'                                    => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'brokerage'                                     => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'interest_from_saving_bank'                     => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'interest_from_fixed_deposit'                   => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'dividend'                                      => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'family_pension'                                => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'other_rent'                                    => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'other_interest'                                => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'mutual_fund'                                   => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'uti_income'                                    => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'agricultural_gross_income'                     => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'agricultural_expenses'                         => ['nullable', 'numeric', 'min:0', 'max:10000000'],
        ]);

        $array_1 = $array_2 = $array_3 = $array_4 = $array_5 = [];
        if ($request->boolean('is_salary_income')) {
            $array_1 = $request->only(['is_salary_income', 'employer_name', 'employer_tan', 'employer_flat_number', 'employer_address', 'employer_city', 'employer_state', 'employer_pincode', 'employer_type', 'salary', 'dearness_allowances', 'bonus_commission', 'other_amount_head', 'other_amount']);
        }

        if ($request->boolean('is_house_income')) {
            $array_2 = $request->only(['is_house_income', 'income_house_type', 'income_house_flat_number', 'income_house_address', 'income_house_city', 'income_house_state', 'income_house_pincode', 'income_house_rent_received', 'interest_paid_on_home_loan', 'principal_paid_on_home_loan']);
        }

        if ($request->boolean('is_business_income')) {
            $array_3 = $request->only(['is_business_income', 'business_name', 'business_type', 'turnover', 'net_profit', 'description', 'partners_own_capital', 'liabilities_secured_loans', 'liabilities_unsecured_loans', 'liabilities_advances', 'liabilities_sundry_creditors', 'liabilities_other_liabilities', 'assets_fixed_assets', 'assets_inventories', 'assets_sundry_debtors', 'assets_balance_with_banks', 'assets_cash_in_hand', 'assets_loans_and_advances', 'assets_other_assets']);
        }

        if ($request->boolean('is_capital_gain_income')) {
            $array_4 = $request->only(['is_capital_gain_income', 'capital_gains_type_1', 'capital_gains_purchase_date_1', 'capital_gains_purchase_amount_1', 'capital_gains_sale_date_1', 'capital_gains_sale_amount_1', 'capital_gains_type_2', 'capital_gains_purchase_date_2', 'capital_gains_purchase_amount_2', 'capital_gains_sale_date_2', 'capital_gains_sale_amount_2', 'capital_gains_type_3', 'capital_gains_purchase_date_3', 'capital_gains_purchase_amount_3', 'capital_gains_sale_date_3', 'capital_gains_sale_amount_3', 'capital_gains_type_4', 'capital_gains_purchase_date_4', 'capital_gains_purchase_amount_4', 'capital_gains_sale_date_4', 'capital_gains_sale_amount_4', 'investment_sale_amount_in_house', 'investment_sale_amount_in_securities', 'investment_sale_amount_in_capital_gain_bank_a_c']);
        }

        if ($request->boolean('is_other_income')) {
            $array_5 = $request->only(['is_other_income', 'commission', 'brokerage', 'interest_from_saving_bank', 'interest_from_fixed_deposit', 'dividend', 'family_pension', 'other_rent', 'other_interest', 'mutual_fund', 'uti_income', 'agricultural_gross_income', 'agricultural_expenses']);
        }

        $yearsArr = explode('-', $request->assessment_year);
        $data = [
            ...$array_1,
            ...$array_2,
            ...$array_3,
            ...$array_4,
            ...$array_5,
            'is_step_2_complete' => 1,
            'assessment_year' => $request->assessment_year,
            'financial_year' => ($yearsArr[0] - 1) . '-' . $yearsArr[0],
            'tax_regime' => $request->tax_regime,
        ];

        if (count($data) == 4) {
            return back()->withInput()->withError('Please select at least one income source.');
        }

        if ($request->hasFile('form_16_file')) {
            $data['form_16_file'] = saveFile($request->file('form_16_file'), 'customer-documents');
        }

        if ($request->hasFile('rent_agreement')) {
            $data['rent_agreement'] = saveFile($request->file('rent_agreement'), 'customer-documents');
        }

        $request->itr->update($data);

        return to_route('file-itr', ['step' => 'tax-saving', 'slug' => $request->itr['slug']])->withSuccess('Information has been saved.');
    }

    protected static function stepThree(Request $request, $user_id, $user_type): RedirectResponse
    {
        $request->validate([
            '80c_life_insurance_premium_paid'       => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_gpf_ppf'                           => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_ulip'                              => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_provident_fund'                    => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_mutual_fund'                       => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_principal_on_home_loan'            => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_tuition_fees_upto_2_children'      => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_fixed_deposit'                     => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80c_tax_saving_bonds'                  => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80d_checkup_fee_for_self'              => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80d_checkup_fee_for_parents'           => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80d_medical_expenditures_for_self'     => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80d_medical_expenditures_for_parents'  => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80tta_interest_earned_saving_banks'    => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80ccc_pension_annuity_fund'            => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80ccd_own_contribution_nps'            => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80ccd_employer_contribution_nps'       => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80u_disablity'                         => ['nullable', 'integer'],
            '80ee_interest_on_home_loan'            => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            '80eeb_electric_vehicle_loan'           => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'tds_certificates_form_26as'            => ['nullable', 'max:2048', 'mimes:png,jpg,jpeg,pdf'],
            'is_make_donation'                      => ['nullable', 'boolean'],
            '80g_donee_name'                        => ['required_if:is_make_donation,1', 'nullable', 'string', 'min:2', 'max:100'],
            '80g_donee_address'                     => ['required_if:is_make_donation,1', 'nullable', 'string', 'min:2', 'max:100'],
            '80g_donee_city'                        => ['required_if:is_make_donation,1', 'nullable', 'integer'],
            '80g_donee_state'                       => ['required_if:is_make_donation,1', 'nullable', 'integer'],
            '80g_donee_pincode'                     => ['required_if:is_make_donation,1', 'nullable', 'string', 'digits:6'],
            '80g_donee_pancard'                     => ['required_if:is_make_donation,1', 'nullable', 'alpha_num', 'max:15'],
            '80g_donation_amount_cash'              => ['required_if:is_make_donation,1', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            '80g_donation_amount_no_cash'           => ['required_if:is_make_donation,1', 'nullable', 'numeric', 'min:0', 'max:10000000'],
            '80g_donee_qualifying_percentage'       => ['required_if:is_make_donation,1', 'nullable', 'integer'],
        ]);

        $data = [...$request->only('80c_life_insurance_premium_paid', '80c_gpf_ppf', '80c_ulip', '80c_provident_fund', '80c_mutual_fund', '80c_principal_on_home_loan', '80c_tuition_fees_upto_2_children', '80c_fixed_deposit', '80c_tax_saving_bonds', '80d_checkup_fee_for_self', '80d_checkup_fee_for_parents', '80d_medical_expenditures_for_self', '80d_medical_expenditures_for_parents', '80tta_interest_earned_saving_banks', '80ccc_pension_annuity_fund', '80ccd_own_contribution_nps', '80ccd_employer_contribution_nps', '80u_disablity', '80ee_interest_on_home_loan', '80eeb_electric_vehicle_loan'), 'is_step_3_complete' => 1];

        if ($request->boolean('is_make_donation')) {
            $data = [
                ...$data,
                ...$request->only('is_make_donation', '80g_donee_name', '80g_donee_address', '80g_donee_city', '80g_donee_state', '80g_donee_pincode', '80g_donee_pancard', '80g_donation_amount_cash', '80g_donation_amount_no_cash', '80g_donee_qualifying_percentage'),
                '80g_donee_country' => "India",
            ];
        }

        if ($request->hasFile('tds_certificates_form_26as')) {
            $data['tds_certificates_form_26as'] = saveFile($request->file('tds_certificates_form_26as'), 'customer-documents');
        }

        $request->itr->update($data);
        return to_route('file-itr', ['step' => 'tax-summary', 'slug' => $request->itr['slug']])->withSuccess('Information has been saved.');
    }

    protected static function stepFour(Request $request, $user_id, $user_type, $service_id): RedirectResponse
    {
        $serviceLog = ServicesLog::where([
            'user_id'       => $user_id,
            'user_type'     => $user_type,
            'service_id'    => $service_id,
            'status'        => 1
        ])->first();

        if (!$serviceLog)
            return to_route('retailer.dashboard')->withError("Service Can't be used..!!");

        if ($serviceLog->sale_rate > auth('retailer')->user()->user_balance)
            return to_route('retailer.dashboard')->withError("Insufficient Balance to use this service..!!");

        DB::beginTransaction();

        try {

            if ($request->itr->is_step_4_complete == 0 && $request->itr->status == 0) {
                ServiceUsesLog::create([
                    'user_id'                       => $serviceLog->user_id,
                    'user_type'                     => $serviceLog->user_type,
                    'service_id'                    => $serviceLog->service_id,
                    'customer_id'                   => $request->itr->customer_id,
                    'request_id'                    => $request->itr->id,
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

                LedgerController::chargeItrService($request->itr, $serviceLog);
            }

            $request->itr->update(['is_step_4_complete' => 1, 'status' => 1]);
            DB::commit();
            return to_route('itr-list')->withSuccess('Information has been Submitted.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withError($e->getMessage());
        }
    }

    protected static function getUrl($file): string|null
    {
        return $file ? asset('storage/' . $file) : null;
    }
}
