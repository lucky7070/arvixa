@extends('layouts.app')

@section('css')
<link href="{{ asset('assets/css/tom-select.default.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">

                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" id="table-example">Categories :: Categories List </h5>
                    </div>
                    <div class="col-auto ms-auto">
                        @if(userCan(124, 'can_add'))
                        <div class="nav nav-pills nav-pills-falcon">
                            <button class="btn btn-outline-secondary me-4 add">
                                <i class="fa fa-plus me-1"></i> Add Category
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive scrollbar">
                    <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                        style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th>Name</th>
                                <th>Short Order</th>
                                <th>Is Feature</th>
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

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content position-relative">
            <form id="addForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="parent_id">Parent Category</label>
                            <select class="form-select js-choice_add" id="parent_id" name="parent_id">
                                <option value="">Select Parent Category</option>
                                @if($categories)
                                @foreach($categories as $category)
                                <?php $dash=''; ?>
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="name">Category Name</label>
                            <input class="form-control" name="name" id="name" type="text" />
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="sort_order">Sort Order</label>
                            <input class="form-control" name="sort_order" id="sort_order" type="number" value="0" />
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="image">Image Icon</label>
                            <input class="form-control" name="image" id="image" type="file" />
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="banner">Banner</label>
                            <input class="form-control" name="banner" id="banner" type="file" />
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="is_feature">Is Feature</label>
                            <select name="is_feature" class="form-select" id="is_feature">
                                <option value="1"> Yes</option>
                                <option value="0"> No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="status">Status</label>
                            <select name="status" class="form-select" id="status">
                                <option value="1"> Active</option>
                                <option value="0"> Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light-dark" data-bs-dismiss="modal">Discard</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content position-relative">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="parent_id">Parent Category</label>
                            <select class="form-select js-choice_edit" id="parent_id" name="parent_id">
                                <option value="">Select Parent Category</option>
                                @if($categories)
                                @foreach($categories as $category)
                                <?php $dash=''; ?>
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="name">Category Name</label>
                            <input class="form-control" name="name" id="name" type="text" />
                            <input class="form-control" name="id" id="" type="hidden" />
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="sort_order">Sort Order</label>
                            <input class="form-control" name="sort_order" id="sort_order" type="number" />
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="image">Image Icon</label>
                            <input class="form-control" name="image" id="image" type="file" />
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="banner">Banner</label>
                            <input class="form-control" name="banner" id="banner" type="file" />
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="is_feature">Is Feature</label>
                            <select name="is_feature" class="form-select" id="is_feature">
                                <option value="1"> Yes</option>
                                <option value="0"> No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="status">Status</label>
                            <select name="status" class="form-select" id="status">
                                <option value="1"> Active</option>
                                <option value="0"> Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light-dark" data-bs-dismiss="modal">Discard</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/tom-select.base.js') }}"></script>
<script type="text/javascript">
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('categories') }}",
            order: [
                [0, 'desc']
            ],
            columns: [{
                data: 'name',
                name: 'name'
            },
            {
                data: 'sort_order',
                name: 'sort_order'
            },
            {
                data: 'is_feature',
                name: 'is_feature'
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

        const addSelect = new TomSelect(".js-choice_add");
        const editSelect = new TomSelect(".js-choice_edit");

        $('.add').on('click', function () {
            $('#addModal').modal('show');
        })

        $("#addForm").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                sort_order: {
                    required: true,
                },
                description: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                },
                image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                banner: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                sort_order: {
                    required: "Please enter sort order",
                },
                description: {
                    required: "Please enter description",
                },
                image: {
                    extension: "Please select only image file.",
                },
                banner: {
                    extension: "Please select only image file.",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ route('categories') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data.message);
                            $('#addModal').modal('hide');
                            $(form).trigger("reset")
                            table.draw();
                            $("#overlay").hide();
                        } else {
                            $(form).validate().showErrors(data.data);
                            toastr.error(data.message);
                            $("#overlay").hide();
                        }
                    }
                });
            }
        });


        $(document).on('click', ".edit", function () {
            var data = $(this).data('all');
            $('[name="id"]').val(data.id)
            document.forms['editForm']['name'].value = data.name;
            document.forms['editForm']['status'].value = data.status;
            document.forms['editForm']['is_feature'].value = data.is_feature;
            document.forms['editForm']['sort_order'].value = data.sort_order;
            document.forms['editForm']['description'].value = data.description;
            document.forms['editForm']['is_feature'].value = data.is_feature;
            editSelect.setValue(data.parent_id)
            $('#editModal').modal('show');
        })

        $("#editForm").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                sort_order: {
                    required: true,
                },
                description: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                },
                image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                banner: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                sort_order: {
                    required: "Please enter sort order",
                },
                description: {
                    required: "Please enter description",
                },
                image: {
                    extension: "Please select only image file.",
                },
                banner: {
                    extension: "Please select only image file.",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                formData.append('_method', 'PUT')

                $.ajax({
                    url: "{{ route('categories') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data.message);
                            $('#editModal').modal('hide');
                            table.draw();
                            $("#overlay").hide();
                        } else {
                            $(form).validate().showErrors(data.data);
                            toastr.error(data.message);
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
                        url: "{{ route('categories.delete') }}",
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