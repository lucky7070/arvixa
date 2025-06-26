<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/apps/invoice-preview.css') }}" rel="stylesheet">
    <style>
        .invoice {
            height: 294mm;
            width: 210mm;
            margin: 0 auto;
        }

        .avatar {
            position: relative;
            display: inline-block;
            width: 5.125rem;
            height: 5.125rem;
            font-size: 1.70833rem;
        }

        .avatar img {
            width: 100%;
            height: 100%;
        }

        p {
            margin-bottom: 0;
        }

        .float-left {
            float: left;
        }

        .bold {
            font-weight: 600;
        }

        @media print {

            .printSection,
            .printSection *,
            table.table-striped,
            table.table-striped * {
                visibility: visible;
            }

            .printSection {
                width: 100%;
                padding: 0;
                position: absolute;
                left: 0;
                top: 0;
                margin: 0;
            }

            @page {
                margin: 0mm;
                padding: 0mm;
                height: 270mm;
                width: 210mm;
            }
        }

        .printBtn {
            position: absolute;
            top: 20px;
            right: -120px;
        }

        .goBack {
            position: absolute;
            top: 70px;
            right: -107px;
        }

        .min-w-110 {
            min-width: 110px;
        }
    </style>
</head>

<body>
    <div class="invoice printSection bg-light1 position-relative">
        <div class="table-responsive">
            <table class="w-100">
                <tbody>
                    <tr>
                        <td class="w-50 p-4 border-bottom border-2">
                            <div class="avatar avatar-xl">
                                <img class="company-logo rounded"
                                    src="{{ asset('storage/'.$site_settings['favicon']) }}" alt="Logo" />
                            </div>
                            <h5 class="in-heading align-self-center w-100 ms-0 bold">
                                {{ $site_settings['application_name'] }}
                            </h5>
                            <p class="inv-street-addr mt-2">
                                {{ $site_settings['address'] }}
                            </p>
                        </td>
                        <td class="w-50 p-4 border-bottom border-2">
                            <p class="fs-5 mt-4 text-end">
                                <span class="text-dark">Invoice : </span>
                                <span class="text-dark bold">{{ $order->voucher_no }}</span>
                            </p>
                            <p class="mb-1 mt-4 text-end">
                                <span class="inv-title">Invoice Date : </span>
                                <span class="inv-date">
                                    {{ $order->date->format('d M, Y') }}
                                </span>
                            </p>
                            <p class="mb-1 text-muted text-end">{{ $site_settings['email'] }}</p>
                            <p class="mb-1 text-muted text-end">{{ $site_settings['phone'] }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-50 p-4">
                            <h5 class="inv-to">Invoice To</h5>
                            <p class="text-dark bold">
                                {{ trim($order->customer_name_1." ".$order->customer_name_2) }}
                            </p>
                            <p class="text-muted">
                                {{ $order->shipping_address_1 }}
                                {{ $order->shipping_address_2 }} <br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }},
                                {{ $order->shipping_postcode }}
                            </p>
                            <p class="text-muted">{{ $order->customer_email }}</p>
                            <p class="text-muted">{{ $order->customer_mobile }}</p>
                        </td>
                        <td class="w-50 p-4">
                            <h5 class="text-end">Invoice From</h5>
                            <p class="mb-1 text-end text-dark bold"> {{ $site_settings['application_name'] }} </p>
                            <p class="mb-1 text-end text-muted"> {{ $site_settings['address'] }} </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="w-100">
                            <div class="w-100 float-left px-4">
                                <table class="table table-striped">
                                    <thead style="background-color: #e6f4ff;">
                                        <tr>
                                            <th class="bold" scope="col">S.No</th>
                                            <th class="bold" scope="col">Items</th>
                                            <th class="bold text-end min-w-110" scope="col">Qty</th>
                                            <th class="bold text-end min-w-110" scope="col">Price</th>
                                            <th class="bold text-end min-w-110" scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->products as $key => $row)
                                        <tr>
                                            <td>{{ ++$key }}.</td>
                                            <td>{{ Str::limit($row->product_name, 100) }}</td>
                                            <td class="text-end">{{ $row->quantity }}</td>
                                            <td class="text-end">
                                                ₹ {{ round($row->unit_price_without_tax , 2) }}
                                            </td>
                                            <td class="text-end">
                                                ₹ {{ round($row->unit_price_without_tax * $row->quantity , 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-50 p-4"></td>
                        <td class="w-50 p-4">
                            <div class="text-sm-end">
                                <div class="row">
                                    @if($order->sub_total > 0)
                                    <div class="col-sm-8 col-7">
                                        <p class="mb-1">Sub Total :</p>
                                    </div>
                                    <div class="col-sm-4 col-5">
                                        <p class="mb-1">₹ {{ $order->sub_total }}</p>
                                    </div>
                                    @endif
                                    @if($order->tax > 0)
                                    <div class="col-sm-8 col-7">
                                        <p class="mb-1">Tax :</p>
                                    </div>
                                    <div class="col-sm-4 col-5">
                                        <p class="mb-1">₹ {{ $order->tax }}</p>
                                    </div>
                                    @endif
                                    @if($order->delivery > 0)
                                    <div class="col-sm-8 col-7">
                                        <p class="discount-rate">Shipping :</p>
                                    </div>
                                    <div class="col-sm-4 col-5">
                                        <p class="mb-1">₹ {{ $order->delivery }}</p>
                                    </div>
                                    @endif
                                    @if($order->discount > 0)
                                    <div class="col-sm-8 col-7">
                                        <p class="discount-rate">Discount :</p>
                                    </div>
                                    <div class="col-sm-4 col-5">
                                        <p class="mb-1">-₹ {{ $order->discount }}</p>
                                    </div>
                                    @endif
                                    @if($order->total > 0)
                                    <div class="col-sm-8 col-7 grand-total-title mt-1">
                                        <h5 class="text-dark">Grand Total :</h5>
                                    </div>
                                    <div class="col-sm-4 col-5 grand-total-amount mt-1">
                                        <h5 class="text-dark">₹ {{ $order->total }}</h5>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="w-100 p-3 border-bottom border-top border-2">
                            <p class="mb-0">Note: Thank you for doing Business with us.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button class="btn btn-secondary printBtn" onclick="window.print()">Print Page</button>
        <a href="{{ route('order_details', $order->slug) }}" class="btn btn-danger goBack">Go Back</a>
    </div>
</body>

</html>