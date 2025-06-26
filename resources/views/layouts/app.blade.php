<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('partial.common.header')

<body class="layout-sidebar layout-boxed dark">
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
            <a href="javascript:void(0);" class="sidebarCollapse">
                <i class="fa-sharp fa-solid fa-bars fs-4"></i>
            </a>
            <div class="search-animated toggle-search-"> </div>

            <ul class="navbar-item flex-row ms-lg-auto ms-0">
                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-container">
                            <div class="avatar avatar-sm avatar-indicators avatar-online">
                                <img alt="avatar" src="{{ asset('storage/' . Auth::user()->image) }}"
                                    class="rounded-2 profile-img">
                            </div>
                        </div>
                    </a>

                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">
                                <div class="me-2"> </div>
                                <div class="media-body">
                                    @if (Auth::check())
                                    <span class="dropdown-item fw-bold text-warning">
                                        <h5>{{ Auth::user()->name }}</h5>
                                        <p>Admin</p>
                                    </span>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <a href="{{ route('profile') }}">
                                <i class="fa-duotone fa-user me-1"></i>
                                <span>Profile</span>
                            </a>
                        </div>
                        <div class="dropdown-item">
                            <a href="{{ route('lock') }}">
                                <i class="fa-duotone fa-lock"></i>
                                <span>Lock Screen</span>
                            </a>
                        </div>
                        <div class="dropdown-item">
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa-regular fa-arrow-right-from-bracket me-1"></i>
                                <span>Log Out</span>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </a>
                        </div>
                    </div>

                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container " id="container">
        <div class="overlay"></div>
        <div class="cs-overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <div class="sidebar-wrapper sidebar-theme">
            @include('partial.sidebar')
        </div>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <!-- ===============================================-->
            <!--    Main Content-->
            <!-- ===============================================-->
            <main class="layout-px-spacing mt-4">
                <div class="page-meta mb-3">
                    <!-- @include('partial.common.breadcrumb') -->
                </div>
                <div class="middle-content container-xxl p-0">
                    @yield('content')
                </div>
            </main>
            <!-- ===============================================-->
            <!--    End of Main Content-->
            <!-- ===============================================-->

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

        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!-- END MAIN CONTAINER -->
    @include('partial.common.footer')

</body>

</html>