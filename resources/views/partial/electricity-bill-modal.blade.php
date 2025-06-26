<div class="modal fade" id="electricityBillModal" tabindex="-1" aria-labelledby="electricityBillModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <form method="POST" action="{{ route('retailer.electricity-payment-submit') }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title" id="electricityBillModalLabel">Electricity Bill Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <table class="table table-bordered mb-3">
            <tr>
              <th>Customer Name</th>
              <td>{{ $customer_name }}</td>
            </tr>
            <tr>
              <th>Amount Due</th>
              <td>₹ {{ $amount_due }}</td>
            </tr>
            <tr>
              <th>Due Date</th>
              <td>{{ $due_date }}</td>
            </tr>
            <tr>
              <th>Bill Number</th>
              <td>{{ $bill_number }}</td>
            </tr>
          </table>

          <div id="modal-error-msg" class="text-danger d-none"></div>

          <!-- Hidden Inputs to Submit -->
          <input type="hidden" name="customer_name" value="{{ $customer_name }}">
          <input type="hidden" name="amount_due" value="{{ $amount_due }}">
          <input type="hidden" name="due_date" value="{{ $due_date }}">
          <input type="hidden" name="bill_number" value="{{ $bill_number }}">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Pay Now</button>
        </div>

      </form>
    </div>
  </div>
</div>

