<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>{{ $site_settings['application_name'] }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link href="{{ asset('assets/css/light/loader.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/js/loader.js') }}"></script>

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/' . $site_settings['favicon']) }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/' . $site_settings['favicon']) }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/' . $site_settings['favicon']) }}" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $site_settings['favicon']) }}" />

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/main.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/horizontal-light-menu/structure.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/perfect-scrollbar.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/dt-global_style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/datatables.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />
    <link href="{{ asset('assets/css/retailer.css') }}" rel="stylesheet" id="user-style-default" />
    <link href="{{ asset('assets/css/sweetalert2.min.css') }}" rel="stylesheet" id="user-style-default" />

    <style>
        #accordionExample {
            overflow: visible !important;
        }

        .pay-more {
            top: 68px !important;
        }
    </style>
    @yield('css')
</head>

<body class="layout-boxed enable-secondaryNav retailer">
    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div class="header-container container-xxl">
        <header class="header navbar navbar-expand-sm expand-header">
            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-menu">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </a>

            <ul class="navbar-item theme-brand flex-row text-center">
                <li class="nav-item theme-logo d-block d-md-none">
                    <a href="{{ route('retailer.dashboard') }}">
                        <img src="{{ asset('storage/' . $site_settings['logo']) }}" class="navbar-logo" alt="logo">
                        {{-- $site_settings['application_name'] --}}
                    </a>
                </li>
                <li class="nav-item theme-text">
                    <span class="nav-link d-flex gap-2 align-items-center">
                        <i class="fa-solid fa-user-headset fs-5 text-blue"></i>
                        <span class="fs-6 fw-bold text-200">
                            {{ $site_settings['helpline_numbers'] }}
                        </span>
                    </span>
                </li>
                <li class="nav-item theme-text">
                    <span class="nav-link d-flex gap-2 align-items-center">
                        <i class="fa-solid fa-comments-dollar fs-5 text-blue"></i>
                        <span class="fs-6 fw-bold text-200">
                            {{ $site_settings['helpline_numbers_accounting'] }}
                        </span>
                    </span>
                </li>
            </ul>

            <ul class="navbar-item flex-row ms-lg-auto ms-0 action-area">
                <li class="nav-item theme-text">
                    <ul class="list-unstyled d-flex gap-1 social-icon">
                        <li class="bg-blue rounded-circle">
                            <a href="{{ $site_settings['twitter'] }}" rel="noreferrer" target="_blank"
                                class=" text-white text-decoration-none">
                                <i class="fa-brands fa-twitter "></i>
                            </a>
                        </li>
                        <li class="bg-blue rounded-circle">
                            <a href="{{ $site_settings['facebook'] }}" rel="noreferrer" target="_blank"
                                class=" text-white text-decoration-none">
                                <i class="fa-brands fa-facebook "></i>
                            </a>
                        </li>
                        <li class="bg-blue rounded-circle">
                            <a href="{{ $site_settings['instagram'] }}" rel="noreferrer" target="_blank"
                                class=" text-white text-decoration-none">
                                <i class="fa-brands fa-instagram "></i>
                            </a>
                        </li>
                        <li class="bg-blue rounded-circle">
                            <a href="{{ $site_settings['linkdin'] }}" rel="noreferrer" target="_blank"
                                class=" text-white text-decoration-none">
                                <i class="fa-brands fa-linkedin "></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-container">
                            <div class="avatar avatar-sm avatar-indicators avatar-online">
                                <img alt="avatar"
                                    src="{{ asset('storage/' . Auth::guard('retailer')->user()->image) }}"
                                    class="rounded-circle">
                            </div>
                        </div>
                    </a>
                    @if (Auth::guard('retailer')->check())
                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">
                                <div class="media-body">
                                    <h5>{{ Auth::guard('retailer')->user()->name }}</h5>
                                    <p class="text-secondary fw-bold">
                                        {{ Auth::guard('retailer')->user()->mobile }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <a href="{{ route('retailer.profile') }}">
                                <i class="fa fa-user me-2"></i>
                                <span>Profile</span>
                            </a>
                        </div>

                        <div class="dropdown-item">
                            <a href="{{ route('retailer.lock') }}">
                                <i class="fa-duotone fa-lock me-2"></i>
                                <span>Lock Screen</span>
                            </a>
                        </div>
                        <div class="dropdown-item">
                            <a href="{{ route('retailer.logout') }}">
                                <i class="fa-regular fa-arrow-right-from-bracket me-2"></i>
                                <span>Log Out</span>
                            </a>
                        </div>
                    </div>
                    @endif
                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <div class="sidebar-wrapper sidebar-theme">
            <nav id="sidebar">
                <ul class="list-unstyled menu-categories" id="accordionExample">
                    <li class="menu">
                        <a href="{{ route('retailer.dashboard') }}" class="me-5">
                            <img src="{{ asset('storage/' . $site_settings['logo']) }}"
                                class="navbar-logo brightness">
                        </a>
                    </li>

                    <li class="menu @routeis('retailer.dashboard')
                        active
                        @endrouteis">
                        <a href="{{ route('retailer.dashboard') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-regular fa-house"></i>
                                <span>Dashboard</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu @routeis('retailer.wallet')
                        active
                        @endrouteis">
                        <a href="{{ route('retailer.wallet') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-wallet"></i>
                                <span>My Wallet </span>
                            </div>
                        </a>
                    </li>
                    <li class="menu @routeis('retailer.request-money')
                        active
                        @endrouteis">
                        <a href="{{ route('retailer.request-money') }}" aria-expanded="false"
                            class="dropdown-toggle">
                            <div>
                                <i class="fa fa-inr"></i>
                                <span>Request Money</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu @routeis('retailer.request-money')
                        active
                        @endrouteis">
                        <a type="button" class="dropdown-toggle" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">
                            <div>
                                <i class="fa-solid fa-file-lines"></i>
                                <span>PanCard Report</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu @routeis('retailer.my-commission')
                        active
                        @endrouteis">
                        <a href="{{ route('retailer.my-commission') }}" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-file-lines"></i>
                                <span>My Commission</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu dropdown @routeis('retailer.electricity-bill') active @endrouteis">
                        <a href="#" class="dropdown-toggle d-flex align-items-center justify-content-start" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-file-lines me-2"></i>
                            <span>Bill Payment</span>
                        </a>
                        <ul class="dropdown-menu pay-more mt-5">
                            <li><a class="dropdown-item" href="{{ route('retailer.electricity-bill') }}">Electricity Bill</a></li>
                            <li><a class="dropdown-item" href="{{ route('retailer.water-bill') }}">Water Bill</a></li>
                            <li><a class="dropdown-item" href="{{ route('retailer.gas-bill') }}">Gas Bill</a></li>
                            <li><a class="dropdown-item" href="{{ route('retailer.lic-bill') }}">Lic Bill</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

        </div>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="middle-content container-xxl p-0">
                    <div class="row layout-top-spacing d-flex">
                        @if (request()->is('retailer/product/*') == false && request()->is('retailer/product') == false)
                        <div class="col-lg-3 col-md-12 col-sm-12 col-12 layout-spacing">
                            <div class="card rounded-4 wallet-card">
                                <div class="card-body">
                                    <div class="p-35">
                                        <div class="bg-blue p-2 rounded-3 d-flex gap-3">
                                            <div class="icon">
                                                <i class="fa-light fa-wallet fs-1"></i>
                                            </div>
                                            <div>
                                                <p class="text-white mb-0 fs-6">Balance</p>
                                                <h2 class="text-white mb-0 fw-bold">₹
                                                    {{ auth('retailer')->user()->user_balance }}
                                                </h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <a href="{{ route('retailer.wallet') }}" class="btn btn-primary mt-2"
                                            id="viewHistory">
                                            <i class="fa-solid fa-wallet"></i>
                                            <span>View History</span>
                                        </a>
                                    </div>

                                    <div class="mt-3">
                                        <div class="input-group">
                                            <div class="btn btn-primary" id="rs_btn">
                                                <span class="">RS.</span>
                                            </div>
                                            <input type="number" class="form-control" id="amount" name="amount">
                                        </div>
                                        <p id="span_informantion" class="instruction-text">Fill the Amount you want to add in a wallet, after scan QR and Pay !</p>
                                        <input type="hidden" id="payment_gateway" name="payment_gateway" value="{{ env('PAYMENT_GATEWAY_NAME') }}">
                                        <button url="{{ route('retailer.upi-payment') }}" type="button"
                                            class="btn btn-primary btn-large mt-3" id="addBalanceBtn">Show QR COde
                                        </button>
                                        <div id="errorMsg" class="text-danger" style="display: none;">Please enter a positive number.</div>

                                    </div>
                                    <div id="qrCodeContainer" class="w-100 mt-2 text-center"
                                        style="display: none;">
                                        <!-- QR code image will be inserted here -->
                                    </div>

                                    <ul class="list-group mt-3 mb-2">
                                        <li class="list-group-item">
                                            <h5 class="fw-bold text-dark mb-">Back Account Details</h5>
                                        </li>
                                        <li class="list-group-item">
                                            <h6 class="mb-1 fw-semibold">Bank Name</h6>
                                            <p class="mb-1">{{ $site_settings['company_bank_name'] }}</p>
                                        </li>
                                        <li class="list-group-item">
                                            <h6 class="mb-1 fw-semibold">Account Number</h6>
                                            <p class="mb-1">{{ $site_settings['company_account_number'] }}</p>
                                        </li>
                                        <li class="list-group-item">
                                            <h6 class="mb-1 fw-semibold">IFSC Code</h6>
                                            <p class="mb-1">{{ $site_settings['company_ifsc_code'] }}</p>
                                        </li>
                                        <li class="list-group-item">
                                            <h6 class="mb-1 fw-semibold">Bank Holder Name</h6>
                                            <p class="mb-1">{{ $site_settings['bank_holder_name'] }}</p>
                                        </li>
                                    </ul>
                                    <a href="{{ route('retailer.request-money') }}" class="btn btn-sm btn-primary">Request Money</a>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-9 col-md-12 col-sm-12 col-12 layout-spacing">
                            @yield('content')
                        </div>
                        @else
                        <div class="col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                            @yield('content')
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title fw-bold" id="exampleModalLabel">Select PanCard Type</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <a href="{{ route('pan-card', 'physical') }}" aria-expanded="false"
                        class="btn btn-primary w-100 mb-2">
                        Physical PanCard
                    </a>

                    <a href="{{ route('pan-card', 'digital') }}" aria-expanded="false"
                        class="btn btn-secondary w-100 mb-2">
                        Digital PanCard
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- ===============================================-->
    <!--    FOOTER      -->
    <!-- ===============================================-->
    <div class="footer-wrapper mt-0">
        <div class="footer-section f-section-1">
            <p class=""> {{ $site_settings['copyright'] }}</p>
        </div>
        <div class="footer-section f-section-2">
            <p class="">
                Delvelop By : <a href="http://arvixa.in" target="_lucky">Arvixa Technologies Private Limited.</a>
            </p>
        </div>
    </div>
    <!--  END CONTENT AREA  -->
    <!-- END MAIN CONTAINER -->
    @include('partial.common.footer')
</body>

</html>