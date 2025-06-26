@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Main Distributors :: Main Distributors List </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    @if(userCan(104, 'can_view'))
                    <a href="{{ route('main_distributors.export') }}" class="btn btn-outline-success me-2">
                        <i class="fa fa-file-excel me-1"></i>
                        Export
                    </a>
                    @endif

                    @if(userCan(104, 'can_add'))
                    <a href="{{ route('main_distributors.add') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-plus me-1"></i>
                        Add Main Distributor
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
                        <th>Name</th>
                        <th>Email / User Id</th>
                        <th>Mobile</th>
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
            ajax: "{{ route('main_distributors') }}",
            order: [
                [4, 'desc']
            ],
            columns: [{
                data: 'image',
                name: 'userId',
                orderable: false,
            },
            {
                data: 'name',
                name: 'name',
                class: "fw-bold text-dark"
            },
            {
                data: 'email',
                name: 'email',
                class: "fw-bold text-dark"
            },
            {
                data: 'mobile',
                name: 'mobile',
                class: "fw-bold text-dark"
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
                name: 'action',
                orderable: false,
                searchable: false
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
                        url: "{{ route('main_distributors.delete') }}",
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