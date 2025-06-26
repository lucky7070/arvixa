<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('partial.common.header')

<body>
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
    <div class="header-container container-xxl bg-light-primary">
        <header class="header navbar expand-header justify-content-between">
            <a href="javascript:void(0);" class="sidebarCollapse">
                <i class="fa-sharp fa-solid fa-bars fs-4"></i>
            </a>
            <span></span>
            <h6 class="text-primary fw-bold border border-dashed border-primary p-2 rounded-2 mb-0">
                <i class="fa-duotone fa-wallet me-1"></i>
                Balance : ₹ {{ auth()->guard('main_distributor')->user()->user_balance }}
            </h6>
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
            @include('partial.main_distributor_sidebar')
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