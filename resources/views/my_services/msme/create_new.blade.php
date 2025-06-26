@extends('layouts.retailer_app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create MSME Certificate</h5>
            <a href="{{ route('msme-certificate.list') }}" class="btn btn-secondary me-1">
                <i class="fa fa-arrow-left me-1"></i>
                Go Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label" for="mobile">Mobile</label>
                <input class="form-control rm-number" id="mobile" type="number" name="mobile" value=""
                    placeholder="Please enter mobile number." />
            </div>
            <div class="col-md-2">
                <label class="form-label d-block" for="mobile"><br /></label>
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

        <form action="{{ request()->url() }}" id="add" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <hr class="mt-2 mb-0">
                    <h5 class="text-secondary p-2 bg-gray">Aadhaar Details :-</h5>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="name">Name of Entrepreneur <span class="text-danger">*</span></label>
                    <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}"
                        placeholder="Name as per Aadhaar" minlength="2" maxlength="50" required />
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="aadharcard">Aadhaar Number <span class="text-danger">*</span></label>
                    <input class="form-control rm-number" id="aadharcard" type="number" name="aadharcard"
                        value="{{ old('aadharcard') }}" placeholder="Your Aadhaar Number" maxlength="12" required />
                    @error('aadharcard')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="aadhar_file">
                        Aadhar Card File <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-file-pdf"></i></span>
                        <input type="hidden" name="aadhar_file_old">
                        <input class="form-control" id="aadhar_file" type="file" name="aadhar_file" required />
                    </div>
                    @error('aadhar_file')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <hr class="mt-2 mb-0">
                    <h5 class="text-secondary p-2 bg-gray">PanCard Details :-</h5>
                </div>

                <div class="col-md-4 mb-2">
                    <label for="pancard_type" class="form-label">
                        Type of Organisation / Pancard <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" name="pancard_type" id="pancard_type" required>
                        <option value="" selected>Select One</option>
                        @foreach(config('constant.pancard_type_list', []) as $key => $value)
                        <option value="{{ $key }}" @selected(old('pancard_type')==$key)>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('pancard_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="pancard">
                        PanCard Number <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" placeholder="PanCard Number" id="pancard" type="text" name="pancard"
                        value="{{ old('pancard') }}" minlength="10" maxlength="10" required />
                    @error('pancard')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="pancard_file">
                        PanCard File <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-file-pdf"></i></span>
                        <input type="hidden" name="pancard_file_old">
                        <input type="file" name="pancard_file" class="form-control" required>
                    </div>
                    @error('pancard_file')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <hr class="mt-2 mb-0">
                    <h5 class="text-secondary p-2 bg-gray">Personal Details :-</h5>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                    <input class="form-control" id="email" type="email" name="email" placeholder="Email Address"
                        value="{{ old('email') }}" minlength="2" maxlength="50" required />
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="phone">Mobile Number <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            +91
                        </span>
                        <input class="form-control rm-number" id="phone" type="number" name="phone"
                            value="{{ old('phone') }}" minlength="10" maxlength="10" required
                            placeholder="Mobile Number">
                    </div>
                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="phone_2">WhatsApp Number</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            +91
                        </span>
                        <input class="form-control rm-number" id="phone_2" type="number" name="phone_2"
                            value="{{ old('phone_2') }}" minlength="10" maxlength="10" placeholder="Mobile Number">
                    </div>
                    @error('phone_2')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-lg-4 mb-2">
                    <label class="form-label" for="category">Social Category <span class="text-danger">*</span></label>
                    <select class="form-control" id="category" name="category" value="{{ old('category') }}" required>
                        <option value="" selected>Select One</option>
                        @foreach(config('constant.social_category_list', []) as $key => $value)
                        <option value="{{ $key }}" @selected(old('category')==$key)>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('category')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-lg-4 mb-2">
                    <label class="form-label" for="gender">Gender <span class="text-danger">*</span></label>
                    <select class="form-control" id="gender" name="gender" value="{{ old('gender') }}" required>
                        <option value="">Please Select</option>
                        <option value="1" @selected(old('gender')==1)>Male</option>
                        <option value="2" @selected(old('gender')==2)>Female</option>
                        <option value="3" @selected(old('gender')==3)>Transgender</option>
                    </select>
                    @error('gender')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="special_abled">
                        Specially Abled (DlVVANG) <span class="text-danger">*</span>
                    </label>
                    <div class="custom-check-group">
                        <div class="custom-control custom-radio">
                            <input type="radio" required id="special_abled_yes" name="special_abled"
                                class="custom-control-input" value="1" @checked(old('special_abled')==1)>
                            <label class="custom-control-label" for="special_abled_yes">Yes</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" required id="special_abled_no" name="special_abled"
                                class="custom-control-input" value="0" @checked(old('special_abled')==0)>
                            <label class="custom-control-label" for="special_abled_no">No</label>
                        </div>
                    </div>
                    @error('special_abled')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <hr class="mt-2 mb-0">
                    <h5 class="text-secondary p-2 bg-gray">Enterprise / Business Details :-</h5>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label" for="name_enterprise">
                        Name of Enterprise <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="name_enterprise" minlength="2" maxlength="50" required
                        name="name_enterprise" value="{{ old('name_enterprise') }}" placeholder="Enterprise Name">
                    @error('name_enterprise')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label" for="name_plant">
                        Plant/Unit Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="name_plant" minlength="2" maxlength="50" required
                        name="name_plant" value="{{ old('name_plant') }}" placeholder="Plant Name">
                    @error('name_plant')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="flat_plant">
                        Flat/Door/Block no. <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="flat_plant" minlength="2" maxlength="50" required
                        name="flat_plant" value="{{ old('flat_plant') }}" placeholder="Flat/Door/Block no.">
                    @error('flat_plant')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="building_plant">
                        Name of Premises/Building <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="building_plant" minlength="2" maxlength="50" required
                        name="building_plant" value="{{ old('building_plant') }}"
                        placeholder="Name of Premises/Building">
                    @error('building_plant')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="village_plant">
                        Village / Town <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="village_plant" minlength="2" maxlength="50" required
                        name="village_plant" value="{{ old('village_plant') }}" placeholder="Village / Town">
                    @error('village_plant')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="block_plant">Block <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="block_plant" minlength="2" maxlength="50" required
                        name="block_plant" value="{{ old('block_plant') }}" placeholder="Block">
                    @error('block_plant')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="street_plant">Street <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="street_plant" minlength="2" maxlength="50" required
                        name="street_plant" value="{{ old('street_plant') }}" placeholder="Street">
                    @error('street_plant')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="state">State <span class="text-danger">*</span></label>
                    <select name="state" onchange="getCity(this.value, '#city')" class="form-select" id="state"
                        required>
                        <option value="">Select State</option>
                        @foreach ($states as $state)
                        <option value="{{ $state['id'] }}" @selected(old('state')==$state['id'])>
                            {{ $state['name'] }}
                        </option>
                        @endforeach
                    </select>
                    @error('state')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="city">City <span class="text-danger">*</span></label>
                    <select class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                        <option value="">Please Select</option>
                    </select>
                    @error('city')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="pincode">PinCode <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pincode" type="text" name="pincode"
                        value="{{ old('pincode') }}" placeholder="PinCode" maxlength="6" required>
                    @error('pincode')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="country">Country</label>
                    <input type="text" class="form-control text-dark" id="country" type="text" name="country"
                        value="India" placeholder="Country" readonly disabled>
                    @error('country')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <hr class="mt-2 mb-0">
                    <h5 class="text-secondary p-2 bg-gray">Bank Details :-</h5>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="bank_name">Bank Name <span class="text-danger">*</span></label>
                    <input class="form-control" id="bank_name" type="text" name="bank_name"
                        value="{{ old('bank_name') }}" placeholder="Bank Name" minlength="2" maxlength="100" required />
                    @error('bank_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="bank_ifsc">IFSC Code <span class="text-danger">*</span></label>
                    <input class="form-control" id="bank_ifsc" type="text" name="bank_ifsc"
                        value="{{ old('bank_ifsc') }}" placeholder="IFSC Code" minlength="2" maxlength="12" required />
                    @error('bank_ifsc')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="bank_account">
                        Bank Account Number <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" id="bank_account" type="text" name="bank_account"
                        value="{{ old('bank_account') }}" placeholder="Bank Account Number" minlength="2" maxlength="20"
                        required />
                    @error('bank_account')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <hr class="mt-2 mb-0">
                    <h5 class="text-secondary p-2 bg-gray">Business Description :-</h5>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="enterprise_registration">
                        Date of Business Registration <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" id="enterprise_registration" type="date" name="enterprise_registration"
                        value="{{ old('enterprise_registration') }}" max="{{ date('Y-m-d') }}" required />
                    @error('enterprise_registration')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="enterprise_date">
                        Date of Business Start <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" id="enterprise_date" type="date" name="enterprise_date"
                        value="{{ old('enterprise_date') }}" max="{{ date('Y-m-d') }}" required />
                    @error('enterprise_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label" for="unit_type">
                        Major Activity of Unit <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" name="unit_type" id="unit_type" required>
                        <option>Manufacturing</option>
                        <option>Services</option>
                        <option>Trading</option>
                    </select>
                    @error('unit_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-12 mb-2">
                    <label class="form-label" for="nic_description">
                        Description of Unit <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" name="nic_description" placeholder="Description of Unit"
                        id="nic_description" minlength="20" maxlength="500"
                        required>{{ old('nic_description') }}</textarea>
                    @error('nic_description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label" for="emp_male">
                        Male Employee <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" id="emp_male" type="number" name="emp_male"
                        value="{{ old('emp_male', 0) }}" min="0" placeholder="Male Employee" required>
                    @error('emp_male')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label" for="emp_female">
                        Female Employee <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" id="emp_female" type="number" name="emp_female"
                        value="{{ old('emp_female', 0) }}" min="0" placeholder="Female Employee" required>
                    @error('emp_female')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label" for="emp_other">
                        Other Employee <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" id="emp_other" type="number" name="emp_other"
                        value="{{ old('emp_other', 0) }}" min="0" placeholder="Other Employee" required>
                    @error('emp_other')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label" for="emp_total">
                        Total Employee <span class="text-danger">*</span>
                    </label>
                    <input class="form-control text-dark" id="emp_total" type="number" name="emp_total"
                        value="{{ old('emp_total', 0) }}" min="0" placeholder="Total Employee" readonly>
                    @error('emp_total')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label" for="inv_wdv_a">
                        Investment Details <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" id="inv_wdv_a" type="number" name="inv_wdv_a"
                        value="{{ old('inv_wdv_a', 0.00) }}" step="0.01" min="0" required
                        placeholder="Depreciated Cost">
                    <small class="text-muted">
                        Depreciated Cost as on 31st March of the Previous Year (A)
                    </small>
                    @error('inv_wdv_a')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label" for="turnover_a">
                        Total Turnover (in RS.) <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" id="turnover_a" type="number" name="turnover_a"
                        value="{{ old('turnover_a', 0.00) }}" step="0.01" min="0" placeholder="Total Turnover" required>
                    @error('turnover_a')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-12 mb-2">
                    <div class="form-check ps-4">
                        <input class="form-check-input" type="checkbox" name="declaration" id="declaration" required>
                        <label class="form-check-label mb-0" for="declaration">
                            <p>I hereby deciare that information given above are true to the best of my knowledge.
                                for any information, that may be required to be verified, proof/evidence shall be
                                produced immediately before the concerned authority.</p>
                            <p>
                                मैं एतद्द्वारा घोषणा करता हूं कि ऊपर दी गई जानकारी मेरी सर्वोत्तम जानकारी के अनुसार सत्य
                                है। किसी भी जानकारी के लिए, जिसे सत्यापित करने की आवश्यकता हो सकती है, संबंधित
                                प्राधिकारी के समक्ष तुरंत सबूत/साक्ष्य प्रस्तुत किया जाएगा।
                            </p>
                        </label>
                    </div>

                    <div class="col-lg-12 mt-3 d-flex justify-content-start">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>

    var city_id = "{{ old('city') }}";
    var state_id = "{{ old('state') }}";

    function getCity(state_id, city_id = null) {
        $.ajax({
            type: "POST",
            url: "{{ route('cities.list') }}",
            data: { state_id, city_id },
            success: function (data) {
                $('#city').html(data);
                return true;
            },
            error: function (jqXHR, exception) {
                $('#city').html('<option value="">Select City</option>');
                return false;
            }
        });
    }

    setTimeout(() => {
        if (state_id) { getCity(state_id, city_id); }
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

        $("#add").validate({
            ignore: [],
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                email: {
                    customEmail: true,
                },
                phone: {
                    digits: true,
                    exactlength: 10,
                    indiaMobile: true
                },
                aadharcard: {
                    digits: true,
                    exactlength: 12,
                    aadharcard: true,
                },
                aadhar_file: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 1
                },
                pancard: {
                    pancard: true
                },
                pancard_file: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 1
                },
                pincode: {
                    digits: true,
                    exactlength: 6
                },
                enterprise_registration: {
                    required: true,
                    date: true
                },
                enterprise_date: {
                    required: true,
                    date: true
                },
                bank_ifsc: {
                    ifsc: true,
                },
            },
            messages: {
                name: {
                    required: "Please enter name.",
                },
                aadharcard: {
                    required: "Please enter AadharCard Number.",
                },
                aadhar_file: {
                    required: "Please select aadhar card file.",
                    extension: "Allow file with ext. - jpg,jpeg,png,pdf",
                },
                pancard_type: {
                    required: "Please select pancard type",
                },
                pancard: {
                    required: "Please enter pancard number",
                },
                pancard_file: {
                    required: "Please select aadhar card file.",
                    extension: "Allow file with ext. - jpg,jpeg,png,pdf",
                },
                email: {
                    required: "Please enter email.",
                },
                phone: {
                    required: "Please enter phone.",
                },
                category: {
                    required: "Please select Category.",
                },
                gender: {
                    required: "Please select gender.",
                },
                special_abled: {
                    required: "Are you special abled.?",
                },
                name_enterprise: {
                    required: "Please enter enterprise name.",
                },
                name_plant: {
                    required: "Please enter plant name.",
                },
                flat_plant: {
                    required: "Please enter flat.",
                },
                building_plant: {
                    required: "Please enter building.",
                },
                block_plant: {
                    required: "Please enter block.",
                },
                street_plant: {
                    required: "Please enter street name.",
                },
                village_plant: {
                    required: "Please enter village name.",
                },
                city: {
                    required: "Please select city.",
                },
                state: {
                    required: "Please select state.",
                },
                pincode: {
                    required: "Please enter pincode.",
                },
                enterprise_registration: {
                    required: "Please select enterprise registration date.",
                },
                enterprise_date: {
                    required: "Please select enterprise start date.",
                },
                unit_type: {
                    required: "Please select unit type.",
                },
                nic_description: {
                    required: "Please select unit type description.",
                },
                bank_name: {
                    required: "Please enter bank name.",
                },
                bank_ifsc: {
                    required: "Please enter IFSC code.",
                },
                bank_account: {
                    required: "Please enter bank account number.",
                },
                emp_male: {
                    required: "Please enter male employee count.",
                },
                emp_female: {
                    required: "Please enter female employee count.",
                },
                emp_other: {
                    required: "Please enter other employee count.",
                },
                emp_total: {
                    required: "Please enter total employee count.",
                },
                inv_wdv_a: {
                    required: "Please enter amount in rupees.",
                },
                turnover_a: {
                    required: "Please enter amount in rupees.",
                },
                declaration: {
                    required: "You must confirm Declaration.",
                }
            },
            errorPlacement: function (error, element) {
                if ($(element).hasClass('form-check-input')) {
                    error.insertAfter($(element).parent());
                } else if ($(element).parent().hasClass('custom-control') && $(element).parents().eq(1).hasClass('form-control')) {
                    error.insertAfter($(element).parents().eq(1));
                } else if ($(element).parent().hasClass('input-group')) {
                    error.insertAfter($(element).parent());
                } else if ($(element).parents().eq(1).hasClass('custom-check-group')) {
                    error.insertAfter($(element).parents().eq(1));
                } else {
                    error.insertAfter(element);
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

        $('#pancard, #bank_ifsc').on('input', function () {
            this.value = this.value.toString().toUpperCase().replaceAll(' ', '');
        });

        $('#emp_male, #emp_female, #emp_other').on('input', function () {
            var value = parseInt($('#emp_male').val()) + parseInt($('#emp_female').val()) + parseInt($('#emp_other').val());
            $('#emp_total').val(value)
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
                    document.forms['add']['name'].value = data?.name ? data.name : "";
                    document.forms['add']['email'].value = data?.email ? data.email : "";
                    document.forms['add']['phone'].value = data?.mobile ? data.mobile : "";
                    document.forms['add']['state'].value = data?.state_id ? data.state_id : "";
                    document.forms['add']['city'].value = data?.city_id ? data.city_id : "";
                    document.forms['add']['gender'].value = data?.gender ? data.gender : "";
                    document.forms['add']['street_plant'].value = data?.address ? data.address : "";
                    document.forms['add']['pincode'].value = data?.pincode ? data.pincode : "";

                    if (data && data.state_id && data.city_id) {
                        getCity(data.state_id, data.city_id)
                    }

                    if (data && data.documents && data.documents.length > 0) {
                        let aadharcard = data.documents.find(row => row.doc_type == 1);
                        if (aadharcard) {
                            document.forms['add']['aadharcard'].value = aadharcard.doc_number;
                            if (aadharcard.doc_img_front) {
                                document.forms['add']['aadhar_file_old'].value = aadharcard.doc_img_front;
                                $('[name="aadhar_file"]').prop('required', false)
                            }
                        }

                        let pancard = data.documents.find(row => row.doc_type == 4);
                        if (pancard) {
                            console.log(pancard);
                            document.forms['add']['pancard'].value = pancard.doc_number;
                            if (pancard.doc_img_front) {
                                document.forms['add']['pancard_file_old'].value = pancard.doc_img_front;
                                $('[name="pancard_file"]').prop('required', false)
                            }
                        }
                    }

                    if (data && data.bank) {
                        document.forms['add']['bank_ifsc'].value = (data.bank.account_ifsc) ? data.bank.account_ifsc : "";
                        document.forms['add']['bank_name'].value = (data.bank.account_bank) ? data.bank.account_bank : "";
                        document.forms['add']['bank_account'].value = (data.bank.account_number) ? data.bank.account_number : "";
                    }

                    $(btn).parents().eq(2).find('input[id="mobile"]').addClass('is-valid');
                } else {
                    document.forms['add']['phone'].value = mobile
                    toastr.error(message)
                }
            })
        });
    });
</script>

@endsection