@php
    use Illuminate\Support\Facades\DB;

    $providers = DB::table('rproviders')->where('sertype', 'water')->get();
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
            <!-- water Bill -->
            <div class="col-12">
                <div class="card p-3">
                    <h5 class="card-title">Water Bill</h5>
    
                    <form action="{{ route('retailer.water-payment-submit') }}" method="POST" id="water-bill-form">
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
                        <button type="button" class="btn btn-primary w-100 water-btn">Pay Bill</button>
                    </div>
                </div>
            </div><br>
            
           <div class="table-responsive scrollbar">
                <table id="zero-config" class="table table-bordered table-hover table-striped fs--1 mb-0 table-datatable w-100">
                   <thead style="background-color: #cfe2ff; color: #000;">
                        <tr>
                            <th>Transaction ID</th>
                            <th>Retailer Details</th>
                            <th>Providers</th>
                            <th>Bill Details</th>
                            <th>Profit & TDS</th>
                            <th>Status</th>
                            <th style="width: 100px;" class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($bills as $bill)
                            <tr>
                                <td>{{ $bill->transaction_id ?? 'N/A' }}</td>
            
                                <td>
                                    @if(isset($bill->retailer))
                                        <strong>{{ $bill->retailer->name }}</strong><br>
                                        <small>{{ $bill->retailer->mobile }}</small>
                                    @else
                                        N/A
                                    @endif
                                </td>
            
                                <td>{{ $bill->board->name ?? 'N/A' }}</td>
            
                                <td>
                                    <div><strong>Account:</strong> {{ $bill->bill_no ?? 'N/A' }}</div>
                                    <div><strong>Amount:</strong> ₹{{ $bill->bill_amount ?? '0.00' }}</div>
                                    <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($bill->due_date)->format('d-m-Y') }}</div>
                                </td>
            
                                <td>
                                    <div><strong>Profit:</strong> ₹{{ $bill->profit ?? '0.00' }}</div>
                                    <div><strong>TDS:</strong> ₹{{ $bill->tds ?? '0.00' }}</div>
                                </td>
            
                                <td>
                                   <span class="badge bg-success">Success</span>
                                </td>
            
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No bill records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            
                {{-- Laravel Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {!! $bills->links('pagination::bootstrap-5') !!}
                </div>
            </div>
    </div>
</div>
<!-- Confirmation Modal -->
<div class="modal fade" id="confirmwaterModal" tabindex="-1" aria-labelledby="confirmwaterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to pay this water bill?
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

<div id="water-bill-details"></div>
@endsection
@section('js')
<script>
    $(document).ready(function () {
        $('.board-id, .consumer-no').on('change keyup', function () {
            var board_id = $('.board-id').val();
            var consumer_no = $('.consumer-no').val().trim();

            var isValidLength = consumer_no.length >= 6 && consumer_no.length <= 20;

            $('.consumer-message').html('');
            $('.consumer-name, .bill-amount, .due-date').val('');

            if (board_id !== "" && isValidLength) {
                $('.water-btn').prop('disabled', true).text('Fetching...');

                $.ajax({
                    url: '{{ route("retailer.water-bill-details") }}',
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
                            $('.bill-amount').val(response.amount_due);
                            $('.due-date').val(response.due_date);
                            $('.consumer-message').html('<span class="text-success">Bill details fetched successfully.</span>');
                        }
                    },
                    error: function () {
                        $('.consumer-message').html('<span class="text-danger">Failed to fetch bill details. Please check your input.</span>');
                    },
                    complete: function () {
                        $('.water-btn').prop('disabled', false).text('Pay Bill');
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
        $('.water-btn').on('click', function () {
            let billAmount = $('.bill-amount').val();

            if (billAmount && billAmount !== 'Invalid Details') {
                $('#confirmwaterModal').modal('show');
            } else {
                $('.consumer-message').html('<span class="text-danger">Please fetch valid bill details before proceeding.</span>');
            }
        });

        $('#confirmPayBtn').on('click', function () {
            var form = $('#water-bill-form');
            var formData = form.serialize();

            $('#confirmwaterModal').modal('hide');

            $.ajax({
                url: '{{ route("retailer.water-payment-submit") }}',
                type: 'POST',
                data: $('#water-bill-form').serialize(),
                success: function(response) {
                    if (response.status) {
                        $('.water-btn').prop('disabled', true).text('Pay Bill');
                        // Show success modal and fill data
                        $('#modalBillerName').text(response.data.consumer_name);
                        $('#modalAmountPaid').text(parseFloat(response.data.bill_amount).toFixed(2));
            
                        // Generate or route to downloadable receipt (adjust route as needed)
                        const receiptUrl = "{{ url('retailer/water-download-receipt') }}/" + response.data.id;
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