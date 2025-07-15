@extends('layouts.'.($user['route'] != 'web' ? $user['route'].'_': '').'app')

@section('content')

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header p-3">
                <h5 class="mb-0">Load Balance Methods</h5>
            </div>
            <div class="card-body p-2">
                <div class="accordion" id="accordion">
                    @foreach($paymodes as $key => $paymode)

                    <!-- Bank Accounts -->
                    @if($key == 1)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#panel-1" aria-expanded="true" aria-controls="panel-1">
                                Bank Accounts
                            </button>
                        </h2>
                        <div id="panel-1" class="accordion-collapse collapse show" data-bs-parent="#accordion">
                            <div class="accordion-body">
                                @foreach($paymode ?? [] as $key => $mode)
                                <ul class="list-group mb-2">
                                    <li class="list-group-item active" aria-current="true">
                                        <img src="{{ $mode['logo'] }}" alt="" width="25" height="25" class="rounded border border-white me-2">
                                        {{ $mode['name'] }}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Beneficiary Name - </b>{{ $mode['beneficiary_name'] }}
                                        <span role="button" class="text-secondary" data-copy="{{ $mode['beneficiary_name'] }}"><i class="fa-regular fa-copy"></i></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Account Number - </b>{{ $mode['account_number'] }}
                                        <span role="button" class="text-secondary" data-copy="{{ $mode['account_number'] }}"><i class="fa-regular fa-copy"></i></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>IFSC Code - </b>{{ $mode['ifsc_code'] }}
                                        <span role="button" class="text-secondary" data-copy="{{ $mode['ifsc_code'] }}"><i class="fa-regular fa-copy"></i></span>
                                    </li>
                                </ul>
                                @endforeach

                                <form action="" method="post">
                                    <div class="input-group">
                                        <select class="form-select" aria-label="Bank List" id="bank-list">
                                            @foreach($indianBanks as $key => $value)
                                            <option value="{{ $value['link'] }}">{{ $value['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button" id="button-proceed">Proceed</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- UPI Handels -->
                    @if($key == 2)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fs-6 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panel-2" aria-expanded="true" aria-controls="panel-2">
                                UPI Handels
                            </button>
                        </h2>
                        <div id="panel-2" class="accordion-collapse collapse" data-bs-parent="#accordion">
                            <div class="accordion-body">
                                @foreach($paymode ?? [] as $key => $mode)
                                <ul class="list-group mb-2">
                                    <li class="list-group-item active" aria-current="true">
                                        <img src="{{ $mode['logo'] }}" alt="" width="25" height="25" class="rounded border border-white me-2">
                                        {{ $mode['name'] }}
                                    </li>
                                    <li class="list-group-item">
                                        <b>UPI Handel - </b>{{ $mode['upi'] }}
                                        <span role="button" class="text-secondary" data-copy="{{ $mode['upi'] }}"><i class="fa-regular fa-copy"></i></span>
                                    </li>
                                </ul>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Requested Money</h5>
                    <div>
                        <button class="btn btn-outline-secondary add">
                            <i class="fa fa-plus me-2"></i>
                            Request Money
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive scrollbar">
                    <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th>Request ID</th>
                                <th>Payment Mode</th>
                                <th>Amount</th>
                                <th>Date</th>
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
                <form id="requestMoney" action="" method="post">
                    <div class="row">

                        <div class="col-md-12">
                            <label class="col-form-label" for="payment_mode_id">Payment Mode :</label>
                            <select class="form-select" aria-label="Payment Mode" name="payment_mode_id" id="payment_mode_id">
                                <option selected>Open this select menu</option>
                                @foreach($paymodesAll as $mode)
                                <option value="{{ $mode['id'] }}">{{ $mode['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="col-form-label" for="description">Description :</label>
                            <textarea class="form-control" placeholder="Enter UTR/RRN Number" name="description"
                                id="description"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label" for="amount">Amount :</label>
                            <input class="form-control" placeholder="Enter Amount" name="amount" id="amount"
                                type="number" step="0.01" value="100" />
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label" for="attachment">Attachment / Payment Slip :</label>
                            <input class="form-control" name="attachment" type="file" />
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn mx-auto btn-lg btn-info w-50 submit">
                                    Submit Request
                                    <i class="fa-duotone fa-forward ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-none">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card style-2 mb-md-0 mb-4 requestMoney">
                    <h5 class="card-title-2 mb-1 text-secondary fw-bold">...</h5>
                    <h5 class="card-title mb-1">...</h5>
                    <div class="card-body p-0">...</div>
                    <p class="text-danger my-1 reason" style="display: none;">
                        <b>Cancel Reason : </b>
                        <span>...</span>
                    </p>
                    <div class="mt-1">
                        <p class="fw-bold">Amount : <span class="amount"></span></p>
                        <a href="#" class="btn btn-secondary" target="_blank" download=""
                            style="display: none;">Download</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(function() {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route($route_name) }}",
            order: [
                [1, 'desc']
            ],
            columns: [{
                    data: 'request_number',
                    name: 'request_number',
                },
                {
                    data: 'payment_mode_name',
                    name: 'payment_modes.name'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'status',
                    name: 'status',
                },
            ]
        });

        $('.add').on('click', function() {
            $('#addModal').modal('show');
        })

        $('#amount').keypress(function(e) {
            var validkeys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
            if (validkeys.indexOf(e.key) < 0) return false;
        });

        // requestMoney
        $("#requestMoney").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                payment_mode_id: {
                    required: true,
                },
                description: {
                    required: true,
                    minlength: 2,
                    maxlength: 500
                },
                amount: {
                    required: true,
                    minlength: 2,
                    maxlength: 500
                },
                attachment: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2,
                },
            },
            messages: {

                description: {
                    required: "Please enter description.",
                },
                amount: {
                    required: "Please enter amount.",
                },
                attachment: {
                    extension: "Supported Format Only : jpg, jpeg, png, pdf"
                },
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ route( $route_name ) }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data) {
                        if (data.success) {
                            toastr.success(data?.message);
                            $('#addModal').modal('hide');
                            $(form).trigger("reset")
                            table.draw();
                            $("#overlay").hide();
                        } else {
                            $(form).validate().showErrors(data.data);
                            toastr.error(data?.message);
                            $("#overlay").hide();
                        }
                    }
                });
            }
        });

        $(document).on('click', '.viewDetails', function() {
            var data = $(this).data('data');
            var path = "{{ asset('storage/') }}"
            $('.requestMoney .card-title-2').text(data.request_id || "")
            $('.requestMoney .card-title').text(data.title || "")
            $('.requestMoney .card-body').text(data.description || "")
            $('.requestMoney .amount').text(data.amount || "")
            if (data.attachment) {
                $('.requestMoney a').attr('href', path + '/' + data.attachment).show()
            } else {
                $('.requestMoney a').attr('href', '...').hide()
            }

            if (data.reason && data.status == 2) {
                $('.requestMoney .reason span').text(data.reason)
                $('.requestMoney .reason').show()
            } else {
                $('.requestMoney .reason span').text('...')
                $('.requestMoney .reason').hide()
            }
            $('#viewModal').modal('show');
        })

        $('[data-copy]').on('click', function() {
            copyToClipboard($(this).data('copy'));
            toastr.success('data copied to clipboard..!!')
        });

        $('#button-proceed').on('click', function() {
            const link = $('#bank-list').val();
            window.open(link, '_blank')
        })
    });
</script>

@endsection