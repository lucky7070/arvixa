@extends('my_services.income_tax_return.index')

@section('sub_section')
<form action="{{ request()->url() }}" method="post" id="stepThree" enctype="multipart/form-data">
    @csrf
    <fieldset @disabled($itr->status > 0 && $itr->status != 4)>
        <div class="row">
            <div class="col-12">
                <div id="mainAccordion" class="accordion-icons accordion">
                    <div class="card">
                        <div class="card-header custom-accordion" id="from_2">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#popularDeduct" aria-expanded="false" aria-controls="popularDeduct">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-piggy-bank"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold">Popular Deductions</h6>
                                        <small class="fs--1">80C (PPF, Life Insurance, ELSS Mutual funds etc.), Health
                                            Insurance, NPS, Interest earned on Savings Bank Account </small>
                                    </div>
                                    <div class="icons px-3 lh-lg">
                                        <i class="fa fa-chevron-down"></i>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div id="popularDeduct" class="collapse" aria-labelledby="from_2"
                            data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-bold"> Deductions under Section 80C </h6>
                                        <p>80C: PPF (Public Provident Fund), Sukanya Samridhi, ULIPs, Insurance premium,
                                            Five year fixed deposits, Postal deposits, ELSS Mutual Funds are eligible
                                            for
                                            80C. Children school fees, payment of principal of housing loan is also
                                            eligible.</p>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Life Insurance Premium Paid
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80c_life_insurance_premium_paid', $itr['80c_life_insurance_premium_paid']) }}"
                                                        name="80c_life_insurance_premium_paid" step="0.01"
                                                        max="10000000" placeholder="Life Insurance Premium Paid" />
                                                </div>
                                                @if($errors->has('80c_life_insurance_premium_paid'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80c_life_insurance_premium_paid') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                General / Public Provident Fund
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80c_gpf_ppf', $itr['80c_gpf_ppf']) }}"
                                                        name="80c_gpf_ppf" step="0.01" max="10000000"
                                                        placeholder="Life Insurance Premium Paid" />
                                                </div>
                                                @if($errors->has('80c_gpf_ppf'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80c_gpf_ppf') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Unit Linked Insurance Plan
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80c_ulip', $itr['80c_ulip']) }}" name="80c_ulip"
                                                        step="0.01" max="10000000"
                                                        placeholder="Unit Linked Insurance Plan (ULIP)" />
                                                </div>
                                                @if($errors->has('80c_ulip'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80c_ulip') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Provident Fund
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80c_provident_fund', $itr['80c_provident_fund']) }}"
                                                        name="80c_provident_fund" step="0.01" max="10000000"
                                                        placeholder="Provident Fund" />
                                                </div>
                                                @if($errors->has('80c_provident_fund'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80c_provident_fund') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Mutual Fund
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80c_mutual_fund', $itr['80c_mutual_fund']) }}"
                                                        name="80c_mutual_fund" step="0.01" max="10000000"
                                                        placeholder="Mutual Fund" />
                                                </div>
                                                @if($errors->has('80c_mutual_fund'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80c_mutual_fund') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Principal on Home Loan
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80c_principal_on_home_loan', $itr['80c_principal_on_home_loan']) }}"
                                                        name="80c_principal_on_home_loan" step="0.01" max="10000000"
                                                        placeholder="Principal on Home Loan" />
                                                </div>
                                                @if($errors->has('80c_principal_on_home_loan'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80c_principal_on_home_loan') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Tuition Fees Upto 2 Children
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80c_tuition_fees_upto_2_children', $itr['80c_tuition_fees_upto_2_children']) }}"
                                                        name="80c_tuition_fees_upto_2_children" step="0.01"
                                                        max="10000000" placeholder="Tuition Fees Upto 2 Children" />
                                                </div>
                                                @if($errors->has('80c_tuition_fees_upto_2_children'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80c_tuition_fees_upto_2_children') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Fixed Deposit
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80c_fixed_deposit', $itr['80c_fixed_deposit']) }}"
                                                        name="80c_fixed_deposit" step="0.01" max="10000000"
                                                        placeholder="Fixed Deposit " />
                                                </div>
                                                @if($errors->has('80c_fixed_deposit'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80c_fixed_deposit') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Tax Saving Bonds
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80c_tax_saving_bonds', $itr['80c_tax_saving_bonds']) }}"
                                                        name="80c_tax_saving_bonds" step="0.01" max="10000000"
                                                        placeholder="Tax Saving Bonds" />
                                                </div>
                                                @if($errors->has('80c_tax_saving_bonds'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80c_tax_saving_bonds') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h6 class="fw-bold">
                                            Section 80D - Medical Insurance and Preventive Health Checkup
                                        </h6>
                                        <p>Deductions for Medical Insurance or Preventive Health Check-Up fees or
                                            Medical
                                            Expenditure incurred by you.</p>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Preventive health check up fee
                                            </label>
                                            <div class="col-sm-4">
                                                <label for="">For Self and family</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80d_checkup_fee_for_self', $itr['80d_checkup_fee_for_self']) }}"
                                                        name="80d_checkup_fee_for_self" step="0.01" max="10000000"
                                                        placeholder="For Self and family" />
                                                </div>
                                                @if($errors->has('80d_checkup_fee_for_self'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80d_checkup_fee_for_self') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="">For Parents</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80d_checkup_fee_for_parents', $itr['80d_checkup_fee_for_parents']) }}"
                                                        name="80d_checkup_fee_for_parents" step="0.01" max="10000000"
                                                        placeholder="For Parents" />
                                                </div>
                                                @if($errors->has('80d_checkup_fee_for_parents'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80d_checkup_fee_for_parents') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row justify-content-end">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Medical Expenditures
                                            </label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80d_medical_expenditures_for_self', $itr['80d_medical_expenditures_for_self']) }}"
                                                        name="80d_medical_expenditures_for_self" step="0.01"
                                                        max="10000000" placeholder="For Self and family" readonly />
                                                </div>
                                                @if($errors->has('80d_medical_expenditures_for_self'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80d_medical_expenditures_for_self') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80d_medical_expenditures_for_parents', $itr['80d_medical_expenditures_for_parents']) }}"
                                                        name="80d_medical_expenditures_for_parents" step="0.01"
                                                        max="10000000" placeholder="For Parents" />
                                                </div>
                                                @if($errors->has('80d_medical_expenditures_for_parents'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80d_medical_expenditures_for_parents') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-8">
                                                <small>This expenditure can be claimed only for senior citizens who
                                                    don't
                                                    have medical insurance policy</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h6 class="fw-bold">
                                            Section 80TTA - Deduction for Interest earned on Savings Bank Account
                                        </h6>
                                        <p>A value for 80TTA deduction which is a sum of your declared interest from
                                            Savings
                                            Bank account and post office savings bank account. However, as per gov
                                            mandate
                                            we only consider upto a max deduction limit of 10K.</p>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Interest earned on Savings Bank
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80tta_interest_earned_saving_banks', $itr['80tta_interest_earned_saving_banks']) }}"
                                                        name="80tta_interest_earned_saving_banks" step="0.01"
                                                        max="10000"
                                                        placeholder="Interest earned on Savings Bank Account" />
                                                </div>
                                                <small class="text-muted">[ Max Limit: ₹ 10,000 ]</small>
                                                @if($errors->has('80tta_interest_earned_saving_banks'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80tta_interest_earned_saving_banks') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h6 class="fw-bold">
                                            Section 80CCC - Contribution to Pension Plan / Annuity Fund
                                        </h6>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Contribution amount to Pension Plan / Annuity Fund
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80ccc_pension_annuity_fund', $itr['80ccc_pension_annuity_fund']) }}"
                                                        name="80ccc_pension_annuity_fund" step="0.01" max="150000"
                                                        placeholder="Contribution amount to Pension Plan / Annuity Fund" />
                                                </div>
                                                <small class="text-muted">[ Max Limit: ₹ 1,50,000 ]</small>
                                                @if($errors->has('80ccc_pension_annuity_fund'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80ccc_pension_annuity_fund') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h6 class="fw-bold">
                                            Section 80CCD (1) and (1B) - Employee Contribution to New Pension Scheme
                                            (NPS)
                                        </h6>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Enter your own contribution to NPS
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80ccd_own_contribution_nps', $itr['80ccd_own_contribution_nps']) }}"
                                                        name="80ccd_own_contribution_nps" step="0.01" max="150000"
                                                        placeholder="Enter your own contribution to NPS" />
                                                </div>
                                                <small class="text-muted">[ Max Limit: ₹ 1,50,000 ]</small>
                                                @if($errors->has('80ccd_own_contribution_nps'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80ccd_own_contribution_nps') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Section 80CCD(2) - Employer contribution to NPS
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80ccd_employer_contribution_nps', $itr['80ccd_employer_contribution_nps']) }}"
                                                        name="80ccd_employer_contribution_nps" step="0.01"
                                                        max="10000000" placeholder="Employer contribution to NPS" />
                                                </div>
                                                @if($errors->has('80ccd_employer_contribution_nps'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80ccd_employer_contribution_nps') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header custom-accordion" id="from_2">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#moreDeduct" aria-expanded="false" aria-controls="moreDeduct">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-flag"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold">Other Deductions</h6>
                                        <small class="fs--1">Donations to charitable organizations, educational loan,
                                            house
                                            rent for self employed and more </small>
                                    </div>
                                    <div class="icons px-3 lh-lg">
                                        <i class="fa fa-chevron-down"></i>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div id="moreDeduct" class="collapse" aria-labelledby="from_2" data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Section 80U - Self Disablity
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <div class="custom-control-inline">
                                                        <input type="radio" id="0_disability" name="80u_disablity"
                                                            value="0" @checked(old('80u_disablity',
                                                            $itr['80u_disablity'])==0) class="custom-control-input">
                                                        <label class="custom-control-label"
                                                            for="0_disability">None</label>
                                                    </div>
                                                    <div class="custom-control-inline">
                                                        <input type="radio" id="40_disability" name="80u_disablity"
                                                            value="40" class="custom-control-input"
                                                            @checked(old('80u_disablity', $itr['80u_disablity'])==40)>
                                                        <label class="custom-control-label" for="40_disability">40%
                                                            Disability</label>
                                                    </div>
                                                    <div class="custom-control-inline">
                                                        <input type="radio" id="80_disability" name="80u_disablity"
                                                            value="80" class="custom-control-input"
                                                            @checked(old('80u_disablity', $itr['80u_disablity'])==80)>
                                                        <label class="custom-control-label" for="80_disability">80%
                                                            Disability</label>
                                                    </div>
                                                </div>
                                                @if($errors->has('80u_disablity'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80u_disablity') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Section 80EE - Interest on Home Loan
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80ee_interest_on_home_loan', $itr['80ee_interest_on_home_loan']) }}"
                                                        name="80ee_interest_on_home_loan" step="0.01" max="10000000"
                                                        placeholder="Interest on Home Loan" />
                                                </div>
                                                <small class="text-muted">
                                                    Interest on loan taken from a financial institution on your first
                                                    house
                                                    purchased. The value of the house can be upto Rs. 45 Lakhs.
                                                    Deduction
                                                    can be claimed upto Rs. 1.5 lakhs.
                                                </small>
                                                @if($errors->has('80ee_interest_on_home_loan'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80ee_interest_on_home_loan') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Section 80EEB - Electric Vehicle Loan
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('80eeb_electric_vehicle_loan', $itr['80eeb_electric_vehicle_loan']) }}"
                                                        name="80eeb_electric_vehicle_loan" step="0.01" max="10000000"
                                                        placeholder="Interest on Home Loan" />
                                                </div>
                                                <small class="text-muted">
                                                    Read about the eligibility criteria of Section 80EEB: Deduction
                                                    under
                                                    section 80EEB is available on interest on loan taken from a
                                                    financial
                                                    institution for purchase of an electric vehicle.
                                                </small>
                                                @if($errors->has('80eeb_electric_vehicle_loan'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('80eeb_electric_vehicle_loan') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <h6 class="fw-bold">
                                            Taxes Paid, TDS and TCS
                                        </h6>
                                        <p>Taxes deducted or collected at source, Advance Tax or Self Assessment Tax
                                            already
                                            paid; you can Upload <b>Form 26AS</b> to fetch these details.</p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Form 26AS
                                            </label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-file-pdf"></i>
                                                    </span>
                                                    <input type="file" class="form-control"
                                                        name="tds_certificates_form_26as" />
                                                    @if($itr->tds_certificates_form_26as)
                                                    <a href="{{ asset('storage/'. $itr->tds_certificates_form_26as) }}"
                                                        class="input-group-text" download>
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                                <spam class="text-muted">Choose your Form-26AS PDF to upload</spam>
                                                @if($errors->has('tds_certificates_form_26as'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('tds_certificates_form_26as') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <h6 class="fw-bold"> Section 80G - Donations to charitable organizations </h6>
                                        <p> Did you donate any amount to charitable organisations in the last financial
                                            year?</p>
                                        <div
                                            class="switch form-switch-custom form-switch-secondary d-flex align-items-start">
                                            <input class="switch-input" name="is_make_donation" value="1"
                                                id="donationBlockCheck" type="checkbox" role="switch"
                                                @checked(old('is_make_donation', $itr->is_make_donation)==1) />
                                        </div>
                                        @if($errors->has('employer_name'))
                                        <span class="invalid-feedback">
                                            {{ $errors->first('employer_name') }}
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-12" id="isDonation" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label col-form-label-sm">
                                                        Name of Donee Organization
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control"
                                                            value="{{ old('80g_donee_name', $itr['80g_donee_name']) }}"
                                                            name="80g_donee_name" minlength="5" maxlength="100"
                                                            placeholder="Name of Donee Organization">
                                                        @if($errors->has('80g_donee_name'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('80g_donee_name') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label col-form-label-sm">
                                                        Address of Donee
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <div class="row">
                                                            <div class="col-sm-12 mb-2">
                                                                <input type="text" class="form-control"
                                                                    value="{{ old('80g_donee_address', $itr['80g_donee_address']) }}"
                                                                    name="80g_donee_address" minlength="5"
                                                                    maxlength="100" placeholder="Address of Donee">
                                                                @if($errors->has('80g_donee_address'))
                                                                <span class="invalid-feedback">
                                                                    {{ $errors->first('80g_donee_address') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-4 mb-2">
                                                                <select id="80g_donee_state" name="80g_donee_state"
                                                                    onchange="getCity(this.value, '#80g_donee_city')"
                                                                    class="form-select">
                                                                    <option value="">Select State</option>
                                                                    @foreach ($states as $state)
                                                                    <option
                                                                        @selected(old('80g_donee_state',$itr['80g_donee_state'])==$state['id'])
                                                                        value="{{ $state['id'] }}">
                                                                        {{ $state['name'] }}
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                                @if($errors->has('80g_donee_state'))
                                                                <span class="invalid-feedback">
                                                                    {{ $errors->first('80g_donee_state') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-4 mb-2">
                                                                <select id="80g_donee_city" name="80g_donee_city"
                                                                    class="form-select">
                                                                    <option value="">Select City</option>
                                                                </select>
                                                                @if($errors->has('80g_donee_city'))
                                                                <span class="invalid-feedback">
                                                                    {{ $errors->first('80g_donee_city') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-4 mb-2">
                                                                <input type="text" class="form-control"
                                                                    value="{{ old('80g_donee_pincode', $itr['80g_donee_pincode']) }}"
                                                                    name="80g_donee_pincode" minlength="6" maxlength="6"
                                                                    placeholder="Pin Code">
                                                                @if($errors->has('80g_donee_pincode'))
                                                                <span class="invalid-feedback">
                                                                    {{ $errors->first('80g_donee_pincode') }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label col-form-label-sm">
                                                        PAN of Donee
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control w-md-50"
                                                            value="{{ old('80g_donee_pancard', $itr['80g_donee_pancard']) }}"
                                                            name="80g_donee_pancard" minlength="10" maxlength="15"
                                                            placeholder="PAN of Donee">
                                                        <small class="text-muted">
                                                            Enter 'GGGGG0000G' if you have donated to Government Funds
                                                            that
                                                            don't have a PAN.
                                                        </small>
                                                        @if($errors->has('80g_donee_pancard'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('80g_donee_pancard') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label col-form-label-sm">
                                                        Donation Amount (Cash)
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group w-md-50">
                                                            <span class="input-group-text">
                                                                <i class="fa fa-inr"></i>
                                                            </span>
                                                            <input type="number" class="form-control rm-number"
                                                                value="{{ old('80g_donation_amount_cash', $itr['80g_donation_amount_cash']) }}"
                                                                name="80g_donation_amount_cash" step="0.01"
                                                                max="10000000" placeholder="Donation Amount (Cash)">
                                                        </div>
                                                        @if($errors->has('80g_donation_amount_cash'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('80g_donation_amount_cash') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label col-form-label-sm">
                                                        Donation Amount (Non Cash)
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group w-md-50">
                                                            <span class="input-group-text">
                                                                <i class="fa fa-inr"></i>
                                                            </span>
                                                            <input type="number" class="form-control rm-number"
                                                                value="{{ old('80g_donation_amount_no_cash', $itr['80g_donation_amount_no_cash']) }}"
                                                                name="80g_donation_amount_no_cash" step="0.01"
                                                                max="10000000" placeholder="Donation Amount (Non Cash)">
                                                        </div>
                                                        @if($errors->has('80g_donation_amount_no_cash'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('80g_donation_amount_no_cash') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label col-form-label-sm">
                                                        Qualifying Percentage
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <div class="form-group">
                                                            <div class="custom-control-inline">
                                                                <input type="radio" id="50_lility"
                                                                    name="80g_donee_qualifying_percentage" value="50"
                                                                    @checked(old('80g_donee_qualifying_percentage',
                                                                    $itr['80g_donee_qualifying_percentage'])==50)
                                                                    class="custom-control-input">
                                                                <label class="custom-control-label" for="50_lility">50%
                                                                </label>
                                                            </div>
                                                            <div class="custom-control-inline">
                                                                <input type="radio" id="100_lility"
                                                                    name="80g_donee_qualifying_percentage" value="100"
                                                                    @checked(old('80g_donee_qualifying_percentage',
                                                                    $itr['80g_donee_qualifying_percentage'])==100)
                                                                    class="custom-control-input">
                                                                <label class="custom-control-label"
                                                                    for="100_lility">100%
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @if($errors->has('80g_donee_qualifying_percentage'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('80g_donee_qualifying_percentage') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <button class="btn btn-success btn-lg">Save Information</button>
                </div>
            </div>
        </div>
    </fieldset>
</form>
@endsection

@section('js')
<script>
    var is_make_donation = parseInt("{{ old('is_make_donation', $itr->is_make_donation) }}");
    var city_id = parseInt("{{ old('80g_donee_city', $itr['80g_donee_city']) }}");
    var state_id = parseInt("{{ old('80g_donee_state', $itr['80g_donee_state']) }}");

    function getCity(state_id, selector) {
        $.ajax({
            type: "POST",
            url: "{{ route('cities.list') }}",
            data: { state_id, city_id, _token: "{{ csrf_token() }}" },
            success: function (data) {
                $(selector).html(data);
            },
        });
        return true;
    }

    $(function () {

        $('#donationBlockCheck').on('change', function () { $('#isDonation').fadeToggle(); });
        setTimeout(() => {
            state_id && getCity(state_id, '#80g_donee_city')
            is_make_donation && $('#isDonation').fadeToggle();
        }, 200)

        $("#stepThree").validate({
            ignore: [],
            errorClass: "text-danger",
            errorElement: "small",
            rules: {
                'tds_certificates_form_26as': {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2
                },
                '80g_donee_name': { required: () => $('[name="is_make_donation"]').is(":checked") },
                '80g_donee_address': { required: () => $('[name="is_make_donation"]').is(":checked") },
                '80g_donee_city': { required: () => $('[name="is_make_donation"]').is(":checked") },
                '80g_donee_state': { required: () => $('[name="is_make_donation"]').is(":checked") },
                '80g_donee_pincode': { required: () => $('[name="is_make_donation"]').is(":checked") },
                '80g_donee_pancard': { required: () => $('[name="is_make_donation"]').is(":checked"), pancard: true, },
                '80g_donation_amount_cash': { required: () => $('[name="is_make_donation"]').is(":checked") },
                '80g_donation_amount_no_cash': { required: () => $('[name="is_make_donation"]').is(":checked") },
                '80g_donee_qualifying_percentage': { required: () => $('[name="is_make_donation"]').is(":checked") },
            },
            messages: {
                'tds_certificates_form_26as': {
                    extension: "Supported Format Only : pdf, jpg, jpeg, png"
                },
                '80g_donee_name': { required: "Please enter donee name." },
                '80g_donee_address': { required: "Please enter donee address." },
                '80g_donee_city': { required: "Please select donee city." },
                '80g_donee_state': { required: "Please select donee state." },
                '80g_donee_pincode': { required: "Please enter donee pincode." },
                '80g_donee_pancard': { required: "Please enter donee Pancard.", pancard: true, },
                '80g_donation_amount_cash': { required: "Please enter donation amount (Cash)." },
                '80g_donation_amount_no_cash': { required: "Please enter donation amount (Non Cash)" },
                '80g_donee_qualifying_percentage': { required: "Please select Qualifying Percentage." },
            },
            errorPlacement: function (error, element) {
                if ($(element).parent().hasClass('input-group') || $(element).parent().hasClass('input-group-text')) {
                    error.appendTo(element.parents().eq(1));
                } else if ($(element).parent().hasClass('custom-control-inline')) {
                    error.appendTo(element.parents().eq(2));
                } else {
                    error.insertAfter(element);
                }

                if ($(element).closest('.card').find('.is-invalid').length == 0) {
                    $(element).closest('.card').addClass('border-success')
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid')
                $(element).closest('.card').addClass('border-danger')
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid')
                if ($(element).closest('.card').find('.is-invalid').length == 0) {
                    $(element).closest('.card').addClass('border-success')
                }
            },
        });
    })
</script>

@endsection