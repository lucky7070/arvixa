@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="row">
        <div class="col-md-12 mb-3">
            <h2>Sign In as <span class="text-secondary">{{ ucwords(str_replace('_', ' ', $guard)) }}</span></h2>
            <p>Enter your email and password to login</p>
        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <input class="form-control @error('email') is-invalid @enderror" type="text" name="email"
                    autocomplete="email" placeholder="Mobile / Email address / User Id" value="{{ old('email') }}" />
                <input type="hidden" name="login_as" value="{{ $guard == 'admin' ? 'web' :  $guard }}">
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <input class="form-control @error('password') is-invalid @enderror" type="password" name="password"
                    placeholder="Password" />
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            <div class="mb-3">
                <div class="form-check form-check-primary form-check-inline">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked="checked" {{
                        old('remember') ? 'checked' : '' }} />
                    <label class="form-check-label mb-0" for="remember">Remember me</label>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="mb-3 text-end">
                @if (Route::has('forget.password'))
                <a class="fs--1" href="{{ route('forget.password', $guard) }}">Forgot Password?</a>
                @endif
            </div>
        </div>
        <div class="col-12">
            <div class="mb-4">
                <button class="btn btn-secondary w-100" type="submit" name="submit">LOG IN</button>
            </div>
        </div>

        <div class="col-12 mb-4">
            <div class="">
                <div class="seperator">
                    <hr>
                </div>
            </div>
        </div>
    </div>

</form>
@endsection


@section('js')
<script type="text/javascript">
    $("form").validate({
        rules: {
            email: {
                required: true,

            },
            login_as: {
                required: true,
            },
            password: {
                required: true,
                minlength: 6,
                maxlength: 50
            },
        },
        messages: {
            email: {
                required: "Please enter Email.",
            },
            login_as: {
                required: "Please select Login As.",
            },
            password: {
                required: "Please enter Password.",
            }
        },
    });
</script>
@endsection