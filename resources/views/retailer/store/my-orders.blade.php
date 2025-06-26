@extends('layouts.retailer_app')

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
        border-color: #2196f3;
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
        border-color: #2196f3;
        width: 0;
        height: auto;
        top: 25px;
        bottom: -15px;
        border-right-width: 0;
        border-top-width: 0;
        border-bottom-width: 0;
        border-radius: 0;
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

    .timeline-line .item-timeline:last-child .t-dot:after {
        display: none;
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
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" id="table-example">My Orders </h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive scrollbar">
                    <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                        style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th>Order Id</th>
                                <th>Date</th>
                                <th>Mobile</th>
                                <th>Order Total</th>
                                <th>Status</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content position-relative orderDetails">
            <div class="modal-header">
                <h5 class="modal-title" id="tabsModalLabel">View Order Details</h5>
                <div class="d-flex gap-1 align-items-center">
                    <span role="button" class="window">
                        <i class="fa fa-sharp fa-solid fa-expand fs-5"></i>
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body scrollbar-thin">
                <div class="card">
                    <div class="card-body p-3 text-dark">
                        <div class="row">
                            <div
                                class="col-md-12 d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom border-light">
                                <p class="lead fw-bold mb-0">Purchase Reciept</p>
                                <button class="btn btn-sm btn-danger cancelOrderBtn" role="button"
                                    data-order-id="">Cancel Order</button>
                            </div>
                            <div class=" col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <p class="small text-muted mb-1">Date</p>
                                        <p class="mb-0" data-id="date"></p>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <p class="small text-muted mb-1">Order No.</p>
                                        <p class="mb-0" data-id="voucher_no"></p>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <p class="small text-muted mb-1">Phone Number</p>
                                        <p class="mb-0" data-id="phone"></p>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <p class="small text-muted mb-1">Email</p>
                                        <p class="mb-0" data-id="email"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-1">
                                <p class="small text-muted mb-1">Shipping Address</p>
                                <p data-id="address"></p>
                            </div>
                        </div>
                        <div class="mx-n5 p-3 mb-2" style="background-color: #f2f2f2;" id="orderData"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <p class="lead fw-bold mb-0 pb-2">Tracking Order</p>
                                <div class="mt-container mx-auto">
                                    <div class="timeline-line" id="trackingData"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark" data-bs-dismiss="modal">Discard</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script>
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('retailer.my-orders') }}",
            order: [
                [0, 'desc']
            ],
            columns: [{
                data: 'voucher_no',
                name: 'voucher_no',
                class: 'fw-bold text-primary'
            },
            {
                data: 'date',
                name: 'date'
            },
            {
                data: 'customer_mobile',
                name: 'customer_mobile'
            },
            {
                data: 'total',
                name: 'total',
                class: ''
            },
            {
                data: 'order_status',
                name: 'order_status_id',
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
            ]
        });

        $(document).on('click', '.view', function () {
            var data = $(this).data('data')
            let address = `${data.customer_name_1} ${data.customer_name_2 ? data.customer_name_2 : ""}, 
                    ${data.shipping_address_1}, ${data.shipping_address_2 ? data.shipping_address_2 : ''}, 
                    ${data.shipping_city}, ${data.shipping_state}, 
                    ${data.shipping_postcode}`;


            if (data?.order_status_id <= 1) {
                $('.cancelOrderBtn').show().attr('data-order-id', data?.id);
            } else {
                $('.cancelOrderBtn').hide().attr('data-order-id', '');
            }

            $('[data-id="voucher_no"]').text(data?.voucher_no);
            $('[data-id="date"]').text(data?.date);
            $('[data-id="address"]').text(address);
            $('[data-id="email"]').text(data?.customer_email);
            $('[data-id="phone"]').text(data?.customer_mobile);

            var html = '';
            data?.products?.forEach(row => {
                html += `<div class="row mb-1">
                            <div class="col-8">${row?.product_name} x ${row?.quantity}</div>
                            <div class="col-4 text-end">₹${Math.round(row?.unit_price_without_tax * row?.quantity * 100) / 100}</div>
                        </div>`;
            });

            html += `<hr class="my-1" />`;
            if (data.sub_total > 0) {
                html += `<div class="row mb-1">
                    <div class="col-8 text-end">Sub Total</div>
                    <div class="col-4 text-end">₹${data.sub_total}</div>
                </div>`;
            }

            if (data.tax > 0) {
                html += `<div class="row mb-1">
                    <div class="col-8 text-end">Tax</div>
                    <div class="col-4 text-end">₹${data.tax}</div>
                </div>`;
            }

            if (data.delivery > 0) {
                html += `<div class="row mb-1">
                    <div class="col-8 text-end">Delivery</div>
                    <div class="col-4 text-end">₹${data.delivery}</div>
                </div>`;
            }

            if (data.discount > 0) {
                html += `<div class="row mb-1">
                    <div class="col-8 text-end">Discount</div>
                    <div class="col-4 text-end">-₹${data.discount}</div>
                </div>`;
            }

            if (data.total > 0) {
                html += `<div class="row mb-1">
                    <div class="col-8 text-end fw-bold">Total</div>
                    <div class="col-4 text-end fw-bold">₹${data.total}</div>
                </div>`;
            }

            var tracking = '';
            data?.history?.forEach(row => {
                tracking += `<div class="item-timeline">
                        <p class="t-time">${row?.order_status}</p>
                        <div class="t-dot t-dot-${row?.order_status_class}"></div>
                        <div class="t-text">
                            <p>${row?.comment}</p>
                            <p class="t-meta-time">${row?.date}</p>
                        </div>
                    </div>`;
            });

            $('#trackingData').html(tracking);
            $('#orderData').html(html);
            $('#viewModal').modal('show');
        });

        $('.window').on('click', function () {
            $('#viewModal .modal-dialog').toggleClass('modal-fullscreen');
            $(this).find('.fa').toggleClass('fa-expand').toggleClass('fa-compress');
        })

        $('.cancelOrderBtn').on('click', function () {
            var order_id = $(this).data('order-id');
            swal({
                title: "Are you sure?",
                text: "Once Cancelled, you can't revert this..!!",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancel",
                        visible: true,
                        className: "btn btn-dark",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Yes, Cancel It.!",
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
                        url: "{{ route('retailer.cancel-order') }}",
                        data: { order_id },
                        type: 'POST',
                        success: function (data) {
                            $("#overlay").hide();
                            if (data.status) {
                                swal({
                                    title: "Cancelled",
                                    text: data.message,
                                    icon: "success",
                                    button: "Okay",
                                }).then(() => {
                                    table.draw();
                                    $('#viewModal').modal('hide');
                                })
                            } else {
                                toastr.error(data.message);
                            }
                        }
                    });
                }
            });
        })
    })
</script>
@endsection