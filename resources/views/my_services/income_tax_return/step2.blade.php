@extends('my_services.income_tax_return.index')

@php($capital_gain_type = config('constant.capital_gain_asset_type'))

@section('sub_section')
<form action="{{ request()->url() }}" id="stepTwo" method="post" enctype="multipart/form-data">
    @csrf
    <fieldset @disabled($itr->status > 0 && $itr->status != 4 )>
        <div class="row">
            <div class="col-12 accordion">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 border-end">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="fs-6 text-dark"> Q1. Income from <b>Salary/Pension ?</b> </div>
                                    <div
                                        class="switch form-switch-custom form-switch-secondary d-flex align-items-start">
                                        <input class="switch-input" name="is_salary_income" value="1"
                                            id="salaryBlockCheck" @checked(old('is_salary_income',
                                            $itr->is_salary_income)==1) type="checkbox"
                                        role="switch" />
                                        <i data-bs-container="body" data-bs-trigger="hover"
                                            data-bs-content="Did you earn any income from Salary or Pension? You can simply upload your Form 16 and we shall prepare your ITR automatically or you can fill the details manually like deductions, TDS etc."
                                            class="fa fa-info mx-2 bg-gray text-dark bs-popover rounded-circle p-info-icon"></i>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <div class="fs-6 text-dark"> Q2. Income from <b>House Property ?</b></div>
                                    <div
                                        class="switch form-switch-custom form-switch-secondary d-flex align-items-start">
                                        <input class="switch-input" name="is_house_income" value="1"
                                            id="houseBlockCheck" @checked(old('is_house_income',
                                            $itr->is_house_income)==1) type="checkbox"
                                        role="switch" />
                                        <i data-bs-container="body" data-bs-trigger="hover"
                                            data-bs-content="Do you own a house & Earn Rent? Fill in details of your House Property incomes here. Further, do you have a Home Loan? You can claim deduction on interest of your home loan."
                                            class="fa fa-info mx-2 bg-gray text-dark bs-popover rounded-circle p-info-icon"></i>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <div class="fs-6 text-dark"> Q3. Income from <b>Business/Profession ?</b> </div>
                                    <div
                                        class="switch form-switch-custom form-switch-secondary d-flex align-items-start">
                                        <input class="switch-input" name="is_business_income" value="1"
                                            @checked(old('is_business_income', $itr->is_business_income)==1)
                                        id="businessBlockCheck" type="checkbox"
                                        role="switch" />
                                        <i data-bs-container="body" data-bs-html="true" data-bs-trigger="hover"
                                            data-bs-content="Select if: <br>
                                                (a) You own a small business or <br>
                                                (b) You are a contractor or <br>
                                                (c) You earn as Freelancer."
                                            class="fa fa-info mx-2 bg-gray text-dark bs-popover rounded-circle p-info-icon"></i>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <div class="fs-6 text-dark"> Q4. Income from <b>Capital Gains ?</b> </div>
                                    <div
                                        class="switch form-switch-custom form-switch-secondary d-flex align-items-start">
                                        <input class="switch-input" name="is_capital_gain_income" value="1"
                                            @checked(old('is_capital_gain_income', $itr->is_capital_gain_income)==1)
                                        id="capitalGainBlockCheck"
                                        type="checkbox" role="switch" />
                                        <i data-bs-container="body" data-bs-trigger="hover"
                                            data-bs-content="Have you sold any shares or property in the last year? Then you can go for capital gains."
                                            class="fa fa-info mx-2 bg-gray text-dark bs-popover rounded-circle p-info-icon"></i>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <div class="fs-6 text-dark"> Q5. Income from <b>Other Sources ?</b> </div>
                                    <div
                                        class="switch form-switch-custom form-switch-secondary d-flex align-items-start">
                                        <input class="switch-input" name="is_other_income" value="1"
                                            id="otherBlockCheck" @checked(old('is_other_income',
                                            $itr->is_other_income)==1) type="checkbox"
                                        role="switch" />
                                        <i data-bs-container="body" data-bs-trigger="hover"
                                            data-bs-content="Selects this if you have any of these incomes - you have received gifts or interest on Fixed deposits or interest from PPF, Dividend Income or income from Mutual Funds etc."
                                            class="fa fa-info mx-2 bg-gray text-dark bs-popover rounded-circle p-info-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="mb-2">
                                    <label for="assessmentYear" class="form-label">Assessment Year</label>
                                    <select class="form-select" name="assessment_year" id="assessmentYear">
                                        @foreach($assessment_year_list as $value)
                                        <option value="{{ $value }}" @selected(old('assessment_year', $itr->
                                            assessment_year )==$value)>
                                            {{ $value }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('assessment_year'))
                                    <span class="invalid-feedback">{{ $errors->first('assessment_year')
                                        }}</span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <label for="assessmentYear" class="form-label">Tax Regime</label>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tax_regime"
                                                id="taxRegimeNew" value="N" @checked(old('tax_regime', $itr->tax_regime
                                            )== 'N')>
                                            <label class="form-check-label" for="taxRegimeNew">
                                                New Regime
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tax_regime"
                                                id="taxRegimeOld" value="O" @checked(old('tax_regime', $itr->tax_regime
                                            )== 'O')>
                                            <label class="form-check-label" for="taxRegimeOld">
                                                Old Regime
                                            </label>
                                        </div>
                                    </div>
                                    @if($errors->has('tax_regime'))
                                    <span class="invalid-feedback">
                                        {{ $errors->first('tax_regime') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div id="mainAccordion" class="accordion-icons accordion">
                    <div class="card" style="display: none;" id="salaryBlock">
                        <div class="card-header custom-accordion" id="from_1">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#salaryInfo" aria-expanded="false" aria-controls="salaryInfo">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-hand-holding-dollar fs-6"></i>
                                    </div>
                                    Salary Income
                                    <div class="icons px-3 lh-lg">
                                        <i class="fa fa-chevron-down"></i>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div id="salaryInfo" class="collapse" aria-labelledby="from_1" data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-dark fw-bold">Employer & TDS Details</h6>
                                        <p> If you are working as employee, You can refer to your Form 16 for the data
                                        </p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Employer Name
                                            </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" minlength="5" maxlength="100"
                                                    placeholder="Employer Name"
                                                    value="{{ old('employer_name', $itr->employer_name) }}"
                                                    name="employer_name" />
                                                @if($errors->has('employer_name'))
                                                <span class="invalid-feedback">{{ $errors->first('employer_name')
                                                    }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Employer Address
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="row">
                                                    <div class="col-5 mb-2">
                                                        <input type="text" class="form-control"
                                                            value="{{ old('employer_flat_number', $itr->employer_flat_number) }}"
                                                            name="employer_flat_number" minlength="2" maxlength="100"
                                                            placeholder="Flat Number">
                                                        @if($errors->has('employer_flat_number'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('employer_flat_number') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="col-7 mb-2">
                                                        <input type="text" class="form-control"
                                                            value="{{ old('employer_address', $itr->employer_address) }}"
                                                            name="employer_address" minlength="5" maxlength="100"
                                                            placeholder="Building, Apartment or Street">
                                                        @if($errors->has('employer_address'))
                                                        <span class="invalid-feedback">{{
                                                            $errors->first('employer_address')
                                                            }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-5 mb-2">
                                                        <select id="state" name="employer_state"
                                                            onchange="getCity(this.value, '#city')" class="form-select">
                                                            <option value="">Select State</option>
                                                            @foreach ($states as $state)
                                                            <option value="{{ $state['id'] }}"
                                                                @selected(old('employer_state', $itr->employer_state
                                                                )==$state['id'])>
                                                                {{ $state['name'] }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('employer_state'))
                                                        <span class="invalid-feedback">{{
                                                            $errors->first('employer_state')
                                                            }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <select id="city" name="employer_city" class="form-select">
                                                            <option value="">Select City</option>
                                                        </select>
                                                        @if($errors->has('employer_city'))
                                                        <span class="invalid-feedback">{{
                                                            $errors->first('employer_city')
                                                            }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3 mb-2">
                                                        <input type="text" class="form-control" name="employer_pincode"
                                                            value="{{ old('employer_pincode', $itr->employer_pincode) }}"
                                                            maxlength="6" minlength="6" placeholder="Zip Code">
                                                        @if($errors->has('employer_pincode'))
                                                        <span class="invalid-feedback">{{
                                                            $errors->first('employer_pincode')
                                                            }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Employer Category
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <select class="form-select" name="employer_type" id="employer_type">
                                                        <option value="">Choose Option</option>
                                                        @foreach(config('constant.employeer_types', []) as $key =>
                                                        $value)
                                                        <option value="{{ $key }}" @selected(old('employer_type', $itr->
                                                            employer_type )==$key)>
                                                            {{ $value }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @if($errors->has('employer_type'))
                                                    <span class="invalid-feedback">{{ $errors->first('employer_type')
                                                        }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Employer TAN
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        value="{{ old('employer_tan', $itr->employer_tan) }}"
                                                        name="employer_tan" maxlength="15" placeholder="Employer TAN" />
                                                </div>
                                                <small class="text-muted">In your Form 16, find this under Part-A -
                                                    TAN of Deductor</small>
                                                @if($errors->has('employer_tan'))
                                                <span class="invalid-feedback">{{ $errors->first('employer_tan')
                                                    }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <hr class="mt-1">
                                        <h6 class="text-dark fw-bold">Salary Details</h6>
                                        <p> You can add salary income from multiple jobs </p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Basic Salary
                                            </label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number decimal"
                                                        placeholder="Basic Salary"
                                                        value="{{ old('salary', $itr->salary) }}" name="salary"
                                                        step="0.01" min="0.00" max="10000000" />
                                                </div>
                                                @if($errors->has('salary'))
                                                <span class="invalid-feedback">{{ $errors->first('salary') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Dearness Allowances
                                            </label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        placeholder="Dearness Allowances"
                                                        value="{{ old('dearness_allowances', $itr->dearness_allowances) }}"
                                                        name="dearness_allowances" step="0.01" min="0.00"
                                                        max="10000000" />
                                                </div>
                                                @if($errors->has('dearness_allowances'))
                                                <span class="invalid-feedback">{{ $errors->first('dearness_allowances')
                                                    }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Bonus / Commission
                                            </label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        placeholder="Bonus / Commission"
                                                        value="{{ old('bonus_commission',  $itr->bonus_commission) }}"
                                                        name="bonus_commission" step="0.01" min="0.00" max="10000000" />
                                                </div>
                                                @if($errors->has('bonus_commission'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('bonus_commission') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Other
                                            </label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" placeholder="Other Amount Head"
                                                    name="other_amount_head"
                                                    value="{{ old('other_amount_head',  $itr->other_amount_head) }}"
                                                    minlength="5" maxlength="100" />
                                                @if($errors->has('other_amount_head'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('other_amount_head') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        placeholder="Other Amount"
                                                        value="{{ old('other_amount',  $itr->other_amount) }}"
                                                        name="other_amount" min="0.00" step="0.01" max="10000000">
                                                </div>
                                                @if($errors->has('other_amount'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('other_amount') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Form 16
                                            </label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-file-pdf"></i>
                                                    </span>
                                                    <input type="file" class="form-control" name="form_16_file" />
                                                    @if($itr->form_16_file)
                                                    <a href="{{ asset('storage/'. $itr->form_16_file) }}"
                                                        class="input-group-text" target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                                <spam class="text-muted">Choose your Form-16 PDF to upload</spam>
                                                @if($errors->has('form_16_file'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('form_16_file') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="display: none;" id="houseBlock">
                        <div class="card-header custom-accordion" id="from_2">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#houseInfo" aria-expanded="false" aria-controls="houseInfo">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-house-day fs-6"></i>
                                    </div>
                                    Income From House Property
                                    <div class="icons px-3 lh-lg">
                                        <i class="fa fa-chevron-down"></i>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div id="houseInfo" class="collapse" aria-labelledby="from_2" data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-dark">House Details</h6>
                                        <p> Please add the details if you earned rent from your house property or paid
                                            interest on housing loan </p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                House Type
                                            </label>
                                            <div class="col-sm-5">
                                                <select name="income_house_type" class="form-select">
                                                    <option value="">Choose House Type</option>
                                                    @foreach(config('constant.rented_house_type', []) as $key => $value)
                                                    <option value="{{ $key }}" @selected(old('income_house_type',$itr->
                                                        income_house_type)==$key)>
                                                        {{ $value }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('income_house_type'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('income_house_type') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                House Address
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="row">
                                                    <div class="col-5 mb-2">
                                                        <input type="text" class="form-control"
                                                            placeholder="Flat Number"
                                                            value="{{ old('income_house_flat_number',$itr->income_house_flat_number) }}"
                                                            name="income_house_flat_number" minlength="2"
                                                            maxlength="100">
                                                        @if($errors->has('income_house_flat_number'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('income_house_flat_number') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="col-7 mb-2">
                                                        <input type="text" class="form-control"
                                                            value="{{ old('income_house_address',$itr->income_house_address) }}"
                                                            name="income_house_address" minlength="5" maxlength="100"
                                                            placeholder="Building, Apartment or Street">
                                                        @if($errors->has('income_house_address'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('income_house_address') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-5 mb-2">
                                                        <select id="income_house_state" name="income_house_state"
                                                            onchange="getCity(this.value, '#income_house_city')"
                                                            class="form-select">
                                                            <option value="">Select State</option>
                                                            @foreach ($states as $state)
                                                            <option value="{{ $state['id'] }}"
                                                                @selected(old('income_house_state',$itr->
                                                                income_house_state)==$state['id'])>
                                                                {{ $state['name'] }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('income_house_state'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('income_house_state') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <select id="income_house_city" name="income_house_city"
                                                            class="form-select">
                                                            <option value="">Select City</option>
                                                        </select>
                                                        @if($errors->has('income_house_city'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('income_house_city') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3 mb-2">
                                                        <input type="text" class="form-control"
                                                            value="{{ old('income_house_pincode',$itr->income_house_pincode) }}"
                                                            name="income_house_pincode" minlength="6" maxlength="6"
                                                            placeholder="Zip Code">
                                                        @if($errors->has('income_house_pincode'))
                                                        <span class="invalid-feedback">
                                                            {{ $errors->first('income_house_pincode') }}
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
                                                Rent Received
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        placeholder="Rent Received"
                                                        value="{{ old('income_house_rent_received',$itr->income_house_rent_received) }}"
                                                        name="income_house_rent_received" step="0.01" max="100000000" />
                                                </div>
                                                @if($errors->has('income_house_rent_received'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('income_house_rent_received') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Rent Agreement
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-file-pdf"></i>
                                                    </span>
                                                    <input type="file" class="form-control" name="rent_agreement" />
                                                    @if($itr->rent_agreement)
                                                    <a href="{{ asset('storage/'. $itr->rent_agreement) }}"
                                                        class="input-group-text" target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                                @if($errors->has('rent_agreement'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('rent_agreement') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Interest Paid on Housing loan
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        placeholder="Interest Paid on Housing loan"
                                                        value="{{ old('interest_paid_on_home_loan',$itr->interest_paid_on_home_loan) }}"
                                                        name="interest_paid_on_home_loan" step="0.01" max="100000000" />
                                                    <small class="text-muted">
                                                        If you have a housing loan against a house you live in then you
                                                        can claim a tax deduction of upto Rs. 2,00,000.
                                                    </small>
                                                    @if($errors->has('interest_paid_on_home_loan'))
                                                    <span class="invalid-feedback">
                                                        {{ $errors->first('interest_paid_on_home_loan') }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Principal Paid on Housing loan
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        placeholder="Principal Paid on Housing loan"
                                                        value="{{ old('principal_paid_on_home_loan',$itr->principal_paid_on_home_loan) }}"
                                                        name="principal_paid_on_home_loan" step="0.01"
                                                        max="100000000" />
                                                </div>
                                                @if($errors->has('principal_paid_on_home_loan'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('principal_paid_on_home_loan') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="display: none;" id="businessBlock">
                        <div class="card-header custom-accordion" id="from_2">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#businessInfo" aria-expanded="false" aria-controls="businessInfo">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-signal-bars fs-6"></i>
                                    </div>
                                    Professional and Business Income
                                    <div class="icons px-3 lh-lg">
                                        <i class="fa fa-chevron-down"></i>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div id="businessInfo" class="collapse" aria-labelledby="from_2"
                            data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-dark">Please Provide details of your businesses</h6>
                                        <p> For Doctors, Lawyers, CAs, Other Professionals, Freelancers, Small & Medium
                                            businesses, Tutors, Influencers etc. </p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Trade / Business Name
                                            </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"
                                                    placeholder="Trade / Business Name"
                                                    value="{{ old('business_name',$itr->business_name) }}"
                                                    name="business_name" minlength="5" maxlength="100" />
                                                @if($errors->has('business_name'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('business_name') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Nature of Business
                                            </label>
                                            <div class="col-sm-5">
                                                <select id="business_type" name="business_type" class="form-select">
                                                    <option value="">Choose Nature of Business</option>
                                                    @foreach(config('constant.business_type_list', []) as $key =>
                                                    $value)
                                                    <option value="{{ $key }}" @selected(old('business_type', $itr->
                                                        business_type )==$key)>
                                                        {{ $value }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('business_type'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('business_type') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Gross Turnover
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        placeholder="Gross Turnover"
                                                        value="{{ old('turnover',$itr->turnover) }}" name="turnover"
                                                        step="0.01" max="100000000" />
                                                </div>
                                                @if($errors->has('turnover'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('turnover') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Net Income / Profit
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        placeholder="Total Income as per your calculation"
                                                        value="{{ old('net_profit',$itr->net_profit) }}"
                                                        name="net_profit" step="0.01" max="100000000" />
                                                </div>
                                                @if($errors->has('net_profit'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('net_profit') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Description
                                            </label>
                                            <div class="col-sm-8">
                                                <textarea rows="2" name="description"
                                                    placeholder="Add Description about your income."
                                                    class="form-control">{{ old('description',$itr->description) }}</textarea>
                                                @if($errors->has('description'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('description') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h6 class="text-dark fw-bold">Add Financial Particulars</h6>
                                        <p> Please add the financial particulars of your Business(es) and Profession(s)
                                        </p>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <p class="fw-bold">Add Details of Liabilities</p>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Secured Loans</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('liabilities_secured_loans',$itr->liabilities_secured_loans) }}"
                                                        name="liabilities_secured_loans" class="form-control rm-number"
                                                        placeholder="Secured Loans">
                                                    @if($errors->has('liabilities_secured_loans'))
                                                    <span class="invalid-feedback">
                                                        {{ $errors->first('liabilities_secured_loans') }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Unsecured Loans</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('liabilities_unsecured_loans',$itr->liabilities_unsecured_loans) }}"
                                                        name="liabilities_unsecured_loans"
                                                        class="form-control rm-number" placeholder="Unsecured Loans">
                                                    @if($errors->has('liabilities_unsecured_loans'))
                                                    <span class="invalid-feedback">
                                                        {{ $errors->first('liabilities_unsecured_loans') }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Advances</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('liabilities_advances',$itr->liabilities_advances) }}"
                                                        name="liabilities_advances" class="form-control rm-number"
                                                        placeholder="Advances">
                                                    @if($errors->has('liabilities_advances'))
                                                    <span class="invalid-feedback">
                                                        {{ $errors->first('liabilities_advances') }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Sundry Creditors</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('liabilities_sundry_creditors',$itr->liabilities_sundry_creditors) }}"
                                                        name="liabilities_sundry_creditors"
                                                        class="form-control rm-number" placeholder="Sundry Creditors">
                                                    @if($errors->has('liabilities_sundry_creditors'))
                                                    <span class="invalid-feedback">
                                                        {{ $errors->first('liabilities_sundry_creditors') }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Partners Own Capital</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('partners_own_capital',$itr->partners_own_capital) }}"
                                                        name="partners_own_capital" class="form-control rm-number"
                                                        placeholder="Partners Own Capital">
                                                    @if($errors->has('partners_own_capital'))
                                                    <span class="invalid-feedback">
                                                        {{ $errors->first('partners_own_capital') }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Other Liabilities</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('liabilities_other_liabilities',$itr->liabilities_other_liabilities) }}"
                                                        name="liabilities_other_liabilities"
                                                        class="form-control rm-number" placeholder="Other Liabilities">
                                                    @if($errors->has('liabilities_other_liabilities'))
                                                    <span class="invalid-feedback">
                                                        {{ $errors->first('liabilities_other_liabilities') }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <!-- <hr class="mt-0"> -->
                                                <p class="fw-bold">Add Details of Assets</p>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Fixed assets</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('assets_fixed_assets',$itr->assets_fixed_assets) }}"
                                                        name="assets_fixed_assets" class="form-control rm-number"
                                                        placeholder="Fixed assets">
                                                </div>
                                                @if($errors->has('assets_fixed_assets'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('assets_fixed_assets') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Inventories</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('assets_inventories',$itr->assets_inventories) }}"
                                                        name="assets_inventories" class="form-control rm-number"
                                                        placeholder="Inventories">
                                                </div>
                                                @if($errors->has('assets_inventories'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('assets_inventories') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Sundry Debtors</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('assets_sundry_debtors',$itr->assets_sundry_debtors) }}"
                                                        name="assets_sundry_debtors" class="form-control rm-number"
                                                        placeholder="Sundry Debtors">
                                                </div>
                                                @if($errors->has('assets_sundry_debtors'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('assets_sundry_debtors') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Balance With Banks</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('assets_balance_with_banks',$itr->assets_balance_with_banks) }}"
                                                        name="assets_balance_with_banks" class="form-control rm-number"
                                                        placeholder="Balance With Banks">
                                                </div>
                                                @if($errors->has('assets_balance_with_banks'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('assets_balance_with_banks') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Cash In Hand</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('assets_cash_in_hand',$itr->assets_cash_in_hand) }}"
                                                        name="assets_cash_in_hand" class="form-control rm-number"
                                                        placeholder="Cash In Hand">
                                                </div>
                                                @if($errors->has('assets_cash_in_hand'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('assets_cash_in_hand') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Loans & Advances</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('assets_loans_and_advances',$itr->assets_loans_and_advances) }}"
                                                        name="assets_loans_and_advances" class="form-control rm-number"
                                                        placeholder="Loans & Advances">
                                                </div>
                                                @if($errors->has('assets_loans_and_advances'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('assets_loans_and_advances') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="" class="mb-1">Other Assets</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1"><i
                                                            class="fa fa-inr"></i></span>
                                                    <input type="number" step="0.01" min="0" max="100000000"
                                                        value="{{ old('assets_other_assets',$itr->assets_other_assets) }}"
                                                        name="assets_other_assets" class="form-control rm-number"
                                                        placeholder="Other Assets">
                                                </div>
                                                @if($errors->has('assets_other_assets'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('assets_other_assets') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="display: none;" id="capitalGainBlock">
                        <div class="card-header custom-accordion" id="from_4">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#capitangainInfo" aria-expanded="false"
                                    aria-controls="capitangainInfo">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-chart-line-up fs-6"></i>
                                    </div>
                                    Capital Gain Income
                                    <div class="icons px-3 lh-lg">
                                        <i class="fa fa-chevron-down"></i>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div id="capitangainInfo" class="collapse" aria-labelledby="from_4"
                            data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-dark">Income From Capital Gains</h6>
                                        <p> Did you sell any asset (Mutual Funds, shares, property, house, land,
                                            building,
                                            etc) between the financial year period. </p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="bg-gray">
                                                        <th scope="col">Select Type</th>
                                                        <th scope="col">Date of Purchase</th>
                                                        <th scope="col">Purchase Amount</th>
                                                        <th scope="col">Date of Sale</th>
                                                        <th scope="col">Sale Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="p-2">
                                                            <select class="form-select form-control-sm"
                                                                name="capital_gains_type_1">
                                                                <option value="0">Choose Option</option>
                                                                @foreach($capital_gain_type as $key => $value)
                                                                <option value="{{ $key }}"
                                                                    @selected(old('capital_gains_type_1',$itr->
                                                                    capital_gains_type_1)==$key)>
                                                                    {{ $value }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            @if($errors->has('capital_gains_type_1'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_type_1') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="date" name="capital_gains_purchase_date_1"
                                                                value="{{ old('capital_gains_purchase_date_1',$itr->capital_gains_purchase_date_1) }}"
                                                                class="form-control form-control-sm">
                                                            @if($errors->has('capital_gains_purchase_date_1'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_purchase_date_1') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text form-control-sm">
                                                                    <i class="fa fa-inr small"></i>
                                                                </span>
                                                                <input type="number"
                                                                    class="form-control rm-number form-control-sm"
                                                                    placeholder="Purchase Amount"
                                                                    value="{{ old('capital_gains_purchase_amount_1',$itr->capital_gains_purchase_amount_1) }}"
                                                                    name="capital_gains_purchase_amount_1" step="0.01"
                                                                    min="0.00" max="100000000" />
                                                            </div>
                                                            @if($errors->has('capital_gains_purchase_amount_1'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_purchase_amount_1') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="date" name="capital_gains_sale_date_1"
                                                                max="{{ date('Y-m-d') }}"
                                                                value="{{ old('capital_gains_sale_date_1',$itr->capital_gains_sale_date_1) }}"
                                                                class="form-control form-control-sm">
                                                            @if($errors->has('capital_gains_sale_date_1'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_sale_date_1') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text form-control-sm">
                                                                    <i class="fa fa-inr small"></i>
                                                                </span>
                                                                <input type="number"
                                                                    class="form-control rm-number form-control-sm"
                                                                    placeholder="Sale Amount"
                                                                    value="{{ old('capital_gains_sale_amount_1',$itr->capital_gains_sale_amount_1) }}"
                                                                    name="capital_gains_sale_amount_1" step="0.01"
                                                                    min="0.00" max="100000000" />
                                                            </div>
                                                            @if($errors->has('capital_gains_sale_amount_1'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_sale_amount_1') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="p-2">
                                                            <select class="form-select form-control-sm"
                                                                name="capital_gains_type_2">
                                                                <option value="0">Choose Option</option>
                                                                @foreach($capital_gain_type as $key => $value)
                                                                <option value="{{ $key }}"
                                                                    @selected(old('capital_gains_type_2',$itr->
                                                                    capital_gains_type_2)==$key)>
                                                                    {{ $value }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            @if($errors->has('capital_gains_type_2'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_type_2') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="date" name="capital_gains_purchase_date_2"
                                                                value="{{ old('capital_gains_purchase_date_2',$itr->capital_gains_purchase_date_2) }}"
                                                                class="form-control form-control-sm">
                                                            @if($errors->has('capital_gains_purchase_date_2'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_purchase_date_2') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text form-control-sm">
                                                                    <i class="fa fa-inr small"></i>
                                                                </span>
                                                                <input type="number"
                                                                    class="form-control rm-number form-control-sm"
                                                                    placeholder="Purchase Amount"
                                                                    value="{{ old('capital_gains_purchase_amount_2',$itr->capital_gains_purchase_amount_2) }}"
                                                                    name="capital_gains_purchase_amount_2" step="0.01"
                                                                    min="0.00" max="100000000" />
                                                            </div>
                                                            @if($errors->has('capital_gains_purchase_amount_2'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_purchase_amount_2') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="date" name="capital_gains_sale_date_2"
                                                                max="{{ date('Y-m-d') }}"
                                                                value="{{ old('capital_gains_sale_date_2',$itr->capital_gains_sale_date_2) }}"
                                                                class="form-control form-control-sm">
                                                            @if($errors->has('capital_gains_sale_date_2'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_sale_date_2') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text form-control-sm">
                                                                    <i class="fa fa-inr small"></i>
                                                                </span>
                                                                <input type="number"
                                                                    class="form-control rm-number form-control-sm"
                                                                    placeholder="Sale Amount"
                                                                    value="{{ old('capital_gains_sale_amount_2',$itr->capital_gains_sale_amount_2) }}"
                                                                    name="capital_gains_sale_amount_2" step="0.01"
                                                                    min="0.00" max="100000000" />
                                                            </div>
                                                            @if($errors->has('capital_gains_sale_amount_2'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_sale_amount_2') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="p-2">
                                                            <select class="form-select form-control-sm"
                                                                name="capital_gains_type_3">
                                                                <option value="0">Choose Option</option>
                                                                @foreach($capital_gain_type as $key => $value)
                                                                <option value="{{ $key }}"
                                                                    @selected(old('capital_gains_type_3',$itr->
                                                                    capital_gains_type_3)==$key)>
                                                                    {{ $value }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            @if($errors->has('capital_gains_type_3'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_type_3') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="date" name="capital_gains_purchase_date_3"
                                                                value="{{ old('capital_gains_purchase_date_3',$itr->capital_gains_purchase_date_3) }}"
                                                                class="form-control form-control-sm">
                                                            @if($errors->has('capital_gains_purchase_date_3'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_purchase_date_3') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text form-control-sm">
                                                                    <i class="fa fa-inr small"></i>
                                                                </span>
                                                                <input type="number"
                                                                    class="form-control rm-number form-control-sm"
                                                                    placeholder="Purchase Amount"
                                                                    value="{{ old('capital_gains_purchase_amount_3',$itr->capital_gains_purchase_amount_3) }}"
                                                                    name="capital_gains_purchase_amount_3" step="0.01"
                                                                    min="0.00" max="100000000" />
                                                            </div>
                                                            @if($errors->has('capital_gains_purchase_amount_3'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_purchase_amount_3') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="date" name="capital_gains_sale_date_3"
                                                                max="{{ date('Y-m-d') }}"
                                                                value="{{ old('capital_gains_sale_date_3',$itr->capital_gains_sale_date_3) }}"
                                                                class="form-control form-control-sm">
                                                            @if($errors->has('capital_gains_sale_date_3'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_sale_date_3') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text form-control-sm">
                                                                    <i class="fa fa-inr small"></i>
                                                                </span>
                                                                <input type="number"
                                                                    class="form-control rm-number form-control-sm"
                                                                    placeholder="Sale Amount"
                                                                    value="{{ old('capital_gains_sale_amount_3',$itr->capital_gains_sale_amount_3) }}"
                                                                    name="capital_gains_sale_amount_3" step="0.01"
                                                                    min="0.00" max="100000000" />
                                                            </div>
                                                            @if($errors->has('capital_gains_sale_amount_3'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_sale_amount_3') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="p-2">
                                                            <select class="form-select form-control-sm"
                                                                name="capital_gains_type_4">
                                                                <option value="0">Choose Option</option>
                                                                @foreach($capital_gain_type as $key => $value)
                                                                <option value="{{ $key }}"
                                                                    @selected(old('capital_gains_type_4',$itr->
                                                                    capital_gains_type_4)==$key)>
                                                                    {{ $value }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            @if($errors->has('capital_gains_type_4'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_type_4') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="date" name="capital_gains_purchase_date_4"
                                                                value="{{ old('capital_gains_purchase_date_4',$itr->capital_gains_purchase_date_4) }}"
                                                                class="form-control form-control-sm">
                                                            @if($errors->has('capital_gains_purchase_date_4'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_purchase_date_4') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text form-control-sm">
                                                                    <i class="fa fa-inr small"></i>
                                                                </span>
                                                                <input type="number"
                                                                    class="form-control rm-number form-control-sm"
                                                                    placeholder="Purchase Amount"
                                                                    value="{{ old('capital_gains_purchase_amount_4',$itr->capital_gains_purchase_amount_4) }}"
                                                                    name="capital_gains_purchase_amount_4" step="0.01"
                                                                    min="0.00" max="100000000" />
                                                            </div>
                                                            @if($errors->has('capital_gains_purchase_amount_4'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_purchase_amount_4') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="date" name="capital_gains_sale_date_4"
                                                                max="{{ date('Y-m-d') }}"
                                                                value="{{ old('capital_gains_sale_date_4',$itr->capital_gains_sale_date_4) }}"
                                                                class="form-control form-control-sm">
                                                            @if($errors->has('capital_gains_sale_date_4'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_sale_date_4') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text form-control-sm">
                                                                    <i class="fa fa-inr small"></i>
                                                                </span>
                                                                <input type="number"
                                                                    class="form-control rm-number form-control-sm"
                                                                    placeholder="Sale Amount"
                                                                    value="{{ old('capital_gains_sale_amount_4',$itr->capital_gains_sale_amount_4) }}"
                                                                    name="capital_gains_sale_amount_4" step="0.01"
                                                                    min="0.00" max="100000000" />
                                                            </div>
                                                            @if($errors->has('capital_gains_sale_amount_4'))
                                                            <span class="invalid-feedback">
                                                                {{ $errors->first('capital_gains_sale_amount_4') }}
                                                            </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr class="mt-0">
                                        <h6 class="text-dark">Investment from Sale Amount</h6>
                                        <p> Did you sell any asset (Mutual Funds, shares, property, house, land,
                                            building,
                                            etc) between the financial year period. </p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                In House
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number" step="0.01"
                                                        value="{{ old('investment_sale_amount_in_house',$itr->investment_sale_amount_in_house) }}"
                                                        name="investment_sale_amount_in_house" min="0.00"
                                                        max="100000000" placeholder="Investment In House" />
                                                </div>
                                                @if($errors->has('investment_sale_amount_in_house'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('investment_sale_amount_in_house') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                In Securities
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number" step="0.01"
                                                        value="{{ old('investment_sale_amount_in_securities',$itr->investment_sale_amount_in_securities) }}"
                                                        name="investment_sale_amount_in_securities" min="0.00"
                                                        max="100000000" placeholder="Investment In Securities" />
                                                </div>
                                                @if($errors->has('investment_sale_amount_in_securities'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('investment_sale_amount_in_securities') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                In Capital Gain Bank A/C
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number" step="0.01"
                                                        value="{{ old('investment_sale_amount_in_capital_gain_bank_a_c',$itr->investment_sale_amount_in_capital_gain_bank_a_c) }}"
                                                        name="investment_sale_amount_in_capital_gain_bank_a_c"
                                                        min="0.00" max="100000000"
                                                        placeholder="Investment In Capital Gain Bank A/C" />
                                                </div>
                                                @if($errors->has('investment_sale_amount_in_capital_gain_bank_a_c'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('investment_sale_amount_in_capital_gain_bank_a_c')
                                                    }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="display: none;" id="otherBlock">
                        <div class="card-header custom-accordion" id="from_5">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#otherInfo" aria-expanded="false" aria-controls="otherInfo">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-sack-dollar"></i>
                                    </div>
                                    Other Income
                                    <div class="icons px-3 lh-lg">
                                        <i class="fa fa-chevron-down"></i>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div id="otherInfo" class="collapse" aria-labelledby="from_5" data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-dark">Other Income </h6>
                                        <p> Interest from savings bank, deposits or any other income that you might wish
                                            to declare </p>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Commission
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('commission',$itr->commission) }}"
                                                        name="commission" step="0.01" max="100000000"
                                                        placeholder="Commission" />
                                                </div>
                                                @if($errors->has('commission'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('commission') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Brokerage
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('brokerage', $itr->brokerage) }}" name="brokerage"
                                                        step="0.01" max="100000000" placeholder="Brokerage" />
                                                </div>
                                                @if($errors->has('brokerage'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('brokerage') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Interest from Saving Bank
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('interest_from_saving_bank',$itr->interest_from_saving_bank) }}"
                                                        name="interest_from_saving_bank" step="0.01" max="100000000"
                                                        placeholder="Interest from Saving Bank" />
                                                </div>
                                                <small class="text-muted">Any interest earned from saving banks,
                                                    deposits, income tax refund etc.</small>
                                                @if($errors->has('interest_from_saving_bank'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('interest_from_saving_bank') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Interest from Fixed Deposit
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('interest_from_fixed_deposit',$itr->interest_from_fixed_deposit) }}"
                                                        name="interest_from_fixed_deposit" step="0.01" max="100000000"
                                                        placeholder="Interest from Fixed Deposit" />
                                                </div>
                                                <small class="text-muted">Interest from sweep accounts converted to FDs,
                                                    post-office fixed deposits etc.</small>
                                                @if($errors->has('interest_from_fixed_deposit'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('interest_from_fixed_deposit') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Dividend
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('dividend',$itr->dividend) }}" name="dividend"
                                                        step="0.01" max="100000000" placeholder="Dividend" />
                                                </div>
                                                @if($errors->has('dividend'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('dividend') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Family Pension
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('family_pension',$itr->family_pension) }}"
                                                        name="family_pension" step="0.01" max="100000000"
                                                        placeholder="Family Pension" />
                                                </div>
                                                @if($errors->has('family_pension'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('family_pension') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Other Rent
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('other_rent',$itr->other_rent) }}"
                                                        name="other_rent" step="0.01" max="100000000"
                                                        placeholder="Other Rent" />
                                                </div>
                                                @if($errors->has('other_rent'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('other_rent') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Other Interest
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('other_interest',$itr->other_interest) }}"
                                                        name="other_interest" step="0.01" max="100000000"
                                                        placeholder="Other Interest" />
                                                </div>
                                                @if($errors->has('other_interest'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('other_interest') }}
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
                                                        value="{{ old('mutual_fund',$itr->mutual_fund) }}"
                                                        name="mutual_fund" step="0.01" max="100000000"
                                                        placeholder="Mutual Fund" />
                                                </div>
                                                @if($errors->has('mutual_fund'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('mutual_fund') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                UTI (Unit Trust of India) Income
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('uti_income',$itr->uti_income) }}"
                                                        name="uti_income" step="0.01" max="100000000"
                                                        placeholder="UTI (Unit Trust of India) Income" />
                                                </div>
                                                @if($errors->has('uti_income'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('uti_income') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Agricultural Gross Income
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('agricultural_gross_income',$itr->agricultural_gross_income) }}"
                                                        name="agricultural_gross_income" step="0.01" max="100000000"
                                                        placeholder="Agricultural Gross Income" />
                                                </div>
                                                @if($errors->has('agricultural_gross_income'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('agricultural_gross_income') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Agricultural Expenses
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group w-md-50">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-inr"></i>
                                                    </span>
                                                    <input type="number" class="form-control rm-number"
                                                        value="{{ old('agricultural_expenses',$itr->agricultural_expenses) }}"
                                                        name="agricultural_expenses" step="0.01" max="100000000"
                                                        placeholder="Agricultural Expenses" />
                                                </div>
                                                @if($errors->has('agricultural_expenses'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('agricultural_expenses') }}
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

    var city_id = parseInt("{{ old('employer_city',$itr->employer_city) }}");
    var state_id = parseInt("{{ old('employer_state', $itr->employer_state) }}");
    var income_house_city = parseInt("{{ old('income_house_city', $itr->income_house_city) }}");
    var income_house_state = parseInt("{{ old('income_house_state', $itr->income_house_state) }}");
    var is_salary_income = parseInt("{{ old('is_salary_income', $itr->is_salary_income) }}");
    var is_house_income = parseInt("{{ old('is_house_income', $itr->is_house_income) }}");
    var is_business_income = parseInt("{{ old('is_business_income', $itr->is_business_income) }}");
    var is_capital_gain_income = parseInt("{{ old('is_capital_gain_income', $itr->is_capital_gain_income) }}");
    var is_other_income = parseInt("{{ old('is_other_income', $itr->is_other_income) }}");
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

    setTimeout(() => {
        state_id && getCity(state_id, '#city')
        income_house_state && getCity(income_house_state, '#income_house_city')
        is_salary_income && $('#salaryBlock').fadeToggle();
        is_business_income && $('#businessBlock').fadeToggle();
        is_house_income && $('#houseBlock').fadeToggle();
        is_capital_gain_income && $('#capitalGainBlock').fadeToggle();
        is_other_income && $('#otherBlock').fadeToggle();
    }, 200)

    $(function () {

        $('#salaryBlockCheck').on('change', function () { $('#salaryBlock').fadeToggle(); })
        $('#businessBlockCheck').on('change', function () { $('#businessBlock').fadeToggle(); })
        $('#houseBlockCheck').on('change', function () { $('#houseBlock').fadeToggle(); })
        $('#capitalGainBlockCheck').on('change', function () { $('#capitalGainBlock').fadeToggle(); })
        $('#otherBlockCheck').on('change', function () { $('#otherBlock').fadeToggle(); })

        // $('[type="number"]').on('input', function (e) {
        //     if (this.maxLength != -1) {
        //         return this.value = parseFloat(this.value); //.slice(0, this.maxLength)
        //     } else {
        //         return this.value = parseFloat(this.value);
        //     }

        // });

        $('[type="number"]').on('keypress', function (event) {
            return (event.charCode != 8 && event.charCode == 0 || event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57))
        });

        $("#stepTwo").validate({
            ignore: [],
            errorClass: "text-danger",
            errorElement: "small",
            rules: {
                employer_name: { required: () => $('[name="is_salary_income"]').is(":checked") },
                employer_tan: {
                    pancard: true,
                    required: () => $('[name="is_salary_income"]').is(":checked")
                },
                employer_flat_number: { required: () => $('[name="is_salary_income"]').is(":checked") },
                employer_address: { required: () => $('[name="is_salary_income"]').is(":checked") },
                employer_city: { required: () => $('[name="is_salary_income"]').is(":checked") },
                employer_state: { required: () => $('[name="is_salary_income"]').is(":checked") },
                employer_pincode: { required: () => $('[name="is_salary_income"]').is(":checked") },
                employer_type: { required: () => $('[name="is_salary_income"]').is(":checked") },
                salary: { required: () => $('[name="is_salary_income"]').is(":checked") },
                form_16_file: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2
                },
                income_house_type: { required: () => $('[name="is_house_income"]').is(":checked") },
                income_house_flat_number: { required: () => $('[name="is_house_income"]').is(":checked") },
                income_house_address: { required: () => $('[name="is_house_income"]').is(":checked") },
                income_house_city: { required: () => $('[name="is_house_income"]').is(":checked") },
                income_house_state: { required: () => $('[name="is_house_income"]').is(":checked") },
                income_house_pincode: { required: () => $('[name="is_house_income"]').is(":checked") },
                income_house_rent_received: { required: () => $('[name="is_house_income"]').is(":checked") },
                rent_agreement: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2
                },
                business_name: { required: () => $('[name="is_business_income"]').is(":checked") },
                business_type: { required: () => $('[name="is_business_income"]').is(":checked") },
                turnover: { required: () => $('[name="is_business_income"]').is(":checked") },
                net_profit: { required: () => $('[name="is_business_income"]').is(":checked") },
                description: { required: () => $('[name="is_business_income"]').is(":checked") },

                capital_gains_purchase_date_1: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_1"]').val() > 0 },
                capital_gains_purchase_amount_1: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_1"]').val() > 0 },
                capital_gains_sale_date_1: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_1"]').val() > 0 },
                capital_gains_sale_amount_1: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_1"]').val() > 0 },
                capital_gains_purchase_date_2: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_2"]').val() > 0 },
                capital_gains_purchase_amount_2: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_2"]').val() > 0 },
                capital_gains_sale_date_2: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_2"]').val() > 0 },
                capital_gains_sale_amount_2: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_2"]').val() > 0 },
                capital_gains_purchase_date_3: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_3"]').val() > 0 },
                capital_gains_purchase_amount_3: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_3"]').val() > 0 },
                capital_gains_sale_date_3: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_3"]').val() > 0 },
                capital_gains_sale_amount_3: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_3"]').val() > 0 },
                capital_gains_purchase_date_4: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_4"]').val() > 0 },
                capital_gains_purchase_amount_4: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_4"]').val() > 0 },
                capital_gains_sale_date_4: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_4"]').val() > 0 },
                capital_gains_sale_amount_4: { required: () => $('[name="is_capital_gain_income"]').is(":checked") && $('[name="capital_gains_type_4"]').val() > 0 },
            },
            messages: {
                employer_name: { required: "Please enter employer name" },
                employer_tan: { required: "Please enter employer tan" },
                employer_flat_number: { required: "Please enter employer flat/plot number" },
                employer_address: { required: "Please enter employer address" },
                employer_city: { required: "Please select employer city" },
                employer_state: { required: "Please select employer state" },
                employer_pincode: { required: "Please enter employer pincode" },
                employer_type: { required: "Please select employer type" },
                salary: { required: "Please enter value" },

                income_house_type: { required: "Please select house type." },
                income_house_flat_number: { required: "Please enter flat / plot number" },
                income_house_address: { required: "Please enter address." },
                income_house_city: { required: "Please select city." },
                income_house_state: { required: "Please select state" },
                income_house_pincode: { required: "Please enter pincode" },
                income_house_rent_received: { required: "Please enter rent received." },
                interest_paid_on_home_loan: { required: "Please enter interest paid on home loan." },
                principal_paid_on_home_loan: { required: "Please enter principal paid on home loan." },

                business_name: { required: "Please enter business name" },
                business_type: { required: "Please select type" },
                turnover: { required: "Please enter turnover" },
                net_profit: { required: "Please enter net profit" },
                description: { required: "Please enter business details" },

                capital_gains_purchase_date_1: { required: "Please select date" },
                capital_gains_purchase_amount_1: { required: "Please enter amount" },
                capital_gains_sale_date_1: { required: "Please select date" },
                capital_gains_sale_amount_1: { required: "Please enter amount" },

                capital_gains_purchase_date_2: { required: "Please select date" },
                capital_gains_purchase_amount_2: { required: "Please enter amount" },
                capital_gains_sale_date_2: { required: "Please select date" },
                capital_gains_sale_amount_2: { required: "Please enter amount" },

                capital_gains_purchase_date_3: { required: "Please select date" },
                capital_gains_purchase_amount_3: { required: "Please enter amount" },
                capital_gains_sale_date_3: { required: "Please select date" },
                capital_gains_sale_amount_3: { required: "Please enter amount" },

                capital_gains_purchase_date_4: { required: "Please select date" },
                capital_gains_purchase_amount_4: { required: "Please enter amount" },
                capital_gains_sale_date_4: { required: "Please select date" },
                capital_gains_sale_amount_4: { required: "Please enter amount" },
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