@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">ITR Report</h5>
            <div class="dropdown-list dropdown" role="group">
                <button type="submit" form="exportForm" class="btn btn-outline-success">
                    <i class="fa fa-file-excel me-1"></i> Export
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="exportForm" method="get" action="{{ route('reports.itr-files.export') }}">
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
                    <label class="fs--1 mb-0" for="status">Status</label>
                    <select class="form-select form-select-sm update" name="status" id="status">
                        <option value="" selected>All</option>
                        <option value="0">Pending</option>
                        <option value="1">Submitted</option>
                        <option value="2">Completed</option>
                        <option value="3">Rejected</option>
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

        <div class="table-responsive scrollbar">
            <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>TXN ID</th>
                        <th>Retailer</th>
                        <th>Full Name</th>
                        <th>Aadhar Card</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="width: 120px;">Action</th>
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
                    d.status = $("#status").val();
                }
            },
            order: [
                [4, 'desc']
            ],
            columns: [{
                    data: 'token',
                    name: 'token',
                    class: 'fw-bold'
                },
                {
                    data: 'retailer_name',
                    name: 'retailer.name'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'adhaar_number',
                    name: 'adhaar_number'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'retailer.userId',
                    orderable: false,
                },
            ]
        });

        $('.submit').click(function() {
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            table.draw();
        });

        $('.reset').click(function() {
            setTimeout(() => table.draw(), 500)
        });
    });
</script>
@endsection