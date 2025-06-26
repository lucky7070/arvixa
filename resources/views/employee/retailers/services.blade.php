@extends('layouts.employee_app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Retailers :: Services -
                    <span class="text-primary">{{ $user['name'] }} </span>
                </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('employee.retailers')  }}" class="btn btn-outline-secondary me-4"> <i
                            class="fa fa-arrow-left me-1"></i>
                        Go Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Select</th>
                        <th scope="col" class="fw-bold">Service Name</th>
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
    $(function () {
        var table = $('.table-datatable').DataTable({
            searching: false,
            paging: false,
            ordering: false,
            info: false,
            ajax: "{{ request()->url() }}",
            oLanguage: {
                sEmptyTable: '<b class="text-danger">No Service Assign to this user.</b>',
            },
            order: [
                [0, 'asc']
            ],
            columns: [
                {
                    data: 'check',
                    name: 'check',
                },
                {
                    data: 'name',
                    name: 'name',
                }
            ]
        });

        $(document).on('change', '.switch-input', function () {
            var service_id = $(this).data('service-id');
            var selector = this;
            $.ajax({
                url: "{{ route('employee.retailers.services', [ 'slug' => $user['slug'] ]) }}",
                data: { service_id },
                type: 'POST',
                success: function (data) {
                    if (data.status == true) {
                        table.draw();
                        toastr.success(data.message);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        })
    });

</script>
@endsection