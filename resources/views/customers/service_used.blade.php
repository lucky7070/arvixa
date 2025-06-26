@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Customers :: Customer Service Used - <span
                        class="text-secondary">{{ $customer->name }}</span> </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('customers')  }}" class="btn btn-outline-secondary me-4"> <i
                            class="fa fa-arrow-left me-1"></i> Go
                        Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="" for="user_type">User Type</label>
                <select class="form-select" id="user_type">
                    <option value="">Choose...</option>
                    <option value="4">Retailer</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="" for="user_id">Name</label>
                <select class="form-select" id="user_id">
                    <option value="">Choose...</option>
                </select>
            </div>
        </div>
        <div class="table-responsive scrollbar">
            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Date</th>
                        <th>Service Name</th>
                        <th>User Type</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">

    function getUser(user_type) {
        $.get("{{ route('get_user_list') }}", { user_type }, function (data) {
            $('#user_id').html('<option value="">Choose...</option>');
            if (data.length > 0) {
                data.forEach((row, i) => {
                    $('#user_id').append(`<option value="${row.id}" >${row.name}</option>`);
                })
            }
        })
    }


    $(function () {

        var user_type = "{{ request('user_type') }}";
        var user_id = "{{ request('user_id') }}";
        var table = $('.table-datatable').DataTable({
            ajax: {
                url: "{{ request()->url() }}",
                type: 'GET',
                data: function (d) {
                    d.user_type = $('#user_type').val() || user_type;
                    d.user_id = $('#user_id').val() || user_id;
                }
            },
            processing: true,
            serverSide: true,
            order: [
                [0, 'desc']
            ],
            columns: [
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'service',
                    name: 'service.name'
                },
                {
                    data: 'used_by',
                    name: 'used_in'
                },
                {
                    data: 'name',
                    name: 'name',
                    searchable: false
                },
            ]
        });

        $('#user_type').on('change', function () {
            var user_type = $(this).val();
            getUser(user_type)
            $('#user_id').val(null)
            table.draw(true);
        });

        $('#user_id').on('change', function () {
            table.draw(true);
        });

        if (user_type) {
            $('#user_type').val(user_type).attr('disabled', true);
            getUser(user_type)
        }

        setTimeout(() => {
            if (user_id) $('#user_id').val(user_id).attr('disabled', true);
        }, 500);
    })

</script>
@endsection