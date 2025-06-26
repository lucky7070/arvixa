<!doctype html>
<html lang="en">

@include('front.partial.header')

<body>
    <nav class="text-light w-100 top-nav bg-theme">
        <div class="container">
            <div class="d-flex py-2">
                <div class="">
                    <a href="tel:{{ $site_settings['phone'] }}" class="top-nav-links first">
                        <i class="fa fa-phone"></i>
                        <small class="d-none d-md-inline-block"> {{ $site_settings['phone'] }}</small>
                    </a>
                </div>
                <div class="">
                    <a href="mailto:{{ $site_settings['email'] }}" class="top-nav-links">
                        <i class="fa-solid fa-envelope"></i>
                        <small class="d-none d-md-inline-block">{{ $site_settings['email'] }}</small>
                    </a>
                </div>
                <div class="offset-2 d-flex align-items-center me-0 ms-auto social-size">
                    <ul class="m-0 p-0 list-unstyled d-flex gap-3 header">

                        @if($site_settings['twitter'])
                        <li>
                            <a href="{{ $site_settings['twitter'] }}" rel="noreferrer" target="_blank"
                                class="social-icons">
                                <i class="fa-brands fa-twitter"></i>
                            </a>
                        </li>
                        @endif
                        @if($site_settings['facebook'])
                        <li>
                            <a href="{{ $site_settings['facebook'] }}" rel="noreferrer" target="_blank"
                                class="social-icons">
                                <i class="fa-brands fa-facebook"></i>
                            </a>
                        </li>
                        @endif
                        @if($site_settings['instagram'])
                        <li>
                            <a href="{{ $site_settings['instagram'] }}" rel="noreferrer" target="_blank"
                                class="social-icons">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        </li>
                        @endif
                        @if($site_settings['linkdin'])
                        <li>
                            <a href="{{ $site_settings['linkdin'] }}" rel="noreferrer" target="_blank"
                                class="social-icons">
                                <i class="fa-brands fa-linkedin"></i>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top-custom bg-white" id="top-bar-custom">
        <div class="container">
            <a class="navbrand pe-10 navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('storage/'.$site_settings['logo']) }}" alt="Adiyogi Fintech" />
            </a>
            <div class="d-flex d-md-none align-items-center gap-4 justify-content-end" style="min-width: 130px;">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto gap-1 gap-lg-2">
                    <li class="nav-item"><a class="nav-link @routeis('home') active @endrouteis "
                            href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link @routeis('about') active @endrouteis "
                            href="{{ route('about') }}">About Us</a></li>
                    <li class="nav-item"><a class="nav-link @routeis('our-services') active @endrouteis "
                            href="{{ route('our-services') }}">Services</a></li>
                    <li class="nav-item"><a class="nav-link @routeis('testimonial') active @endrouteis "
                            href="{{ route('testimonial') }}">Testimonial</a></li>
                    <li class="nav-item"><a class="nav-link @routeis('contact') active  @endrouteis "
                            href="{{ route('contact') }}">Contact Us</a></li>
                </ul>

                <div class="d-flex gap-2 justify-content-lg-center nav-item">
                    @if(auth('retailer')->check())
                    <a class="btn btn-theme-secondary" href="{{ route('dashboard') }}">
                        <i class="fa-solid fa-home me-1"></i>
                        Dashboard
                    </a>
                    @else
                    <a class="btn btn-theme-secondary me-2" href="{{ route('join_us')}}">Join Us</a>
                    <div class="dropdown">
                        <a class="dropdown-toggle btn btn-theme" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-right-from-bracket me-1"></i>
                            Login
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('loginPage','retailer') }}">As Retailer</a></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('loginPage','distributor') }}">
                                    As Distributor</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('loginPage','main_distributor') }}">
                                    As Main Distributor </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    <main>
        @yield('main_content')
    </main>
    @include('front.partial.footer')
</body>

</html>