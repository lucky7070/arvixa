@php
    use Illuminate\Support\Facades\DB;

    $providers = DB::table('rproviders')->where('sertype', 'gas')->get();
@endphp


@extends('layouts.retailer_app')

@section('content')

<style>
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .card-title {
      font-weight: bold;
    }
    .form-control[readonly]{
        color: #6f6666 !important;
    }
  </style>

<div class="row">
    <div class="g-4">
            <!-- gas Bill -->
            <div class="col-12">
                <div class="card p-3">
                    <h5 class="card-title">Gas Bill</h5>
    
                    <form action="{{ route('retailer.gas-payment-submit') }}" method="POST" id="gas-bill-form">
                        @csrf
                        {{-- Row 1: State and Board --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="board_id" class="form-label">Select Board</label>
                                <select class="form-control board-id" name="board_id" required>
                                    <option value="">-- Select Board --</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="consumer_no" class="form-label">Consumer Number</label>
                                <input type="text" class="form-control consumer-no" name="consumer_no" placeholder="Consumer No" required>
                                <div class="consumer-message mt-1 small"></div>
                            </div>
                        </div>
            
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="consumer_name">Biller Name</label>
                                <input type="text" class="form-control consumer-name" name="consumer_name" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="bill_no">Bill Number</label>
                                <input type="text" class="form-control bill-no" name="bill_no" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="bill_amount">Pending Bill Amount</label>
                                <input type="text" class="form-control bill-amount" name="bill_amount" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="due_date">Due Date</label>
                                <input type="text" class="form-control due-date" name="due_date" readonly>
                            </div>
                        </div>
                    
                    </form>
                    <div class="col-md-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary w-100 gas-btn">Pay Bill</button>
                    </div>
                </div>
            </div>
    </div>
</div>
<!-- Confirmation Modal -->
<div class="modal fade" id="confirmgasModal" tabindex="-1" aria-labelledby="confirmgasModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to pay this gas bill?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmPayBtn">Yes, Pay Now</button>
      </div>
    </div>
  </div>
</div>
<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Payment Successful</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body success-message text-success">
        <!-- Success message will be injected here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<div id="gas-bill-details"></div>
@endsection
@section('js')
<script>
    $(document).ready(function () {
        $('.board-id, .consumer-no').on('change keyup', function () {
            var board_id = $('.board-id').val();
            var consumer_no = $('.consumer-no').val().trim();

            var isValidLength = consumer_no.length >= 6 && consumer_no.length <= 20;

            $('.consumer-message').html('');
            $('.consumer-name, .bill-no, .bill-amount, .due-date').val('');

            if (board_id !== "" && isValidLength) {
                $('.gas-btn').prop('disabled', true).text('Fetching...');

                $.ajax({
                    url: '{{ route("retailer.gas-bill-details") }}',
                    type: 'GET',
                    data: {
                        operator: board_id,
                        tel: consumer_no,
                        offer: 'roffer'
                    },
                    success: function (response) {
                        if (response.biller_name === 'Invalid Details') {
                            $('.consumer-message').html('<span class="text-danger">Invalid bill details.</span>');
                        } else {
                            $('.consumer-name').val(response.biller_name);
                            $('.bill-no').val(response.bill_number);
                            $('.bill-amount').val(response.amount_due);
                            $('.due-date').val(response.due_date);
                            $('.consumer-message').html('<span class="text-success">Bill details fetched successfully.</span>');
                        }
                    },
                    error: function () {
                        $('.consumer-message').html('<span class="text-danger">Failed to fetch bill details. Please check your input.</span>');
                    },
                    complete: function () {
                        $('.gas-btn').prop('disabled', false).text('Pay Bill');
                    }
                });

            } else if (consumer_no !== "" && !isValidLength) {
                $('.consumer-message').html('<span class="text-danger">Consumer Number must be between 6 and 20 digits.</span>');
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('.gas-btn').on('click', function () {
            let billAmount = $('.bill-amount').val();

            if (billAmount && billAmount !== 'Invalid Details') {
                $('#confirmgasModal').modal('show');
            } else {
                $('.consumer-message').html('<span class="text-danger">Please fetch valid bill details before proceeding.</span>');
            }
        });

        $('#confirmPayBtn').on('click', function () {
            var form = $('#gas-bill-form');
            var formData = form.serialize();

            $('#confirmgasModal').modal('hide');

            $.ajax({
                url: '{{ route("retailer.gas-payment-submit") }}',
                type: 'POST',
                data: $('#gas-bill-form').serialize(),
                success: function(response) {
                    if (response.status) {
                        $('.gas-btn').prop('disabled', true).text('Pay Bill');
                        // Show success modal and fill data
                        $('#modalBillerName').text(response.data.consumer_name);
                        $('#modalBillNo').text(response.data.bill_no);
                        $('#modalAmountPaid').text(parseFloat(response.data.bill_amount).toFixed(2));
            
                        // Generate or route to downloadable receipt (adjust route as needed)
                        const receiptUrl = "{{ url('retailer/gas-download-receipt') }}/" + response.data.id;
                        $('#downloadReceiptBtn').attr('href', receiptUrl);
            
                        $('#successModal').modal('show');
                    } else {
                        $('.consumer-message').html('<span class="text-danger">' + response.message + '</span>');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message ?? 'Something went wrong.';
                    $('.consumer-message').html('<span class="text-danger">' + message + '</span>');
                }
            });
        });
        $(document).on('click', '#okThanksBtn', function () {
            location.reload();
        });
    });
</script>
@endsection