@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0">Products :: Product Details </h5>
                    </div>
                    <div class="col-auto ms-auto">
                        <div class="nav nav-pills nav-pills-falcon">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('products') }}">
                                <i class="fa fa-arrow-left me-1"></i> Go Back
                            </a>
                            <a class="btn btn-sm btn-outline-secondary ms-2"
                                href="{{ route('products.edit', $product->slug) }}">
                                <i class="fa fa-edit me-1"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5 mb-4 mb-lg-0 text-center h-100">
                        <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @if(count($product->images) > 0)
                                @foreach($product->images as $key => $row)
                                <div class="carousel-item  {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ $row->image }}" class="w-100 aspect-ratio-1" alt="">
                                </div>
                                @endforeach
                                @else
                                <div class="carousel-item active">
                                    <img src="{{ asset('storage/product/default.jpg') }}" class="w-100 aspect-ratio-1"
                                        alt="">
                                </div>
                                @endif
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade"
                                data-bs-slide="prev">
                                <span class="fas fa-angles-left fs-5 carousel-handel" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade"
                                data-bs-slide="next">
                                <span class="fas fa-angles-right fs-5 carousel-handel" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-7 d-flex flex-column justify-content-around">
                        <h5 class="mb-0">{{ $product->name }}</h5>
                        <p class="mb-0 text-primary">{{ @$product->category['name'] }}</p>
                        <h4 class="d-flex align-items-center">
                            <span class="text-warning me-2">₹{{ $product->price }}</span>
                            <span class="me-1 text-500">
                                <del class="me-1 fs--1">₹{{ $product->mrp }}</del>
                            </span>
                        </h4>
                        <p class="mb-1">Stock: <strong class="text-success">{{ $product->stock }}</strong></p>
                        <hr class="my-2">
                        <h6 class="mb-1">Other Details</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Brand Name</b>
                                <span>{{ @$product->brand->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>HSN Code</b>
                                <span>
                                    {{ @$product->hsn_code->code }}
                                    ( {{ @$product->hsn_code->tax_rate }}% )
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Weight</b>
                                <span>{{ $product->weight }} Grams</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Item Dimensions</b>
                                <span>{{ $product->length }} x {{ $product->width }} x {{ $product->height }}
                                    cm<sup>3</sup></span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <hr>
                        <h6 class="text-secondary fw-bold">Discription</h6>
                        <p>{{ $product->sort_description }}</p>
                        <div>{!! $product->description !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection