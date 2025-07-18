@extends('layouts.retailer_app')

@section('css')
<link href="{{ asset('assets/css/tom-select.default.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom-tomSelect.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    @if($service->notice)
    <div class="col-12">
        <div class="alert alert-arrow-left alert-icon-left alert-light-primary" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            {{ $service->notice }}
        </div>
    </div>
    @endif
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">LIC Premium</h5>
                    <div>
                        <a href="{{ route('retailer.dashboard') }}" class="btn btn-dark">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                        <a href="{{ route('retailer.lic-bill-list') }}" class="btn btn-primary me-1">
                            <i class="fa fa-list me-1"></i>
                            LIC Premium History
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="lic-fetch-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="operator" class="form-label">Select Board</label>
                            <select class="form-control board-id" name="operator" id="operator" required>
                                <option value=""> -- Select Board --</option>
                                @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" @selected(auth()->user()->default_lic_board == $provider->id)>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="consumer_no" class="form-label">Policy Number</label>
                            <input type="text" class="form-control consumer-no" name="consumer_no" placeholder="Consumer No" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email">Email Id</label>
                            <input type="email" class="form-control text-dark" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dob">DOB Date</label>
                            <input type="date" class="form-control text-dark" name="dob" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary px-4">Fetch Bill</button>
                            <button type="button" id="save-board" class="btn btn-secondary px-4">Save Board</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 mb-3" id="bill-details" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Bill Details</h5>
            </div>
            <div class="card-body">
                <form id="submit-form" action="{{ route('retailer.lic-payment-submit') }}" method="post">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label for="consumer_name">Policy Holder Name</label>
                            <input type="text" class="form-control text-dark consumer-name" name="consumer_name">
                            <input type="hidden" class="" name="transaction_id" id="transaction-id">
                            @csrf
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="bill_amount">Amount</label>
                            <input type="number" class="form-control text-dark bill-amount" name="bill_amount">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="due_date">Due Date</label>
                            <input type="date" class="form-control text-dark due-date" name="due_date" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary px-4">Pay Bill</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Recent Transactions</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable dataTable no-footer">
                        <thead class="bg-200 text-900">
                            <tr role="row">
                                <th>Transaction Id</th>
                                <th>Consumer Name</th>
                                <th>Consumer No</th>
                                <th>Email</th>
                                <th>DOB</th>
                                <th>Payment Date</th>
                                <th>Bill Amount</th>
                                <th>Commission</th>
                                <th>TDS Amount</th>
                                <th>Provider Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resent as $key => $row)
                            <tr role="row" class="{{ $key % 2 === 0 ? 'even' : 'odd' }}">
                                <td>
                                    <b class="text-primary view" data-all='{!! htmlspecialchars(json_encode($row)) !!}'>{{ $row->transaction_id }}</b>
                                </td>
                                <td><b>{{ $row->consumer_name }}</b></td>
                                <td>{{ $row->consumer_no }}</td>
                                <td>{{ empty($row->bill_no) ? '--' : $row->bill_no }}</td>
                                <td>{{ empty($row->bu_code) ? '--' : $row->bu_code }}</td>
                                <td>{{ $row->created_at->format('d F, Y h:i A') }}</td>
                                <td><b class="text-primary">₹ {{ $row->bill_amount }}</b></td>
                                <td><b class="text-success">₹ {{ $row->commission }}</b></td>
                                <td><b class="text-danger">₹ {{ $row->tds }}</b></td>
                                <td>{{ $row->provider_name }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary view" data-all='{!! htmlspecialchars(json_encode($row)) !!}'>View</button>
                                </td>
                            </tr>
                            @endforeach

                            @if($resent->count() === 0)
                            <tr>
                                <td class="text-center text-danger" colspan="8">No Transactions Available..!!</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partial.receipt-modal')

@endsection

@section('js')
<script src="{{ asset('assets/js/tom-select.base.js') }}"></script>
<script>
    $(function() {
        const tom = new TomSelect("#operator");
        const validator = $("#lic-fetch-form").validate({
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                operator: {
                    required: true
                },
                consumer_no: {
                    minlength: 4,
                    required: true
                },
                email: {
                    required: true,
                    email: true,
                    minlength: 5,
                    maxlength: 50
                },
                dob: {
                    date: true,
                    required: true
                },
            },
            messages: {
                operator: {
                    required: "Please select board name.",
                },
                consumer_no: {
                    required: "Please enter consumer no.",
                },
                email: {
                    required: "Please enter Email",
                    email: "Provide valid email.",
                    minlength: "Email must be at least 5 characters.",
                    maxlength: "Email cannot exceed 50 characters."
                },
                dob: {
                    required: "Please select dob date",
                    date: "Please enter a valid date"
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: '{{ route("retailer.lic-bill-details") }}',
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data) {
                        if (data.status) {
                            $('.consumer-name').val(data.data.consumer_name);
                            $('.bill-amount').val(data.data.bill_amount);
                            $('.due-date').val(data.data.due_date);
                            $('#transaction-id').val(data.data.transaction_id)
                            $('#bill-details').show()
                            toastr.success(data.message);
                            $("#overlay").hide();
                        } else {
                            toastr.error(data.message);
                            $("#overlay").hide();
                            validator.showErrors(data.data);
                        }
                    },
                    error: function() {
                        $("#overlay").hide();
                    },
                });
            },
        });

        $("#submit-form").validate({
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                consumer_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                bill_amount: {
                    required: true,
                    number: true,
                    min: 1,
                    max: 9999999
                },
                bill_amount: {
                    required: true,
                    number: true,
                    min: 1,
                    max: 9999999
                },
                due_date: {
                    date: true,
                    required: true
                },
            },
            messages: {
                consumer_name: {
                    required: "Please enter consumer name",
                    minlength: "Name must be at least 3 characters",
                    maxlength: "Name cannot exceed 100 characters"
                },
                bill_amount: {
                    required: "Please enter bill amount",
                    number: "Please enter a valid number",
                    min: "Bill amount must be at least 1",
                    max: "Bill amount cannot exceed 9,999,999"
                },
                due_date: {
                    required: "Please select due date",
                    date: "Please enter a valid date"
                }
            },
            submitHandler: function(form) {
                const submitBtn = $(form).find('button[type="submit"]');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to proceed with the payment?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, pay now!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                        form.submit();
                    }
                });
            }
        });

        $(document).on('click', '.view', function() {
            const data = $(this).data('all');
            const date = new Date(data.created_at);
            const formatter = new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });

            $('#bill-details .created_at').text(formatter.format(date))
            $('#bill-details .consumer_name').text(data.consumer_name)
            $('#bill-details .bill_amount').text(data.bill_amount)
            $('#bill-details .consumer_no').text(data.consumer_no)
            $('#bill-details .transaction_id').text(data.transaction_id)
            $('#bill-details .type').text('LIC Bill')
            $('#receipt').prop('href', "{{ $receipt }}" + data.id)
            $('#recipt-modal').modal('show')
        });

        $('#save-board').on('click', function() {
            const board_id = $('#operator').val();
            $.post("{{ route('retailer.update-board') }}", {
                board_id,
                type: 'lic'
            }, function(data) {
                if (data.status) {
                    toastr.success(data.message)
                } else {
                    toastr.error(data.message)
                }
            })
        })
    });
</script>


@endsection