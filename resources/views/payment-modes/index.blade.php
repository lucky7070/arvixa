@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">

                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" id="table-example">Payment Mode :: Payment Mode List </h5>
                    </div>
                    <div class="col-auto ms-auto">
                        @if(userCan(124, 'can_add'))
                        <div class="nav nav-pills nav-pills-falcon">
                            <button class="btn btn-outline-secondary me-4 add">
                                <i class="fa fa-plus me-1"></i> Add Payment Mode
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
                                <th>Type</th>
                                <th>Payment Mode Name</th>
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

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title" id="tabsModalLabel">Add Payment Mode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-none">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="form-label" for="type">Payment Mode Type</label>
                            <select name="type" class="form-select" id="type">
                                <option value="1">Bank Account</option>
                                <option value="2">UPI</option>
                            </select>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="col-form-label" for="name">Payment Mode Name :</label>
                            <input class="form-control" name="name" id="name" type="text" placeholder="Payment Mode Name" />
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="col-form-label" for="logo">Logo :</label>
                            <input class="form-control" name="logo" id="logo" type="file" />
                        </div>
                        <div class="col-lg-6 mb-2 bank-fields">
                            <label class="col-form-label" for="beneficiary_name">Beneficiary Name :</label>
                            <input class="form-control" name="beneficiary_name" id="beneficiary_name" type="text" placeholder="Beneficiary Name " />
                        </div>
                        <div class="col-lg-6 mb-2 bank-fields">
                            <label class="col-form-label" for="account_number">Account Number :</label>
                            <input class="form-control" name="account_number" id="account_number" type="text" placeholder="Account Number" />
                        </div>
                        <div class="col-lg-6 mb-2 bank-fields">
                            <label class="col-form-label" for="ifsc_code">IFSC Code :</label>
                            <input class="form-control" name="ifsc_code" id="ifsc_code" type="text" placeholder="IFSC Code" />
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="col-form-label" for="note">Note :</label>
                            <input class="form-control" name="note" id="note" type="text" placeholder="Note" />
                        </div>

                        <div class="col-lg-6 mb-2 upi-fields">
                            <label class="col-form-label" for="upi">UPI Handle :</label>
                            <input class="form-control" name="upi" id="upi" type="text" placeholder="UPI Handle" />
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="form-label" for="status">Status</label>
                            <select name="status" class="form-select" id="status">
                                <option value="1"> Active</option>
                                <option value="0"> Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-12 modal-footer">
                            <button class="btn btn-light-dark" data-bs-dismiss="modal">Discard</button>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title" id="tabsModalLabel">Edit Payment Mode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-none">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="form-label" for="type">Payment Mode Type</label>
                            <input class="form-control" name="id" id="" type="hidden" />
                            <select name="type" class="form-select" id="type">
                                <option value="1">Bank Account</option>
                                <option value="2">UPI</option>
                            </select>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="col-form-label" for="name">Payment Mode Name :</label>
                            <input class="form-control" name="name" id="name" type="text" placeholder="Payment Mode Name" />
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="col-form-label" for="logo">Logo :</label>
                            <input class="form-control" name="logo" id="logo" type="file" />
                        </div>
                        <div class="col-lg-6 mb-2 bank-fields">
                            <label class="col-form-label" for="beneficiary_name">Beneficiary Name :</label>
                            <input class="form-control" name="beneficiary_name" id="beneficiary_name" type="text" placeholder="Beneficiary Name " />
                        </div>
                        <div class="col-lg-6 mb-2 bank-fields">
                            <label class="col-form-label" for="account_number">Account Number :</label>
                            <input class="form-control" name="account_number" id="account_number" type="text" placeholder="Account Number" />
                        </div>
                        <div class="col-lg-6 mb-2 bank-fields">
                            <label class="col-form-label" for="ifsc_code">IFSC Code :</label>
                            <input class="form-control" name="ifsc_code" id="ifsc_code" type="text" placeholder="IFSC Code" />
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="col-form-label" for="note">Note :</label>
                            <input class="form-control" name="note" id="note" type="text" placeholder="Note" />
                        </div>

                        <div class="col-lg-6 mb-2 upi-fields">
                            <label class="col-form-label" for="upi">UPI Handle :</label>
                            <input class="form-control" name="upi" id="upi" type="text" placeholder="UPI Handle" />
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label class="form-label" for="status">Status</label>
                            <select name="status" class="form-select" id="status">
                                <option value="1"> Active</option>
                                <option value="0"> Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-12 modal-footer">
                            <button class="btn btn-light-dark" data-bs-dismiss="modal">Discard</button>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function() {

        function togglePaymentFields(type = '#addForm') {
            var paymentType = $(`${type} #type`).val();
            if (paymentType == '1') {
                $('.bank-fields').show();
                $('.upi-fields').hide();
                $('[name="beneficiary_name"], [name="account_number"], [name="ifsc_code"]').rules('add', {
                    required: true
                });
                $('[name="upi"]').rules('remove', 'required');
            } else {
                $('.bank-fields').hide();
                $('.upi-fields').show();
                $('[name="upi"]').rules('add', {
                    required: true
                });
                $('[name="beneficiary_name"], [name="account_number"], [name="ifsc_code"]').rules('remove', 'required');
            }
        }

        const table = $('.table-datatable').DataTable({
            ajax: "{{ request()->url() }}",
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'type-label',
                    name: 'type'
                },
                {
                    data: 'name',
                    name: 'name'
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

        $('.add').on('click', function() {
            togglePaymentFields();
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
                logo: {
                    required: true,
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                beneficiary_name: {
                    required: function() {
                        return $('#addForm #type').val() == '1';
                    },
                    minlength: 2,
                    maxlength: 100
                },
                account_number: {
                    required: function() {
                        return $('#addForm #type').val() == '1';
                    },
                    number: true,
                    minlength: 2,
                    maxlength: 20
                },
                ifsc_code: {
                    required: function() {
                        return $('#addForm #type').val() == '1';
                    },
                    ifsc: true,
                    minlength: 2,
                    maxlength: 20
                },
                note: {
                    minlength: 2,
                    maxlength: 100
                },
                upi: {
                    required: function() {
                        return $('#addForm #type').val() == '2';
                    },
                    upiId: true,
                    minlength: 2,
                    maxlength: 50
                },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                logo: {
                    required: "Please upload logo",
                    extension: "Supported formats: jpg, jpeg, png"
                },
                beneficiary_name: {
                    required: "Please enter beneficiary name",
                },
                account_number: {
                    required: "Please enter account number",
                    number: "Please enter a valid account number"
                },
                ifsc_code: {
                    required: "Please enter IFSC code",
                },
                upi: {
                    required: "Please enter UPI ID",
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ request()->url()  }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data) {
                        if (data.success) {
                            toastr.success(data?.message);
                            $('#addModal').modal('hide');
                            $(form).trigger("reset");
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

        $(document).on('click', ".edit", function() {
            var data = $(this).data('all')
            $('[name="id"]').val(data.id)
            document.forms['editForm']['type'].value = data.type;
            document.forms['editForm']['name'].value = data.name;
            document.forms['editForm']['beneficiary_name'].value = data.beneficiary_name;
            document.forms['editForm']['account_number'].value = data.account_number;
            document.forms['editForm']['ifsc_code'].value = data.ifsc_code;
            document.forms['editForm']['note'].value = data.note;
            document.forms['editForm']['status'].value = data.status;
            document.forms['editForm']['upi'].value = data.upi;
            $('#editModal').modal('show');
            togglePaymentFields('#editForm');
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
                logo: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                beneficiary_name: {
                    required: function() {
                        return $('#editForm #type').val() == '1';
                    },
                    minlength: 2,
                    maxlength: 100
                },
                account_number: {
                    required: function() {
                        return $('#editForm #type').val() == '1';
                    },
                    number: true,
                    minlength: 2,
                    maxlength: 20
                },
                ifsc_code: {
                    required: function() {
                        return $('#editForm #type').val() == '1';
                    },
                    ifsc: true,
                    minlength: 2,
                    maxlength: 20
                },
                note: {
                    minlength: 2,
                    maxlength: 100
                },
                upi: {
                    required: function() {
                        return $('#editForm #type').val() == '2';
                    },
                    upiId: true,
                    minlength: 2,
                    maxlength: 50
                },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                logo: {
                    required: "Please upload logo",
                    extension: "Supported formats: jpg, jpeg, png"
                },
                beneficiary_name: {
                    required: "Please enter beneficiary name",
                },
                account_number: {
                    required: "Please enter account number",
                    number: "Please enter a valid account number"
                },
                ifsc_code: {
                    required: "Please enter IFSC code",
                },
                upi: {
                    required: "Please enter UPI ID",
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $("#overlay").show();
                formData.append('_method', 'PUT');
                $.ajax({
                    url: "{{ request()->url()  }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data) {
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

        $(document).on('click', ".delete", function() {
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
                        url: "{{ route('roles.delete') }}",
                        data: {
                            'id': id
                        },
                        type: 'DELETE',
                        success: function(data) {
                            if (data.success) {
                                swal(data?.message, {
                                    icon: "success"
                                });
                                table.draw();
                            } else {
                                toastr.error(data?.message);
                            }
                        }
                    });
                }
            });
        });

        togglePaymentFields();
        $('#editForm [name="type"]').change(function() {
            togglePaymentFields('#editForm');
        });

        $('#addForm [name="type"]').change(function() {
            togglePaymentFields('#addForm');
        });
    });
</script>
@endsection