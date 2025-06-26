<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Cabin|Ubuntu:700'>

    <style>
        body {
            background-color: #332851;
        }

        body .base {
            width: 100%;
            height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-direction: column;
            -webkit-tap-highlight-color: rgba(255, 255, 255, 0);
        }

        body .base h1 {
            -webkit-tap-highlight-color: rgba(255, 255, 255, 0);
            font-family: 'Ubuntu', sans-serif;
            text-transform: uppercase;
            text-align: center;
            font-size: 20vw;
            display: block;
            margin: 0;
            color: #9ae1e2;
            position: relative;
            z-index: 0;
            animation: colors 0.4s ease-in-out forwards;
            animation-delay: 1.7s;
        }

        body .base h1:before {
            content: "U";
            position: absolute;
            top: -9%;
            right: 40%;
            transform: rotate(180deg);
            font-size: 10vw;
            color: #f6c667;
            z-index: -1;
            text-align: center;
            animation: lock 0.2s ease-in-out forwards;
            animation-delay: 1.5s;
        }

        body .base h2 {
            font-family: 'Cabin', sans-serif;
            color: #9ae1e2;
            font-size: 3.5vw;
            margin: 0;
            text-transform: uppercase;
            text-align: center;
            animation: colors 0.4s ease-in-out forwards;
            animation-delay: 2s;
            -webkit-tap-highlight-color: rgba(255, 255, 255, 0);
        }

        body .base h5 {
            font-family: 'Cabin', sans-serif;
            color: #9ae1e2;
            font-size: 2vw;
            margin: 0;
            text-align: center;
            opacity: 0;
            animation: show 2s ease-in-out forwards;
            color: #ca3074;
            animation-delay: 3s;
            -webkit-tap-highlight-color: rgba(255, 255, 255, 0);
        }

        @keyframes lock {
            50% {
                top: -4%;
            }

            100% {
                top: -6%;
            }
        }

        @keyframes colors {
            50% {
                transform: scale(1.1);
            }

            100% {
                color: #ca3074;
            }
        }

        @keyframes show {
            100% {
                opacity: 1;
            }
        }

        .my-4 {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .btn {
            padding: 0.4375rem 1.25rem;
            text-shadow: none;
            font-size: 14px;
            color: #3b3f5c;
            font-weight: normal;
            white-space: normal;
            word-wrap: break-word;
            transition: 0.2s ease-out;
            touch-action: manipulation;
            border-radius: 6px;
            cursor: pointer;
            background-color: #e0e6ed;
            will-change: opacity, transform;
            transition: all 0.3s ease-out;
            -webkit-transition: all 0.3s ease-out;
        }

        .btn-rounded {
            -webkit-border-radius: 1.875rem;
            -moz-border-radius: 1.875rem;
            -ms-border-radius: 1.875rem;
            -o-border-radius: 1.875rem;
            border-radius: 1.875rem;
        }

        .btn:hover {
            color: #3b3f5c;
            background-color: #f1f2f3;
            border-color: #d3d3d3;
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
            box-shadow: none;
            -webkit-transform: translateY(-3px);
            transform: translateY(-3px);
        }

        .btn-outline-danger {
            border: 1px solid #dc3545 !important;
            color: #dc3545 !important;
            background-color: transparent;
            box-shadow: none;
        }

        .btn-outline-danger:hover {
            color: #fff !important;
            background-color: #e7515a !important;
            box-shadow: 0 10px 20px -10px rgba(231, 81, 90, 0.588) !important;
        }

        .text-decoration-none {
            text-decoration: none;
        }
    </style>
</head>

<body>

    <body>
        <div class="base">
            <h1 class="io">403</h1>
            <h2>Access forbidden</h2>
            <h5>(I'm sorry buddy...)</h5>
            <a href="{{ route('home') }}" class="btn btn-outline-danger btn-rounded my-4 text-decoration-none">
                Go To Home
            </a>
        </div>
    </body>

</html>