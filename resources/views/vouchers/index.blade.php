@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Vouchers :: Vouchers List </h5>
            </div>
            <div class="col-auto ms-auto d-flex">
                @if(userCan(129, 'can_add'))
                <div class="nav nav-pills nav-pills-falcon">
                    <a href="{{ route('vouchers.add') }}" class="btn btn-outline-secondary me-4">
                        <i class="fa fa-plus me-1"></i>
                        Add Voucher
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="col-md-12">
            <div class="table-responsive scrollbar">
                <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Duration</th>
                            <th>Visibility</th>
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
</div>
</div>

@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('vouchers') }}",
            order: [
                [4, 'desc']
            ],
            columns: [
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'discount_amount',
                    name: 'discount_amount',
                    class: 'text-center'
                },
                {
                    data: 'starts_at',
                    name: 'starts_at'
                },
                {
                    data: 'is_public',
                    name: 'is_public'
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
                    name: 'name',
                    orderable: false,
                },
            ]
        });


        $(document).on('click', ".delete", function () {
            var id = $(this).data('id')
            swal(deleteSweetAlertConfig).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('vouchers.delete') }}",
                        data: { 'id': id },
                        type: 'DELETE',
                        success: function (data) {
                            if (data.status) {
                                swal(data.message, { icon: "success" });
                                table.draw();
                            } else {
                                toastr.error(data.message);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection