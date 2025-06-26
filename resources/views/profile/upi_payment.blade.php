@extends('layouts.'.($user['route'] != 'web' ? $user['route'].'_': '').'app')

@section('content')
<div class="row g-0">
    <div class="col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">UPI Payments </h5>
                </div>
            </div>
            <div class="card-body">
                <form id="exportForm" action="{{ route($route_name).'/export' }}" method="get">
                    <div class="row">
                        <div class="col-sm-6 col-md-3 mb-2">
                            <label class="fs--1 mb-0" for="start_date">Start Date</label>
                            <input class="form-control form-control-sm update" type="date" name="start_date"
                                id="start_date">
                        </div>
                        <div class="col-sm-6 col-md-3 mb-2">
                            <label class="fs--1 mb-0" for="end_date">End Date</label>
                            <input class="form-control form-control-sm update" type="date" name="end_date"
                                id="end_date">
                        </div>

                        <div class="col-sm-6 col-md-3 mb-2 text-end ms-auto me-0">
                            <br class="d-inline-block">
                            <button type="reset" class="btn btn-outline-danger btn-sm reset">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-outline-success me-2" form="exportForm">
                                <i class="fa fa-file-excel me-1"></i> Export
                            </button>
                        </div>
                    </div>
                </form>
                <hr class="mt-0">
                <div class="table-responsive scrollbar">
                    <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th>Request ID</th>
                                <th>UserName</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-none">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card style-2 mb-md-0 mb-4 requestMoney">
                    <h5 class="card-title-2 mb-1 text-secondary fw-bold">...</h5>
                    <h5 class="card-title mb-1">...</h5>
                    <div class="card-body p-0">...</div>
                    <p class="text-danger my-1 reason" style="display: none;">
                        <b>Cancel Reason : </b>
                        <span>...</span>
                    </p>
                    <div class="mt-1">
                        <p class="fw-bold">Amount : <span class="amount"></span></p>
                        <a href="#" class="btn btn-secondary" target="_blank" download=""
                            style="display: none;">Download</a>
                    </div>
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
            ajax: {
                url: "{{ route($route_name) }}",
                searching: false,
                data: function (d) {
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                }
            },

            order: [
                [2, 'desc']
            ],
            columns: [{
                data: 'request_number',
                name: 'request_number',
            },
            {
                data: 'user_name',
                name: 'user_name'
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
                data: 'action',
                name: 'action',
            },
            ]
        });

        $('.update').change(function () {
            table.draw();
        });

        $('.reset').click(function () {
            setTimeout(() => table.draw(), 500)
        });

        $(document).on('click', '.viewDetails', function () {
            var data = $(this).data('data');
            var path = "{{ asset('storage/') }}"
            $('.requestMoney .card-title-2').text(data.request_id || "")
            $('.requestMoney .card-title').text(data.title || "")
            $('.requestMoney .card-body').text(data.description || "")
            $('.requestMoney .amount').text(data.amount || "")
            if (data.attachment) {
                $('.requestMoney a').attr('href', path + '/' + data.attachment).show()
            } else {
                $('.requestMoney a').attr('href', '').hide()
            }

            if (data.reason && data.status == 2) {
                $('.requestMoney .reason span').text(data.reason)
                $('.requestMoney .reason').show()
            } else {
                $('.requestMoney .reason span').text('...')
                $('.requestMoney .reason').hide()
            }
            $('#viewModal').modal('show');
        })

        const updateStatus = vData => {
            $.post("{{ route($route_name) }}", vData, function (data) {
                if (data.status == true) {
                    table.draw();
                    swal.close();
                    toastr.success(data.message);
                }
                else {
                    swal.close();
                    toastr.error(data.message)
                }
            })
        }

        $(document).on('click', '.updateStatus', function () {
            var data = {
                id: $(this).data('id'),
                type: $(this).data('type'),
                reason: null
            }

            swal({
                title: "Are you Sure..!!",
                text: "What do you want to do.?",
                buttons: {
                    cancel: "Cancel",
                    defeat: "Yes..!!",
                },
            }).then((value) => {
                if (value) {
                    if (data.type == 2) {
                        var textarea = document.createElement('textarea');
                        textarea.rows = 3;
                        textarea.className = 'swal-content__textarea';
                        textarea.setAttribute("id", "myTextArea");
                        swal({
                            title: 'Please enter rejection reason.',
                            content: textarea,
                            buttons: {
                                cancel: {
                                    text: 'Cancel',
                                    visible: true
                                },
                                confirm: {
                                    text: 'Submit',
                                    closeModal: false
                                }
                            }
                        }).then((value) => {
                            if (value) {
                                data.reason = $('#myTextArea').val();
                                updateStatus(data);
                            }
                        });
                    } else {
                        updateStatus(data);
                    }
                }
            });
        })
    });
</script>
@endsection