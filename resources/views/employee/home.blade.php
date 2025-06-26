@extends('layouts.employee_app')

@section('content')


<div class="row">
    <div class="col-md-12 mb-3">
        @if(count($banners))
        <div class="card rounded-2">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded-2">
                    @foreach($banners as $key => $row)
                    <div class="carousel-item {{ $key == 0 ? 'active' : ''}}">
                        <img class="d-block w-100 object-fit-cover" src="{{ asset('storage/'.$row->image) }}"
                            alt="First slide" style="max-height: 175px;">
                    </div>
                    @endforeach
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </a>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-4 mb-3">
        <div class="widget widget-six bg-light-info">
            <a href="{{ route('employee.main_distributors') }}">
                <div class="widget-heading">
                    <h4 class="fw-bold text-info">Main Distributors</h4>
                </div>
                <div class="w-chart">
                    <div class="w-chart-section">
                        <div class="w-detail">
                            <p class="w-title">Today Assigned</p>
                            <p class="w-stats text-info">{{ $main_distributor['today'] }}</p>
                        </div>
                    </div>
                    <div class="w-chart-section">
                        <div class="w-detail text-end">
                            <p class="w-title">Total Assigned</p>
                            <p class="w-stats text-info">{{ $main_distributor['total'] }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="widget widget-six bg-light-secondary">
            <a href="{{ route('employee.distributors') }}">
                <div class="widget-heading">
                    <h4 class="fw-bold text-secondary">Distributors</h4>
                </div>
                <div class="w-chart">
                    <div class="w-chart-section">
                        <div class="w-detail">
                            <p class="w-title">Today Assigned</p>
                            <p class="w-stats text-secondary">{{ $distributor['today'] }}</p>
                        </div>
                    </div>
                    <div class="w-chart-section">
                        <div class="w-detail text-end">
                            <p class="w-title">Total Assigned</p>
                            <p class="w-stats text-secondary">{{ $distributor['total'] }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="widget widget-six bg-light-danger">
            <a href="{{ route('employee.retailers') }}">
                <div class="widget-heading">
                    <h4 class="fw-bold text-danger">Retailers</h4>
                </div>
                <div class="w-chart">
                    <div class="w-chart-section">
                        <div class="w-detail">
                            <p class="w-title">Today Assigned</p>
                            <p class="w-stats text-danger">{{ $retailers['today'] }}</p>
                        </div>
                    </div>
                    <div class="w-chart-section">
                        <div class="w-detail text-end">
                            <p class="w-title">Total Assigned</p>
                            <p class="w-stats text-danger">{{ $retailers['total'] }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

@endsection