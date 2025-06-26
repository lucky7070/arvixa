@extends('layouts.employee_app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Retailer :: PanCards - 
                    <span class="text-primary">{{ $user['name'] }}</span> 
                </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    <a href="{{ route('employee.retailers')  }}" class="btn btn-outline-secondary me-2">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive scrollbar">
            <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Card Type</th>
                        <th>Type</th>
                        <th>NSDL TXN</th>
                        <th>Full Name</th>
                        <th>Date</th>
                        <th>Status</th>
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
                    data: 'is_physical_card',
                    name: 'is_physical_card'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'nsdl_txn_id',
                    name: 'nsdl_txn_id'
                },
                {
                    data: 'full_name',
                    name: 'full_name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'nsdl_complete',
                    name: 'nsdl_complete',
                    orderable: false,
                },
             
            ]
        });
    });
</script>
@endsection