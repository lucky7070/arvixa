@extends('layouts.retailer_app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ @$service->name }}</h5>
            <div>
                <a href="{{ route('pan-card-export', $card_type) }}" class="btn btn-success me-1">
                    <i class="fa fa-file-excel me-1"></i>
                    Export
                </a>
                <a href="{{ route('create-pan-card', $card_type) }}" class="btn btn-primary me-1">
                    <i class="fa fa-plus me-1"></i>
                    Create Pan Card
                </a>
                <a href="{{ route('update-pan-card', $card_type) }}" class="btn btn-secondary">
                    <i class="fa fa-edit"></i>
                    Update Pan Card
                </a>
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
                        <th>NSDL TXN</th>
                        <th>Full Name</th>
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

<form id="incompleteForm" action="{{ route('incomplete-pan-card') }}" method="post">
    @csrf
    <input type="hidden" name="txn_id" value="">
</form>
@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ request()->url() }}",
            order: [
                [3, 'desc']
            ],
            columns: [
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'nsdl_txn_id',
                    name: 'nsdl_txn_id'
                },
                {
                    data: 'full_name',
                    name: 'full_name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'nsdl_complete',
                    name: 'nsdl_complete',
                    orderable: false,
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $(document).on('click', ".complatePan", function () {
            var txnId = $(this).data('txn-id');
            document.forms['incompleteForm']['txn_id'].value = txnId;
            $('#incompleteForm').submit();
        });

        $(document).on('click', ".checkStatus", function () {
            var ackNo = $(this).data('ack-no')
            var txnId = $(this).data('txn-id');

            $('#overlay').show();
            $.ajax({
                url: "{{ route('pan-card-status') }}",
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

    });
</script>
@endsection