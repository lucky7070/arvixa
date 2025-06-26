@extends('front.layouts.main')

@section('main_content')
<div class="container-fluid bg-light-theme text-center py-4 text-theme">
    <h2 class="mb-1 fw-bolder">Services</h2>
    <p class="mb-1">
        <a href="" class="text-decoration-none text-theme">Home</a> / Services
    </p>
</div>


<div class="container-xl my-5">
    <div class="card">
        <div class="card-body services">
            <div class="row">
                @if($services->count())
                @foreach($services as $key => $row)
                <div class="col-lg-4 col-md-6">
                    <div class="box">
                        <div class="our-services settings">
                            <div class="header">
                                <div class="icon">
                                    <img src="{{ asset('storage/'.$row->image) }}">
                                </div>
                            </div>
                            <h5 class="my-3">{{ $row->name }}</h5>
                            <a href="{{ route('retailer.dashboard') }}" class="btn btn-theme-secondary">{{
                                $row->btn_text }}</a>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-md-12 mb-3">
                    <p class="mb-0 text-danger text-center">No Services Found.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection