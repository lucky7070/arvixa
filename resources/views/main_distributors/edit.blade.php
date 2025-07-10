@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Main Distributors :: Main Distributor Edit </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('main_distributors')  }}" class="btn btn-outline-secondary me-4"> <i
                            class="fa fa-arrow-left me-1"></i>
                        Go Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="ediUser" method="POST"
            action="{{ route('main_distributors.edit', $main_distributor['id']) }}" enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="name">First Name</label>
                <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                    value="{{ old('name', $main_distributor['name'])  }}" />
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" placeholder="Enter Email" type="email" name="email"
                    value="{{ old('email', $main_distributor['email']) }}" />
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="mobile">Mobile</label>
                <input class="form-control" id="mobile" placeholder="Enter Mobile Number" name="mobile" type="text"
                    value="{{ old('mobile',$main_distributor['mobile']) }}" />
                @error('mobile')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" {{ (old('status', $main_distributor['status'] )==1) ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="0" {{ (old('status', $main_distributor['status'] )==0) ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="image">Image</label>
                <div class="input-group">
                    <input class="form-control" id="image" name="image" type="file" value="" />
                    @if($main_distributor['image'])
                    <a href="{{ asset('/storage/'.$main_distributor['image']) }}" target="_blank" class="btn btn-dark" type="button"><i class="fa-solid fa-download"></i></a>
                    @endif
                </div>
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="date_of_birth">Date of Birth <span class="required">*</span></label>
                <input class="form-control" id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', $main_distributor['date_of_birth']) }}" />
                @error('date_of_birth')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="gender">Gender <span class="required">*</span></label>
                <select name="gender" class="form-select" id="gender">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', $main_distributor['gender'])=='male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $main_distributor['gender'])=='female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $main_distributor['gender'])=='other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="address">Address <span class="required">*</span></label>
                <input class="form-control" id="address" name="address" value="{{ old('address', $main_distributor['address']) }}" placeholder="Address" />
                @error('address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="shop_name">Shop Name <span class="required">*</span></label>
                <input class="form-control" id="shop_name" placeholder="Enter Shop Name" name="shop_name" type="text" value="{{ old('shop_name', $main_distributor['shop_name']) }}" />
                @error('shop_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="shop_address">Shop Address <span class="required">*</span></label>
                <input class="form-control" id="shop_address" name="shop_address" value="{{ old('shop_address', $main_distributor['shop_address']) }}" placeholder="Shop Address" />
                @error('shop_address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="aadhar_no">Aadhar Number <span class="required">*</span></label>
                <input class="form-control" id="aadhar_no" placeholder="Enter Aadhar Number" name="aadhar_no" type="text" value="{{ old('aadhar_no', $main_distributor['aadhar_no']) }}" />
                @error('aadhar_no')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="pan_no">PAN Number <span class="required">*</span></label>
                <input class="form-control" id="pan_no" placeholder="Enter PAN Number" name="pan_no" type="text" value="{{ old('pan_no', $main_distributor['pan_no']) }}" />
                @error('pan_no')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="aadhar_doc">Aadhar Document <span class="required">*</span></label>
                <div class="input-group">
                    <input class="form-control" id="aadhar_doc" name="aadhar_doc" type="file" />
                    @if($main_distributor['aadhar_doc'])
                    <a href="{{ asset('/storage/'.$main_distributor['aadhar_doc']) }}" target="_blank" class="btn btn-dark" type="button"><i class="fa-solid fa-download"></i></a>
                    @endif
                </div>
                @error('aadhar_doc')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="pan_doc">PAN Document <span class="required">*</span></label>
                <div class="input-group">
                    <input class="form-control" id="pan_doc" name="pan_doc" type="file" />
                    @if($main_distributor['pan_doc'])
                    <a href="{{ asset('/storage/'.$main_distributor['pan_doc']) }}" target="_blank" class="btn btn-dark" type="button"><i class="fa-solid fa-download"></i></a>
                    @endif
                </div>
                @error('pan_doc')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="bank_proof_doc">Bank Proof Document <span class="required">*</span></label>
                <div class="input-group">
                    <input class="form-control" id="bank_proof_doc" name="bank_proof_doc" type="file" />
                    @if($main_distributor['bank_proof_doc'])
                    <a href="{{ asset('/storage/'.$main_distributor['bank_proof_doc']) }}" target="_blank" class="btn btn-dark" type="button"><i class="fa-solid fa-download"></i></a>
                    @endif
                </div>
                @error('bank_proof_doc')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="bank_name">Bank Name <span class="required">*</span></label>
                <input class="form-control" id="bank_name" placeholder="Enter Bank Name" name="bank_name" type="text" value="{{ old('bank_name', $main_distributor['bank_name']) }}" />
                @error('bank_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="bank_account_number">Account Number <span class="required">*</span></label>
                <input class="form-control" id="bank_account_number" placeholder="Enter Account Number" name="bank_account_number" type="text" value="{{ old('bank_account_number', $main_distributor['bank_account_number']) }}" />
                @error('bank_account_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="bank_ifsc_code">IFSC Code <span class="required">*</span></label>
                <input class="form-control" id="bank_ifsc_code" placeholder="Enter IFSC Code" name="bank_ifsc_code" type="text" value="{{ old('bank_ifsc_code', $main_distributor['bank_ifsc_code']) }}" />
                @error('bank_ifsc_code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="password">New Password</label>
                <input class="form-control" placeholder="Enter Password" name="password" id="new-password"
                    type="password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 col-md-6 mt-2">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input class="form-control" placeholder="Enter Confirm Password" name="password_confirmation"
                    id="password_confirmation" type="password">
                @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                <button class="btn btn-primary submitbtn" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $("#ediUser").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            email: {
                required: true,
                email: true
            },
            mobile: {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            password: {
                required: false,
                minlength: 8,
                maxlength: 50
            },
            password_confirmation: {
                required: false,
                minlength: 8,
                maxlength: 50,
                equalTo: "#new-password"
            },
            image: {
                extension: "jpg|jpeg|png",
                filesize: 2
            },
            date_of_birth: {
                required: true,
                date: true
            },
            gender: {
                required: true
            },
            address: {
                required: true,
                minlength: 10,
                maxlength: 255
            },
            shop_name: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            shop_address: {
                required: true,
                minlength: 10,
                maxlength: 255
            },
            aadhar_no: {
                required: true,
                digits: 12
            },
            pan_no: {
                required: true,
                pattern: /[A-Z]{5}[0-9]{4}[A-Z]{1}/
            },
            aadhar_doc: {
                extension: "jpg|jpeg|png|pdf",
                filesize: 2
            },
            pan_doc: {
                extension: "jpg|jpeg|png|pdf",
                filesize: 2
            },
            bank_proof_doc: {
                extension: "jpg|jpeg|png|pdf",
                filesize: 2
            },
            bank_name: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            bank_account_number: {
                required: true,
                minlength: 8,
                maxlength: 20,
                number: true
            },
            bank_ifsc_code: {
                required: true,
                pattern: /^[A-Z]{4}0[A-Z0-9]{6}$/
            }
        },
        messages: {
            name: {
                required: "Please enter name",
            },
            email: {
                required: "Please enter Email",
            },
            mobile: {
                required: "Please enter Mobile number",
            },
            image: {
                extension: "Supported Format Only : jpg, jpeg, png"
            },
            date_of_birth: {
                required: "Please select date of birth",
                date: "Please enter valid date"
            },
            gender: {
                required: "Please select gender"
            },
            address: {
                required: "Please enter address",
                minlength: "Address must be at least 10 characters",
                maxlength: "Address cannot exceed 255 characters"
            },
            shop_name: {
                required: "Please enter shop name",
                minlength: "Shop name must be at least 2 characters",
                maxlength: "Shop name cannot exceed 100 characters"
            },
            shop_address: {
                required: "Please enter shop address",
                minlength: "Shop address must be at least 10 characters",
                maxlength: "Shop address cannot exceed 255 characters"
            },
            aadhar_no: {
                required: "Please enter Aadhar number",
                digits: "Aadhar number must be 12 digits"
            },
            pan_no: {
                required: "Please enter PAN number",
                pattern: "Please enter valid PAN number (e.g., ABCDE1234F)"
            },
            aadhar_doc: {
                required: "Please upload Aadhar document",
                extension: "Supported formats: jpg, jpeg, png, pdf"
            },
            pan_doc: {
                required: "Please upload PAN document",
                extension: "Supported formats: jpg, jpeg, png, pdf"
            },
            bank_proof_doc: {
                required: "Please upload bank proof document",
                extension: "Supported formats: jpg, jpeg, png, pdf"
            },
            bank_name: {
                required: "Please enter bank name",
                minlength: "Bank name must be at least 2 characters",
                maxlength: "Bank name cannot exceed 100 characters"
            },
            bank_account_number: {
                required: "Please enter account number",
                minlength: "Account number must be at least 8 digits",
                maxlength: "Account number cannot exceed 20 digits",
                number: "Please enter valid account number"
            },
            bank_ifsc_code: {
                required: "Please enter IFSC code",
                pattern: "Please enter valid IFSC code (e.g., ABCD0123456)"
            }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.col-lg-4, .col-md-6').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
</script>
@endsection