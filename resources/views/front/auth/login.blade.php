@extends('front.layouts.main')

@section('main_content')
<div class="container-xl">
    <div class="row my-5">
        <div class="col-md-6 py-lg-5 p-8 text-center d-none d-lg-flex justify-content-center align-items-center">
            <img src="{{ asset('assets/img/project-start1.png') }}" alt="" class="img-fluid" style="max-width: 350px;">
        </div>
        <div class="col-md-6 py-lg-5 p-8 login-box p-5">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="row h-100">
                    <div class="col-md-12">
                        <h2 class="text-theme-info fw-bold">LOGIN</h2>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3 ">
                            <div class="input-group">
                                <span class="fa fa-user"></span>
                                <input class="form-control " type="text" name="email" autocomplete="email"
                                    placeholder="Mobile / Email address" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="fa-sharp fa-solid fa-key"></span>
                                <input class="form-control " type="password" name="password" placeholder="Password">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check form-check-primary form-check-inline">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    checked="checked">
                                <label class="form-check-label mb-0" for="remember">Remember me</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 text-end">
                            <a class="text-gray text-decoration-none" href="http://nsdl2.test/password/reset">Forgot
                                Password?</a>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-4">
                            <button class="btn btn-theme-info px-4" type="submit" name="submit">SIGN IN</button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="text-center">
                            <p class="mb-0">Dont't have an account ?
                                <a href="{{ route('register') }}" class="text-theme-info text-decoration-none">Sign
                                    Up</a>
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
                email: {
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
                password: {
                    required: "Please enter Password.",
                }
            },
            errorPlacement: function (error, element) {
                if (element.parent().hasClass('input-group')) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    })
</script>
@endsection