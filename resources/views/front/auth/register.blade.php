@extends('front.layouts.main')

@section('main_content')
<div class="container-xl">
    <div class="row my-5">
        <div class="col-md-6 py-lg-5 p-8 text-center d-none d-lg-flex justify-content-center align-items-center">
            <img src="{{ asset('assets/img/project-start1.png') }}" alt="" class="img-fluid" style="max-width: 350px;">
        </div>
        <div class="col-md-6 py-lg-5 p-8 login-box p-5">

            <form method="POST" action="{{ route('register') }}" novalidate="novalidate">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="text-theme-info">Sign Up - <span class="text-secondary fw-bold">Customer</span></h2>
                        <p>Enter your email and password to register</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="input-group">
                            <span class="fa fa-user"></span>
                            <input placeholder="First Name" id="first_name" type="text" class="form-control "
                                name="first_name" value="{{ old('first_name') }}" required="" autocomplete="first_name"
                                autofocus="">
                            <input placeholder="Middle" id="middle_name" type="text" class="form-control "
                                name="middle_name" value="{{ old('middle_name') }}">
                            <input placeholder="Last" id="last_name" type="text" class="form-control " name="last_name"
                                value="{{ old('last_name') }}">
                            @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            @error('middle_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-group">
                            <span class="fa fa-envelope"></span>
                            <input class="form-control " type="email" name="email" required="" autocomplete="email"
                                placeholder="Email Address" value="{{ old('email') }}">
                        </div>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="input-group">
                            <span class="fa fa-phone"></span>
                            <input class="form-control " type="text" name="mobile" required="" id="mobile"
                                placeholder="Mobile" value="{{ old('mobile') }}">
                        </div>
                        @error('mobile')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!-- <div class="col-md-4 mb-3 text-end">
                        <button class="btn btn-theme-info" type="button" id="sendOtp">Send OTP</button>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="fa-solid fa-lock-open"></span>
                                <input class="form-control " type="text" name="otp" required="" id="otp"
                                    placeholder="OTP" value="" maxlength="4">
                            </div>
                        </div>
                    </div> -->
                    <div class="col-12">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="fa fa-key"></span>
                                <input class="form-control " type="password" name="password" placeholder="Password"
                                    id="new-password">
                            </div>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="fa fa-key"></span>
                                <input class="form-control " type="password" name="password_confirmation"
                                    placeholder="Confirm Password" autocomplete="current-password">
                            </div>
                            @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <div class="form-check form-check-primary form-check-inline-">
                                <input name="terms" class="form-check-input me-3" type="checkbox" value="1"
                                    id="form-check-default" @checked(old('terms')==1 )>
                                <label class="form-check-label" for="form-check-default">
                                    I agree the <a href="{{ route('terms_and_condition') }}"
                                        class="text-primary text-decoration-none">
                                        Terms and Conditions
                                    </a>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-4">
                            <button class="btn btn-theme-info px-4" type="submit" name="submit">SIGN UP</button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="text-center">
                            <p class="mb-0">Already have an account ?
                                <a href="{{ route('login') }}" class="text-theme-info text-decoration-none">Log
                                    In</a>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/custom-methods.js') }}"></script>
<script>
    $(function () {
        $("form").validate({
            rules: {
                first_name: {
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
                otp: {
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 6
                },
                password: {
                    required: true,
                    minlength: 8,
                    maxlength: 50
                },
                password_confirmation: {
                    required: true,
                    minlength: 8,
                    maxlength: 50,
                    equalTo: "#new-password"
                },
                terms: {
                    required: true,
                },
            },
            messages: {
                first_name: {
                    required: "Please enter name",
                },
                email: {
                    required: "Please enter Email",
                },
                mobile: {
                    required: "Please enter Mobile number",
                },
                otp: {
                    required: "Please enter OTP",
                },
                password: {
                    required: "Please enter Password",
                },
                password_confirmation: {
                    required: "Please enter Confirm Password",
                },
                terms: {
                    required: "Please select Terms and Conditions checkbox",
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "terms") {
                    error.insertAfter(".form-check");
                } else if (element.parent().hasClass('input-group')) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $('#sendOtp').on('click', function () {
            var mobile = $('#mobile').val();
            if (!mobile) {
                return toastr.error('Please enter mobile number.');
            }

            $('#sendOtp').prop('disabled', true)
            $.ajax({
                url: "{{ url('api/retailer/send-otp') }}",
                type: 'POST',
                data: { mobile, is_register: 1 },
                headers: {
                    'x-api-key': "{{ config('ayt.secret_token') }}"
                },
                dataType: 'json',
                success: function (data) {
                    if (data.status === true) {
                        setTimeout(() => {
                            $('#sendOtp').prop('disabled', false)
                        }, 20000);
                        toastr.success(data.message);
                    }
                    else {
                        toastr.error(data.message);
                    }
                },
                error: function (request, status, error) {
                    toastr.error(request.responseJSON.message);
                }
            });
        })
    })
</script>
@endsection