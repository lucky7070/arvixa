@php
    use Illuminate\Support\Facades\DB;

    $providers = DB::table('rproviders')->where('sertype', 'electricity')->get();
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
    .consumer-message {
        font-size: 14px !important; 
    }
  </style>

<div class="row">
    <div class="g-4">
            <!-- Electricity Bill -->
            <div class="col-12">
                <div class="card p-3">
                    <h5 class="card-title">Electricity Bill</h5>
    
                    <form action="{{ route('retailer.electricity-payment-submit') }}" method="POST" id="electricity-bill-form">
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
                                <input type="text" class="form-control consumer-no" name="consumer_no" placeholder="Consumer No" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <!--<div class="consumer-message mt-1 small"></div>-->
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
                        <button type="button" class="btn btn-primary w-100 electricity-btn">Pay Bill</button>
                    </div>
                    <div class="consumer-message mt-1 small"></div>
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
                                    <!--<a href="#" class="btn btn-sm btn-primary">View</a>-->
                                    <a href="javascript:void(0)" class="btn btn-sm btn-primary view-bill-btn">View</a>

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


<!-- View Bill Modal -->
<div class="modal fade" id="billDetailsModal" tabindex="-1" aria-labelledby="billDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="billDetailsModalLabel">Bill Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Transaction ID:</strong> <span id="modalTransactionId"></span></p>
        <p><strong>Retailer Name:</strong> <span id="modalRetailerName"></span></p>
        <p><strong>Retailer Mobile:</strong> <span id="modalRetailerMobile"></span></p>
        <p><strong>Provider:</strong> <span id="modalProvider"></span></p>
        <p><strong>Account:</strong> <span id="modalAccount"></span></p>
        <p><strong>Amount:</strong> ₹<span id="modalAmount"></span></p>
        <p><strong>Due Date:</strong> <span id="modalDueDate"></span></p>
        <p><strong>Profit:</strong> ₹<span id="modalProfit"></span></p>
        <p><strong>TDS:</strong> ₹<span id="modalTds"></span></p>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
      </div>
    </div>
  </div>
</div>





<!-- Confirmation Modal -->
<div class="modal fade" id="confirmElectricityModal" tabindex="-1" aria-labelledby="confirmElectricityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to pay this electricity bill?
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
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Payment Successful</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Biller Name:</strong> <span id="modalBillerName"></span></p>
        <p><strong>Bill Number:</strong> <span id="modalBillNo"></span></p>
        <p><strong>Amount Paid:</strong> ₹<span id="modalAmountPaid"></span></p>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <a href="#" id="downloadReceiptBtn" class="btn btn-outline-primary" target="_blank">Download Receipt</a>
        <button type="button" class="btn btn-success" id="okThanksBtn" data-bs-dismiss="modal">OK, Thanks</button>
      </div>
    </div>
  </div>
</div>


<div id="electricity-bill-details"></div>
@endsection
@section('js')

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.view-bill-btn').forEach(function(button) {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const cells = row.querySelectorAll('td');

            // Assign values from respective <td>s
            document.getElementById('modalTransactionId').innerText = cells[0].innerText.trim();
            document.getElementById('modalRetailerName').innerText = cells[1].querySelector('strong')?.innerText || 'N/A';
            document.getElementById('modalRetailerMobile').innerText = cells[1].querySelector('small')?.innerText || 'N/A';
            document.getElementById('modalProvider').innerText = cells[2].innerText.trim();
            document.getElementById('modalAccount').innerText = cells[3].querySelectorAll('div')[0]?.innerText.split(':')[1]?.trim() || 'N/A';
            document.getElementById('modalAmount').innerText = cells[3].querySelectorAll('div')[1]?.innerText.split(':')[1]?.trim().replace('₹','') || '0.00';
            document.getElementById('modalDueDate').innerText = cells[3].querySelectorAll('div')[2]?.innerText.split(':')[1]?.trim() || 'N/A';
            document.getElementById('modalProfit').innerText = cells[4].querySelectorAll('div')[0]?.innerText.split(':')[1]?.trim().replace('₹','') || '0.00';
            document.getElementById('modalTds').innerText = cells[4].querySelectorAll('div')[1]?.innerText.split(':')[1]?.trim().replace('₹','') || '0.00';
            document.getElementById('modalStatus').innerText = cells[5].innerText.trim();

            // Show the modal
            new bootstrap.Modal(document.getElementById('billDetailsModal')).show();
        });
    });
});
</script>




<script>
    $(document).ready(function () {
        $('.board-id, .consumer-no').on('change keyup', function () {
            var board_id = $('.board-id').val();
            var consumer_no = $('.consumer-no').val().trim();

            // Example: Minimum 6, maximum 20 digits allowed
            var isValidLength = consumer_no.length >= 6 && consumer_no.length <= 20;

            // Clear messages and fields first
            $('.consumer-message').html('');
            $('.consumer-name, .bill-no, .bill-amount, .due-date').val('');

            if (board_id !== "" && isValidLength) {
                $('.electricity-btn').prop('disabled', true).text('Fetching...');

                $.ajax({
                    url: '{{ route("retailer.electricity-bill-details") }}',
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
                        $('.electricity-btn').prop('disabled', false).text('Pay Bill');
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
        $('.electricity-btn').on('click', function () {
            let billAmount = $('.bill-amount').val();

            if (billAmount && billAmount !== 'Invalid Details') {
                $('#confirmElectricityModal').modal('show');
            } else {
                $('.consumer-message').html('<span class="text-danger">Please fetch valid bill details before proceeding.</span>');
            }
        });

        $('#confirmPayBtn').on('click', function () {
            var form = $('#electricity-bill-form');
            var formData = form.serialize();

            $('#confirmElectricityModal').modal('hide');

            $.ajax({
                url: '{{ route("retailer.electricity-payment-submit") }}',
                type: 'POST',
                data: $('#electricity-bill-form').serialize(),
                success: function(response) {
                    if (response.status) {
                        $('.electricity-btn').prop('disabled', true).text('Pay Bill');
                        // Show success modal and fill data
                        $('#modalBillerName').text(response.data.consumer_name);
                        $('#modalBillNo').text(response.data.bill_no);
                        $('#modalAmountPaid').text(parseFloat(response.data.bill_amount).toFixed(2));
            
                        // Generate or route to downloadable receipt (adjust route as needed)
                        const receiptUrl = "{{ url('retailer/download-receipt') }}/" + response.data.id;
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