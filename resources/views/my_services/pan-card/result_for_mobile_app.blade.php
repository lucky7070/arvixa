<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pan Card Responce</title>
    <style>
        *,
        *::after,
        *::before {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100vh;
        }

        .container {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            height: 400px;
            width: 320px;
            background: #f2f2f2;
            overflow: hidden;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 0 10px 8px #d0d0d0;
        }

        .content {
            position: absolute;
            top: 50%;
            transform: translatey(-50%);
            color: black;
            padding: 20px;
        }

        p {
            font-weight: 300;
        }

        .info-msg,
        .success-msg,
        .warning-msg,
        .error-msg {
            margin: 10px 0;
            padding: 10px;
            border-radius: 3px 3px 3px 3px;
        }

        .success-msg {
            color: #270;
            background-color: #DFF2BF;
            border-bottom: 2px solid;
        }

        .error-msg {
            color: #D8000C;
            background-color: #FFBABA;
            border-bottom: 2px solid;
        }

        .btn {
            border: none;
            color: #FFFFFF;
            padding: 10px 32px;
            text-align: center;
            -webkit-transition-duration: 0.4s;
            transition-duration: 0.4s;
            margin: 8px 0 !important;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            font-weight: 500;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

        .btn-success {
            background-color: #4CAF50;
        }

        .btn-error {
            background-color: #D8000C;
        }

        .txn-id {
            box-shadow: inset 0 0 0 0 #b5b7b8;
            color: #a9b5b9;
            margin: 0 -.25rem;
            padding: 0 .25rem;
        }

        .text-dark {
            color: #5d5f5f;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="content">
            <h3 class="txn-id">Transaction Id: <span class="text-dark">{{ $txnId }}</span></h3>
            <p class="{{ $type }}-msg"> {{ $message }}</p>
            <button class="btn btn-{{ $type }}">Go Back</button>
        </div>
    </div>
</body>

</html>