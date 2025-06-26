@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-10">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" id="table-example">Brand :: Brands List </h5>
                    </div>
                    @if(userCan(125, 'can_add'))
                    <div class="col-auto ms-auto">
                        <div class="nav nav-pills nav-pills-falcon">
                            <button class="btn btn-sm btn-outline-secondary add"> <i class="fa fa-plus me-1"></i> Add
                                Brand</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class=" card-body">
                <div class="table-responsive scrollbar">
                    <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <div class="modal-header bg-light-primary">
                <h4 class="mb-1" id="modalExampleDemoLabel">Add Brand </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3">
                    <form id="addForm">
                        <div class="mb-2">
                            <label class="form-label" for="name">Brand Name <span class="required">*</span></label>
                            <input class="form-control" name="name" id="name" type="text" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="status">Status</label>
                            <select name="status" class="form-select" id="status">
                                <option value="1"> Active</option>
                                <option value="0"> Inactive</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <div class="modal-header bg-light-primary">
                <h4 class="mb-1" id="modalExampleDemoLabel">Edit Brand </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3">
                    <form id="editForm">
                        <div class="mb-2">
                            <label class="form-label" for="name">Brand Name <span class="required">*</span></label>
                            <input class="form-control" name="name" id="name" type="text" />
                            <input class="form-control" name="id" id="" type="hidden" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="status">Status</label>
                            <select name="status" class="form-select" id="status">
                                <option value="1"> Active</option>
                                <option value="0"> Inactive</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
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
            ajax: "{{ route('brands') }}",
            order: [
                [0, 'desc']
            ],
            columns: [{
                data: 'name',
                name: 'name'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
            ]
        });
        $('.add').on('click', function () {
            $('#addModal').modal('show');
        })
        $("#addForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ route('brands') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data?.message);
                            $('#addModal').modal('hide');
                            $(form).trigger("reset")
                            table.draw();
                            $("#overlay").hide();
                        } else {
                            $(form).validate().showErrors(data.data);
                            toastr.error(data?.message);
                            $("#overlay").hide();
                        }
                    }
                });
            }
        });
        $(document).on('click', ".edit", function () {
            var data = $(this).data('all')
            $('[name="id"]').val(data.id)
            document.forms['editForm']['name'].value = data.name;
            document.forms['editForm']['status'].value = data.status;
            $('#editModal').modal('show');
        })
        $("#editForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                const formDataObj = {};
                formData.forEach((value, key) => (formDataObj[key] = value));
                $.ajax({
                    url: "{{ route('brands') }}",
                    data: formDataObj,
                    type: 'PUT',
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data?.message);
                            $('#editModal').modal('hide');
                            table.draw();
                            $("#overlay").hide();
                        } else {
                            $(form).validate().showErrors(data.data);
                            toastr.error(data?.message);
                            $("#overlay").hide();
                        }
                    }
                });
            }
        });

        $(document).on('click', ".delete", function () {
            var id = $(this).data('id')
            swal(deleteSweetAlertConfig).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('brands.delete') }}",
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
    });
</script>
@endsection