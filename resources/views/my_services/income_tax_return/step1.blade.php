@extends('my_services.income_tax_return.index')

@section('sub_section')

@if(!$itr->is_step_1_complete)
<div class="card mb-3 bg-light-secondary border-secondary">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label text-secondary fw-bold" for="mobile">Mobile</label>
                <input class="form-control rm-number border-secondary bg-light-dark text-secondary" id="mobile"
                    type="number" name="mobile" maxlength="10" placeholder="Please enter mobile number."
                    value="{{ request('mobile') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label d-block" for="mobile"><br></label>
                <span class="btn btn-secondary sync" role="button">
                    Sync <i class="fa fa-sync ms-1"></i>
                </span>
            </div>
            <div class="col-md-12">
                <p class="text-secondary my-1">
                    Please enter customer phone number above field, If we have customer information related to this
                    phone number, then we will auto fill information in form.
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<form action="{{ request()->url() }}" method="post" id="stepOne" enctype="multipart/form-data">
    @csrf
    <fieldset @disabled($itr->status > 0 && $itr->status != 4)>
        <div class="row">
            <div class="col-12">
                <div id="mainAccordion" class="accordion-icons accordion has-validation">
                    <div class="card">
                        <div class="card-header custom-accordion" id="headingOne3">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#userInfo" aria-expanded="true" aria-controls="userInfo">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-user-check fs-6"></i>
                                    </div>
                                    Permanent Information
                                    <div class="icons px-3 lh-lg"><i class="fa fa-chevron-down"></i></div>
                                </div>
                            </section>
                        </div>

                        <div id="userInfo" class="collapse show" aria-labelledby="headingOne3"
                            data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <p class="text-muted fs-7">Please ensure all information provided is as per your
                                    government identity documents (Like PAN, Aadhaar)</p>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Customer Name <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text"
                                                        value="{{ old('first_name', $itr->first_name ) }}"
                                                        name="first_name" minlength="2" class="form-control"
                                                        placeholder="First Name" maxlength="100">
                                                    <input type="text"
                                                        value="{{ old('middle_name', $itr->middle_name ) }}"
                                                        name="middle_name" minlength="2" class="form-control"
                                                        placeholder="Middle Name" maxlength="100">
                                                    <input type="text" value="{{ old('last_name', $itr->last_name ) }}"
                                                        name="last_name" minlength="2" class="form-control"
                                                        placeholder="Last Name" required maxlength="100">
                                                </div>
                                                <small class="text-muted">
                                                    Make sure your name is as per the PAN card; 5th character of PAN
                                                    number is the first letter of your last name.
                                                </small>
                                                @if($errors->has('first_name') || $errors->has('middle_name')
                                                ||$errors->has('last_name'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('first_name') }}
                                                    {{ $errors->first('middle_name') }}
                                                    {{ $errors->first('last_name') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Date of Birth <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <input type="date" name="date_of_birth" class="form-control"
                                                        required max="{{ date('Y-m-d') }}"
                                                        value="{{ old('date_of_birth', $itr->date_of_birth ? $itr->date_of_birth->format('Y-m-d') : '' ) }}">
                                                </div>
                                                <small class="text-muted">
                                                    Specify date in a format like DD/MM/YYYY
                                                </small>
                                                @if($errors->has('date_of_birth'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('date_of_birth') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Father's Name <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="father_first_name"
                                                        value="{{ old('father_first_name', $itr->father_first_name ) }}"
                                                        maxlength="100" minlength="2" placeholder="First Name">
                                                    <input type="text" class="form-control" name="father_middle_name"
                                                        value="{{ old('father_middle_name', $itr->father_middle_name ) }}"
                                                        maxlength="100" minlength="2" placeholder="Middle Name">
                                                    <input type="text" class="form-control" name="father_last_name"
                                                        value="{{ old('father_last_name', $itr->father_last_name ) }}"
                                                        maxlength="100" minlength="2" required placeholder="Last Name">
                                                </div>
                                                @if($errors->has('father_first_name') ||
                                                $errors->has('father_middle_name')
                                                ||$errors->has('father_last_name'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('father_first_name') }}
                                                    {{ $errors->first('father_middle_name') }}
                                                    {{ $errors->first('father_last_name') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Gender <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-8">
                                                <div class="input-group gap-2">
                                                    <div class="custom-control-inline">
                                                        <input type="radio" id="male" value="1" name="gender"
                                                            class="custom-control-input"
                                                            @checked(old('gender',$itr->gender)==1)>
                                                        <label class="custom-control-label" for="male">Male</label>
                                                    </div>
                                                    <div class="custom-control-inline">
                                                        <input type="radio" id="female" value="2" name="gender"
                                                            class="custom-control-input"
                                                            @checked(old('gender',$itr->gender)==2)>
                                                        <label class="custom-control-label" for="female">Female</label>
                                                    </div>
                                                </div>
                                                @if($errors->has('gender'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('gender') }}
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
                        <div class="card-header custom-accordion" id="headingOne3">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#identification" aria-expanded="false"
                                    aria-controls="identification">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-id-card fs-6"></i>
                                    </div>
                                    Identification & Contact Details
                                    <div class="icons px-3 lh-lg"><i class="fa fa-chevron-down"></i></div>
                                </div>
                            </section>
                        </div>

                        <div id="identification" class="collapse _show" aria-labelledby="headingOne3"
                            data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="text-muted fs-7">It is a mandate by the Government to provide us these
                                            details.</p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Aadhaar Details <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-4 mb-2 mb-lg-0">
                                                <input type="number" class="form-control rm-number" name="adhaar_number"
                                                    placeholder="Aadhaar Number"
                                                    value="{{ old('adhaar_number', $itr->adhaar_number ) }}"
                                                    minlength="12" maxlength="12" required>
                                                @if($errors->has('adhaar_number'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('adhaar_number') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4 mb-2 mb-lg-0">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa fa-file-pdf"></i></span>
                                                    <input type="hidden" name="adhaar_file_old">
                                                    <input type="file" class="form-control" name="adhaar_file"
                                                        @required(!$itr->adhaar_file)>
                                                    @if($itr->adhaar_file)
                                                    <a href="{{ asset('storage/'. $itr->adhaar_file) }}"
                                                        class="input-group-text" target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                                @if($errors->has('adhaar_file'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('adhaar_file') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                PanCard Details <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-4  mb-2 mb-lg-0">
                                                <input type="text" class="form-control" name="pancard_number"
                                                    value="{{ old('pancard_number', $itr->pancard_number ) }}"
                                                    placeholder="PanCard Number" required maxlength="10">
                                                @if($errors->has('pancard_number'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('pancard_number') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4  mb-2 mb-lg-0">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa fa-file-pdf"></i></span>
                                                    <input type="hidden" name="pancard_file_old">
                                                    <input type="file" class="form-control" name="pancard_file"
                                                        @required(!$itr->pancard_file)>
                                                    @if($itr->pancard_file)
                                                    <a href="{{ asset('storage/'.$itr->pancard_file) }}"
                                                        class="input-group-text" target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                                @if($errors->has('pancard_file'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('pancard_file') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Mobile No <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">+91</span>
                                                    <input type="number" class="form-control rm-number" name="phone"
                                                        value="{{ old('phone', $itr->phone ) }}" placeholder="Mobile No"
                                                        required maxlength="10">
                                                </div>
                                                @if($errors->has('phone'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('phone') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Email <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <input type="email" name="email"
                                                        value="{{ old('email', $itr->email ) }}" class="form-control"
                                                        placeholder="Email Address" required>
                                                </div>
                                                @if($errors->has('email'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('email') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-12">
                                        <h6 class="text-dark fw-bold">Additional Information (Optional)</h6>
                                        <p>Leave empty if you don't have additional information</p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                IT Portal Password
                                            </label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" name="itr_password"
                                                    value="{{ old('itr_password', $itr->itr_password ) }}" minlength="2"
                                                    maxlength="100" placeholder="Income Tax Portal Password">
                                                <small class="text-muted">
                                                    If you have Income Tax portal account, Enter Password here.
                                                </small>
                                                @if($errors->has('itr_password'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('itr_password') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                WhatsApp Number
                                            </label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">+91</span>
                                                    <input type="number" class="form-control rm-number" name="phone_2"
                                                        value="{{ old('phone_2', $itr->phone_2 ) }}" maxlength="10"
                                                        placeholder="WhatsApp Number">
                                                </div>
                                                @if($errors->has('phone_2'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('phone_2') }}
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
                        <div class="card-header custom-accordion" id="headingOne3">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#address" aria-expanded="false" aria-controls="address">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-location-dot fs-6"></i>
                                    </div>
                                    Your Address
                                    <div class="icons px-3 lh-lg"><i class="fa fa-chevron-down"></i></div>
                                </div>
                            </section>
                        </div>
                        <div id="address" class="collapse _show" aria-labelledby="headingOne3"
                            data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="text-muted fs-7">It is mandatory to provide the address details to
                                            submit your IT returns.</p>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Flat / Door No <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" placeholder="Flat / Door No"
                                                    required name="flat_number"
                                                    value="{{ old('flat_number', $itr->flat_number ) }}" minlength="2"
                                                    maxlength="100">
                                                @if($errors->has('flat_number'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('flat_number') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Area Locality <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" placeholder="Area Locality"
                                                    required name="address" value="{{ old('address', $itr->address ) }}"
                                                    minlength="2" maxlength="100">
                                                @if($errors->has('address'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('address') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                State / City <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-4 mb-2 mb-lg-0">
                                                <select id="state" name="state" onchange="getCity(this.value)"
                                                    class="form-select" required>
                                                    <option value="">Select State</option>
                                                    @foreach ($states as $state)
                                                    <option value="{{ $state['id'] }}" @selected(old('state',$itr->state
                                                        )==$state['id'])>
                                                        {{ $state['name'] }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('state'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('state') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4 mb-2 mb-lg-0">
                                                <select id="city" name="city" class="form-select" required>
                                                    <option value="">Select City</option>
                                                </select>
                                                @if($errors->has('city'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('city') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Pin Code <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-4 mb-2 mb-lg-0">
                                                <input type="number" placeholder="Pin Code" name="pincode"
                                                    value="{{ old('pincode', $itr->pincode ) }}"
                                                    class="form-control rm-number" maxlength="6" id="pincode" required>
                                                @if($errors->has('pincode'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('pincode') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4 mb-2 mb-lg-0">
                                                <input type="text" class="form-control text-dark" name="country"
                                                    value="India" readonly>
                                                @if($errors->has('country'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('country') }}
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
                        <div class="card-header custom-accordion" id="headingOne3">
                            <section class="mb-0 mt-0">
                                <div role="menu" class="collapsed d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#bank_details" aria-expanded="false" aria-controls="bank_details">
                                    <div class="accordion-icon-custom">
                                        <i class="fa-solid fa-university fs-6"></i>
                                    </div>
                                    Bank Details
                                    <div class="icons px-3 lh-lg"><i class="fa fa-chevron-down"></i></div>
                                </div>
                            </section>
                        </div>
                        <div id="bank_details" class="collapse _show" aria-labelledby="headingOne3"
                            data-bs-parent="#mainAccordion">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <p>Your primary bank account is where you will receive your refunds, if
                                            eligible.</p>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                IFSC code <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" placeholder="IFSC code"
                                                    name="bank_ifsc" value="{{ old('bank_ifsc', $itr->bank_ifsc ) }}"
                                                    maxlength="15" minlength="10" required>
                                                @if($errors->has('bank_ifsc'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('bank_ifsc') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Bank Name <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" placeholder="Bank Name"
                                                    name="bank_name" value="{{ old('bank_name', $itr->bank_name ) }}"
                                                    maxlength="100" minlength="2" required>
                                                @if($errors->has('bank_name'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('bank_name') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Account Number <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control rm-number"
                                                    placeholder="Account Number" name="bank_account_no"
                                                    value="{{ old('bank_account_no', $itr->bank_account_no ) }}"
                                                    maxlength="20" minlength="5" required>
                                                @if($errors->has('bank_account_no'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('bank_account_no') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Account Type <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-4 mb-2 mb-lg-0">
                                                <select class="form-select" name="bank_account_type" required>
                                                    @foreach(config('constant.bank_account_holder_type', []) as $key =>
                                                    $value)
                                                    <option value="{{ $key }}" @selected(old('bank_account_type', $itr->
                                                        bank_account_type )==$key)>
                                                        {{ $value }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('bank_account_type'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('bank_account_type') }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4 mb-2 mb-lg-0">
                                                <select class="form-select" name="account_type" required>
                                                    @foreach(config('constant.bank_account_type', []) as $key =>
                                                    $value)
                                                    <option value="{{ $key }}" @selected(old('account_type',$itr->
                                                        account_type )==$key)>
                                                        {{ $value }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('account_type'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('account_type') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">
                                                Bank Statement File
                                            </label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa fa-file-pdf"></i></span>
                                                    <input type="file" class="form-control" name="bank_statment_file">
                                                    @if($itr->bank_statment_file)
                                                    <a href="{{ asset('storage/'.$itr->bank_statment_file) }}"
                                                        class="input-group-text" target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                                @if($errors->has('bank_statment_file'))
                                                <span class="invalid-feedback">
                                                    {{ $errors->first('bank_statment_file') }}
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
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-success btn-lg submit">Save Information</button>
            </div>
        </div>
    </fieldset>
</form>
@endsection

@section('js')
<script>

    var city_id = "{{ old('city', $itr->city ) }}";
    var state_id = "{{ old('state', $itr->state ) }}";

    function getCity(state_id, city_id = null) {
        $.ajax({
            type: "POST",
            url: "{{ route('cities.list') }}",
            data: { state_id, city_id, _token: "{{ csrf_token() }}" },
            success: function (data) {
                $('#city').html(data);
            },
        });
        return true;
    }

    setTimeout(() => {
        if (state_id) { getCity(state_id, city_id); }
        let mobile = $('#mobile').val();
        if (mobile) {
            $('.sync').trigger("click");
        }
    }, 200);

    $(function () {
        $('[type="number"]').on('input', function (e) {
            let maxLength = this.maxLength != -1 ? this.maxLength : 10;
            if (this.value.length > maxLength) {
                this.value = this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0').slice(0, maxLength);
            } else {
                this.value = this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');
            }
        });

        $("#stepOne").validate({
            ignore: [],
            errorClass: "text-danger",
            errorElement: "small",
            rules: {
                gender: {
                    required: true,
                },
                email: {
                    customEmail: true,
                },
                phone: {
                    indiaMobile: true,
                    required: true,
                },
                pancard_number: {
                    pancard: true,
                },
                pancard_file: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2
                },
                adhaar_number: {
                    aadharcard: true,
                },
                adhaar_file: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2
                },
                phone_2: {
                    indiaMobile: true,
                },
                bank_ifsc: {
                    ifsc: true
                },
                bank_statment_file: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2
                },
            },
            messages: {
                last_name: {
                    required: "Please enter Last Name."
                },
                date_of_birth: {
                    required: "Please enter Date of Birth."
                },
                father_last_name: {
                    required: "Please enter father's Last Name"
                },
                gender: {
                    required: "Please select Gender"
                },
                adhaar_number: {
                    required: "Please enter Aadhaar Number"
                },
                adhaar_file: {
                    required: "Please select Aadhaar Document.",
                    extension: "Supported Format Only : pdf, jpg, jpeg, png"
                },
                pancard_number: {
                    required: "Please enter Pancard Number"
                },
                pancard_file: {
                    required: "Please select Pancard Document.",
                    extension: "Supported Format Only : pdf, jpg, jpeg, png"
                },
                email: {
                    required: "Please enter Email"
                },
                phone: {
                    required: "Please enter Mobile no."
                },
                flat_number: {
                    required: "Please enter House / Flat number."
                },
                address: {
                    required: "Please enter Address"
                },
                city: {
                    required: "Please select City"
                },
                state: {
                    required: "Please select State"
                },
                pincode: {
                    required: "Please enter PinCode"
                },
                bank_ifsc: {
                    required: "Please enter Bank IFSC"
                },
                bank_name: {
                    required: "Please enter Bank Name"
                },
                bank_account_no: {
                    required: "Please enter Bank Account No."
                },
                account_type: {
                    required: "Please select Bank Account Type"
                },
                bank_account_type: {
                    required: "Please select Bank Account Type"
                },
                bank_statment_file: {
                    required: "Please select Pancard Document.",
                    extension: "Supported Format Only : pdf, jpg, jpeg, png"
                },
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

        $('.sync').on('click', function () {
            var btn = $(this);
            var mobile = $('#mobile').val();
            if (!mobile) {
                $(btn).parents().eq(2).find('input[id="mobile"]').addClass('is-invalid');
                return toastr.error("Please provide mobile number.")
            }

            btn.find('.fa-sync').addClass('fa-spin')
            $.post("{{ route('customer.find') }}", { mobile, _token: "{{ csrf_token() }}" }, function ({ data, status, message }) {
                btn.find('.fa-sync').removeClass('fa-spin')
                $(btn).parents().eq(2).find('input[id="mobile"]').removeClass('is-invalid');
                if (status && data) {
                    document.forms['stepOne']['first_name'].value = data?.first_name ? data.first_name : "";
                    document.forms['stepOne']['middle_name'].value = data?.middle_name ? data.middle_name : "";
                    document.forms['stepOne']['last_name'].value = data?.last_name ? data.last_name : "";
                    document.forms['stepOne']['email'].value = data?.email ? data.email : "";
                    document.forms['stepOne']['phone'].value = data?.mobile ? data.mobile : "";
                    document.forms['stepOne']['date_of_birth'].value = data?.dob ? data.dob : "";
                    document.forms['stepOne']['state'].value = data?.state_id ? data.state_id : "";
                    document.forms['stepOne']['city'].value = data?.city_id ? data.city_id : "";

                    document.forms['stepOne']['father_first_name'].value = data?.father_first_name ? data.father_first_name : "";
                    document.forms['stepOne']['father_middle_name'].value = data?.father_middle_name ? data.father_middle_name : "";
                    document.forms['stepOne']['father_last_name'].value = data?.father_last_name ? data.father_last_name : "";
                    document.forms['stepOne']['itr_password'].value = data?.itr_password ? data.itr_password : "";
                    document.forms['stepOne']['address'].value = data?.address ? data.address : "";
                    document.forms['stepOne']['pincode'].value = data?.pincode ? data.pincode : "";


                    $(`input[name="gender"][value="${data.gender}"]`).prop('checked', true);

                    if (data && data.state_id && data.city_id) {
                        getCity(data.state_id, data.city_id)
                    }

                    if (data && data.documents && data.documents.length > 0) {
                        let aadharcard = data.documents.find(row => row.doc_type == 1);
                        if (aadharcard) {
                            document.forms['stepOne']['adhaar_number'].value = aadharcard.doc_number;
                            if (aadharcard.doc_img_front) {
                                document.forms['stepOne']['adhaar_file_old'].value = aadharcard.doc_img_front;
                                console.log($('[name="adhaar_file"]'));
                                $('[name="adhaar_file"]').prop('required', false)
                            }
                        }

                        let pancard = data.documents.find(row => row.doc_type == 4);
                        if (pancard) {
                            document.forms['stepOne']['pancard_number'].value = pancard.doc_number;
                            if (pancard.doc_img_front) {
                                document.forms['stepOne']['pancard_file_old'].value = pancard.doc_img_front;
                                $('[name="pancard_file"]').prop('required', false)
                            }
                        }
                    }

                    if (data && data.bank) {
                        document.forms['stepOne']['bank_ifsc'].value = (data.bank.account_ifsc) ? data.bank.account_ifsc : "";
                        document.forms['stepOne']['bank_name'].value = (data.bank.account_bank) ? data.bank.account_bank : "";
                        document.forms['stepOne']['bank_account_no'].value = (data.bank.account_number) ? data.bank.account_number : "";
                    }

                    $(btn).parents().eq(2).find('input[id="mobile"]').addClass('is-valid');
                } else {
                    document.forms['stepOne']['phone'].value = mobile
                    toastr.error(message)
                }
            })

        })
    })
</script>
@endsection