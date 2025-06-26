@extends('layouts.app')

@section('content')

@include('partial.common.user_detail')

<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Retailer :: Ledger</h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    <a href="{{ route('retailers')  }}" class="btn btn-outline-secondary me-2">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                    <a href="{{ route('ledger.export', ['user' => $user['slug'], 'user_type' => 4 ])  }}"
                        class="btn btn-outline-success btn-icon me-2">
                        <i class="fa-duotone fa-file-excel"></i>
                    </a>
                    @if(userCan(104, 'can_add'))
                    <button class="add btn btn-outline-primary">
                        <i class="fa fa-plus me-1"></i>
                        Add Ledger Entry
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive scrollbar">
            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Transaction ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Particulars</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <form id="addForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Add Ledger Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="col-form-label" for="amount">Amount :</label>
                            <input class="form-control" name="amount" id="amount" type="number" step="0.01" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="payment_type">Payment Type</label>
                            <select name="payment_type" class="form-select" id="payment_type">
                                <option value="1"> Credit</option>
                                <option value="2"> Debit</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="col-form-label" for="particulars">Description :</label>
                            <textarea class="form-control" name="particulars" id="particulars"></textarea>
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
            ajax: "{{ route('retailers.ledger', $user['slug']) }}",
            order: [
                [1, 'desc']
            ],
            columns: [{
                data: 'voucher_no',
                name: 'voucher_no',
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'amount',
                name: 'amount'
            },
            {
                data: 'updated_balance',
                name: 'updated_balance',
            },
            {
                data: 'particulars',
                name: 'particulars'
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
                payment_type: {
                    required: true,
                },
                particulars: {
                    required: true,
                    minlength: 2,
                    maxlength: 250
                }
            },
            messages: {
                amount: {
                    required: "Please enter Amount.",
                },
                payment_type: {
                    required: "Please select payment type.",
                },
                particulars: {
                    required: "Please enter Description.",
                }
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ route('retailers.ledger', $user['slug']) }}",
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
    });
</script>
@endsection