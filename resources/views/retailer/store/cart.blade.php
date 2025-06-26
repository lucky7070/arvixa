@extends('layouts.retailer_app')

@section('css')
<style>
    .box {
        min-height: 100px;
    }

    /* common */
    .ribbon {
        width: 130px;
        height: 130px;
        overflow: hidden;
        position: absolute;
    }

    .ribbon::before,
    .ribbon::after {
        position: absolute;
        z-index: 0;
        content: '';
        display: block;
        border: 5px solid var(--ribbon-color, #2980b9);
    }

    .ribbon span {
        position: absolute;
        display: block;
        width: 125px;
        padding: 2px 0;
        background-color: var(--ribbon-color, #3498db);
        box-shadow: 0 5px 10px rgba(0, 0, 0, .1);
        color: #fff;
        text-shadow: 0 1px 1px rgba(0, 0, 0, .2);
        text-transform: uppercase;
        text-align: center;
        opacity: 0.8;
    }

    /* top right*/
    .ribbon-top-right {
        top: -10px;
        right: -10px;
    }

    .ribbon-top-right::before,
    .ribbon-top-right::after {
        border-top-color: transparent;
        border-right-color: transparent;
    }

    .ribbon-top-right::before {
        top: 0;
        left: 40px;
    }

    .ribbon-top-right::after {
        bottom: 40px;
        right: 0;
    }

    .ribbon-top-right span {
        left: 30px;
        top: 25px;
        transform: rotate(45deg);
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-1">Shopping Cart</h5>
            <a href="{{ route('retailer.product') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i>
                Go Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 cartItems">
                <div class="d-flex justify-content-center align-items-center">
                    <i class="fa-duotone fa-spinner text-secondary fa-3x fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h1 class="modal-title text-secondary fs-5" id="voucherModalLabel">Promo / Voucher Code</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @if($offers->count())
                    @foreach($offers as $row)
                    <div class="col-md-12 mb-3">
                        <div class="card box">
                            <div class="ribbon ribbon-top-right" style="--ribbon-color:red">
                                <span>Offer</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex flex-column gap-1 justify-content-between">
                                    <h6 class="mb-0 text-red">{{ $row->name }}</h6>
                                    <div class="small text-muted mb-2">{{ nl2br($row->description) }}</div>
                                    <div class="d-flex justify-content-between align-items-end">
                                        <p class="mb-0 px-2 py-1 coupon-code text-dark bg-gray">{{ $row->code }}</p>
                                        <p class="mb-0 small text-dark">Expire:
                                            {{ $row->expires_at->format('d M, Y h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-0 text-danger">No Offers Available.</h6>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/jquery.ba-throttle-debounce.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/e-commerce.js') }}"></script>
<script>
    $(function () {
        window.getCartItems();
    })
</script>
@endsection