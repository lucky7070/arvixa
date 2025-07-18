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
                        <th>Provider Name</th>
                        <th>Bill Details</th>
                        <th>Profit & TDS</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('partial.receipt-modal')

@endsection

@section('js')
<script type="text/javascript">
    $(function() {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ request()->url() }}",
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'transaction_id',
                    name: 'bills.created_at'
                },
                {
                    data: 'provider_name',
                    name: 'providers.name',
                },
                {
                    data: 'consumer_no',
                    name: 'consumer_no',
                },
                {
                    data: 'commission',
                    name: 'commission',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'transaction_id',
                    name: 'bills.transaction_id',
                    visible: false
                },
                {
                    data: 'remark',
                    name: 'bills.remark',
                    visible: false
                },
                {
                    data: 'bill_no',
                    name: 'bills.bill_no',
                    visible: false
                },
                {
                    data: 'bu_code',
                    name: 'bills.bu_code',
                    visible: false
                },
            ]
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
            $('#bill-details .type').text('Gas Bill')
            $('#receipt').prop('href', data.receipt)
            $('#recipt-modal').modal('show')
        });
    });
</script>
@endsection