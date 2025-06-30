@extends('layouts.retailer_app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gas Bill List</h5>
            <div>
                <a href="{{ route('retailer.gas-bill-export') }}" class="btn btn-success me-1">
                    <i class="fa fa-file-excel me-1"></i>
                    Export
                </a>
                <a href="{{ route('retailer.gas-bill') }}" class="btn btn-primary me-1">
                    <i class="fa fa-plus me-1"></i>
                    Pay Gas Bill
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive scrollbar">
            <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Transaction Id</th>
                        <th>Consumer Name</th>
                        <th>Consumer No</th>
                        <th>Bill No</th>
                        <th>Date</th>
                        <th>Bill Amount</th>
                        <th>Commission</th>
                        <th>TDS Amount</th>
                        <th>Provider Name</th>
                        <th>Action</th>
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
    $(function() {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ request()->url() }}",
            order: [
                [3, 'desc']
            ],
            columns: [{
                    data: 'transaction_id',
                    name: 'transaction_id'
                },
                {
                    data: 'consumer_name',
                    name: 'consumer_name'
                },
                {
                    data: 'consumer_no',
                    name: 'consumer_no',
                },
                {
                    data: 'bill_no',
                    name: 'bill_no',
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'bill_amount',
                    name: 'bill_amount',
                },
                {
                    data: 'commission',
                    name: 'commission',
                },
                {
                    data: 'tds',
                    name: 'tds',
                },
                {
                    data: 'provider_name',
                    name: 'provider_name',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    });
</script>
@endsection