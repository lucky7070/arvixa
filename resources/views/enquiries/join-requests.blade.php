@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Join Requests :: Join Requests List </h5>
            </div>

        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive scrollbar">
            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Request For</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Created Date</th>
                        <th width="100px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title" id="tabsModalLabel">View Join Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-none">&times;</span>
                </button>
            </div>
            <div class="modal-body enquiry-details">
                <p class="mb-1 fs-5 fw-bold text-secondary request_for"></p>
                <p class="mb-1 fs-6 fw-bold text-secondary name"></p>
                <p class="mb-1">
                    <a class="fs-6 email-link" href="#">
                        <i class="fa fa-envelope me-2"></i>
                        <span class="email"></span>
                    </a>
                </p>
                <p class="mb-1">
                    <a class="fs-6 phone-link" href="#">
                        <i class="fa fa-phone me-2"></i>
                        <span class="phone"></span>
                    </a>
                </p>
                <p class="message"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark" data-bs-dismiss="modal">Discard</button>
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
            ajax: "{{ route('join-requests') }}",
            order: [
                [5, 'desc']
            ],
            columns: [{
                data: 'request_for',
                name: 'request_for'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'message',
                name: 'message'
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

        $(document).on('click', ".view", function () {
            var data = $(this).data('all')
            $('.enquiry-details .request_for').text(data.request_for);
            $('.enquiry-details .phone').text(data.phone);
            $('.enquiry-details .email').text(data.email);
            $('.enquiry-details .phone-link').attr('href', `tel:${data.phone}`);
            $('.enquiry-details .email-link').attr('href', `mailto:${data.email}`);
            $('.enquiry-details .name').text(data.name);
            $('.enquiry-details .message').text(data.message);
            $('#viewModal').modal('show');
        })

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
                        url: "{{ route('join-requests.delete') }}",
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