@extends('layouts.employee_app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Main Distributors :: Ledger - <span class="text-primary">{{
                        $user['name'] }} ( ₹ {{ $user['user_balance'] }})</span> </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    <a href="{{ route('employee.main_distributors')  }}" class="btn btn-outline-secondary me-2">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>

                    <a href="{{ route('ledger.export', ['user' => $user['slug'], 'user_type' => 2 ])  }}"
                        class="btn btn-outline-success btn-icon me-2">
                        <i class="fa-duotone fa-file-excel"></i>
                    </a>

                </div>
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
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('employee.main_distributors.ledger', $user['slug']) }}",
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
            ]
        });

    });
</script>
@endsection