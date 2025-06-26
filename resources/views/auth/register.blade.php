@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="row">
        <div class="col-md-12 mb-3">

            <h2>Sign Up</h2>
            <p>Enter your email and password to register</p>

        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <input placeholder="Name" id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" required
                    autocomplete="email" placeholder="Email Address" value="{{ old('email') }}" />
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <input class="form-control @error('mobile') is-invalid @enderror" type="text" name="mobile" required
                    autocomplete="mobile" placeholder="Mobile" value="{{ old('mobile') }}" />
                @error('mobile')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <input class="form-control @error('password') is-invalid @enderror" type="password" name="password"
                    placeholder="Password" id="new-password" />
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password"
                    name="password_confirmation" placeholder="Confirm Password" autocomplete="current-password" />
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
                    <input name="terms" class="form-check-input me-3" type="checkbox" id="form-check-default">
                    <label class="form-check-label" for="form-check-default">
                        I agree the <a href="javascript:void(0);" class="text-primary">Terms and Conditions</a>
                    </label>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="mb-4">
                <button class="btn btn-secondary w-100">SIGN UP</button>
            </div>
        </div>

        <div class="col-12">
            <div class="text-center">
                @if (Route::has('login'))
                <p class="mb-0">Already have an account ?
                    <a class="text-warning" href="{{ route('login') }}">LOG IN</a>
                </p>
                @endif
            </div>
        </div>
    </div>
</form>
@endsection

@section('js')
<script type="text/javascript">
    $("form").validate({
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
            name: {
                required: "Please enter name",
            },
            email: {
                required: "Please enter Email",
            },
            mobile: {
                required: "Please enter Mobile number",
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
            }
            else {
                error.insertAfter(element);
            }
        }
    });
</script>
@endsection