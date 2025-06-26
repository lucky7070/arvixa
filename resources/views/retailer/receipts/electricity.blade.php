<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
</head>
<body style="background-color:#f8f9fa; font-family: Arial, sans-serif; padding: 20px;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <tr>
            <td style="padding: 20px; background-color: #343a40; color: #ffffff; text-align: center;">
                <h2 style="margin: 0;">Payment Receipt</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <table width="100%" cellpadding="5" cellspacing="0" border="0" style="font-size: 14px; line-height: 1.6;">
                    <tr>
                        <td style="width: 50%; font-weight: bold;">Transaction ID:</td>
                        <td>{{ $bill->transaction_id }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">User ID:</td>
                        <td>{{ $bill->retailer->name }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Board ID:</td>
                        <td>{{ $bill->board->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Consumer No:</td>
                        <td>{{ $bill->consumer_no }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Consumer Name:</td>
                        <td>{{ $bill->consumer_name }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Bill No:</td>
                        <td>{{ $bill->bill_no }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Bill Amount:</td>
                        <td>{{ $bill->bill_amount }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Due Date:</td>
                        <td>{{ $bill->due_date->format('d-m-Y') }}</td>
                    </tr>
                    <!--<tr>-->
                    <!--    <td style="font-weight: bold;">Status:</td>-->
                    <!--    <td style="color: green;">{{ ucfirst($bill->status) }}</td>-->
                    <!--</tr>-->
                    <tr>
                        <td style="font-weight: bold;">Payment Date:</td>
                        <td>{{ $bill->created_at->format('d-m-Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
