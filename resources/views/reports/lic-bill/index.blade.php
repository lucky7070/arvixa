@extends('layouts.app')

@section('css')
<link href="{{ asset('assets/css/tom-select.default.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom-tomSelect.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">LIC Bill Repoprt</h5>
            <div class="dropdown-list dropdown" role="group">
                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-success" data-form="exportForm">
                    <i class="fa fa-file-excel me-1"></i> Export
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="exportForm" method="get" action="{{ route('reports.bills.export', 'lic') }}">
            <div class="row">
                <div class="col-sm-6 col-md-2 mb-2">
                    <label class="fs--1 mb-0" for="start_date">Start Date</label>
                    <input class="form-control update" type="date" value="{{ old('start_date') }}"
                        name="start_date" id="start_date">
                    <input type="hidden" name="id" value="1">
                </div>
                <div class="col-sm-6 col-md-2 mb-2">
                    <label class="fs--1 mb-0" for="end_date">End Date</label>
                    <input class="form-control update" type="date"
                        value="{{ old('end_date', date('Y-m-d')) }}" name="end_date" id="end_date">
                </div>
                <div class="col-sm-6 col-md-2 mb-2">
                    <label class="fs--1 mb-0" for="status">Status</label>
                    <select class="form-select update" name="status" id="status">
                        <option value="">All Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Success</option>
                        <option value="2">Cancelled</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-6 mb-2">
                    <label class="fs--1 mb-0" for="provider">Provider</label>
                    <select class="form-select update" name="provider" id="provider">
                        <option value="">All Provider</option>
                        @foreach($providers as $provider)
                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-3 mb-2 text-end ms-auto me-0">
                    <br class="d-inline-block">
                    <button type="button" class="btn btn-outline-primary btn-sm submit">
                        <i class="fa fa-check"></i> Submit
                    </button>
                    <button type="reset" class="btn btn-outline-danger btn-sm reset">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </div>
            </div>
        </form>

        <hr>
        <div class="table-responsive scrollbar">
            <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Transaction Id</th>
                        <th>Retailer Name</th>
                        <th></th>
                        <th>Provider Name</th>
                        <th>Bill Details</th>
                        <th>Profit & TDS</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="statusModalLabel">Transaction Details</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group mb-3" id="bill-details">
                    <li class="list-group-item d-flex justify-content-between">
                        <div class="fw-bold">Transaction Id</div>
                        <div class="transaction_id"></div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <div class="fw-bold">Bill Amount</div>
                        <div class="bill_amount"></div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <div class="fw-bold">Provider Name</div>
                        <div class="provider_name text-end" style="max-width: 50%;"></div>
                    </li>
                </ul>
                <form id="updateStatus" class="row">
                    <div class="col-12 mb-2">
                        <label for="status" class="form-label">Status</label>
                        <input type="hidden" name="id" value="" id="id">
                        <input type="hidden" name="" value="" id="hidden-status">
                        <select name="status" id="status" class="form-select">
                            <option value="">Select Status</option>
                            <option value="1">Success</option>
                            <option value="2">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea name="remark" id="remark" class="form-control" placeholder="Enter your Remark"></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/tom-select.base.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function() {

        const tom = new TomSelect("#provider");
        var table = $('.table-datatable').DataTable({
            ajax: {
                url: "{{ request()->url() }}",
                searching: false,
                data: function(d) {
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                    d.status = $("#status").val();
                    d.provider = $("#provider").val();
                }
            },
            order: [
                [1, 'desc']
            ],
            columns: [{
                    data: 'transaction_id',
                    name: 'electricity_bills.created_at'
                },
                {
                    data: 'retailer_name',
                    name: 'retailers.name',
                },
                {
                    data: 'retailer_userId',
                    name: 'retailers.mobile',
                    visible: false
                },
                {
                    data: 'provider_name',
                    name: 'rproviders.name',
                },
                {
                    data: 'consumer_no',
                    name: 'consumer_no',
                },
                {
                    data: 'commission',
                    name: 'commission',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('[data-form="exportForm"]').on('click', function() {
            $('#exportForm').submit()
        })

        $('.submit').click(function() {
            table.draw();
        });

        $('.reset').click(function() {
            tom.clear()
            setTimeout(() => table.draw(), 500)
        });

        $(document).on('click', '.action', function() {
            const data = $(this).data('all');
            $('#bill-details .transaction_id').text(data.transaction_id);
            $('#bill-details .bill_amount').text(`₹${data.bill_amount}`);
            $('#bill-details .provider_name').text(data.provider_name);

            $("#updateStatus").validate().resetForm()
            $('#updateStatus [name="id"]').val(data.id);
            $('#updateStatus [name="remark"]').val(data.remark || '');
            $('#updateStatus [name="status"]').val(data.status == 0 ? '' : data.status);
            if ([1, 2].includes(data.status)) {
                $('#updateStatus [name="status"]').prop('disabled', true).prop('readonly', true);
                $('#hidden-status').prop('name', 'status').val(data.status)
            } else {
                $('#updateStatus [name="status"]').prop('disabled', false).prop('readonly', false);
                $('#hidden-status').prop('name', '').val('')
            }

            $('#statusModal').modal('show');
        });

        $("#updateStatus").validate({
            ignore: [],
            rules: {
                status: {
                    required: true,
                    number: true,
                    min: 1,
                    max: 2,
                },
                remark: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                }
            },
            messages: {
                status: {
                    required: "Please select status..!!",
                },
                remark: {
                    required: "Please enter remark.",
                },
            },
            submitHandler: function(form, event) {
                const formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ route('reports.bill-submit') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data) {
                        if (data.status) {
                            toastr.success(data?.message);
                            $('#statusModal').modal('hide');
                            $(form).trigger("reset")
                            table.draw();
                            $("#overlay").hide();
                        } else {
                            $(form).validate().showErrors(data.data);
                            toastr.error(data?.message);
                            $("#overlay").hide();
                            table.draw();
                        }
                    }
                });
            }
        });
    });
</script>
@endsection