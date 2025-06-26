@extends('front.layouts.main')

@section('css')
<style>
    .input-group span {
        color: var(--theme-color-primary);
    }
</style>
@endsection

@section('main_content')
<div class="container-fluid bg-light-theme text-center py-4 text-theme">
    <h2 class="mb-1 fw-bolder">Contact Us</h2>
    <p class="mb-1">
        <a href="" class="text-decoration-none text-theme">Home</a> / Contact Us
    </p>
</div>

<div class="container my-5">
    <div class="card">
        <div class="card-body">
            <!-- <h2 class="text-center border-bottom border-5 pb-3 fw-bold text-theme mb-4">⬽ Contact Us ⤘</h2> -->
            <div class="w-100">
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <div class="bg-theme p-3 text-white rounded-3">
                            <div class="sec-title mb-3">
                                <span class="text-white">Let's Talk</span>
                                <h4 class="text-white">Speak With Experts.</h4>
                            </div>
                            <div class="d-flex mb-3 gap-2">
                                <div class="p-2 me-2 bg-white text-theme fs-6 rounded-circle contact-icon">
                                    <i class="fa fa-envelope"></i>
                                </div>
                                <div class="address-text">
                                    <label class="mb-0 d-block">Email:</label>
                                    <a class="text-white" href="mailto:{{ $site_settings['email'] }}">{{
                                        $site_settings['email'] }}</a>
                                </div>
                            </div>
                            <div class="d-flex mb-3 gap-2">
                                <div class="p-2 me-2 bg-white text-theme fs-6 rounded-circle contact-icon">
                                    <i class="fa fa-phone"></i>
                                </div>
                                <div class="address-text">
                                    <label class="mb-0 d-block">Phone:</label>
                                    <a class="text-white" href="tel:{{ $site_settings['phone'] }}">{{
                                        $site_settings['phone'] }}</a>
                                </div>
                            </div>
                            <div class="d-flex mb-3 gap-2">
                                <div class="p-2 me-2 bg-white text-theme fs-6 rounded-circle contact-icon">
                                    <i class="fa fa-map-marker"></i>
                                </div>
                                <div class="address-text">
                                    <label class="mb-0 d-block">Address:</label>
                                    <div class="desc">{{ $site_settings['address'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 mb-3">
                        <div class="w-100 h-100 my-2">
                            <form id="contact-form" action="{{ request()->url() }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <div class="md-form mb-0">
                                            <div class="input-group">
                                                <span class="fa fa-user text-theme"></span>
                                                <input type="text" id="name" name="name"
                                                    class="form-control form-control-lg" value="{{ old('name') }}"
                                                    placeholder="Your Name">
                                            </div>
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="md-form mb-0">
                                            <div class="input-group">
                                                <span class="fa fa-phone text-theme"></span>
                                                <input type="text" id="phone" name="phone"
                                                    class="form-control form-control-lg" value="{{ old('phone') }}"
                                                    placeholder="Your Phone">
                                            </div>
                                            @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="md-form mb-0">
                                            <div class="input-group">
                                                <span class="fa fa-envelope text-theme"></span>
                                                <input type="text" id="email" name="email"
                                                    class="form-control form-control-lg" value="{{ old('email') }}"
                                                    placeholder="Your Email">
                                            </div>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="md-form">
                                            <div class="input-group">
                                                <textarea type="text" id="message" name="message" rows="3"
                                                    class="form-control form-control-lg md-textarea"
                                                    placeholder="Your Message">{{ old('message') }}</textarea>
                                            </div>
                                            @error('message')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <button class="btn btn-theme">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card my-3">
        <div class="card-body">
            <h2 class="text-center border-bottom border-5 pb-3 fw-bold text-theme mb-4">
                Frequently Asked Questions
            </h2>
            <div class="row">
                @if($faqs->count())
                <div class="col-lg-12 mb-3">
                    <div class="accordion" id="accordionExample">
                        @foreach($faqs as $key => $row)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading_{{ $key }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse_{{ $key }}" aria-expanded="false"
                                    aria-controls="collapse_{{ $key }}">
                                    {{ $row->question }}
                                </button>
                            </h2>
                            <div id="collapse_{{ $key }}" class="accordion-collapse collapse"
                                aria-labelledby="heading_{{ $key }}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    {{ $row->answer }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="col-md-12 mb-3">
                    <p class="mb-0 text-danger text-center">No FAQs Found.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/custom-methods.js') }}"></script>
<script>
    $(function () {
        $("#contact-form").validate({
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
                phone: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10
                },
                message: {
                    required: true,
                    minlength: 10,
                },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                email: {
                    required: "Please enter Email",
                },
                phone: {
                    required: "Please enter phone number",
                },
                message: {
                    required: "Please enter your message.",
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
    })
</script>

@endsection