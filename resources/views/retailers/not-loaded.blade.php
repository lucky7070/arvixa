@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Retailers :: Who Not Loaded Balance </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    <a href="{{ route('retailers')  }}" class="btn btn-outline-secondary me-2">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                    @if(userCan(106, 'can_view'))
                    <a href="{{ route('retailers.not_loaded.export', [ 'main_distributor' => request('main_distributor'), 'distributor' => request('distributor') ]) }}"
                        class="btn btn-outline-success me-2">
                        <i class="fa fa-file-excel me-1"></i>
                        Export
                    </a>
                    @endif

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
                        <th></th>
                        <th>Name</th>
                        <th>M.D. / Distributor</th>
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
            "pageLength": 10,
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
                name: 'mobile',
                data: 'mobile',
                visible: false
            },
            {
                data: 'main_distributor',
                name: 'main_distributor.name'
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
                name: 'created_at'
            },
            {
                data: 'action',
                name: 'distributor.name',
                orderable: false,
            },
            ]
        });

        $(document).on('click', ".delete", function () {
            var id = $(this).data('id')
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this record..!",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancel",
                        visible: true,
                        className: "btn btn-dark",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Yes, Delete It.!",
                        value: true,
                        visible: true,
                        className: "btn btn-danger",
                        closeModal: true
                    }
                },
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('retailers.delete') }}",
                        data: { 'id': id },
                        type: 'DELETE',
                        success: function (data) {
                            if (data.success) {
                                swal(data?.message, { icon: "success" });
                                table.draw();
                            } else {
                                toastr.error(data?.message);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection