<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>{{ $site_settings['application_name'] }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

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
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/owl.carousel.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/front.css') }}" rel="stylesheet" />
    @yield('css')
</head>