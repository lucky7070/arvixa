@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">LIC Bill Repoprt</h5>
            <div class="dropdown-list dropdown" role="group">
                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-success" data-form="exportForm">
                    <i class="fa fa-file-excel me-1"></i> Export
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="exportForm" method="get" action="{{ route('reports.bills.export', 'lic') }}">
            <div class="row">
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="start_date">Start Date</label>
                    <input class="form-control form-control-sm update" type="date" value="{{ old('start_date') }}"
                        name="start_date" id="start_date">
                    <input type="hidden" name="id" value="1">
                </div>
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="end_date">End Date</label>
                    <input class="form-control form-control-sm update" type="date"
                        value="{{ old('end_date', date('Y-m-d')) }}" name="end_date" id="end_date">
                </div>
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="is_refunded">Refunded</label>
                    <select class="form-select form-select-sm update" name="is_refunded" id="is_refunded">
                        <option value="" selected>All</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-3 mb-2 text-end ms-auto me-0">
                    <br class="d-inline-block">
                    <button type="button" class="btn btn-outline-primary btn-sm submit">
                        <i class="fa fa-check"></i> Submit
                    </button>
                    <button type="reset" class="btn btn-outline-danger btn-sm reset">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </div>
            </div>
        </form>

        <hr>
        <div class="table-responsive scrollbar">
            <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Transaction Id</th>
                        <th>Retailer Name</th>
                        <th>Consumer Name</th>
                        <th>Consumer No</th>
                        <th>Bill No</th>
                        <th>Date</th>
                        <th>Amount</th>
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
            ajax: {
                url: "{{ request()->url() }}",
                searching: false,
                data: function(d) {
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                    d.is_refunded = $("#is_refunded").val();
                }
            },
            order: [
                [3, 'desc']
            ],
            columns: [{
                    data: 'transaction_id',
                    name: 'transaction_id'
                },
                {
                    data: 'retailer_name',
                    name: 'retailers.name',
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
                    data: 'provider_name',
                    name: 'rproviders.name',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('[data-form="exportForm"]').on('click', function() {
            $('#exportForm').submit()
        })

        $('.submit').click(function() {
            table.draw();
        });

        $('.reset').click(function() {
            setTimeout(() => table.draw(), 500)
        });
    });
</script>
@endsection