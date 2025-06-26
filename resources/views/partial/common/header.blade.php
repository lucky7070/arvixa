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
    <link href="{{ asset('assets/css/light/structure.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/perfect-scrollbar.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/dt-global_style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/datatables.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />
    <link href="{{ asset('assets/css/dark-sidebar.css') }}" rel="stylesheet" id="user-style-default" />
    @yield('css')
</head>

