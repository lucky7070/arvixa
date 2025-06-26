@extends('layouts.app')


@section('css')
<link href="{{ asset('assets/css/tom-select.default.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom-tomSelect.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">

                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" id="table-example">Admin Banner :: Admin Banners List </h5>
                    </div>
                    <div class="col-auto ms-auto">
                        @if(userCan(113, 'can_add'))
                        <div class="nav nav-pills nav-pills-falcon">
                            <button class="btn btn-outline-secondary me-4 add"> <i class="fa fa-plus me-1"></i> Add
                                Banner</button>
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
                                <th>Created Date</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" State="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content position-relative">
            <form id="addForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Add Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="image">Image :</label>
                            <input class="form-control" name="image" id="image" type="file" />
                            <p class="text-secondary small">Image size must be of 1024px * 175px</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="is_special" class="form-label">Is Special Banner</label>
                            <select class="form-select" name="is_special" id="is_special">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="banner_for" class="form-label">Banner Show To</label>
                            <select multiple class="form-select" name="banner_for[]" id="banner_for">
                                <option value="">Select</option>
                                @foreach(config('constant.user_type_list',[]) as $key => $row)
                                <option value="{{ $key }}">{{ $row }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="url">URL</label>
                            <input class="form-control" id="url" placeholder="Url" name="url" type="text" value="" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="1">Active</option>
                                <option value="0">In-Active</option>
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
                    <h5 class="modal-title" id="tabsModalLabel">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="image">Image :</label>
                            <input type="hidden" name="id" value="">
                            <input class="form-control" name="image" id="image" type="file" />
                            <p class="text-secondary small">Image size must be of 1024px * 175px</p>
                            <img class="viewImg img-thumbnail" src="" alt="" style="width: 150px; max-height: 50px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="is_special" class="form-label">Is Special Banner</label>
                            <select class="form-select" name="is_special" id="is_special">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="banner_for" class="form-label">Banner Show To</label>
                            <select multiple class="form-select" name="banner_for[]" id="banner_for_edit">
                                <option value="">Select</option>
                                @foreach(config('constant.user_type_list',[]) as $key => $row)
                                <option value="{{ $key }}">{{ $row }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="url">URL</label>
                            <input class="form-control" id="url" placeholder="Url" name="url" type="text" value="" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="1">Active</option>
                                <option value="0">In-Active</option>
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
<script src="{{ asset('assets/js/tom-select.base.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {

        const tomAdd = new TomSelect("#banner_for");
        const tomEdit = new TomSelect("#banner_for_edit");

        var base_url = "{{ asset('storage') }}/";
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('admin-banners') }}",
            order: [
                [0, 'desc']
            ],
            columns: [{
                data: 'image',
                name: 'image'
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
            }]
        });


        $('.add').on('click', function () {
            $('#addModal').modal('show');
        })

        $("#addForm").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                image: {
                    required: true,
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                is_special: {
                    required: true,
                },
                status: {
                    required: true,
                },
                'banner_for[]': {
                    required: true,
                },
                url: {
                    url: true
                }
            },
            messages: {
                image: {
                    required: "Please select image file.",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ route('admin-banners') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (data) {
                        if (data.success) {
                            toastr.success(data?.message);
                            $('#addModal').modal('hide');
                            $(form).trigger("reset")
                            table.draw();
                            $("#overlay").hide();
                        } else {
                            var error = { ...data.data, 'banner_for[]': data.data.banner_for };
                            delete error.banner_for;
                            $(form).validate().showErrors(error);
                            toastr.error(data?.message);
                            $("#overlay").hide();
                        }
                    }
                });
            },
            errorPlacement: function (error, element) {
                if ($(element).hasClass('tomselected')) {
                    $(element).parent().append(error)
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $(document).on('click', ".edit", function () {
            var data = $(this).data('all')
            $('[name="id"]').val(data.id)

            $('#editForm .viewImg').attr('src', base_url + data.image)
            document.forms['editForm']['url'].value = data.url;
            document.forms['editForm']['is_special'].value = data.is_special;
            document.forms['editForm']['status'].value = data.status;
            tomEdit.setValue(data.banner_for.split(','));
            $('#editModal').modal('show');
        })

        $("#editForm").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                is_special: {
                    required: true,
                },
                status: {
                    required: true,
                },
                'banner_for[]': {
                    required: true,
                },
                url: {
                    url: true
                }
            },
            messages: {
                image: {
                    required: "Please select image file.",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                formData.append('_method', 'PUT')
                $.ajax({
                    url: "{{ route('admin-banners') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (data) {
                        if (data.success) {
                            toastr.success(data?.message);
                            $('#editModal').modal('hide');
                            table.draw();
                            $("#overlay").hide();
                        } else {
                            var error = { ...data.data, 'banner_for[]': data.data.banner_for };
                            delete error.banner_for;
                            $(form).validate().showErrors(data.data);
                            toastr.error(data?.message);
                            $("#overlay").hide();
                        }
                    }
                });
            },
            errorPlacement: function (error, element) {
                if ($(element).hasClass('tomselected')) {
                    $(element).parent().append(error)
                } else {
                    error.insertAfter(element);
                }
            }
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
                        url: "{{ route('admin-banners') }}",
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