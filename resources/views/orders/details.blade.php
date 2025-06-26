@extends('layouts.app')

@section('css')
<style>
    .timeline-line .item-timeline {
        display: flex;
        justify-content: space-around;
    }

    .timeline-line .item-timeline .t-dot {
        position: relative;
    }

    .timeline-line .item-timeline .t-dot:before {
        content: "";
        position: absolute;
        border-color: inherit;
        border-width: 2px;
        border-style: solid;
        border-radius: 50%;
        width: 10px;
        height: 10px;
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
    }

    .timeline-line .item-timeline .t-dot:after {
        content: "";
        position: absolute;
        border-color: inherit;
        border-width: 2px;
        border-style: solid;
        border-radius: 50%;
        width: 10px;
        height: 10px;
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: auto;
        top: 25px;
        bottom: -15px;
        border-right-width: 0;
        border-top-width: 0;
        border-bottom-width: 0;
        border-radius: 0;
    }

    .timeline-line .item-timeline:last-child .t-dot::after {
        display: none;
    }

    .timeline-line .item-timeline .t-dot.t-dot-primary:before {
        border-color: #4361ee;
    }

    .timeline-line .item-timeline .t-dot.t-dot-success:before {
        border-color: #00ab55;
    }

    .timeline-line .item-timeline .t-dot.t-dot-warning:before {
        border-color: #e2a03f;
    }

    .timeline-line .item-timeline .t-dot.t-dot-info:before {
        border-color: #2196f3;
    }

    .timeline-line .item-timeline .t-dot.t-dot-danger:before {
        border-color: #e7515a;
    }

    .timeline-line .item-timeline .t-dot.t-dot-secondary:before {
        border-color: #805dca;
    }

    .timeline-line .item-timeline .t-dot.t-dot-primary:after {
        border-color: #4361ee;
    }

    .timeline-line .item-timeline .t-dot.t-dot-success:after {
        border-color: #00ab55;
    }

    .timeline-line .item-timeline .t-dot.t-dot-warning:after {
        border-color: #e2a03f;
    }

    .timeline-line .item-timeline .t-dot.t-dot-info:after {
        border-color: #2196f3;
    }

    .timeline-line .item-timeline .t-dot.t-dot-danger:after {
        border-color: #e7515a;
    }

    .timeline-line .item-timeline .t-dot.t-dot-secondary:after {
        border-color: #805dca;
    }

    .timeline-line .item-timeline .t-text {
        padding: 10px;
        align-self: center;
        margin-left: 10px;
        width: 60%;
    }

    .timeline-line .item-timeline .t-text p {
        font-size: 13px;
        margin: 0;
        color: #3b3f5c;
        font-weight: 600;
    }

    .timeline-line .item-timeline .t-time {
        margin: 0;
        min-width: 100px;
        max-width: 200px;
        font-size: 16px;
        font-weight: 600;
        color: #3b3f5c;
        padding: 10px 0;
        display: flex;
        justify-content: end;
        padding-right: 15px;
        align-items: start;
    }

    .timeline-line .item-timeline .t-text .t-meta-time {
        margin: 0;
        min-width: 160px;
        max-width: 100px;
        font-size: 12px;
        font-weight: 700;
        color: #888ea8;
        align-self: center;
    }
</style>
@endsection

