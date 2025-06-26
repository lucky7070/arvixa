<footer class="bg-theme pt-3 pt-lg-5">
    <div class="container">
        <div class="row text-white pb-3">
            <div class="col-md-5 col-12 mb-md-0 mb-4 text-center text-lg-start">
                <img src="{{ asset('storage/'.$site_settings['logo']) }}" alt="{{ $site_settings['application_name'] }}"
                    class="footer-logo mb-2" />
                <p class="footer-logo-text text-justify pe-3">
                    {{ $site_settings['tagline'] }}
                </p>
                <div class="d-flex flex-column flex-lg-row  gap-3 align-items-center">
                    <h5>Follow Us</h5>
                    <ul class="d-flex align-items-center gap-3 p-0 social-icons-footer mb-0">
                        @if($site_settings['twitter'])
                        <li>
                            <a target=" _blank" rel="noreferrer" href="{{ $site_settings['twitter'] }}">
                                <i class="fa-brands fa-twitter"></i>
                            </a>
                        </li>
                        @endif
                        @if($site_settings['facebook'])
                        <li>
                            <a target="_blank" rel="noreferrer" href="{{ $site_settings['facebook'] }}">
                                <i class="fa-brands fa-facebook"></i>
                            </a>
                        </li>
                        @endif
                        @if($site_settings['instagram'])
                        <li>
                            <a target="_blank" rel="noreferrer" href="{{ $site_settings['instagram'] }}">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        </li>
                        @endif
                        @if($site_settings['linkdin'])
                        <li>
                            <a target="_blank" rel="noreferrer" href="{{ $site_settings['linkdin'] }}">
                                <i class="fa-brands fa-linkedin"></i>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-md-3 col-12 mb-md-0 mb-3">
                <h4 class="text-uppercase fw-bold text-center text-lg-start">
                    Information
                </h4>
                <ul class="pt-0 list-unstyled">
                    <li class="text-center text-lg-start">
                        <a class="text-white text-decoration-none" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="text-center text-lg-start">
                        <a class="text-white text-decoration-none" href="{{ route('about') }}">About Us</a>
                    </li>
                    <li class="text-center text-lg-start">
                        <a class="text-white text-decoration-none" href="{{ route('terms_and_condition') }}">Terms &
                            Condition</a>
                    </li>
                    <li class="text-center text-lg-start">
                        <a class="text-white text-decoration-none" href="{{ route('privacy_policy') }}">Privacy
                            Policy</a>
                            
                            </li>
                    <li class="text-center text-lg-start">
                        <a class="text-white text-decoration-none" href="{{ route('refund_policy') }}">Refund
                            Policy</a>
                            
                    </li>
                    <li class="text-center text-lg-start">
                        <a class="text-white text-decoration-none" href="{{ route('our-services') }}">Services</a>
                    </li>
                    <li class="text-center text-lg-start">
                        <a class="text-white text-decoration-none" href="{{ route('testimonial') }}">Testimonial</a>
                    </li>
                    <li class="text-center text-lg-start">
                        <a class="text-white text-decoration-none" href="{{ route('contact') }}">Contact Us</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-4 col-12 mb-md-0 mb-3">
                <h4 class="text-uppercase fw-bold text-center text-lg-start">
                    Contact Details
                </h4>
                <ul class="pt-0 list-unstyled">
                    <li class="d-flex gap-3 align-items-center mb-1 mb-lg-3">
                        <i class="fa-solid fa-phone"></i>
                        <a class="text-white text-decoration-none" href="tel:{{ $site_settings['phone'] }}">
                            {{ $site_settings['phone'] }}
                        </a>
                    </li>
                    <li class="d-flex gap-3 align-items-center mb-1 mb-lg-3">
                        <i class="fa-solid fa-envelope"></i>
                        <a class="text-white text-decoration-none" href="mailto:{{ $site_settings['email'] }}">
                            {{ $site_settings['email'] }}
                        </a>
                    </li>
                    <li class="d-flex gap-3 align-items-start mb-1 mb-lg-3">
                        <i class="fa-solid fa-location"></i>
                        <p class="text-white mb-0">{{ $site_settings['address'] }}</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row border-top border-white">
            <div class="col-lg-6 my-0 my-lg-2">
                <p class="text-white text-center text-lg-start mb-0">{{ $site_settings['copyright'] }}</p>
            </div>
            <div class="col-lg-6 my-0 my-lg-2">
                <p class="text-white text-center text-lg-end mb-0">Delvelop By :
                    <a class="text-decoration-none text-white" href="http://arvixa.in"
                        target="_blank">Arvixa Technologies Private Limited. </a>
                </p>
            </div>
        </div>
    </div>
</footer>


<span class="back-to-top" style="display: none">
    <i class="fa-solid fa-arrow-up"></i>
</span>
<input name="config" type="hidden" value="{{ json_encode($config_front) }}">
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/js/front.js') }}"></script>
@yield('js')
@include('partial.toastr')