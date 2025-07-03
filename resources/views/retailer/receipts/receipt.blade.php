<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            color: #333;
            font-size: 20px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        td,
        th {
            width: 50%;
        }

        .disclaimer {
            margin-top: 20px;
            font-size: 12px;
            line-height: 1.5;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .note {
            font-style: italic;
            margin-top: 10px;
            font-size: 12px;
        }
    </style>

</head>

<body>
    <table class="without-border" style="width: 100%;">
        <tbody>
            <tr>
                <td>
                    <img width="150" src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/img/bharat-connect.svg'))) }}" alt="">
                </td>
                <td style="text-align:right;">
                    <img width="150" src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('storage/'.str_replace('/', DIRECTORY_SEPARATOR, $site_settings['logo'])))) }}" alt="">
                </td>
            </tr>
        </tbody>
    </table>
    <h1># Payment Receipt</h1>
    <table>
        <tr>
            <th colspan="2" style="text-align: center;">Consumer Details</th>
        </tr>
        <tr>
            <td><strong>Txn Date</strong></td>
            <td>{{ $bill->created_at->format('F j, Y \a\t h:i A') }}</td>
        </tr>
        <tr>
            <td><strong>Consumer Name</strong></td>
            <td>{{ $bill->consumer_name }}</td>
        </tr>
        <tr>
            <td><strong>Amount</strong></td>
            <td>{{ number_format($bill->bill_amount, 2) }}</td>
        </tr>
        <tr>
            <td><strong>K No</strong></td>
            <td>{{ $bill->consumer_no }}</td>
        </tr>
        <tr>
            <td><strong>Txn No.</strong></td>
            <td>{{ $bill->transaction_id }}</td>
        </tr>
        <tr>
            <td><strong>Service</strong></td>
            <td>{{ $service ?? '--' }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th colspan="3" style="text-align: center;">Kiosk Details</th>
        </tr>
        <tr>
            <td><strong>Kiosk Name</strong></td>
            <td>{{ $bill->retailer->name }}</td>
        </tr>
        <tr>
            <td><strong>Kiosk Mobile</strong></td>
            <td>{{ $bill->retailer->mobile }}</td>
        </tr>
        <tr>
            <td><strong>Kiosk Address</strong></td>
            <td>{{ $bill->retailer->address  ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="disclaimer">
        <strong>Disclaimer:</strong> Thank you for your payment. Your payment will be updated at the biller's end within 2-3 working days. If you have paid your bill before the due date, no late payment fees would be charged to your bill. If you have paid your bill partially, you could be charged a late payment fees by the biller. Any excess payment made would be adjusted with the next bill due. Partial payments would be liable to late payment fees.
    </div>

    <div class="note">
        <strong>Note:</strong> This is computer generated invoice no physical signature required.
    </div>
</body>

</html>