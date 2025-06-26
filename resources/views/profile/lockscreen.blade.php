<!DOCTYPE html>
<html lang="en">

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
    <link href="{{ asset('assets/css/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/main.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/structure.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/authentication/auth-cover.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />

</head>

<body class="form">
    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <div class="auth-container d-flex h-100">
        <div class="container mx-auto align-self-center">
            <div class="row">
                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center mx-auto">
                    <div class="card mt-3 mb-3">
                        <div class="card-body">
                            <form action="{{ url($path.'/lock') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="media mb-4">
                                            <div class="avatar avatar-lg me-3">
                                                <img alt="avatar" src="{{  asset('storage/' . $user['image']) }}"
                                                    class="rounded-circle">
                                            </div>
                                            <div class="media-body align-self-center">
                                                <h3 class="mb-0">{{ $user['name'] }}</h3>
                                                <p class="mb-0">Enter your password to unlock your ID</p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label">Password</label>
                                            <input name="password" type="password" class="form-control" min="2"
                                                max="100" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-4">
                                            <button class="btn btn-secondary w-100">UNLOCK</button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-4">
                                            <a href="{{ url($path.'/logout') }}" class="btn btn-danger w-100">Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>

@include('partial.toastr')