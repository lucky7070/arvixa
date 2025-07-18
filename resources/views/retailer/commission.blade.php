@extends('layouts.retailer_app')

@section('content')
<div class="row">
    <div class="col-lg-12 mb-3">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Commission</h5>
                    <a href="{{ route('retailer.dashboard') }}" class="btn btn-dark">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Service Name</th>
                            <th scope="col">Charge / Commission</th>
                            <th scope="col">Commission Slots</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servicesLog as $key => $service )
                        <tr>
                            <td scope="row">{{ $key + 1 }}</td>
                            <td>{{ $service->service_name }}</td>
                            <td class="text-center">
                                {{ $service->commission_slots  ? "--" : "₹$service->sale_rate  / Request" }}
                            </td>
                            <td>
                                @if($service->commission_slots)
                                <button class="btn btn-sm btn-primary details" data-all="{{  json_encode($service->commission_slots) }}">View</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Bill Payment Statistics</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">Service Name</th>
                                <th colspan="2" class="fw-bold">Current Month</th>
                                <th colspan="2" class="fw-bold">Last Month</th>
                                <th colspan="2" class="fw-bold">Last Month till Date</th>
                                <th colspan="2" class="fw-bold">All Over</th>
                            </tr>
                            <tr>
                                <th scope="col">Bill Pay</th>
                                <th scope="col">Commission</th>
                                <th scope="col">Bill Pay</th>
                                <th scope="col">Commission</th>
                                <th scope="col">Bill Pay</th>
                                <th scope="col">Commission</th>
                                <th scope="col">Bill Pay</th>
                                <th scope="col">Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistics as $key => $row)
                            <tr>
                                <th scope="row">{{ $key + 1 }}</th>
                                <td>{{ $row['name'] }}</td>
                                <td>₹ {{ $row['current_month_bill_value'] }}</td>
                                <td>₹ {{ $row['current_month_commission'] }}</td>
                                <td>₹ {{ $row['last_month_bill_value'] }}</td>
                                <td>₹ {{ $row['last_month_commission'] }}</td>
                                <td>₹ {{ $row['last_month_till_date_bill_value'] }}</td>
                                <td>₹ {{ $row['last_month_till_date_commission'] }}</td>
                                <td>₹ {{ $row['total_bill_value'] }}</td>
                                <td>₹ {{ $row['total_commission'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="commissionModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Commission Slots</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mb-0" id="commissionTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Start Amount</th>
                            <th scope="col">End Amount</th>
                            <th scope="col">Percent</th>
                        </tr>
                    </thead>
                    <tbody> </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(function() {
        $('.details').on('click', function() {
            const data = $(this).data('all');
            if (data && Array.isArray(data)) {
                let html = '';
                data.forEach((r, i) => {
                    html += `<tr>
                            <td scope="row">${i + 1}</td>
                            <td>₹ ${r.start}</td>
                            <td>₹ ${r.end}</td>
                            <td>${r.commission}%</td>
                        </tr>`
                })

                $('#commissionTable tbody').html(html);
            }

            $('#commissionModal').modal('show')
        })
    });
</script>
@endsection