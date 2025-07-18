@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0">Customer :: Banks -
                            <span class="text-primary">{{ $customer['name'] }}</span>
                        </h5>
                    </div>
                    <div class="col-auto ms-auto">
                        <div class="nav nav-pills nav-pills-falcon">
                            <a class="btn btn-outline-secondary me-1" href="{{ route('customers') }}">
                                <i class="fa fa-arrow-left me-1"></i> Go Back
                            </a>
                            <button class="btn btn-outline-primary add">
                                <i class="fa fa-plus me-1"></i> Add Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive scrollbar">
                    <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th>Bank Name</th>
                                <th>Account Holder Name</th>
                                <th>Account Number</th>
                                <th>Account IFSC</th>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <form id="addForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Add Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="px-3">
                        <div class="mb-1">
                            <label class="col-form-label" for="account_bank">Bank Name :</label>
                            <input class="form-control" name="account_bank" id="account_bank" type="text" />
                        </div>
                        <div class="mb-1">
                            <label class="col-form-label" for="account_name">Account Holder Name :</label>
                            <input class="form-control" name="account_name" id="account_name" type="text" />
                        </div>

                        <div class="mb-1">
                            <label class="col-form-label" for="account_number">Account Number :</label>
                            <input class="form-control" name="account_number" id="account_number" type="text" />
                        </div>

                        <div class="mb-1">
                            <label class="col-form-label" for="account_ifsc">Account IFSC :</label>
                            <input class="form-control" name="account_ifsc" id="account_ifsc" type="text" />
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

<div class="modal fade" id="editModal" tabindex="-1" Account="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Edit Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="px-3">
                        <div class="mb-1">
                            <label class="col-form-label" for="account_bank">Bank Name :</label>
                            <input class="form-control" name="account_bank" id="account_bank" type="text" />
                            <input class="form-control" name="id" id="" type="hidden" />
                        </div>
                        <div class="mb-1">
                            <label class="col-form-label" for="account_name">Account Holder Name :</label>
                            <input class="form-control" name="account_name" id="account_name" type="text" />
                        </div>

                        <div class="mb-1">
                            <label class="col-form-label" for="account_number">Account Number :</label>
                            <input class="form-control" name="account_number" id="account_number" type="text" />
                        </div>

                        <div class="mb-1">
                            <label class="col-form-label" for="account_ifsc">Account IFSC :</label>
                            <input class="form-control" name="account_ifsc" id="account_ifsc" type="text" />
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
            ajax: "{{ request()->url() }}",
            order: [
                [4, 'desc']
            ],
            columns: [
                {
                    data: 'account_bank',
                    name: 'account_bank'
                },
                {
                    data: 'account_name',
                    name: 'account_name'
                },
                {
                    data: 'account_number',
                    name: 'account_number'
                },
                {
                    data: 'account_ifsc',
                    name: 'account_ifsc'
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
                account_bank: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                account_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                account_number: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                account_ifsc: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
            },
            messages: {
                account_bank: {
                    required: "Please enter Bank Name."
                },
                account_name: {
                    required: "Please enter Account Holder Name."
                },
                account_number: {
                    required: "Please enter Account Number."
                },
                account_ifsc: {
                    required: "Please enter Account IFSC."
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ request()->url() }}",
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
            document.forms['editForm']['account_bank'].value = data.account_bank;
            document.forms['editForm']['account_name'].value = data.account_name;
            document.forms['editForm']['account_number'].value = data.account_number;
            document.forms['editForm']['account_ifsc'].value = data.account_ifsc;
            $('#editModal').modal('show');
        })

        $("#editForm").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                account_bank: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                account_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                account_number: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                account_ifsc: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
            },
            messages: {
                account_bank: {
                    required: "Please enter Bank Name."
                },
                account_name: {
                    required: "Please enter Account Holder Name."
                },
                account_number: {
                    required: "Please enter Account Number."
                },
                account_ifsc: {
                    required: "Please enter Account IFSC."
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                const formDataObj = {};
                formData.forEach((value, key) => (formDataObj[key] = value));
                $.ajax({
                    url: "{{ request()->url() }}",
                    data: formDataObj,
                    type: 'PUT',
                    success: function (data) {
                        if (data.success) {
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
                        url: "{{ request()->url() }}",
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