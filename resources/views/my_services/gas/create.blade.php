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
                    <h5 class="card-title">Gas Bill</h5>
                    <div>
                        <a href="{{ route('retailer.dashboard') }}" class="btn btn-dark">
                            <i class="fa fa-arrow-left me-1"></i>
                            Go Back
                        </a>
                        <a href="{{ route('retailer.gas-bill-list') }}" class="btn btn-primary me-1">
                            <i class="fa fa-list me-1"></i>
                            Gas Bill History
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="gas-fetch-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="operator" class="form-label">Select Board</label>
                            <select class="form-control board-id" name="operator" id="operator" required>
                                <option value=""> -- Select Board --</option>
                                @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" @selected(auth()->user()->default_gas_board == $provider->id)>{{ $provider->name }}</option>
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
    <div class="col-12" id="bill-details" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Bill Details</h5>
            </div>
            <div class="card-body">
                <form id="submit-form" action="{{ route('retailer.gas-payment-submit') }}" method="post">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label for="consumer_name">Biller Name</label>
                            <input type="text" class="form-control text-dark consumer-name" name="consumer_name">
                            <input type="hidden" class="" name="transaction_id" id="transaction-id">
                            @csrf
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="bill_no">Bill Number</label>
                            <input type="text" class="form-control text-dark bill-no" name="bill_no">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="bill_amount">Pending Bill Amount</label>
                            <input type="number" class="form-control text-dark bill-amount" name="bill_amount">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="due_date">Due Date</label>
                            <input type="date" class="form-control text-dark due-date" name="due_date">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary px-4">Pay Bill</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/tom-select.base.js') }}"></script>
<script>
    $(function() {
        const tom = new TomSelect("#operator");
        const validator = $("#gas-fetch-form").validate({
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
                    url: '{{ route("retailer.gas-bill-details") }}',
                    type: 'POST',
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

        $("#submit-form").validate({
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                consumer_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                bill_no: {
                    required: true,
                    digits: true,
                    minlength: 5,
                    maxlength: 20
                },
                bill_amount: {
                    required: true,
                    number: true,
                    min: 1,
                    max: 9999999
                },
                due_date: {
                    required: true,
                    date: true
                },
            },
            messages: {
                consumer_name: {
                    required: "Please enter consumer name",
                    minlength: "Name must be at least 3 characters",
                    maxlength: "Name cannot exceed 100 characters"
                },
                bill_no: {
                    required: "Please enter bill number",
                    digits: "Bill number should contain only numbers",
                    minlength: "Bill number must be at least 5 digits",
                    maxlength: "Bill number cannot exceed 20 digits"
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
    });
</script>


@endsection