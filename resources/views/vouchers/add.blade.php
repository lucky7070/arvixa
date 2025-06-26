@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Vouchers :: Voucher Add </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('vouchers')  }}" class="btn btn-outline-secondary me-4"> <i
                            class="fa fa-arrow-left me-1"></i> Go
                        Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="addUser" method="POST" action="{{ route('vouchers.add') }}" enctype='multipart/form-data'>
            @csrf
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="type">Voucher Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" id="type">
                        @foreach (config('constant.voucher_type_list', []) as $key => $value)
                        <option name="type" value="{{ $key }}" @selected(old('type')==$key)>
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                    @error('type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-md-6 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Coupon Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                            placeholder="Name of the coupon">
                    </div>
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="code">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}" minlength="5" maxlength="12"
                            class="form-control" placeholder="Promotion code or Generate one">
                    </div>
                    @error('code')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-md-2 mb-2">
                    <div class="form-group text-end">
                        <label class="form-label d-block"><br></label>
                        <button type="button" class="btn btn-primary btn-lg" id="genCouponCode">Generate</button>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="description">Description
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="description" id="description" class="form-control"
                            placeholder="Brief description of coupon" value="{{ old('description') }}">
                    </div>
                    @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="max_uses">Total Usage limit
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="max_uses" id="max_uses" class="form-control"
                            placeholder="Total Usage limit" min="1" value="{{ old('max_uses', 50) }}">
                    </div>
                    @error('max_uses')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-group">
                        <label for="max_uses_per_user">Usage limit per User <span class="text-danger">*</span></label>
                        <input type="number" name="max_uses_per_user" id="max_uses_per_user" class="form-control"
                            placeholder="Usage limit per user" min="1" value="{{ old('max_uses_per_user', 1) }}">
                    </div>
                    @error('max_uses_per_user')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="starts_at">From Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="starts_at" id="starts_at" class="form-control"
                            value="{{ old('starts_at') }}">
                    </div>
                    @error('starts_at')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="expires_at">To Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control"
                            value="{{ old('expires_at') }}">
                    </div>
                    @error('expires_at')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="is_fixed">Discount Applies by
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="is_fixed" name="is_fixed">
                            <option value="1" @selected(old('is_fixed')==1)>Flat Discount</option>
                            <option value="2" @selected(old('is_fixed')==2)>Percentage Discount</option>
                        </select>
                    </div>
                    @error('is_fixed')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="discount_amount">Discount Amount
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="discount_amount" name="discount_amount"
                            placeholder="Discount Amount" value="{{ old('discount_amount') }}" min="1">
                    </div>
                    @error('discount_amount')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-group">
                        <label class="form-label max_discount" for="max_discount">Maximum Discount
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="max_discount" id="max_discount" class="form-control max_discount"
                            value="{{ old('max_discount') }}" min="0" placeholder="Maximum Discount to apply.">
                    </div>
                    @error('max_discount')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="col-lg-3 col-md-4 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="min_cart_value">Minimum Cart Value
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="min_cart_value" id="min_cart_value" class="form-control"
                            value="{{ old('min_cart_value') }}" min="1"
                            placeholder="Minimum cart value to apply discount">
                    </div>
                    @error('min_cart_value')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-lg-3 col-md-4 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="is_free_shipping">With Free Shipping</label>
                        <select name="is_free_shipping" class="form-select" id="is_free_shipping">
                            <option value="0" @selected(old('is_free_shipping', 0)==0)> No</option>
                            <option value="1" @selected(old('is_free_shipping', 0)==1)> Yes</option>
                        </select>
                    </div>
                    @error('is_free_shipping')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-lg-3 col-md-4 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" class="form-select" id="status">
                            <option value="1" @selected(old('status', 1)==1)> Active</option>
                            <option value="0" @selected(old('status', 1)==0)> Inactive</option>
                        </select>
                    </div>
                    @error('status')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-lg-3 col-md-4 mb-2">
                    <div class="form-group">
                        <label class="form-label" for="is_public">Visible to All</label>
                        <select name="is_public" class="form-select" id="is_public">
                            <option value="0" @selected(old('is_public', 1)==0)> No</option>
                            <option value="1" @selected(old('is_public', 1)==1)> Yes</option>
                        </select>
                    </div>
                    @error('is_public')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-lg-12 mt-3 d-flex justify-content-start">
                    <button class="btn btn-primary submitbtn" type="submit">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $(function () {

        let showMaxDiscount = "{{ old('max_discount') }}";
        if (showMaxDiscount) {
            $(".max_discount").show();
        } else {
            $(".max_discount").hide();
        }

        $("#addUser").validate({
            rules: {
                code: {
                    required: true,
                    minlength: 5,
                    maxlength: 12
                },
                name: {
                    required: true,
                    minlength: 5,
                    maxlength: 50
                },
                description: {
                    required: true,
                    minlength: 5,
                    maxlength: 100
                },
                max_uses: {
                    required: true,
                    min: 1,
                    digits: true
                },
                max_uses_per_user: {
                    required: true,
                    min: 1,
                    digits: true
                },
                type: {
                    required: true,
                },
                discount_amount: {
                    required: true,
                    min: 0,
                },
                is_fixed: {
                    required: true,
                },
                max_discount: {
                    required: true,
                },
                min_cart_value: {
                    required: true,
                    min: 1,
                },
                starts_at: {
                    required: true,
                },
                expires_at: {
                    required: true,
                },
            },
            messages: {
                code: {
                    required: "Please enter voucher code.",
                },
                name: {
                    required: "Please enter voucher name.",
                },
                description: {
                    required: "Please enter voucher description.",
                },
                max_uses: {
                    required: "Please enter voucher max uses.",
                },
                max_uses_per_user: {
                    required: "Please enter voucher max uses per user.",
                },
                discount_amount: {
                    required: "Please enter discount amount.",
                },
                max_discount: {
                    required: "Please enter max discount value.",
                },
                min_cart_value: {
                    required: "Please enter min cart value.",
                },
                starts_at: {
                    required: "Please enter start date and time.",
                },
                expires_at: {
                    required: "Please enter end date and time.",
                },
            },
        });

        $('#genCouponCode').on('click', function () {
            var str = makeid(8);
            $('#code').val(str.toUpperCase()).keyup();
        });

        $('#code').on('input', function (e) {
            this.value = slugify(this.value, '').toUpperCase();
        });

        $("#is_fixed").on('change', function () {
            var is_fixed = $(this).val();
            if (is_fixed == 1) {
                $("#discount_amount").attr('placeholder', 'Enter Discount Amount');
                $("#discount_amount").attr('min', '0');
                $("#discount_amount").attr('step', '0.01');
                $("#discount_amount").prop('max', false);
                $(".max_discount").hide();
            } else if (is_fixed == 2) {
                $("#discount_amount").attr('placeholder', 'Enter Discount (%)');
                $("#discount_amount").attr('min', '1');
                $("#discount_amount").attr('max', '99');
                $(".max_discount").show();
            }
        });
    })
</script>
@endsection