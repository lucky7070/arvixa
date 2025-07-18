@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">

                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" id="table-example">Cities :: Cities List </h5>
                    </div>
                    <div class="col-auto ms-auto">
                        @if(userCan(111, 'can_add'))
                        <div class="nav nav-pills nav-pills-falcon">
                            <button class="btn btn-outline-secondary me-4 add"> <i class="fa fa-plus me-1"></i> Add
                                City</button>
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
                                <th>State Name</th>
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

<div class="modal fade" id="addModal" tabindex="-1" City="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <form id="addForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Add City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3">
                        <div class="mt-2">
                            <label class="form-label" for="state_id">State <span class="required">*</span></label>
                            <select name="state_id" class="form-select" id="state_id">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                <option value="{{ $state['id'] }}" {{ old('state_id')==$state['id'] ? 'selected' : ''
                                    }}>
                                    {{ $state['name'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label" for="name">City Name :</label>
                            <input class="form-control" name="name" id="name" type="text" />
                        </div>
                        <div class="mb-3">
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

<div class="modal fade" id="editModal" tabindex="-1" City="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Edit City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3">
                        <div class="mt-2">
                            <label class="form-label" for="state_id">State <span class="required">*</span></label>
                            <select name="state_id" class="form-select" id="state_id">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                <option value="{{ $state['id'] }}" {{ old('state_id')==$state['id'] ? 'selected' : ''
                                    }}>
                                    {{ $state['name'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label" for="name">City Name :</label>
                            <input class="form-control" name="name" id="name" type="text" />
                            <input class="form-control" name="id" id="" type="hidden" />
                        </div>
                        <div class="mb-3">
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
<script type="text/javascript">
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('cities') }}",
            order: [
                [0, 'desc']
            ],
            columns: [{
                data: 'name',
                name: 'name'
            },
            {
                data: 'state_name',
                name: 'states.name'
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
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
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
                    url: "{{ route('cities') }}",
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
            document.forms['editForm']['state_id'].value = data.state_id;
            document.forms['editForm']['status'].value = data.status;
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
                    url: "{{ route('cities') }}",
                    data: formDataObj,
                    type: 'PUT',
                    success: function (data) {
                        if (data.success) {
                            toastr.success(data?.message);
                            $('#editModal').modal('hide');
                            table.draw();
                            $("#overlay").hide();
                        } else {
                            toastr.error(data?.message);
                            $("#overlay").hide();
                        }
                    }
                });
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
                        url: "{{ route('cities.delete') }}",
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