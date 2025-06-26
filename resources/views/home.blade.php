@extends('layouts.app')

@section('content')

@if(count($banners))
<div class="card">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner rounded-2">
            @foreach($banners as $key => $row)
            <div class="carousel-item {{ $key == 0 ? 'active' : ''}}">
                <img class="d-block w-100 object-fit-cover" src="{{ asset('storage/'.$row->image) }}" alt="First slide"
                    style="max-height: 175px;">
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


<div class="row mt-4">
    @if($profile)
    <div class="col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-six bg-light-danger">
            <div class="widget-heading">
                <h4 class="fw-bold text-danger">{{ $profile['company_name'] }}</h4>
            </div>
            <div class="w-chart">
                <div class="w-chart-section">
                    <div class="w-detail">
                        <p class="w-title">Current Company Balance</p>
                        <p class="w-stats text-secondary">
                            <i class="fa-solid fa-indian-rupee-sign"></i>
                            @currency($profile['balance'])
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-six bg-light-secondary">
            <a href="{{ route('main_distributors') }}">
                <div class="widget-heading">
                    <h4 class="fw-bold text-secondary">Main Distributors</h4>
                </div>
                <div class="w-chart">
                    <div class="w-chart-section">
                        <div class="w-detail">
                            <p class="w-title">Total Register</p>
                            <p class="w-stats text-secondary">{{ $main_distributor->count }}</p>
                        </div>
                    </div>
                    <div class="w-chart-section">
                        <div class="w-detail text-end">
                            <p class="w-title">Total Balance</p>
                            <p class="w-stats text-secondary">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                                @currency($main_distributor->user_balance)
                            </p>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
                <div class="w-chart">
                    <div class="w-chart-section">
                        <div class="w-detail">
                            <p class="w-title">Today Registered</p>
                            <p class="w-stats text-secondary">{{ $main_distributor_today }}</p>
                        </div>
                    </div>

                    <div class="w-chart-section">
                        <div class="w-detail text-end">
                            <p class="w-title">Today Allotted Money</p>
                            <p class="w-stats text-secondary">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                                @currency($payment_sum['main_distributor'])
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-six bg-light-danger">
            <a href="{{ route('distributors') }}">
                <div class="widget-heading">
                    <h4 class="fw-bold text-danger">Distributors</h4>
                </div>
                <div class="w-chart">
                    <div class="w-chart-section">
                        <div class="w-detail">
                            <p class="w-title">Total Register</p>
                            <p class="w-stats text-danger">{{ $distributor->count }}</p>
                        </div>
                    </div>
                    <div class="w-chart-section">
                        <div class="w-detail text-end">
                            <p class="w-title">Total Balance</p>
                            <p class="w-stats text-danger">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                                @currency($distributor->user_balance)
                            </p>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
                <div class="w-chart">
                    <div class="w-chart-section">
                        <div class="w-detail">
                            <p class="w-title">Today Registered</p>
                            <p class="w-stats text-danger">{{ $distributor_today }}</p>
                        </div>
                    </div>
                    <div class="w-chart-section">
                        <div class="w-detail text-end">
                            <p class="w-title">Today Allotted Money</p>
                            <p class="w-stats text-danger">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                                @currency($payment_sum['distributor'])
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="widget widget-six bg-light-info">
            <a href="{{ route('retailers') }}">
                <div class="widget-heading">
                    <h4 class="fw-bold text-info">Retailers</h4>
                </div>
                <div class="w-chart">
                    <div class="w-chart-section">
                        <div class="w-detail">
                            <p class="w-title">Total Register</p>
                            <p class="w-stats text-info">{{ $retailer->count }}</p>
                        </div>
                    </div>
                    <div class="w-chart-section">
                        <div class="w-detail text-end">
                            <p class="w-title">Total Balance</p>
                            <p class="w-stats text-info">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                                @currency($retailer->user_balance)
                            </p>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
                <div class="w-chart">
                    <div class="w-chart-section">
                        <div class="w-detail">
                            <p class="w-title">Today Registered</p>
                            <p class="w-stats text-info">{{ $retailer_today }}</p>
                        </div>
                    </div>

                    <div class="w-chart-section">
                        <div class="w-detail text-end">
                            <p class="w-title">Today Allotted Money</p>
                            <p class="w-stats text-info">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                                @currency($payment_sum['retailer'])
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>



@endsection