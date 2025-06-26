@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0">Products :: Product Stock Log -
                            <span class="text-primary">{{ $product['name'] }} ({{ $product['stock'] }})</span>
                        </h5>
                    </div>
                    <div class="col-auto ms-auto">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('products') }}">
                            <i class="fa fa-arrow-left me-1"></i> Go Back
                        </a>
                        <button class="add btn btn-outline-primary">
                            <i class="fa fa-plus me-1"></i>
                            Add Stock Entry
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive scrollbar">
                    <table class="table custom-table table-striped mb-0 table-datatable" style="width:100%">
                        <thead class="">
                            <tr>
                                <th>Voucher No</th>
                                <th>Date</th>
                                <th>Purchase / Sale Price</th>
                                <th>Amount</th>
                                <th>Updated Stock</th>
                                <th>Description</th>
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
                    <h5 class="modal-title" id="tabsModalLabel">Add Stock Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label" for="amount">Stock Amount :</label>
                            <input class="form-control" name="amount" id="amount" type="number" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label" for="price">Purchase / Sale Price :</label>
                            <input class="form-control" name="price" id="price" type="number" step="0.01" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="type">Entry Type</label>
                            <select name="type" class="form-select" id="type">
                                <option value="1"> Inword</option>
                                <option value="2"> Outword</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="col-form-label" for="description">Description :</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
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
<script type="text/javascript">
    $(function () {

        var table = $('.table-datatable').DataTable({
            ajax: "{{ request()->url() }}",
            order: [
                [1, 'desc']
            ],
            columns: [
                {
                    data: 'voucher_no',
                    name: 'voucher_no'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'updated_stock',
                    name: 'updated_stock',
                },
                {
                    data: 'description',
                    name: 'description'
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
                amount: {
                    required: true,
                },
                price: {
                    required: true
                },
                payment_type: {
                    required: true,
                },
                description: {
                    required: true,
                    minlength: 2,
                    maxlength: 250
                }
            },
            messages: {
                amount: {
                    required: "Please enter Amount.",
                },
                price: {
                    required: "Please enter purchase / sale price.",
                },
                payment_type: {
                    required: "Please select payment type.",
                },
                description: {
                    required: "Please enter Description.",
                }
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
    });
</script>
@endsection