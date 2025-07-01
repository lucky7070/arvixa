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
    <div class="col-12" id="bill-details" style="display: none;">
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
                            <label for="bill_no">Bill Number</label>
                            <input type="text" class="form-control text-dark bill-no" name="bill_no" readonly>
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
</div>
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
    });
</script>


@endsection