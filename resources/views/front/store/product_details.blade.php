@extends('front.layouts.main')

@section('main_content')
<div class="container my-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0 text-center h-100">
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
                            <span class="fas fa-angles-left carousel-handel fs-6" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade"
                            data-bs-slide="next">
                            <span class="fas fa-angles-right carousel-handel fs-6" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
                <div class="col-lg-8 d-flex flex-column gap-2 align-items-start">
                    <h5 class="mb-0">{{ $product->name }}</h5>
                    <p class="mb-0 text-theme">{{ @$product->category['name'] }}</p>
                    <h4 class="d-flex align-items-center">
                        <span class="text-theme-secondary me-2">₹{{ $product->price }}</span>
                        <span class="me-1 text-500">
                            <del class="me-1 fs--1">₹{{ $product->mrp }}</del>
                        </span>
                    </h4>
                    <p class="mb-1">Stock:
                        @if($product->stock)
                        <span class="text-success">{{ $product->stock }}</span>
                        @else
                        <span class="text-danger">Out of Stock</span>
                        @endif
                    </p>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary minusQty" @disabled($product->stock == 0 ||
                            max(1, $product->minimum) == checkInRange($product->minimum,$product->maximum))>
                            <i class="fa fa-minus"></i>
                        </button>
                        <input type="number" min="{{ max(1, $product->minimum) }}"
                            max="{{ min( $product->stock, $product->maximum) }}"
                            value="{{ checkInRange($product->minimum,$product->maximum) }}" style="max-width: 80px;"
                            class="form-control text-center rm-number qtyInput form-control-sm rounded-0 border-end-0 border-start-0" />
                        <button type="button" class="btn btn-sm btn-primary plusQty" @disabled($product->stock == 0)>
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary addToCart" @disabled($product->stock == 0)
                            data-product-id="{{ $product->id }}">
                            <i class="fa-duotone fa-cart-plus me-1"></i>
                            Add to Cart
                        </button>
                        <button class="btn btn-outline-primary buyNow" @disabled($product->stock == 0)
                            data-product-id="{{ $product->id }}">
                            <i class="fa-duotone fa-cart-shopping-fast me-1"></i>
                            Buy Now
                        </button>
                    </div>
                    <hr class="my-2 w-100">
                    <h6 class="mb-0">Short Discription</h6>
                    <p>{{ $product->sort_description }}</p>
                </div>
                <hr class="my-2">
                <div class="col-md-12">
                    <ul class="list-group list-group-flush">
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
                        <li class="list-group-item">
                            <b class="fw-bold mb-2 d-block">Discription</b>
                            <div>{!! nl2br($product->description) !!}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/jquery.ba-throttle-debounce.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/e-commerce.js') }}"></script>
<script src="{{ asset('assets/js/jquery.ez-plus.js') }}"></script>
<script>
    $(function () {
        $(".carousel-item img").ezPlus();
    })
</script>
@endsection