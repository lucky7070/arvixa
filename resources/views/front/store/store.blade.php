@extends('front.layouts.main')

@section('main_content')
<div class="container-fluid bg-light-theme text-center py-4 text-theme">
    <h2 class="mb-1 fw-bolder">E-Store</h2>
    <p class="mb-1">
        <a href="" class="text-decoration-none text-theme">Home</a> / E-Store
    </p>
</div>

<div class="container-lg my-5">
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 mb-4">
            <input id="searchText" type="text" placeholder="Search" class="form-control" required="">
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 mb-4 ms-auto">
            <select class="form-select form-select" id="categoryData">
                <option value="">All Category</option>
                @foreach($category as $row)
                <option value="{{ $row->id }}">{{ $row->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 mb-4">
            <select class="form-select form-select" id="orderData">
                <option value="4">Newest</option>
                <option value="1">Low to High Price</option>
                <option value="2">High to Low Price</option>
                <option value="3">Most Viewed</option>
            </select>
        </div>
    </div>
    <div class="row products"></div>
    <div class="w-100 d-flex justify-content-center align-items-center gap-3">
        <button type="button" class="btn btn-outline-primary min-w-150 previous">
            <i class="fa-regular fa-angles-left me-1"></i>
            Previous
        </button>
        <button type="button" class="btn btn-outline-primary min-w-150 next">
            Naxt
            <i class="fa-regular fa-angles-right ms-1"></i>
        </button>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/jquery.ba-throttle-debounce.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/e-commerce.js') }}"></script>
<script type="text/javascript">
    $(function () {
        window.getData();
    })
</script>
@endsection