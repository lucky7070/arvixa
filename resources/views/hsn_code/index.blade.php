@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-10">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" id="table-example">HSN Code :: HSN Code List </h5>
                    </div>
                    @if(userCan(126, 'can_add'))
                    <div class="col-auto ms-auto">
                        <div class="nav nav-pills nav-pills-falcon">
                            <button class="btn btn-sm btn-outline-secondary add">
                                <i class="fa fa-plus me-1"></i>
                                Add HSN Code
                            </button>
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
                                <th>Code</th>
                                <th>Tax Rate</th>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h4 class="mb-1" id="modalExampleDemoLabel">Add HSN Code </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3">
                    <form id="addForm">
                        <div class="mb-2">
                            <label class="form-label" for="code">HSN Code <span class="required">*</span></label>
                            <input class="form-control" name="code" id="code" type="text" />
                        </div>
                        <div class="mb-2">
                            <label for="tax_rate" class="form-label">Tax Rate <span class="required">*</span></label>
                            <select class="form-select" name="tax_rate" id="tax_rate">
                                @foreach (config('constant.tax_percent_list', []) as $key => $tax)
                                <option value="{{ $key }}" {{ old('tax_percent')==$key ? 'selected' : '' }}>
                                    {{ $tax }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="description">
                                Description <span class="required">*</span>
                            </label>
                            <textarea class="form-control" name="description" id="description" type="text"></textarea>
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
            <div class="modal-header">
                <h4 class="mb-1" id="modalExampleDemoLabel">Edit HSN Code </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3">
                    <form id="editForm">
                        <div class="mb-2">
                            <label class="form-label" for="code">HSN Code <span class="required">*</span></label>
                            <input class="form-control" name="code" id="code" type="text" />
                            <input class="form-control" name="id" id="" type="hidden" />
                        </div>
                        <div class="mb-2">
                            <label for="tax_rate" class="form-label">Tax Rate <span class="required">*</span></label>
                            <select class="form-select" name="tax_rate" id="tax_rate">
                                @foreach (config('constant.tax_percent_list', []) as $key => $tax)
                                <option value="{{ $key }}" {{ old('tax_percent')==$key ? 'selected' : '' }}>
                                    {{ $tax }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="description">Description <span
                                    class="required">*</span></label>
                            <textarea class="form-control" name="description" id="description" type="text"></textarea>
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
            ajax: "{{ route('hsn_code') }}",
            order: [
                [0, 'desc']
            ],
            columns: [{
                data: 'code',
                name: 'code'
            },
            {
                data: 'tax_rate_',
                name: 'tax_rate',
                class: 'px-2'
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
        });

        $("#addForm").validate({
            rules: {
                tax_rate: {
                    required: true,
                    min: 0,
                    max: 100,
                },
                code: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                description: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                }
            },
            messages: {
                tax_rate: {
                    required: 'Please select tax Rate.',
                },
                code: {
                    required: "Please enter HSN code",
                },
                description: {
                    required: "Please enter HSN code description.",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ route('hsn_code') }}",
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
            document.forms['editForm']['code'].value = data.code;
            document.forms['editForm']['tax_rate'].value = data.tax_rate;
            document.forms['editForm']['description'].value = data.description;
            document.forms['editForm']['status'].value = data.status;
            $('#editModal').modal('show');
        });

        $("#editForm").validate({
            rules: {
                tax_rate: {
                    required: true,
                    min: 0,
                    max: 100,
                },
                code: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                description: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                }
            },
            messages: {
                tax_rate: {
                    required: 'Please select tax Rate.',
                },
                code: {
                    required: "Please enter HSN code",
                },
                description: {
                    required: "Please enter HSN code description.",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                const formDataObj = {};
                formData.forEach((value, key) => (formDataObj[key] = value));
                $.ajax({
                    url: "{{ route('hsn_code') }}",
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
                        url: "{{ route('hsn_code.delete') }}",
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