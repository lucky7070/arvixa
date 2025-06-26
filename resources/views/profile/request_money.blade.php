@extends('layouts.'.($user['route'] != 'web' ? $user['route'].'_': '').'app')

@section('content')

<div class="row g-0">
    <div class="col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Requested Money</h5>
                    <div>
                        <button class="btn btn-outline-secondary add">
                            <i class="fa fa-plus me-2"></i>
                            Request Money
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive scrollbar">
                    <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th>Request ID</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Title</th>
                                <th>Status</th>
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
                <h5 class="modal-title fw-bold" id="tabsModalLabel">Add Money To Wallet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-none">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="requestMoney" action="" method="post">
                    <div class="row">

                        <div class="col-md-12">
                            <label class="col-form-label" for="description">Description :</label>
                            <textarea class="form-control" placeholder="Enter UTR/RRN Number" name="description"
                                id="description"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label" for="amount">Amount :</label>
                            <input class="form-control" placeholder="Enter Amount" name="amount" id="amount"
                                type="number" step="0.01" value="100" />
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label" for="attachment">Attachment / Payment Slip :</label>
                            <input class="form-control" name="attachment" type="file" />
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn mx-auto btn-lg btn-info w-50 submit">
                                    Submit Request
                                    <i class="fa-duotone fa-forward ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
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
<script type="text/javascript">
    $(function () {
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route($route_name) }}",
            order: [
                [1, 'desc']
            ],
            columns: [{
                data: 'request_number',
                name: 'request_number',
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
                data: 'title',
                name: 'title'
            },
            {
                data: 'status',
                name: 'status',
            },
            ]
        });

        $('.add').on('click', function () {
            $('#addModal').modal('show');
        })

        $('#amount').keypress(function (e) {
            var validkeys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
            if (validkeys.indexOf(e.key) < 0) return false;
        });

        // requestMoney
        $("#requestMoney").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                description: {
                    required: true,
                    minlength: 2,
                    maxlength: 500
                },
                amount: {
                    required: true,
                    minlength: 2,
                    maxlength: 500
                },
                attachment: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2,
                },
            },
            messages: {

                description: {
                    required: "Please enter description.",
                },
                amount: {
                    required: "Please enter amount.",
                },
                attachment: {
                    extension: "Supported Format Only : jpg, jpeg, png, pdf"
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                $.ajax({
                    url: "{{ route( $route_name ) }}",
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
                $('.requestMoney a').attr('href', '...').hide()
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


    });
</script>

@endsection