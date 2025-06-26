@extends('front.layouts.main')

@section('main_content')

@if($main_banner->count())
<div id="carouselExample" class="carousel slide">
    <div class="carousel-inner">
        @foreach($main_banner as $key => $row)
        <div class="carousel-item @if($key == 0) active  @endif">
            <img src="{{ asset('storage/'.$row->image) }}" class="d-block w-100" alt="">
        </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
@endif

<div class="container-fluid bg-theme text-white">
    <marquee class="news-scroll" behavior="scroll" direction="left" onmouseover="this.stop();"
        onmouseout="this.start();">
        {!! $site_settings['message_ticker'] !!}
    </marquee>
</div>

@if($sec_banner->count())
<div class="container-lg bg-white text-theme my-5">
    <div class="owl-carousel owl-carousel-banners owl-theme">
        @foreach($sec_banner as $key => $row)
        <div class="item">
            <img class="img-fluid" src="{{ asset('storage/'.$row->image) }}" alt="">
        </div>
        @endforeach
    </div>
</div>
@endif

@if($services->count())
<div class="w-100 bg-theme text-white py-5">
    <div class="container-lg">
        <div class="row">
            <div class="col-md-12 justify-content-between d-flex align-items-center mb-3">
                <h1 class="fw-bold">Services</h1>
                <a class="btn btn-theme-secondary" href="{{ route('our-services') }}">
                    View All
                    <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-md-12">
                <div class="row services">
                    @foreach($services as $key => $row)
                    <div class="col-lg-2 col-md-3 col-sm-6 col-12">
                        <a href="{{ route('retailer.dashboard') }}" class="text-decoration-none text-white">
                            <div class="service-card">
                                <img src="{{ asset('storage/'.$row->image) }}" alt="">
                                <h5>{{ $row->name }}</h5>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="container-lg py-5">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('assets/img/banner-front.png') }}" alt="" class="img-fluid">
        </div>
        <div class="col-md-6">
            <div class="d-flex1 flex-column">
                <h1 class="fw-bold text-theme">About Us</h1>
                <div class="w-100">
                    {!! $about_us->description !!}
                </div>
                <a href="{{ route('about') }}" class="btn btn-theme btn-lg px-4">Read More</a>
            </div>
        </div>
    </div>
</div>

@if($testimonials->count())
<div class="container-lg bg-white mb-5 testimonail">
    <div class="col-md-12 mb-4">
        <h1 class="fw-bold text-theme text-center">Testimonail</h1>
    </div>
    <div class="col-md-12">
        <div class="owl-carousel owl-carousel-testimonail owl-theme">
            @foreach($testimonials as $key => $row)
            <div class="item">
                <div class="testimonail-card">
                    <div class="d-none d-lg-block">
                        <div class="line">
                            <i class="fa-solid fa-quote-left"></i>
                        </div>
                    </div>
                    <div class="details">
                        <div class="stars text-theme-secondary text-start mb-3">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <p class="fs-small text-muted text-justify">{{ Str::limit( $row->description, 350) }}</p>
                        <div class="user-details">
                            <div>
                                <h6 class="text-start mb-1 text-theme fw-bold">{{ $row->name }}</h6>
                                <p class="text-start mb-1 text-muted fs-7">{{ $row->designation }}</p>
                            </div>
                            <img src="{{ asset('storage/'.$row->image) }}" class="border border-2" alt="">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection