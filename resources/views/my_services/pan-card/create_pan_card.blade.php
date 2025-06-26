@extends('layouts.retailer_app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Pan Cards </h5>
            <a href="{{ route('pan-card') }}" class="btn btn-primary">
                <i class="fa fa-arrow-left me-1"></i>
                Go Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label" for="mobile">Mobile</label>
                <input class="form-control" id="mobile" type="text" name="mobile"
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
        <hr class="my-1" />
        <form action="{{ request()->url() }}" id="add" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-4 mt-2">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" />
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mt-2">
                    <label class="form-label" for="middle_name">Middle Name</label>
                    <input class="form-control" id="middle_name" type="text" name="middle_name"
                        value="{{ old('middle_name') }}" />
                    @error('middle_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-4 mt-2">
                    <label class="form-label" for="last_name">Last Name</label>
                    <input class="form-control" id="last_name" type="text" name="last_name"
                        value="{{ old('last_name') }}" />
                    @error('last_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" />
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-lg-6 mt-2">
                    <label class="form-label" for="phone">Phone</label>
                    <input class="form-control" id="phone" type="text" name="phone" value="{{ old('phone') }}" />
                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-lg-4 mt-2">
                    <label class="form-label" for="gender">Gender</label>
                    <select class="form-control" id="gender" name="gender" value="{{ old('gender') }}">
                        <option value="N">Please Select</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                        <option value="T">Transgender</option>
                    </select>
                    @error('gender')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                @if(request('type') || old('kyc_type'))
                <div class="col-lg-4 mt-2">
                    <label class="form-label" for="kyc_type">KYC Type</label>
                    <input class="form-control text-dark" type="text" value="E-Sign Mode" readonly>
                    <input type="hidden" id="kyc_type" name="kyc_type" value="E">
                    <small id="kyc_typeHelp" class="form-text text-primary">
                        With E-Sign Option you have to upload KYC Document.
                    </small>
                    @error('gender')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                @endif

                <div class="col-lg-4 mt-2">
                    <label class="form-label" for="dob">Date of Birth</label>
                    <input class="form-control" id="dob" type="date" name="dob" value="{{ old('dob') }}" />
                    @error('dob')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
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
    $(function () {
        $("#add").validate({
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                name: {
                    minlength: 2,
                    maxlength: 100
                },
                middle_name: {
                    minlength: 2,
                    maxlength: 100
                },
                last_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                email: {
                    required: true,
                    email: true,
                    customEmail: true,
                    minlength: 2,
                    maxlength: 100
                },
                phone: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10
                },
                gender: {
                    required: true,
                },
                dob: {
                    required: true,
                    date: true,
                    maxDate: true,
                    minDate: true
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                middle_name: {
                    required: "Please enter middle name",
                },
                last_name: {
                    required: "Please enter last name",
                },
                email: {
                    required: "Please enter Email",
                },
                phone: {
                    required: "Please enter phone number",
                },
                gender: {
                    required: "Please select gender.",
                },
                dob: {
                    maxDate: 'Must be today date or less',
                    minDate: 'Must be after : 01 Jan 1901.',
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

            $('#phone').val(mobile)
            btn.find('.fa-sync').addClass('fa-spin')
            $.post("{{ route('customer.find') }}", { mobile }, function (data) {
                btn.find('.fa-sync').removeClass('fa-spin')
                $(btn).parents().eq(2).find('input[id="mobile"]').removeClass('is-invalid');
                if (data.status) {
                    document.forms['add']['name'].value = (data.data && data.data.first_name) ? data.data.first_name : "";
                    document.forms['add']['middle_name'].value = (data.data && data.data.middle_name) ? data.data.middle_name : "";
                    document.forms['add']['last_name'].value = (data.data && data.data.last_name) ? data.data.last_name : "";
                    document.forms['add']['email'].value = (data.data && data.data.email) ? data.data.email : "";
                    document.forms['add']['phone'].value = (data.data && data.data.mobile) ? data.data.mobile : "";
                    document.forms['add']['dob'].value = (data.data && data.data.dob) ? data.data.dob : "";
                    var gender = '';
                    if (data.data && data.data.gender) {
                        switch (parseInt(data.data.gender)) {
                            case 1: gender = "M"; break;
                            case 2: gender = "F"; break;
                            case 3: gender = "T"; break;
                            default: gender = "N"; break;
                        }
                    }
                    document.forms['add']['gender'].value = gender;
                    $(btn).parents().eq(2).find('input[id="mobile"]').addClass('is-valid');
                } else {
                    toastr.error(data.message)
                }
            })

        })
    });
</script>

@endsection