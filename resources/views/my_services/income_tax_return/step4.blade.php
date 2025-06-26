@extends('my_services.income_tax_return.index')

@section('sub_section')
<div class="row">
    <div id="" class="acco">
        <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fs-6">ITR Token </h6>
                <span class="fw-bold"> {{ $itr->token }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fs-6"> Assessment Year </h6>
                <span class=""> {{ $itr->assessment_year }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fs-6">Financial Year </h6>
                <span class=""> {{ $itr->financial_year }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fs-6">Tax Regime </h6>
                <span class=""> {{ $itr->tax_regime == 'O' ? "Old Regime" : "New Regime" }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fs-6">Status </h6>
                @switch($itr->status)
                @case(0)
                <small class="badge fw-semi-bold rounded-pill status badge-light-secondary"> Pending</small>
                @break
                @case(1)
                <small class="badge fw-semi-bold rounded-pill status badge-light-info"> Submitted</small>
                @break
                @case(2)
                <small class="badge fw-semi-bold rounded-pill status badge-light-success"> Completed</small>
                @break
                @case(3)
                <small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Rejected</small>
                @break
                @case(4)
                <small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Under Draft</small>
                @break
                @endswitch
            </li>

            @if(in_array($itr->status, [2, 3, 4]))
            <li class="list-group-item">
                <h6 class="mb-0 fs-6">Comments </h6>
                <span class="d-block">{{ $itr->comments }}</span>
            </li>
            @endif

            @if(in_array($itr->status, [2, 3]))
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fs-6">Completed Date </h6>
                <span class="">{{ $itr->completed_date->format('d F, Y') }}</span>
            </li>
            @endif

            @if(in_array($itr->status, [2]))
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fs-6">ITR Files </h6>
                <span class="">
                    @if($itr->itr_submit_file_1)
                    <a href="{{ asset('storage/'.$itr->itr_submit_file_1) }}" download
                        class="btn btn-outline-secondary"><i class="fa fa-download"></i></a>
                    @endif

                    @if($itr->itr_submit_file_2)
                    <a href="{{ asset('storage/'.$itr->itr_submit_file_2) }}" download
                        class="btn btn-outline-secondary"><i class="fa fa-download"></i></a>
                    @endif

                    @if($itr->itr_submit_file_3)
                    <a href="{{ asset('storage/'.$itr->itr_submit_file_3) }}" download
                        class="btn btn-outline-secondary"><i class="fa fa-download"></i></a>
                    @endif
                </span>
            </li>
            @endif
        </ul>

        @if($errors->any())
        <div class="card mb-3">
            <div class="card-header bg-light-danger border-danger">
                <h6 class="text-danger">Errors</h6>
            </div>
            <div class="card-body">
                <ol>
                    {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}
                </ol>
            </div>
        </div>
        @endif

        <div class="card accordion-icons mb-3">
            <div class="card-header custom-accordion" id="from_2">
                <section class="mb-0 mt-0">
                    <div role="menu" class="collapsed d-flex align-items-center">
                        <div class="accordion-icon-custom text-secondary">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <h6 class="fw-bold text-secondary">Personal Information</h6>
                    </div>
                </section>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Name</h6>
                        <span class="">{{ trim($itr->first_name.' '.$itr->middle_name.' '.$itr->last_name) }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Father's Name</h6>
                        <span class="">{{ trim($itr->father_first_name.' '.$itr->father_middle_name.'
                            '.$itr->father_last_name) }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Date of birth</h6>
                        <span class="">{{ $itr->date_of_birth->format('d F, Y') }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Email</h6>
                        <span class="">{{ $itr->email }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Phone</h6>
                        <span class="">{{ $itr->phone }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Phone (Alternative) </h6>
                        <span class="">{{ $itr->phone_2 }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">ITR Password </h6>
                        <span class="">{{ $itr->itr_password }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Gender</h6>
                        <span class="">{{ config('constant.gender_list.'.$itr->gender) }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">PanCard Details</h6>
                        <span class="d-flex align-items-center gap-2">
                            {{ $itr->pancard_number }}
                            <a href="{{ asset('storage/'.$itr->pancard_file) }}" target="_blank"
                                rel="noopener noreferrer">
                                <i class="fa fa-eye text-secondary"></i>
                            </a>
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Aadhaar Details</h6>
                        <span class="d-flex align-items-center gap-2">
                            {{ $itr->adhaar_number }}
                            <a href="{{ asset('storage/'.$itr->adhaar_file)  }}" target="_blank"
                                rel="noopener noreferrer">
                                <i class="fa fa-eye text-secondary"></i>
                            </a>
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Bank Name</h6>
                        <span class="">{{ $itr->bank_name }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Bank IFSC code</h6>
                        <span class="">{{ $itr->bank_ifsc }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Bank Account Number</h6>
                        <span class="">{{ $itr->bank_account_no }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Bank Account Type</h6>
                        <span class="">
                            {{ config('constant.bank_account_type.'.$itr->account_type) }} + {{
                            config('constant.bank_account_holder_type.'.$itr->bank_account_type) }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Residential Address</h6>
                        <span class="">
                            {{ trim($itr->flat_number.', '.$itr->address.', '.$itr->city.', '.$itr->state.',
                            '.$itr->pincode.', '.$itr->country) }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card accordion-icons mb-3">
            <div class="card-header custom-accordion" id="from_2">
                <section class="mb-0 mt-0">
                    <div role="menu" class="collapsed d-flex align-items-center text-secondary">
                        <div class="accordion-icon-custom text-secondary">
                            <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                        </div>
                        <h6 class="fw-bold text-secondary">Income Sources</h6>
                    </div>
                </section>
            </div>

            <div class="card-body">
                @if($itr->is_salary_income)
                <ul class="list-group mb-2">
                    <li class="list-group-item list-group-item-action text-white bg-secondary">
                        <b> Salary Income</b>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Employer Name</h6>
                        <span class="">{{ $itr->employer_name }}</span>
                    </li>

                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Employer Address</h6>
                        <span class=""> {{ trim($itr->employer_flat_number.', '.$itr->employer_address.',
                            '.$itr->employer_city.', '.$itr->employer_state.',
                            '.$itr->employer_pincode) }}</span>
                    </li>

                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Employer Category</h6>
                        <span class="">{{ config('constant.employeer_types.'. $itr->employer_type) }}</span>
                    </li>

                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Employer TAN</h6>
                        <span class="">{{ $itr->employer_tan }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Basic Salary</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->salary }} </span>
                    </li>

                    @if($itr->dearness_allowances > 0)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Dearness Allowances</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->dearness_allowances }} </span>
                    </li>
                    @endif

                    @if($itr->bonus_commission > 0)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Bonus / Commission</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->bonus_commission }} </span>
                    </li>
                    @endif

                    @if($itr->other_amount > 0)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">{{ $itr->other_amount_head }}</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->other_amount }} </span>
                    </li>
                    @endif

                    @if($itr->form_16_file)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Form 16 File</h6>
                        <a href="{{ asset('storage/'.$itr->form_16_file) }}" class="text-secondry" download>
                            Download
                            <i class="fa fa-download"></i>
                        </a>
                    </li>
                    @endif
                </ul>
                @endif

                @if($itr->is_house_income)
                <ul class="list-group mb-2">
                    <li class="list-group-item list-group-item-action text-white bg-secondary">
                        <b> Income From House Property</b>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">House Type </h6>
                        <span class="">
                            {{ config('constant.rented_house_type.'. $itr->income_house_type) }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">House Address </h6>
                        <span class="">
                            {{ trim($itr->income_house_flat_number.', '.$itr->income_house_address.',
                            '.$itr->income_house_city.', '.$itr->income_house_state.',
                            '.$itr->income_house_pincode) }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">House Rent Received</h6>
                        <span class="">
                            <i class="fa fa-inr"></i> {{ $itr->income_house_rent_received }}
                        </span>
                    </li>

                    @if($itr->interest_paid_on_home_loan)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Interest Paid on Home Loan</h6>
                        <span class="">
                            <i class="fa fa-inr"></i> {{ $itr->interest_paid_on_home_loan }}
                        </span>
                    </li>
                    @endif

                    @if($itr->principal_paid_on_home_loan)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Principal Paid on Home Loan</h6>
                        <span class="">
                            <i class="fa fa-inr"></i> {{ $itr->principal_paid_on_home_loan }}
                        </span>
                    </li>
                    @endif

                    @if($itr->rent_agreement)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Rent Agreement</h6>
                        <a href="{{ asset('storage/'.$itr->rent_agreement) }}" download="">
                            Download <i class="fa fa-download"></i>
                        </a>
                    </li>
                    @endif
                </ul>
                @endif

                @if($itr->is_business_income)
                <ul class="list-group mb-2">
                    <li class="list-group-item list-group-item-action text-white bg-secondary">
                        <b> Business Income</b>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Business Name </h6>
                        <span class="">{{ $itr->business_name }} </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Business Type</h6>
                        <span class="">{{ config('constant.business_type_list.'.$itr->business_type ) }} </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Turnover</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->turnover }} </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Net Profit</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->net_profit }} </span>
                    </li>
                    <li class="list-group-item ">
                        <h6 class="mb-0 fs-6">Description</h6>
                        <p class=""> {{ $itr->description }} </p>
                    </li>

                    @if($itr->partners_own_capital)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Partners Own Capital</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->partners_own_capital }} </span>
                    </li>
                    @endif

                    @if($itr->liabilities_secured_loans)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Liabilities : Secured Loans</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->liabilities_secured_loans }} </span>
                    </li>
                    @endif

                    @if($itr->liabilities_unsecured_loans)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Liabilities : Unsecured Loans</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->liabilities_unsecured_loans }} </span>
                    </li>
                    @endif

                    @if($itr->liabilities_advances)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Liabilities : Advances</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->liabilities_advances }} </span>
                    </li>
                    @endif

                    @if($itr->liabilities_sundry_creditors)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Liabilities : Sundry Creditors</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->liabilities_sundry_creditors }} </span>
                    </li>
                    @endif

                    @if($itr->liabilities_other_liabilities)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Liabilities : Other Liabilities</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->liabilities_other_liabilities }} </span>
                    </li>
                    @endif

                    @if($itr->assets_fixed_assets)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Assets : Fixed Assets</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->assets_fixed_assets }} </span>
                    </li>
                    @endif

                    @if($itr->assets_inventories)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Assets : Inventories</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->assets_inventories }} </span>
                    </li>
                    @endif
                    @if($itr->assets_sundry_debtors)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Assets : Sundry Debtors</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->assets_sundry_debtors }} </span>
                    </li>
                    @endif

                    @if($itr->assets_balance_with_banks)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Assets : Balance with banks</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->assets_balance_with_banks }} </span>
                    </li>
                    @endif

                    @if($itr->assets_cash_in_hand)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Assets : Cash in Hand</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->assets_cash_in_hand }} </span>
                    </li>
                    @endif

                    @if($itr->assets_loans_and_advances)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Assets : Loans & Advances</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->assets_loans_and_advances }} </span>
                    </li>
                    @endif

                    @if($itr->assets_other_assets)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> Assets : Other Assets</h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->assets_other_assets }} </span>
                    </li>
                    @endif
                </ul>
                @endif


                @if($itr->is_capital_gain_income)
                <ul class="list-group mb-2">
                    <li class="list-group-item list-group-item-action text-white bg-secondary">
                        <b> Capital Gain Income</b>
                    </li>
                    <li class="list-group-item p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0 rounded-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Type</th>
                                        <th scope="col">Date of Purchase</th>
                                        <th scope="col">Purchase Amount</th>
                                        <th scope="col">Date of Sale</th>
                                        <th scope="col">Sale Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($itr->capital_gains_type_1 > 0)
                                    <tr>
                                        <td class="fw-bold">
                                            {{
                                            config('constant.capital_gain_asset_type.'.$itr->capital_gains_type_1)
                                            }}
                                        </td>
                                        <td>{{ $itr->capital_gains_purchase_date_1 }}</td>
                                        <td class="text-end">
                                            <i class="fa fa-inr"></i>{{ $itr->capital_gains_purchase_amount_1 }}
                                        </td>
                                        <td>{{ $itr->capital_gains_sale_date_1 }}</td>
                                        <td class="text-end" class="text-end">
                                            <i class="fa fa-inr"></i>{{ $itr->capital_gains_sale_amount_1 }}
                                        </td>
                                    </tr>
                                    @endif

                                    @if($itr->capital_gains_type_2 > 0)
                                    <tr>
                                        <td class="fw-bold">
                                            {{
                                            config('constant.capital_gain_asset_type.'.$itr->capital_gains_type_2)
                                            }}
                                        </td>
                                        <td>{{ $itr->capital_gains_purchase_date_2 }}</td>
                                        <td class="text-end">
                                            <i class="fa fa-inr"></i>{{ $itr->capital_gains_purchase_amount_2 }}
                                        </td>
                                        <td>{{ $itr->capital_gains_sale_date_2 }}</td>
                                        <td class="text-end">
                                            <i class="fa fa-inr"></i>{{ $itr->capital_gains_sale_amount_2 }}
                                        </td>
                                    </tr>
                                    @endif

                                    @if($itr->capital_gains_type_3 > 0)
                                    <tr>
                                        <td class="fw-bold">
                                            {{
                                            config('constant.capital_gain_asset_type.'.$itr->capital_gains_type_3)
                                            }}
                                        </td>
                                        <td>{{ $itr->capital_gains_purchase_date_3 }}</td>
                                        <td class="text-end">
                                            <i class="fa fa-inr"></i>{{ $itr->capital_gains_purchase_amount_3 }}
                                        </td>
                                        <td>{{ $itr->capital_gains_sale_date_3 }}</td>
                                        <td class="text-end">
                                            <i class="fa fa-inr"></i>{{ $itr->capital_gains_sale_amount_3 }}
                                        </td>
                                    </tr>
                                    @endif

                                    @if($itr->capital_gains_type_4 > 0)
                                    <tr>
                                        <td class="fw-bold">
                                            {{
                                            config('constant.capital_gain_asset_type.'.$itr->capital_gains_type_4)
                                            }}
                                        </td>
                                        <td>{{ $itr->capital_gains_purchase_date_4 }}</td>
                                        <td class="text-end">
                                            <i class="fa fa-inr"></i>{{ $itr->capital_gains_purchase_amount_4 }}
                                        </td>
                                        <td>{{ $itr->capital_gains_sale_date_4 }}</td>
                                        <td class="text-end">
                                            <i class="fa fa-inr"></i>{{ $itr->capital_gains_sale_amount_4 }}
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    </li>
                    @if($itr->investment_sale_amount_in_house)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Investment Sale Amount in house </h6>
                        <span class=""><i class="fa fa-inr"></i> {{ $itr->investment_sale_amount_in_house }} </span>
                    </li>
                    @endif

                    @if($itr->investment_sale_amount_in_securities)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Investment Sale amount in securities </h6>
                        <span class=""> <i class="fa fa-inr"></i> {{ $itr->investment_sale_amount_in_securities }}
                        </span>
                    </li>
                    @endif

                    @if($itr->investment_sale_amount_in_capital_gain_bank_a_c)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Investment Sale amount in Capital gain Bank Account</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->investment_sale_amount_in_capital_gain_bank_a_c }}
                        </span>
                    </li>
                    @endif
                </ul>
                @endif

                @if($itr->is_other_income)
                <ul class="list-group mb-2">
                    <li class="list-group-item list-group-item-action text-white bg-secondary">
                        <b> Other Income Sources</b>
                    </li>
                    @if($itr->commission)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Commission</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->commission }}
                        </span>
                    </li>
                    @endif

                    @if($itr->brokerage)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Brokerage</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->brokerage }}
                        </span>
                    </li>
                    @endif

                    @if($itr->interest_from_saving_bank)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Interest From Saving Bank</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->interest_from_saving_bank }}
                        </span>
                    </li>
                    @endif

                    @if($itr->interest_from_fixed_deposit)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Interest From Fixed Deposit</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->interest_from_fixed_deposit }}
                        </span>
                    </li>
                    @endif

                    @if($itr->dividend)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Dividend</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->dividend }}
                        </span>
                    </li>
                    @endif

                    @if($itr->family_pension)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Family Pension</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->family_pension }}
                        </span>
                    </li>
                    @endif

                    @if($itr->other_rent)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Other Rent</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->other_rent }}
                        </span>
                    </li>
                    @endif

                    @if($itr->other_interest)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Other Interest</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->other_interest }}
                        </span>
                    </li>
                    @endif

                    @if($itr->mutual_fund)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Mutual Fund</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->mutual_fund }}
                        </span>
                    </li>
                    @endif

                    @if($itr->uti_income)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">UTI Income</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->uti_income }}
                        </span>
                    </li>
                    @endif

                    @if($itr->agricultural_gross_income)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Agricultural Gross Income</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->agricultural_gross_income }}
                        </span>
                    </li>
                    @endif

                    @if($itr->agricultural_expenses)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">Agricultural Expenses</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr->agricultural_expenses }}
                        </span>
                    </li>
                    @endif
                </ul>
                @endif
            </div>
        </div>

        <div class="card accordion-icons mb-3">
            <div class="card-header custom-accordion" id="from_2">
                <section class="mb-0 mt-0">
                    <div role="menu" class="collapsed d-flex align-items-center text-secondary">
                        <div class="accordion-icon-custom text-secondary">
                            <i class="fa-solid fa-piggy-bank fs-5"></i>
                        </div>
                        <h6 class="fw-bold text-secondary">Tax Savings (Deductions) </h6>
                    </div>
                </section>
            </div>

            <div class="card-body">
                <ul class="list-group mb-2">
                    @if($itr['80c_life_insurance_premium_paid'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80C Life Insurance Premium Paid</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80c_life_insurance_premium_paid'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80c_gpf_ppf'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80C GPF / PPF</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80c_gpf_ppf'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80c_ulip'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80C ULIP</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80c_ulip'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80c_provident_fund'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80C Provident Fund</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80c_provident_fund'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80c_mutual_fund'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80C Mutual Fund</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80c_mutual_fund'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80c_principal_on_home_loan'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80C Principal on Home Loan</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80c_principal_on_home_loan'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80c_tuition_fees_upto_2_children'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80C Tuition Fees Upto 2 Children</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80c_tuition_fees_upto_2_children'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80c_fixed_deposit'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80C Fixed Deposit</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80c_fixed_deposit'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80c_tax_saving_bonds'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80C Tax Saving Bonds</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80c_tax_saving_bonds'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80d_checkup_fee_for_self'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80D Checkup Fee For Self</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80d_checkup_fee_for_self'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80d_checkup_fee_for_parents'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80D Checkup fee for Parents</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80d_checkup_fee_for_parents'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80d_medical_expenditures_for_self'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80D Medical Expenditures for Self</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80d_medical_expenditures_for_self'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80d_medical_expenditures_for_parents'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80D Medical Expenditures for Parents</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80d_medical_expenditures_for_parents'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80tta_interest_earned_saving_banks'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80TTA Interest Earned Saving Banks</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80tta_interest_earned_saving_banks'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80ccc_pension_annuity_fund'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80CCC Pension Annuity Fund</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80ccc_pension_annuity_fund'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80ccd_own_contribution_nps'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80CCD Own Contribution NPS</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80ccd_own_contribution_nps'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80ccd_employer_contribution_nps'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80CCD Employer Contribution NPS</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80ccd_employer_contribution_nps'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80u_disablity'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80U Disablity</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80u_disablity'] }} %
                        </span>
                    </li>
                    @endif

                    @if($itr['80ee_interest_on_home_loan'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80EE Interest on Home Loan</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80ee_interest_on_home_loan'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80eeb_electric_vehicle_loan'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80EEB Electric Vehicle Loan</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80eeb_electric_vehicle_loan'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['tds_certificates_form_26as'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> TDS Certificates form 26AS</h6>
                        <a href="{{ asset('storage/'.$itr['tds_certificates_form_26as']) }}" download=""> Download
                            <i class="fa fa-download"></i> </a>
                    </li>
                    @endif
                </ul>


                @if($itr->is_make_donation)
                <ul class="list-group mb-2">
                    <li class="list-group-item list-group-item-action text-white bg-secondary">
                        <b> Other Sources</b>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">80G Donee Name </h6>
                        <span class="">{{ $itr['80g_donee_name'] }}</span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">80G Donee Address</h6>
                        <span class="">
                            {{ trim($itr['80g_donee_address'].', '.$itr['80g_donee_city'].',
                            '.$itr['80g_donee_state'].', '.$itr['80g_donee_pincode'].',
                            '.$itr['80g_donee_country']) }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6">80G Donee PanCard</h6>
                        <span class=""> {{ $itr['80g_donee_pancard'] }}</span>
                    </li>

                    @if($itr['80g_donation_amount_cash'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80G Donation Amount (Cash)</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80g_donation_amount_cash'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80g_donation_amount_no_cash'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80G Donation Amount (No Cash)</h6>
                        <span class=""> <i class="fa fa-inr"></i>
                            {{ $itr['80g_donation_amount_no_cash'] }}
                        </span>
                    </li>
                    @endif

                    @if($itr['80g_donee_qualifying_percentage'])
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
                        <h6 class="mb-0 fs-6"> 80G Donee Qualifying Percentage</h6>
                        <span class="">
                            {{ $itr['80g_donee_qualifying_percentage'] }} %
                        </span>
                    </li>
                    @endif

                </ul>
                @endif
            </div>
        </div>

        @if($itr->status == 0 || $itr->status == 4)
        <div class="card">
            <div class="card-body">
                <form action="{{ request()->url() }}" id="stepFour" method="post" enctype="multipart/form-data">
                    @csrf
                    <fieldset @disabled($itr->status > 0 && $itr->status != 4)>
                        <div class="form-check ps-4">
                            <input class="form-check-input" type="checkbox" name="declaration" id="declaration"
                                required>
                            <label class="form-check-label mb-0" for="declaration">
                                <p>I hereby deciare that information given above are true to the best of my
                                    knowledge.
                                    for any information, that may be required to be verified, proof/evidence shall
                                    be
                                    produced immediately before the concerned authority.</p>
                                <p>
                                    मैं एतद्द्वारा घोषणा करता हूं कि ऊपर दी गई जानकारी मेरी सर्वोत्तम जानकारी के
                                    अनुसार
                                    सत्य है। किसी भी जानकारी के लिए, जिसे सत्यापित करने की आवश्यकता हो सकती है,
                                    संबंधित
                                    प्राधिकारी के समक्ष तुरंत सबूत/साक्ष्य प्रस्तुत किया जाएगा।
                                </p>
                            </label>
                        </div>

                        <div class="mt-3 d-flex justify-content-start">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        @endif

    </div>
    @endsection


    @section('js')
    <script>
        $(function () {
            $("#stepFour").validate({
                messages: {
                    declaration: "Please check this declaration."
                }
            });
        })
    </script>
    @endsection