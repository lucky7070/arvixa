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
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />
    <style>
        body::before {
            height: 0;
        }
    </style>
</head>

<body>
    <div class="bg-primary min-vh-100">
        <div class="container">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-md-6">
                    <div class="card text-start">
                        <div class="card-body">
                            @if(Session::has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                                {{ Session::get('error') }}
                            </div>
                            @endif

                            <form action="" method="post" id="search">
                                @csrf
                                <div class="mb-3">
                                    <label for="mobile" class="form-label">Search User :- </label>
                                    <input type="number" class="form-control" value="{{ request('mobile') }}"
                                        name="mobile" id="mobile" placeholder="Enter Mobile Number Here" required>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        Find User
                                    </button>
                                </div>
                            </form>
                            @if(!empty($data) && count($data))
                            <ul class="list-group list-group-numbered">
                                @foreach ($data as $row)
                                <li class="list-group-item">
                                    <b>{{ $row->name }} ({{ $row->type }})</b>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
    <script>
        $("#search").validate({
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                mobile: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10
                }
            },
            messages: {
                mobile: {
                    required: "Please enter Mobile number",
                }
            },
        });
        
    </script>
</body>

</html>