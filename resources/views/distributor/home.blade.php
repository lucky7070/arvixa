@extends('layouts.distributor_app')

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

@include('partial.common.qrcode_modal')

@endsection