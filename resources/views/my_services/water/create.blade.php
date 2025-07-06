@extends('layouts.retailer_app')

@section('css')
<link href="{{ asset('assets/css/tom-select.default.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom-tomSelect.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Water Bill</h5>
                    <div>
                        <a href="{{ route('retailer.dashboard') }}" class="btn btn-dark">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                        <a href="{{ route('retailer.water-bill-list') }}" class="btn btn-primary me-1">
                            <i class="fa fa-list me-1"></i>
                            Water Bill History
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="water-fetch-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="operator" class="form-label">Select Board</label>
                            <select class="form-control board-id" name="operator" id="operator" required>
                                <option value=""> -- Select Board --</option>
                                @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" @selected(auth()->user()->default_water_board == $provider->id)>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="consumer_no" class="form-label">Consumer Number</label>
                            <input type="text" class="form-control consumer-no" name="consumer_no" placeholder="Consumer No" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary px-4">Fetch Bill</button>
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
                <form id="submit-form" action="{{ route('retailer.water-payment-submit') }}" method="post">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label for="consumer_name">Biller Name</label>
                            <input type="text" class="form-control text-dark consumer-name" name="consumer_name" readonly>
                            <input type="hidden" class="" name="transaction_id" id="transaction-id">
                            @csrf
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="bill_amount">Pending Bill Amount</label>
                            <input type="text" class="form-control text-dark bill-amount" name="bill_amount" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="due_date">Due Date</label>
                            <input type="text" class="form-control text-dark due-date" name="due_date" readonly>
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
        const validator = $("#water-fetch-form").validate({
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
            },
            messages: {
                operator: {
                    required: "Please select board name.",
                },
                consumer_no: {
                    required: "Please enter consumer no.",
                },
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: '{{ route("retailer.water-bill-details") }}',
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data) {
                        if (data.status) {
                            $('.consumer-name').val(data.data.consumer_name);
                            $('.bill-no').val(data.data.bill_no);
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

        const form = $('#submit-form');
        const submitBtn = form.find('button[type="submit"]');

        form.on('submit', function(e) {
            e.preventDefault();

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
                    submitBtn.prop('disabled', true).text('Processing...');
                    this.submit();
                }
            });
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
            $('#bill-details .type').text('Water Bill')
            $('#receipt').prop('href', "{{ $receipt }}" + data.id)
            $('#recipt-modal').modal('show')
        });
    });
</script>


@endsection