@section('content')
<div class="row invoice layout-top-spacing layout-spacing">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="doc-container">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Order Details</h5>
                                <div class="d-flex">
                                    <a href="{{ route('orders') }}" class="btn btn-primary me-2">
                                        <i class="fa-duotone fa-arrow-left me-2"></i> Go Back</a>
                                    <a href="{{ route('order.export-invoice', $order->slug) }}"
                                        class="btn btn-secondary">
                                        <i class="fa-duotone fa-file-invoice me-2"></i> Invoice</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-sm-1 mb-4">
                                    <h5 class="fw-bold">
                                        <span class="inv-title">Order Id : </span>
                                        <span class="inv-number">{{ $order->voucher_no }}</span>
                                    </h5>
                                    <p class="mb-0 mt-2">
                                        <span class="inv-title">Invoice Date : </span>
                                        <span class="inv-date">
                                            {{ $order->date->format('d M, Y') }}
                                        </span>
                                    </p>
                                    <p class="my-2">
                                        Order Status : <span class="badge badge-light-{{ $order->order_status_class }}">
                                            {{ $order->order_status }}
                                        </span>
                                    </p>
                                    <div style="min-width: 250px;"
                                        class="d-flex border-top gap-2 border-bottom border-2 border-white rounded">
                                        <div class="usr-img-frame me-2 avatar avatar-md m-1">
                                            <img alt="" class="img-fluid rounded"
                                                src="{{ asset('storage/'.@$user->image) }}">
                                        </div>
                                        <div>
                                            <p class="my-1 fs-6" style="color: #506690">
                                                {{ $user->name }}
                                            </p>
                                            <p class="my-0 small" style="color: #f8538d">
                                                {{ @$user->userId }} ( {{ @$user->mobile }} )
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-sm-1 mb-4">
                                    <h4>Shipping Address</h4>
                                    <p class="mb-1">
                                        <b>{{ trim($order->customer_name_1." ".$order->customer_name_2) }}</b>
                                    </p>
                                    <p class="mb-1">
                                        {{ $order->shipping_address_1 }}
                                        {{ $order->shipping_address_2 }} <br>
                                        {{ $order->shipping_city }}, {{ $order->shipping_state }},
                                        {{ $order->shipping_postcode }}
                                    </p>
                                    <p class="mb-1">{{ $order->customer_email }}</p>
                                    <p class="mb-1">{{ $order->customer_mobile }}</p>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="table-responsive scrollbar-thin">
                                        <table class="table table-striped table-bordered">
                                            <thead class="">
                                                <tr>
                                                    <th scope="col">Code</th>
                                                    <th scope="col">Items</th>
                                                    <th class="text-end" scope="col">Qty</th>
                                                    <th class="text-end" scope="col">Price</th>
                                                    <th class="text-end" scope="col">Tax Rate</th>
                                                    <th class="text-end" scope="col">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->products as $key => $row)
                                                <tr>
                                                    <td>{{ orderId($row->product_id, 'I',6) }}</td>
                                                    <td>
                                                        <p class="mb-0">{{ $row->product_name }}</p>
                                                    </td>
                                                    <td class="text-end">{{ $row->quantity }}</td>
                                                    <td class="text-end">
                                                        ₹ {{ round($row->unit_price_without_tax ,2) }}
                                                    </td>
                                                    <td class="text-end">{{ $row->tax_rate }}%</td>
                                                    <td class="text-end">
                                                        ₹ {{ round($row->unit_price_without_tax * $row->quantity, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-sm-5 col-12 order-sm-0 order-1"></div>
                                        <div class="col-sm-7 col-12 order-sm-1 order-0">
                                            <div class="text-sm-end">
                                                <div class="row">
                                                    @if($order->sub_total > 0)
                                                    <div class="col-sm-8 col-7">
                                                        <p class="mb-1">Sub Total :</p>
                                                    </div>
                                                    <div class="col-sm-4 col-5">
                                                        <p class="mb-1 pe-3">₹ {{ $order->sub_total }}</p>
                                                    </div>
                                                    @endif
                                                    @if($order->tax > 0)
                                                    <div class="col-sm-8 col-7">
                                                        <p class="mb-1">Tax :</p>
                                                    </div>
                                                    <div class="col-sm-4 col-5">
                                                        <p class="mb-1 pe-3">₹ {{ $order->tax }}</p>
                                                    </div>
                                                    @endif
                                                    @if($order->delivery > 0)
                                                    <div class="col-sm-8 col-7">
                                                        <p class="discount-rate">Shipping :</p>
                                                    </div>
                                                    <div class="col-sm-4 col-5">
                                                        <p class="mb-1 pe-3">₹ {{ $order->delivery }}</p>
                                                    </div>
                                                    @endif
                                                    @if($order->discount > 0)
                                                    <div class="col-sm-8 col-7">
                                                        <p class="discount-rate">Discount :</p>
                                                    </div>
                                                    <div class="col-sm-4 col-5">
                                                        <p class="mb-1 pe-3">-₹ {{ $order->discount }}</p>
                                                    </div>
                                                    @endif
                                                    <hr class="my-1">
                                                    @if($order->total > 0)
                                                    <div class="col-sm-8 col-7 grand-total-title mt-1">
                                                        <h5 class="text-dark">Grand Total :</h5>
                                                    </div>
                                                    <div class="col-sm-4 col-5 grand-total-amount mt-1">
                                                        <h5 class="text-dark pe-3">₹ {{ $order->total }}</h5>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Order History</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mt-container mx-auto">
                                        <div class="timeline-line" id="trackingData">
                                            @foreach($order->history as $row)
                                            <div class="item-timeline">
                                                <p class="t-time">{{ $row->order_status }}</p>
                                                <div class="t-dot t-dot-{{ $row->order_status_class }}">
                                                </div>
                                                <div class="t-text">
                                                    <p>{{ $row->comment }}</p>
                                                    <p class="t-meta-time">{{ $row->date }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <form action="" method="post" id="changeStatus">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <label for="comment" class="form-label">
                                                    Change Order Status
                                                </label>
                                                <textarea class="form-control" name="comment" id="comment" rows="2"
                                                    placeholder="Enter Your Comment.."></textarea>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <select class="form-select" name="order_status_id" id="order_status_id">
                                                    @foreach(config('constant.order_status_list', []) as $key => $row)
                                                    @if($key >= $order->order_status_id)
                                                    <option value="{{ $key }}">{{ $row }}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <button type="submit" class="btn btn-primary btn-lg">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {

        $("#changeStatus").validate({
            rules: {
                comment: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                order_status_id: {
                    required: true,
                },
            },
            messages: {
                comment: {
                    required: "Please enter your comment",
                },
                order_status_id: {
                    required: "Please select Status",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                swal({
                    title: "Are you sure?",
                    text: "You want to updated this status..!",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Cancel",
                            visible: true,
                            className: "btn btn-dark",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Yes, Update It.!",
                            value: true,
                            visible: true,
                            className: "btn btn-danger",
                            closeModal: true
                        }
                    },
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $("#overlay").show();
                        $.ajax({
                            url: "{{ request()->url() }}",
                            data: formData,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success: function (data) {
                                if (data.status) {
                                    swal(data?.message, { icon: "success" });
                                    $(form).trigger("reset")
                                    $("#overlay").hide();
                                    location.reload(true);
                                } else {
                                    $(form).validate().showErrors(data.data);
                                    toastr.error(data?.message);
                                    $("#overlay").hide();
                                }
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endsection