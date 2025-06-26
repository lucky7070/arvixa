@php
$app_data = \App\Models\Setting::where('setting_type', 1)->get()->toArray();
$site_settings = array_combine(array_column($app_data,'setting_name'),array_column($app_data,'filed_value'));
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>{{ $site_settings['application_name'] }} :: Error 404</title>

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
    <link href="{{ asset('assets/css/light/main.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/structure.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        body:before {
            display: none;
        }

        body.maintanence {
            color: #888ea8;
            height: 100%;
            font-size: 0.875rem;
            background: #fafafa;
            background-image: linear-gradient(to bottom, #a8edea 0%, #fed6e3 100%);
        }

        .min-vh-80 {
            min-height: 80vh !important;
        }

        .py-vh-10 {
            padding-top: 10vh !important;
            padding-bottom: 10vh !important;
        }

        .maintanence .maintanence-hero-img img {
            max-width: 100px;
            width: 100px;
        }
    </style>

</head>

<body class="maintanence text-center">

    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <div class="container py-vh-10">
        <div class="row min-vh-80 justify-content-center align-items-center">
            <div class="col-md-8">
                <div class="maintanence-hero-img text-center mb-4">
                    <a href="index-2.html">
                        <img alt="logo" src="{{ asset('storage/' . $site_settings['logo']) }}" class="theme-logo">
                    </a>
                </div>
                <h1 class="error-title">Under Maintenance</h1>
                <p class="fs-4">Thank you for visiting us.</p>
                <p class="fs-6">We are currently working on making some improvements <br /> to give you better user
                    experience.</p>
                <p class="fs-6">Please visit us again shortly.</p>
                <a href="{{ url('') }}" class="btn btn-dark mt-3">Go to Home Page</a>
            </div>
        </div>
    </div>

</body>

</html>