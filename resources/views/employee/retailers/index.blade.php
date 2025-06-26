@extends('layouts.employee_app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Retailers :: Retailers List </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    <a href="{{ route('employee.dashboard')  }}" class="btn btn-outline-danger me-2">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                    <a href="{{ route('employee.retailers.export') }}" class="btn btn-outline-success me-2"> <i
                            class="fa fa-file-excel me-1"></i>
                        Export</a>
                    <a href="{{ route('employee.retailers.add') }}" class="btn btn-outline-secondary me-2"> <i
                            class="fa fa-plus me-1"></i>
                        Add Retailer</a>
                </div>
            </div>

        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive scrollbar">
            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Distributor</th>
                        <th></th>
                        <th>User Id / Mobile</th>
                        <th>Status</th>
                        <th>Created Date</th>
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
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: {
                url: "{{ request()->url() }}",
                data: function (d) {
                    d.distributor = "{{ request('distributor') }}";
                    d.main_distributor = "{{ request('main_distributor') }}";
                }
            },
            order: [
                [6, 'desc']
            ],
            columns: [{
                data: 'image',
                name: 'image',
                orderable: false,
                searchable: false
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'main_distributor',
                name: 'main_distributor.name'
            },
            {
                data: 'distributor',
                name: 'distributor.name',
                visible: false
            },
            {
                data: 'userId',
                name: 'userId'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'created_at',
                name: 'created_at',
            },
            {
                data: 'action',
                name: 'mobile',
                orderable: false,
            },
            ]
        });
    });
</script>
@endsection