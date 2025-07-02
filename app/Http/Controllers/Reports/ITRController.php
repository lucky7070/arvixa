<?php

namespace App\Http\Controllers\Reports;

use App\Models\ItReturn;
use Illuminate\View\View;
use App\Models\ServicesLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Http\Controllers\Common\LedgerController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ITRController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {

            $query = ItReturn::select('*')->with('retailer');
            $query->whereNot('status', 0);

            if (request('start_date') && request('end_date')) {
                if (request('start_date') == request('end_date')) {
                    $query->whereDate('service_itr.created_at', request('start_date'));
                } else {
                    $startDate = Carbon::parse(request('start_date'));
                    $endDate = Carbon::parse(request('end_date'))->endOfDay();
                    $query->whereBetween('service_itr.created_at', [$startDate, $endDate]);
                }
            }

            if (request('status') || request('status')  === '0') {
                $query->where('status', request('status'));
            }

            return Datatables::of($query)
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
                ->addColumn('retailer_name', function ($row) {
                    return '<b>' . @$row->retailer->name . '</b><br /><b class="text-secondary small">' . @$row->retailer->userId . '</b>';
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
                    return '<a class="btn btn-dark btn-sm" href="' . route('reports.itr-files.details', $row['slug']) . '">Details</a>';
                })
                ->rawColumns(['action', 'status', 'adhaar_number', 'name', 'token', 'retailer_name'])
                ->make(true);
        }

        return view('reports.itr-file.index');
    }

    public function details($slug): View|RedirectResponse
    {
        $itr = ItReturn::firstWhere('slug', $slug);
        if (!$itr) {
            return to_route('reports.itr-files')->withError("Details not found..!!");
        }

        return view('reports.itr-file.details',  compact('itr'));
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $query = ItReturn::query();
        $query->whereNot('status', 0);
        $query->with(['retailer', 'city_name', 'state_name', 'employer_city_name', 'employer_state_name', 'income_house_city_name', 'income_house_state_name', 'donee_city_name', 'donee_state_name']);

        if (request('status') || request('status') === '0') {
            $query->where('status', request('status'));
        }

        if (request('start_date') && request('end_date')) {
            if (request('start_date') == request('end_date')) {
                $query->whereDate('created_at', request('start_date'));
            } else {
                $startDate  = Carbon::parse(request('start_date'));
                $endDate    = Carbon::parse(request('end_date'))->endOfDay();
                if ($startDate->diffInDays($endDate) > 15) {
                    return back()->withInput()->withError("Report can be exported for max 15 Days.");
                }

                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        } else {
            $startDate  = Carbon::now()->startOfDay()->subDays(7);
            $endDate    = Carbon::now();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Start Building Excel Sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('ITR Report');

        $sheet->setCellValue('A1', 'Token');
        $sheet->setCellValue('B1', 'Retailer Id');
        $sheet->setCellValue('C1', 'Retailer Name');
        $sheet->setCellValue('D1', 'Assessment Year');
        $sheet->setCellValue('E1', 'Financial Year');
        $sheet->setCellValue('F1', 'Tax Regime');
        $sheet->setCellValue('G1', 'Name');
        $sheet->setCellValue('H1', 'Date Of Birth');
        $sheet->setCellValue('I1', 'Father Name');
        $sheet->setCellValue('J1', 'Gender');
        $sheet->setCellValue('K1', 'Email');
        $sheet->setCellValue('L1', 'Phone');
        $sheet->setCellValue('M1', 'Itr Password');
        $sheet->setCellValue('N1', 'Pancard Number');
        $sheet->setCellValue('O1', 'Pancard File');
        $sheet->setCellValue('P1', 'Adhaar Number');
        $sheet->setCellValue('Q1', 'Adhaar File');
        $sheet->setCellValue('R1', 'Phone 2');
        $sheet->setCellValue('S1', 'Flat Number');
        $sheet->setCellValue('T1', 'Address');
        $sheet->setCellValue('U1', 'City');
        $sheet->setCellValue('V1', 'State');
        $sheet->setCellValue('W1', 'Pincode');
        $sheet->setCellValue('X1', 'Country');
        $sheet->setCellValue('Y1', 'Bank Ifsc');
        $sheet->setCellValue('Z1', 'Bank Name');
        $sheet->setCellValue('AA1', 'Bank Account No');
        $sheet->setCellValue('AB1', 'Account Type');
        $sheet->setCellValue('AC1', 'Bank Account Type');
        $sheet->setCellValue('AD1', 'Is Salary Income');
        $sheet->setCellValue('AE1', 'Employer Name');
        $sheet->setCellValue('AF1', 'Employer Tan');
        $sheet->setCellValue('AG1', 'Employer Flat Number');
        $sheet->setCellValue('AH1', 'Employer Address');
        $sheet->setCellValue('AI1', 'Employer City');
        $sheet->setCellValue('AJ1', 'Employer State');
        $sheet->setCellValue('AK1', 'Employer Pincode');
        $sheet->setCellValue('AL1', 'Employer Type');
        $sheet->setCellValue('AM1', 'Salary');
        $sheet->setCellValue('AN1', 'Dearness Allowances');
        $sheet->setCellValue('AO1', 'Bonus Commission');
        $sheet->setCellValue('AP1', 'Other Amount Head');
        $sheet->setCellValue('AQ1', 'Other Amount');
        $sheet->setCellValue('AR1', 'Form 16 File');
        $sheet->setCellValue('AS1', 'Is House Income');
        $sheet->setCellValue('AT1', 'Income House Type');
        $sheet->setCellValue('AU1', 'Income House Flat Number');
        $sheet->setCellValue('AV1', 'Income House Address');
        $sheet->setCellValue('AW1', 'Income House City');
        $sheet->setCellValue('AX1', 'Income House State');
        $sheet->setCellValue('AY1', 'Income House Pincode');
        $sheet->setCellValue('AZ1', 'Income House Rent Received');
        $sheet->setCellValue('BA1', 'Interest Paid On Home Loan');
        $sheet->setCellValue('BB1', 'Principal Paid On Home Loan');
        $sheet->setCellValue('BC1', 'Rent Agreement');
        $sheet->setCellValue('BD1', 'Is Business Income');
        $sheet->setCellValue('BE1', 'Business Name');
        $sheet->setCellValue('BF1', 'Business Type');
        $sheet->setCellValue('BG1', 'Turnover');
        $sheet->setCellValue('BH1', 'Net Profit');
        $sheet->setCellValue('BI1', 'Description');
        $sheet->setCellValue('BJ1', 'Partners Own Capital');
        $sheet->setCellValue('BK1', 'Liabilities Secured Loans');
        $sheet->setCellValue('BL1', 'Liabilities Unsecured Loans');
        $sheet->setCellValue('BM1', 'Liabilities Advances');
        $sheet->setCellValue('BN1', 'Liabilities Sundry Creditors');
        $sheet->setCellValue('BO1', 'Liabilities Other Liabilities');
        $sheet->setCellValue('BP1', 'Assets Fixed Assets');
        $sheet->setCellValue('BQ1', 'Assets Inventories');
        $sheet->setCellValue('BR1', 'Assets Sundry Debtors');
        $sheet->setCellValue('BS1', 'Assets Balance With Banks');
        $sheet->setCellValue('BT1', 'Assets Cash In Hand');
        $sheet->setCellValue('BU1', 'Assets Loans And Advances');
        $sheet->setCellValue('BV1', 'Assets Other Assets');
        $sheet->setCellValue('BW1', 'Is Capital Gain Income');
        $sheet->setCellValue('BX1', 'Capital Gains Type 1');
        $sheet->setCellValue('BY1', 'Capital Gains Purchase Date 1');
        $sheet->setCellValue('BZ1', 'Capital Gains Purchase Amount 1');
        $sheet->setCellValue('CA1', 'Capital Gains Sale Date 1');
        $sheet->setCellValue('CB1', 'Capital Gains Sale Amount 1');
        $sheet->setCellValue('CC1', 'Capital Gains Type 2');
        $sheet->setCellValue('CD1', 'Capital Gains Purchase Date 2');
        $sheet->setCellValue('CE1', 'Capital Gains Purchase Amount 2');
        $sheet->setCellValue('CF1', 'Capital Gains Sale Date 2');
        $sheet->setCellValue('CG1', 'Capital Gains Sale Amount 2');
        $sheet->setCellValue('CH1', 'Capital Gains Type 3');
        $sheet->setCellValue('CI1', 'Capital Gains Purchase Date 3');
        $sheet->setCellValue('CJ1', 'Capital Gains Purchase Amount 3');
        $sheet->setCellValue('CK1', 'Capital Gains Sale Date 3');
        $sheet->setCellValue('CL1', 'Capital Gains Sale Amount 3');
        $sheet->setCellValue('CM1', 'Capital Gains Type 4');
        $sheet->setCellValue('CN1', 'Capital Gains Purchase Date 4');
        $sheet->setCellValue('CO1', 'Capital Gains Purchase Amount 4');
        $sheet->setCellValue('CP1', 'Capital Gains Sale Date 4');
        $sheet->setCellValue('CQ1', 'Capital Gains Sale Amount 4');
        $sheet->setCellValue('CR1', 'Investment Sale Amount In House');
        $sheet->setCellValue('CS1', 'Investment Sale Amount In Securities');
        $sheet->setCellValue('CT1', 'Investment Sale Amount In Capital Gain Bank A/C');
        $sheet->setCellValue('CU1', 'Is Other Income');
        $sheet->setCellValue('CV1', 'Commission');
        $sheet->setCellValue('CW1', 'Brokerage');
        $sheet->setCellValue('CX1', 'Interest From Saving Bank');
        $sheet->setCellValue('CY1', 'Interest From Fixed Deposit');
        $sheet->setCellValue('CZ1', 'Dividend');
        $sheet->setCellValue('DA1', 'Family Pension');
        $sheet->setCellValue('DB1', 'Other Rent');
        $sheet->setCellValue('DC1', 'Other Interest');
        $sheet->setCellValue('DD1', 'Mutual Fund');
        $sheet->setCellValue('DE1', 'Uti Income');
        $sheet->setCellValue('DF1', 'Agricultural Gross Income');
        $sheet->setCellValue('DG1', 'Agricultural Expenses');
        $sheet->setCellValue('DH1', '80C Life Insurance Premium Paid');
        $sheet->setCellValue('DI1', '80C Gpf Ppf');
        $sheet->setCellValue('DJ1', '80C Ulip');
        $sheet->setCellValue('DK1', '80C Provident Fund');
        $sheet->setCellValue('DL1', '80C Mutual Fund');
        $sheet->setCellValue('DM1', '80C Principal On Home Loan');
        $sheet->setCellValue('DN1', '80C Tuition Fees Upto 2 Children');
        $sheet->setCellValue('DO1', '80C Fixed Deposit');
        $sheet->setCellValue('DP1', '80C Tax Saving Bonds');
        $sheet->setCellValue('DQ1', '80d Checkup Fee For Self');
        $sheet->setCellValue('DR1', '80d Checkup Fee For Parents');
        $sheet->setCellValue('DS1', '80d Medical Expenditures For Self');
        $sheet->setCellValue('DT1', '80d Medical Expenditures For Parents');
        $sheet->setCellValue('DU1', '80tta Interest Earned Saving Banks');
        $sheet->setCellValue('DV1', '80CCC Pension Annuity Fund');
        $sheet->setCellValue('DW1', '80CCD Own Contribution Nps');
        $sheet->setCellValue('DX1', '80CCD Employer Contribution Nps');
        $sheet->setCellValue('DY1', '80U Disablity');
        $sheet->setCellValue('DZ1', '80EE Interest On Home Loan');
        $sheet->setCellValue('EA1', '80EEB Electric Vehicle Loan');
        $sheet->setCellValue('EB1', 'Tds Certificates Form 26as');
        $sheet->setCellValue('EC1', 'Is Make Donation');
        $sheet->setCellValue('ED1', '80G Donee Name');
        $sheet->setCellValue('EE1', '80G Donee Address');
        $sheet->setCellValue('EF1', '80G Donee City');
        $sheet->setCellValue('EG1', '80G Donee State');
        $sheet->setCellValue('EH1', '80G Donee Pincode');
        $sheet->setCellValue('EI1', '80G Donee Country');
        $sheet->setCellValue('EJ1', '80G Donee Pancard');
        $sheet->setCellValue('EK1', '80G Donation Amount Cash');
        $sheet->setCellValue('EL1', '80G Donation Amount No Cash');
        $sheet->setCellValue('EM1', '80G Donee Qualifying Percentage');
        $sheet->setCellValue('EN1', 'Is Refunded');
        $sheet->setCellValue('EO1', 'Status');
        $sheet->setCellValue('EP1', 'Completed Date');
        $sheet->setCellValue('EQ1', 'Itr Submit File 1');
        $sheet->setCellValue('ER1', 'Itr Submit File 2');
        $sheet->setCellValue('ES1', 'Itr Submit File 3');
        $sheet->setCellValue('ET1', 'Comments');
        $sheet->setCellValue('EU1', 'Created At');

        $rows = 2;
        foreach ($query->get() as $key => $row) {
            $sheet->setCellValue('A' . $rows, $row->token);
            $sheet->setCellValue('B' . $rows, @$row->retailer->userId);
            $sheet->setCellValue('C' . $rows, @$row->retailer->name);
            $sheet->setCellValue('D' . $rows, $row->assessment_year);
            $sheet->setCellValue('E' . $rows, $row->financial_year);
            $sheet->setCellValue('F' . $rows, $row->tax_regime);
            $sheet->setCellValue('G' . $rows, $row->name);
            $sheet->setCellValue('H' . $rows, Date::PHPToExcel($row->date_of_birth));
            $sheet->setCellValue('I' . $rows, $row->father_name);
            $sheet->setCellValue('J' . $rows, config('constant.gender_list.' . $row->gender));
            $sheet->setCellValue('K' . $rows, $row->email);
            $sheet->setCellValue('L' . $rows, $row->phone);
            $sheet->setCellValue('M' . $rows, $row->itr_password);
            $sheet->setCellValue('N' . $rows, $row->pancard_number);
            self::getDownload($sheet, 'O' . $rows, $row->pancard_file);
            $sheet->setCellValue('P' . $rows, $row->adhaar_number);
            self::getDownload($sheet, 'Q' . $rows, $row->adhaar_file);
            $sheet->setCellValue('R' . $rows, $row->phone_2);
            $sheet->setCellValue('S' . $rows, $row->flat_number);
            $sheet->setCellValue('T' . $rows, $row->address);
            $sheet->setCellValue('U' . $rows, @$row->city_name->name);
            $sheet->setCellValue('V' . $rows, @$row->state_name->name);
            $sheet->setCellValue('W' . $rows, $row->pincode);
            $sheet->setCellValue('X' . $rows, $row->country);
            $sheet->setCellValue('Y' . $rows, $row->bank_ifsc);
            $sheet->setCellValue('Z' . $rows, $row->bank_name);
            $sheet->setCellValue('AA' . $rows, $row->bank_account_no);
            $sheet->setCellValue('AB' . $rows, config('constant.bank_account_type.' . $row->account_type));
            $sheet->setCellValue('AC' . $rows, config('constant.bank_account_holder_type.' . $row->bank_account_type));
            $sheet->setCellValue('AD' . $rows, yesNo($row->is_salary_income));
            $sheet->setCellValue('AE' . $rows, $row->employer_name);
            $sheet->setCellValue('AF' . $rows, $row->employer_tan);
            $sheet->setCellValue('AG' . $rows, $row->employer_flat_number);
            $sheet->setCellValue('AH' . $rows, $row->employer_address);
            $sheet->setCellValue('AI' . $rows, @$row->employer_city_name->name);
            $sheet->setCellValue('AJ' . $rows, @$row->employer_state_name->name);
            $sheet->setCellValue('AK' . $rows, $row->employer_pincode);
            $sheet->setCellValue('AL' . $rows, config('constant.employeer_types.' . $row->employer_type));
            $sheet->setCellValue('AM' . $rows, $row->salary);
            $sheet->setCellValue('AN' . $rows, $row->dearness_allowances);
            $sheet->setCellValue('AO' . $rows, $row->bonus_commission);
            $sheet->setCellValue('AP' . $rows, $row->other_amount_head);
            $sheet->setCellValue('AQ' . $rows, $row->other_amount);
            self::getDownload($sheet, 'AR' . $rows, $row->form_16_file);
            $sheet->setCellValue('AS' . $rows, yesNo($row->is_house_income));
            $sheet->setCellValue('AT' . $rows, config('constant.rented_house_type.' . $row->income_house_type));
            $sheet->setCellValue('AU' . $rows, $row->income_house_flat_number);
            $sheet->setCellValue('AV' . $rows, $row->income_house_address);
            $sheet->setCellValue('AW' . $rows, @$row->income_house_city_name->name);
            $sheet->setCellValue('AX' . $rows, @$row->income_house_state_name->name);
            $sheet->setCellValue('AY' . $rows, $row->income_house_pincode);
            $sheet->setCellValue('AZ' . $rows, $row->income_house_rent_received);
            $sheet->setCellValue('BA' . $rows, $row->interest_paid_on_home_loan);
            $sheet->setCellValue('BB' . $rows, $row->principal_paid_on_home_loan);
            self::getDownload($sheet, 'BC' . $rows, $row->rent_agreement);
            $sheet->setCellValue('BD' . $rows, yesNo($row->is_business_income));
            $sheet->setCellValue('BE' . $rows, $row->business_name);
            $sheet->setCellValue('BF' . $rows, config('constant.business_type_list.' . $row->business_type));
            $sheet->setCellValue('BG' . $rows, $row->turnover);
            $sheet->setCellValue('BH' . $rows, $row->net_profit);
            $sheet->setCellValue('BI' . $rows, $row->description);
            $sheet->setCellValue('BJ' . $rows, $row->partners_own_capital);
            $sheet->setCellValue('BK' . $rows, $row->liabilities_secured_loans);
            $sheet->setCellValue('BL' . $rows, $row->liabilities_unsecured_loans);
            $sheet->setCellValue('BM' . $rows, $row->liabilities_advances);
            $sheet->setCellValue('BN' . $rows, $row->liabilities_sundry_creditors);
            $sheet->setCellValue('BO' . $rows, $row->liabilities_other_liabilities);
            $sheet->setCellValue('BP' . $rows, $row->assets_fixed_assets);
            $sheet->setCellValue('BQ' . $rows, $row->assets_inventories);
            $sheet->setCellValue('BR' . $rows, $row->assets_sundry_debtors);
            $sheet->setCellValue('BS' . $rows, $row->assets_balance_with_banks);
            $sheet->setCellValue('BT' . $rows, $row->assets_cash_in_hand);
            $sheet->setCellValue('BU' . $rows, $row->assets_loans_and_advances);
            $sheet->setCellValue('BV' . $rows, $row->assets_other_assets);
            $sheet->setCellValue('BW' . $rows, yesNo($row->is_capital_gain_income));
            $sheet->setCellValue('BX' . $rows, config('constant.capital_gain_asset_type.' . $row->capital_gains_type_1));
            $sheet->setCellValue('BY' . $rows, $row->capital_gains_purchase_date_1);
            $sheet->setCellValue('BZ' . $rows, $row->capital_gains_purchase_amount_1);
            $sheet->setCellValue('CA' . $rows, $row->capital_gains_sale_date_1);
            $sheet->setCellValue('CB' . $rows, $row->capital_gains_sale_amount_1);
            $sheet->setCellValue('CC' . $rows, config('constant.capital_gain_asset_type.' . $row->capital_gains_type_2));
            $sheet->setCellValue('CD' . $rows, $row->capital_gains_purchase_date_2);
            $sheet->setCellValue('CE' . $rows, $row->capital_gains_purchase_amount_2);
            $sheet->setCellValue('CF' . $rows, $row->capital_gains_sale_date_2);
            $sheet->setCellValue('CG' . $rows, $row->capital_gains_sale_amount_2);
            $sheet->setCellValue('CH' . $rows, config('constant.capital_gain_asset_type.' . $row->capital_gains_type_3));
            $sheet->setCellValue('CI' . $rows, $row->capital_gains_purchase_date_3);
            $sheet->setCellValue('CJ' . $rows, $row->capital_gains_purchase_amount_3);
            $sheet->setCellValue('CK' . $rows, $row->capital_gains_sale_date_3);
            $sheet->setCellValue('CL' . $rows, $row->capital_gains_sale_amount_3);
            $sheet->setCellValue('CM' . $rows, config('constant.capital_gain_asset_type.' . $row->capital_gains_type_4));
            $sheet->setCellValue('CN' . $rows, $row->capital_gains_purchase_date_4);
            $sheet->setCellValue('CO' . $rows, $row->capital_gains_purchase_amount_4);
            $sheet->setCellValue('CP' . $rows, $row->capital_gains_sale_date_4);
            $sheet->setCellValue('CQ' . $rows, $row->capital_gains_sale_amount_4);
            $sheet->setCellValue('CR' . $rows, $row->investment_sale_amount_in_house);
            $sheet->setCellValue('CS' . $rows, $row->investment_sale_amount_in_securities);
            $sheet->setCellValue('CT' . $rows, $row->investment_sale_amount_in_capital_gain_bank_a_c);
            $sheet->setCellValue('CU' . $rows, yesNo($row->is_other_income));
            $sheet->setCellValue('CV' . $rows, $row->commission);
            $sheet->setCellValue('CW' . $rows, $row->brokerage);
            $sheet->setCellValue('CX' . $rows, $row->interest_from_saving_bank);
            $sheet->setCellValue('CY' . $rows, $row->interest_from_fixed_deposit);
            $sheet->setCellValue('CZ' . $rows, $row->dividend);
            $sheet->setCellValue('DA' . $rows, $row->family_pension);
            $sheet->setCellValue('DB' . $rows, $row->other_rent);
            $sheet->setCellValue('DC' . $rows, $row->other_interest);
            $sheet->setCellValue('DD' . $rows, $row->mutual_fund);
            $sheet->setCellValue('DE' . $rows, $row->uti_income);
            $sheet->setCellValue('DF' . $rows, $row->agricultural_gross_income);
            $sheet->setCellValue('DG' . $rows, $row->agricultural_expenses);
            $sheet->setCellValue('DH' . $rows, $row['80c_life_insurance_premium_paid']);
            $sheet->setCellValue('DI' . $rows, $row['80c_gpf_ppf']);
            $sheet->setCellValue('DJ' . $rows, $row['80c_ulip']);
            $sheet->setCellValue('DK' . $rows, $row['80c_provident_fund']);
            $sheet->setCellValue('DL' . $rows, $row['80c_mutual_fund']);
            $sheet->setCellValue('DM' . $rows, $row['80c_principal_on_home_loan']);
            $sheet->setCellValue('DN' . $rows, $row['80c_tuition_fees_upto_2_children']);
            $sheet->setCellValue('DO' . $rows, $row['80c_fixed_deposit']);
            $sheet->setCellValue('DP' . $rows, $row['80c_tax_saving_bonds']);
            $sheet->setCellValue('DQ' . $rows, $row['80d_checkup_fee_for_self']);
            $sheet->setCellValue('DR' . $rows, $row['80d_checkup_fee_for_parents']);
            $sheet->setCellValue('DS' . $rows, $row['80d_medical_expenditures_for_self']);
            $sheet->setCellValue('DT' . $rows, $row['80d_medical_expenditures_for_parents']);
            $sheet->setCellValue('DU' . $rows, $row['80tta_interest_earned_saving_banks']);
            $sheet->setCellValue('DV' . $rows, $row['80ccc_pension_annuity_fund']);
            $sheet->setCellValue('DW' . $rows, $row['80ccd_own_contribution_nps']);
            $sheet->setCellValue('DX' . $rows, $row['80ccd_employer_contribution_nps']);
            $sheet->setCellValue('DY' . $rows, $row['80u_disablity'] . '%');
            $sheet->setCellValue('DZ' . $rows, $row['80ee_interest_on_home_loan']);
            $sheet->setCellValue('EA' . $rows, $row['80eeb_electric_vehicle_loan']);
            self::getDownload($sheet, 'EB' . $rows, $row->tds_certificates_form_26as);
            $sheet->setCellValue('EC' . $rows, yesNo($row->is_make_donation));
            $sheet->setCellValue('ED' . $rows, $row['80g_donee_name']);
            $sheet->setCellValue('EE' . $rows, $row['80g_donee_address']);
            $sheet->setCellValue('EF' . $rows, @$row->donee_city_name->name);
            $sheet->setCellValue('EG' . $rows, @$row->donee_state_name->name);
            $sheet->setCellValue('EH' . $rows, $row['80g_donee_pincode']);
            $sheet->setCellValue('EI' . $rows, $row['80g_donee_country']);
            $sheet->setCellValue('EJ' . $rows, $row['80g_donee_pancard']);
            $sheet->setCellValue('EK' . $rows, $row['80g_donation_amount_cash']);
            $sheet->setCellValue('EL' . $rows, $row['80g_donation_amount_no_cash']);
            $sheet->setCellValue('EM' . $rows, $row['80g_donee_qualifying_percentage'] . '%');
            $sheet->setCellValue('EN' . $rows, yesNo($row->is_refunded));
            $sheet->setCellValue('EO' . $rows, config('constant.itr_file_status_list.' . $row->status));
            $sheet->setCellValue('EP' . $rows, $row->completed_date ? Date::PHPToExcel($row->completed_date) : null);
            self::getDownload($sheet, 'EQ' . $rows, $row->itr_submit_file_1);
            self::getDownload($sheet, 'ER' . $rows, $row->itr_submit_file_2);
            self::getDownload($sheet, 'ES' . $rows, $row->itr_submit_file_3);
            $sheet->setCellValue('ET' . $rows, $row->comments);
            $sheet->setCellValue('EU' . $rows, Date::PHPToExcel($row->created_at));
            $rows++;
        }


        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('bbbbbb');

        // AutoWidth Column
        $sheet->getStyle('A1:EU' . $rows)->getAlignment()->setHorizontal('center');
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        foreach (['H1:H', 'BY1:BY', 'CA1:CA', 'CD1:CD', 'CF1:CF', 'CI1:CI', 'CK1:CK', 'CN1:CN', 'CP1:CP', 'EP1:EP'] as $key => $value) {
            $sheet->getStyle($value . $rows)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        }

        foreach (
            [
                'BZ1:BZ',
                'CB1:CB',
                'CD1:CD',
                'CE1:CE',
                'CG1:CG',
                'CJ1:CJ',
                'CL1:CL',
                'CO1:CO',
                'AM1:AQ',
                'AZ1:BB',
                'BG1:BH',
                'BJ1:BV',
                'CQ1:CT',
                'CV1:EA',
                'EK1:EL',
            ] as $key => $value
        ) {
            $sheet->getStyle($value . $rows)->getNumberFormat()->setFormatCode('"₹" #,##0.00_-');
        }

        $sheet->getStyle('EU1:EU' . $rows)->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm AM/PM');
        $sheet->getStyle('P1:P' . $rows)->getNumberFormat()->setFormatCode('#');
        $sheet->getStyle('AA1:AA' . $rows)->getNumberFormat()->setFormatCode('#');

        $fileName = "ITR Export.xlsx";
        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . ($fileName) . '"');
        return $response->send();
    }

    public function template(): StreamedResponse
    {
        $query = ItReturn::query();
        $query->where('status', 1);
        $query->with(['city_name', 'state_name', 'employer_city_name', 'employer_state_name', 'income_house_city_name', 'income_house_state_name', 'donee_city_name', 'donee_state_name']);

        // Start Building Excel Sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Retailer PanCard Count');

        $header = ["Code No", "Prefix", "Client Name", "First Name", "Middle Name", "Last Name", "Bussiness Name", "Father First Name", "Father Middle Name", "Father Last Name", "Sex", "DOB / Date Of InCorporation", "Residential Status", "Pan No", "Passport No", "Tan No", "TIN No", "CST No", "Ward", "Rang", "AO Code", "Signing Person", "Signing Person Father Name", "Designation", "Sign Sex", "Sign Pan No", "Sign Dob", "CIN No", "DIN", "S. Tax Reg No", "Software", "Flat No", "Building No", "Road", "Street", "Area", "Locality", "Taluka", "District", "City", "State", "Pin", "STD Code", "Phone 1", "Phone2", "Mobile No", "Fax", "Email Id", "Bank  Account No", "Bank Name", "Bank Branch", "Account Type", "MICR No", "IFSC Code", "BSR Code", "GSTIN", "STATUS", "Tax ITD User ID", "Tax ITD Password", "GST User ID", "GST Password", "Aadhar No", "Group Code"];
        foreach ($header as $key => $value) {
            $sheet->setCellValue(getNameFromNumber($key + 1) . '1', $value);
            $sheet->setCellValue(getNameFromNumber($key + 1) . '2', $key + 1);
        }

        $rows = 3;
        foreach ($query->get() as $key => $row) {
            $sheet->setCellValue('A' . $rows, $row->pancard_number);
            $sheet->setCellValue('C' . $rows, $row->name);
            $sheet->setCellValue('D' . $rows, $row->first_name);
            $sheet->setCellValue('E' . $rows, $row->middle_name);
            $sheet->setCellValue('F' . $rows, $row->last_name);
            $sheet->setCellValue('G' . $rows, $row->business_name);
            $sheet->setCellValue('H' . $rows, $row->father_first_name);
            $sheet->setCellValue('I' . $rows, $row->father_middle_name);
            $sheet->setCellValue('J' . $rows, $row->father_last_name);
            $sheet->setCellValue('K' . $rows, $row->gender == 1 ? 'M' : ($row->gender == 2 ? 'F' : ''));
            $sheet->setCellValue('L' . $rows, $row->date_of_birth->format("d/m/Y"));
            $sheet->setCellValue('M' . $rows, "Resident");
            $sheet->setCellValue('N' . $rows, $row->pancard_number);
            $sheet->setCellValue('AE' . $rows, "Tax");
            $sheet->setCellValue('AF' . $rows, "");
            $sheet->setCellValue('AG' . $rows, $row->flat_number);
            $sheet->setCellValue('AH' . $rows, $row->address);
            $sheet->setCellValue('AM' . $rows,  @$row->city_name->name);
            $sheet->setCellValue('AN' . $rows,  @$row->city_name->name);
            $sheet->setCellValue('AO' . $rows,  @$row->state_name->name);
            $sheet->setCellValue('AP' . $rows, $row->pincode);
            $sheet->setCellValue('AQ' . $rows, 91);
            $sheet->setCellValue('AR' . $rows, $row->mobile);
            $sheet->setCellValue('AS' . $rows, $row->phone_2);
            $sheet->setCellValue('AT' . $rows, $row->phone);
            $sheet->setCellValue('AV' . $rows, $row->email);
            $sheet->setCellValue('AW' . $rows, $row->bank_account_no);
            $sheet->setCellValue('AX' . $rows, $row->bank_name);
            $sheet->setCellValue('AZ' . $rows, $row->account_type == 1 ? "Saving" : ($row->account_type == 2 ? "Current" : ""));
            $sheet->setCellValue('BB' . $rows, $row->bank_ifsc);
            $sheet->setCellValue('BF' . $rows, $row->pancard_number);
            $sheet->setCellValue('BG' . $rows, $row->itr_password);
            $sheet->setCellValue('BJ' . $rows, $row->adhaar_number);
            ++$rows;
        }

        // Header Row Bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getRowDimension('1')->setRowHeight(35);
        $sheet->getStyle('A1:' . $highestColumn . '1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00ff00');
        $sheet->getStyle('A2:' . $highestColumn . '2')->getFont()->setBold(true);
        $sheet->getStyle('A2:' . $highestColumn . '2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ff99cc');

        $sheet->getStyle('AW1:AW' . $rows)->getNumberFormat()->setFormatCode('#');
        $sheet->getStyle('BJ1:BJ' . $rows)->getNumberFormat()->setFormatCode('#');

        // AutoWidth Column
        $sheet->getStyle('A1:BK' . $rows)->getAlignment()->setHorizontal('center');
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        for ($i = 63; $i <= 256; $i++) {
            $sheet->getColumnDimension(getNameFromNumber($i))->setVisible(false);
        }

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xls($spreadsheet);
            $writer->save('php://output');
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment; filename="Template.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response->send();
    }

    public function update_status(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'id'                => ['required', 'integer'],
            'status'            => ['required', 'integer'],
            'itr_submit_file_1' => ['nullable', 'file', 'max:5000'],
            'message'           => ['required', 'string', 'min: 2', 'max:100'],
        ]);

        if ($validation->fails()) {

            foreach ($validation->errors()->toArray() as $key => $value) {
                $err[$key] = $value[0];
            }

            return response()->json([
                'status'    => false,
                'message'   => "Invalid Input values.",
                "data"      => $err
            ]);
        } else {
            $certificate = ItReturn::find($request->id);
            if (!$certificate) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Details Not valid',
                    'data'      => ''
                ]);
            }

            if ($request->status == 2) {
                if (!$certificate->itr_submit_file_1 && !$request->file('itr_submit_file_1')) {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Details Not valid',
                        'data'      => ''
                    ]);
                }

                $data   = [];
                if ($request->hasFile('itr_submit_file_1')) {
                    $data['itr_submit_file_1'] =  saveFile($request->file('itr_submit_file_1'), 'documents');
                }

                if ($request->hasFile('itr_submit_file_2')) {
                    $data['itr_submit_file_2'] =  saveFile($request->file('itr_submit_file_2'), 'documents');
                }

                if ($request->hasFile('itr_submit_file_3')) {
                    $data['itr_submit_file_3'] =  saveFile($request->file('itr_submit_file_3'), 'documents');
                }

                $certificate->update([...$data, 'completed_date' => now(), 'status' => $request->status, 'comments' => $request->message]);
            }

            if ($request->status == 3) {
                DB::transaction(function () use ($certificate, $request) {
                    $serviceLog = ServicesLog::where([
                        'user_id'       => $certificate->user_id,
                        'user_type'     => $certificate->user_type,
                        'service_id'    => config('constant.service_ids.income_tax_return'),
                        'status'        => 1
                    ])->first();
                    LedgerController::refundItrService($certificate, $serviceLog, Str::limit($request->message));
                });
            }

            if ($request->status == 4) {
                $certificate->update(['status' => $request->status, 'comments' => $request->message]);
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Status Updated Successfully..!!',
                'data'      => ''
            ]);
        }
    }

    protected static function getDownload($sheet, $cell, $file): void
    {
        if ($file) {
            $sheet->setCellValueExplicit($cell, 'Download', DataType::TYPE_STRING2);
            $sheet->getCell($cell)->getHyperlink()->setUrl(strip_tags(asset('storage/' . $file)));
        }
    }
}
