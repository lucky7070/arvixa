@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pan Cards</h5>
            <div class="dropdown-list dropdown" role="group">
                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-success" data-bs-toggle="dropdown"
                    aria-haspopup="false" aria-expanded="false">
                    <i class="fa fa-file-excel me-1"></i> Export
                </a>
                <div class="dropdown-menu left" data-popper-placement="top-start" data-popper-reference-hidden=""
                    data-popper-escaped="">
                    <button class="dropdown-item fs--1" type="submit" data-id="1" data-form="exportForm">
                        Statistics
                    </button>
                    <button class="dropdown-item fs--1" type="submit" data-id="2" data-form="exportForm">
                        Detailed
                    </button>
                    <button class="dropdown-item fs--1" type="submit" data-id="3" data-form="exportForm">
                        Retailer
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="exportForm" method="get" action="{{ route('reports.pan-cards.export') }}">
            <div class="row">
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="start_date">Start Date</label>
                    <input class="form-control form-control-sm update" type="date" value="{{ old('start_date') }}"
                        name="start_date" id="start_date">
                    <input type="hidden" name="id" value="1">
                </div>
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="end_date">End Date</label>
                    <input class="form-control form-control-sm update" type="date"
                        value="{{ old('end_date', date('Y-m-d')) }}" name="end_date" id="end_date">
                </div>

                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="type">Request Type</label>
                    <select class="form-select form-select-sm update" name="type" id="type">
                        <option value="" selected>All</option>
                        <option value="1">New</option>
                        <option value="2">Correction</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="is_physical_card">PanCard Type</label>
                    <select class="form-select form-select-sm update" name="is_physical_card" id="is_physical_card">
                        <option value="" selected>All</option>
                        <option value="Y">Physical</option>
                        <option value="N">Digital</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-3 mb-2">
                    <label class="fs--1 mb-0" for="is_refunded">Refunded</label>
                    <select class="form-select form-select-sm update" name="is_refunded" id="is_refunded">
                        <option value="" selected>All</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
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

        <hr class="my-0">
        <div class="row my-2" id="statistics">
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <b class="text-primary">Total PanCard Request</b>
                    <span class="badge bg-primary rounded-pill total"></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <b class="text-primary"> Successful Created </b>
                    <span class="badge bg-primary rounded-pill success"></span>
                </div>
            </div>
        </div>
        <hr class="mt-0">
        <div class="table-responsive scrollbar">
            <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Transaction ID</th>
                        <th>nsdl_ack_no</th>
                        <th>Type</th>
                        <th>Is Physical Card</th>
                        <th>Retailer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    const getStatistics = (start_date = null, end_date = null) => {
        $.post("{{ route('reports.pan-cards.statistics') }}", { start_date, end_date }, function (data) {
            if (data.status) {
                $('#statistics .total').text(data.data?.total)
                $('#statistics .success').text(data.data?.nsdl_ack_no)
            }
        })
    }

    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: {
                url: "{{ request()->url() }}",
                searching: false,
                data: function (d) {
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                    d.type = $("#type").val();
                    d.is_physical_card = $("#is_physical_card").val();
                    d.used_by = $("#used_by").val();
                    d.is_refunded = $("#is_refunded").val();
                }
            },
            order: [
                [6, 'desc']
            ],
            columns: [
                {
                    data: 'nsdl_txn_id',
                    name: 'nsdl_txn_id',

                },
                {
                    data: 'nsdl_ack_no',
                    name: 'nsdl_ack_no',
                    visible: false
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'is_physical_card',
                    name: 'is_physical_card',
                    class: "text-center py-0",
                },
                {
                    data: 'username',
                    name: 'username',
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'created_at_gmt',
                    name: 'created_at_gmt'
                },
                {
                    data: 'nsdl_complete',
                    name: 'nsdl_complete',
                    class: "text-center py-0",
                    orderable: false,
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false,
                },
            ]
        });

        $('[data-form="exportForm"]').on('click', function () {
            $('#exportForm input[type="hidden"]').val($(this).data('id'))
            $('#exportForm').submit()
        })

        $(document).on('click', ".checkStatus", function () {

            var ackNo = $(this).data('ack-no')
            var txnId = $(this).data('txn-id');
            $('#overlay').show();
            $.ajax({
                url: "{{ route('reports.pan-cards.status') }}",
                data: { ackNo, txnId },
                type: 'POST',
                success: function (data) {
                    if (data.status) {
                        $('#overlay').hide();
                        swal({
                            title: "Status",
                            text: data.message + '\n' + (ackNo ? 'Ack No. ' + ackNo : ''),
                        });

                    } else {
                        $('#overlay').hide();
                        toastr.error(data.message);
                    }
                }
            });
        });

        setTimeout(() => {
            getStatistics();
        }, 500)

        $('.submit').click(function () {
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            getStatistics(start_date, end_date)
            table.draw();
        });

        $('.reset').click(function () {
            setTimeout(() => table.draw(), 500)
        });
    });
</script>
@endsection