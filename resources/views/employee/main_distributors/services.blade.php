@extends('layouts.employee_app')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/light/forms/switches.css') }}">
<style>
    .table thead tr th {
        border: 1px solid #ebedf2 !important;
        background: #eaeaec !important;
        padding: 10px 21px 10px 21px;
        vertical-align: middle;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Main Distributors :: Services -
                    <span class="text-primary">{{ $main_distributor['name'] }} </span>
                </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('employee.main_distributors')  }}" class="btn btn-outline-secondary me-4"> <i
                            class="fa fa-arrow-left me-1"></i>
                        Go Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped- table-bordered">
                <thead>
                    <tr class="">
                        <th class="checkbox-area fw-bold" scope="col">
                            Select
                        </th>
                        <th scope="col" class="fw-bold">Service Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td>
                            <div class="form-group">
                                <div class="switch form-switch-custom form-switch-secondary">
                                    <input data-service-id="{{ $service['id'] }}" class="switch-input" type="checkbox"
                                        role="switch" {{ !empty($service['assign_date']) ? 'checked' : '' }} />
                                </div>
                            </div>
                        </td>
                        <td>{{ $service["name"] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $(document).on('change', '.switch-input', function () {
        var service_id = $(this).data('service-id');
        var selector = this;
        $.ajax({
            url: "{{ route('employee.main_distributors.services', [ 'slug' => $main_distributor['slug'] ]) }}",
            data: { service_id },
            type: 'POST',
            success: function (data) {
                if (data.status == true) {
                    if (service_id == 'on') {
                        $(selector).data('service-id', 'off')
                        $('input:checkbox').not(selector).prop('checked', selector.checked);
                    } else if (service_id == 'off') {
                        $(selector).data('service-id', 'on')
                        $('input:checkbox').not(selector).prop('checked', selector.checked);
                    }
                    toastr.success(data.message);
                } else {
                    toastr.error(data.message);
                }
            }
        });
    })

</script>
@endsection