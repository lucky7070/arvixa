@extends('layouts.retailer_app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ @$service->name }}</h5>
            <div>
                <a href="{{ route('msme-certificate') }}" class="btn btn-secondary me-1">
                    <i class="fa fa-plus me-1"></i>
                    New Certificate
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
                        <th>TXN ID</th>
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
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ request()->url() }}",
            order: [
                [3, 'desc']
            ],
            columns: [
                {
                    data: 'txn_id',
                    name: 'txn_id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'aadharcard',
                    name: 'aadharcard'
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
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    });
</script>
@endsection