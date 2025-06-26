@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0">Products :: Products List </h5>
            </div>

            <div class="col-auto ms-auto">
                <a href="{{ route('products.export') }}" class="btn btn-outline-success me-2 btn-sm export">
                    <i class="fa-regular fa-file-excel me-1"></i>
                    Export
                </a>
                @if(userCan(127, 'can_add'))
                <a href="{{ route('products.add') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-plus me-1"></i>
                    Add Product
                </a>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive scrollbar">
            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Image</th>
                        <th>Item Id</th>
                        <th>Product Name</th>
                        <th>Sale Price</th>
                        <th>Stock</th>
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
            ajax: "{{ route('products') }}",
            order: [
                [1, 'desc']
            ],
            columns: [
                {
                    data: 'main_image',
                    name: 'main_image'
                },
                {
                    data: 'sku',
                    name: 'sku'
                },
                {
                    data: 'name',
                    name: 'name',
                    class: 'white-space-normal'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'stock',
                    name: 'stock'
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
            swal(deleteSweetAlertConfig).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('products.delete') }}",
                        data: { 'id': id },
                        type: 'DELETE',
                        success: function (data) {
                            if (data.status) {
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

        $(document).on('click', ".export", function (e) {
            var link = $(this).attr('href');
            e.preventDefault();
            var list = $('.check:checked');
            if (list.length > 0) {
                var selected = [];
                list.each(function () {
                    selected.push($(this).data('id'));
                });
                link += '?ids=' + JSON.stringify(selected);
            }
            window.open(link);
        });
    });
</script>
@endsection