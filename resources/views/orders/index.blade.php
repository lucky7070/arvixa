@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Store Orders :: Orders List </h5>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-success me-2" form="exportForm">
                    <i class="fa fa-file-excel me-1"></i> Export
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="exportForm" method="get" action="{{ route('orders.export') }}">
            <div class="row">
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="start_date">Start Date</label>
                    <input class="form-control form-control-sm update" type="date" value="" name="start_date"
                        id="start_date">
                </div>
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="end_date">End Date</label>
                    <input class="form-control form-control-sm update" type="date"
                        value="{{ old('end_date', date('Y-m-d')) }}" name="end_date" id="end_date">
                </div>
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="order_status_id">Order Status</label>
                    <select class="form-select form-select-sm update" name="order_status_id" id="order_status_id">
                        <option value="" selected>All</option>
                        @foreach(config('constant.order_status_list', []) as $key => $row)
                        <option value="{{ $key }}">{{ $row }}</option>
                        @endforeach
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
        <div class="table-responsive scrollbar mt-2">
            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Order Id</th>
                        <th>Retailer</th>
                        <th>Mobile / Email</th>
                        <th></th>
                        <th>Order Date</th>
                        <th>Order Total</th>
                        <th>Status</th>
                        <th width="100px">Action</th>
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
            ajax: {
                url: "{{ request()->url() }}",
                searching: false,
                data: function (d) {
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                    d.order_status_id = $("#order_status_id").val();
                }
            },
            order: [
                [4, 'desc']
            ],
            columns: [
                {
                    data: 'voucher_no',
                    name: 'voucher_no',
                },
                {
                    data: 'retailer',
                    name: 'retailer',
                },
                {
                    name: 'customer_mobile',
                    data: 'customer_mobile',
                },
                {
                    name: 'customer_email',
                    data: 'customer_email',
                    visible: false
                },
                {
                    data: 'date',
                    name: 'created_at'
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'order_status',
                    name: 'order_status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('.submit').click(function () {
            table.draw();
        });

        $('.reset').click(function () {
            setTimeout(() => table.draw(), 500)
        });
    });
</script>
@endsection