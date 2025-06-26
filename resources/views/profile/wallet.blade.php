@extends('layouts.' . ($user['route'] != 'web' ? $user['route'] . '_' : '') . 'app')

@section('content')
    <div class="row g-0">
        <div class="col-lg-12">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Wallet </h5>
                        <h6 class="text-primary fw-bold border border-dashed border-primary p-2 rounded-2">
                            Current Balance : <span id="myBalance">{{ $user['user_balance'] }}</span> ₹
                        </h6>
                        <div>
                            <a href="{{ route('ledger.export', ['user' => $user['slug'], 'user_type' => $user_type]) }}"
                                class="btn btn-outline-success me-2 ">
                                <i class="fa-duotone fa-file-excel"></i> Export
                            </a>
                            <!-- <button class="btn btn-outline-secondary add"> <i class="fa fa-plus me-2"></i> Add
                                Money</button> -->
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive scrollbar">
                        <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                            <thead class="bg-200 text-900">
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Balance</th>
                                    <th>Particulars</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="tabsModalLabel">Add Money To Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="col-form-label" for="amount">Amount :</label>
                            <input class="form-control" placeholder="Enter Amount" name="amount" id="amount"
                                type="number" step="0.01" value="100" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="d-flex justify-content-between align-items-center select">
                                <span role="button" data-amount="500" class="btn fw-bold mx-auto btn-outline-info w-25">+ ₹
                                    500</span>
                                <span role="button" data-amount="1000"
                                    class="btn fw-bold mx-auto btn-outline-success w-25">+ ₹
                                    1000</span>
                                <span role="button" data-amount="5000"
                                    class="btn fw-bold mx-auto btn-outline-secondary w-25">+ ₹
                                    5000</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @if ($site_settings['is_commision'] == 0)
                                <p class="text-warning">
                                    <b>Note : </b> The amount will reflect in your wallet after commission deduction.
                                </p>
                            @endif
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn mx-auto btn-lg btn-info w-50 submit">
                                    Proceed to Pay
                                    <i class="fa-duotone fa-forward ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script type="text/javascript">
        $(function() {
            var table = $('.table-datatable').DataTable({
                ajax: "{{ route($route_name) }}",
                order: [
                    [1, 'desc']
                ],
                columns: [{
                        data: 'voucher_no',
                        name: 'voucher_no',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'updated_balance',
                        name: 'updated_balance',
                    },
                    {
                        data: 'particulars',
                        name: 'particulars'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ]
            });

            $('.add').on('click', function() {
                $('#addModal').modal('show');
            })

            $('.select span').on('click', function() {
                var val = $(this).data('amount')
                $('#amount').val(val)
            })

            $('#amount').keypress(function(e) {
                var validkeys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
                if (validkeys.indexOf(e.key) < 0) return false;
            });


            $('.submit').on('click', function() {
                var amount = $('#amount').val();
                if (amount > 0) {
                    $("#overlay").show();
                    $.post("{{ route('razorpay') }}", {
                        amount
                    }, function(data) {
                        if (data.status == true) {
                            $('#amount').val('');
                            $('#addModal').modal('hide');
                            var {
                                key,
                                amount,
                                order_id,
                                currency
                            } = data.data;
                            var options = {
                                key: key,
                                amount: amount,
                                currency: currency,
                                name: "{{ $site_settings['application_name'] }}",
                                description: "{{ 'Money Load For : ' . $user['name'] . ' - ' . ucfirst($role) }} ",
                                image: "{{ asset('storage/' . $site_settings['logo']) }}",
                                order_id: order_id,
                                handler: function(response) {
                                    response.user_id = "{{ $user['id'] }}";
                                    response.user_type = "{{ $user_type }}";
                                    $.post("{{ route('update-wallet') }}", response,
                                        function(data) {
                                            $("#overlay").hide();
                                            if (data.status == true) {
                                                toastr.success(data?.message);
                                                table.draw();
                                                $('#myBalance').text(Math.round(
                                                        parseFloat($('#myBalance')
                                                            .text()) + parseFloat(
                                                            amount / 100) * 100) /
                                                    100)
                                            } else {
                                                toastr.error(data.message);
                                            }
                                        })
                                },
                                prefill: {
                                    name: "{{ $user['name'] }}",
                                    email: "{{ $user['email'] }}",
                                    contact: "{{ $user['mobile'] }}"
                                },
                                notes: {
                                    address: "Corporate Office"
                                },
                                theme: {
                                    color: "#3399cc"
                                },
                                modal: {
                                    ondismiss: function() {
                                        $("#overlay").hide();
                                    }
                                }
                            };

                            var rzp1 = new Razorpay(options);
                            rzp1.on('payment.failed', function({
                                error
                            }) {
                                if (error.description) {
                                    $("#overlay").hide();
                                    toastr.error(error.description);
                                }
                            });
                            rzp1.open();
                        } else {
                            $("#overlay").hide();
                            toastr.error(data.message);
                        }
                    })
                } else {
                    toastr.error('Please enter amount min 1');
                }
            });
        });
    </script>
@endsection
