@extends('layouts.retailer_app')
@section('content')

<div class="row">
    <div class="col-lg-8">
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
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Service Name</th>
                            <th scope="col">Sale Rate</th>
                            <th scope="col">Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servicesLog as $key => $service )
                        <tr>
                            <th scope="row">{{ $key + 1 }}</th>
                            <td>{{ $service->service_name }}</td>
                            <td>{{ $service->retailer_commission > 0 ? "--" : "₹$service->sale_rate" }}</td>
                            <td>{{ $service->retailer_commission > 0 ? "$service->retailer_commission%" : '--' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection