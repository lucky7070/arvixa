@extends('front.layouts.main')

@section('main_content')
<div class="container-lg my-5">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Checkout Page</h5>
                <a href="{{ route('customer.cart') }}" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left me-1"></i>
                    Go Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-5 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary">Your cart</span>
                        <span class="badge bg-primary rounded-pill fs-6">{{ $cart->count() }}</span>
                    </h4>
                    <ul class="list-group mb-3">
                        @foreach($cart as $row)
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <a class="text-decoration-none"
                                href="{{ route('customer.product_details', @$row->product->slug) }}">
                                <h6 class="my-0">{{ @$row->product->name }} x {{ $row->qty }}</h6>
                                <small class="text-secondary me-1 border-end">
                                    Brand - {{ @$row->product->brand->name }}
                                </small>
                                <small class="text-danger me-1">
                                    Item Price -
                                    <i class="fa fa-inr"></i> {{ round($row->sub_total / $row->qty , 2) }}
                                </small>
                            </a>
                            <span class="text-muted1 text-end" style="min-width: 100px;">
                                <i class="fa fa-inr"></i>
                                {{ round($row->sub_total, 2) }}
                            </span>
                        </li>
                        @endforeach

                        <li class="list-group-item d-flex justify-content-between">
                            <span>Sub Total</span>
                            <strong><i class="fa fa-inr"></i> {{ round($summery['sub_total'],2) }} </strong>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span>Tax Amount</span>
                            <strong><i class="fa fa-inr"></i> {{ round($summery['tax'],2) }} </strong>
                        </li>
                        @if(!empty($summery['delivery']) && $summery['delivery'] > 0)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Shipping</span>
                            <strong><i class="fa fa-inr ne-1"></i> {{ round($summery['delivery'],2) }} </strong>
                        </li>
                        @endif
                        @if(!empty($summery['discount']) && $summery['discount'] > 0)
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-success">Discount</span>
                            <strong class="text-success">
                                -<i class="fa fa-inr"></i> {{ round($summery['discount'],2) }}
                            </strong>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total</span>
                            <strong><i class="fa fa-inr ne-1"></i> {{ round($summery['total'], 2) }} </strong>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-7">
                    <h4 class="mb-3">Shipping Address</h4>
                    <form id="shippingAddress" method="post" action="{{ route( 'customer.place-order' ) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="first_name" class="form-label">
                                    First Name<span class="text-danger">*</span>
                                </label>
                                <input type="text" name="first_name" class="form-control" id="first_name"
                                    placeholder="First Name"
                                    value="{{ old('first_name', @$lastOrder->customer_name_1) }}" required="">
                                @error('first_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <label for="last_name" class="form-label">
                                    Last Name<span class="text-danger">*</span>
                                </label>
                                <input type="text" name="last_name" class="form-control" id="last_name"
                                    placeholder="Last Name" value="{{ old('last_name', @$lastOrder->customer_name_2) }}"
                                    required="">
                                @error('last_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span> </label>
                                <input type="email" name="email" class="form-control" id="email"
                                    placeholder="you@example.com"
                                    value="{{ old('email', @$lastOrder->customer_email) }}">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span> </label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', @$lastOrder->customer_mobile) }}" id="phone"
                                    placeholder="9800000000">
                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Address <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control" id="address"
                                    placeholder="1234 Main St"
                                    value="{{ old('address', @$lastOrder->shipping_address_1) }}" required="">
                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="address2" class="form-label">Address 2 <span
                                        class="text-muted">(Optional)</span></label>
                                <input type="text" name="address2" class="form-control" id="address2"
                                    placeholder="Apartment or suite"
                                    value="{{ old('address2', @$lastOrder->shipping_address_2) }}">
                                @error('address2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="state_id" class="form-label">State <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="state_id" id="state_id" required="">
                                    <option value="">Choose...</option>
                                    @foreach ($states as $state)
                                    <option value="{{ $state['id'] }}" @selected(old('state_id')==$state['id'])
                                        @selected($state['name']==@$lastOrder->shipping_state )> {{
                                        $state['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('state_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="city_id" class="form-label">City <span class="text-danger">*</span></label>
                                <select class="form-select" name="city_id" id="city_id" required="">
                                    <option value="">Choose...</option>
                                </select>
                                @error('city_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="zip" class="form-label">Zip <span class="text-danger">*</span></label>
                                <input type="text" name="zip" class="form-control" id="zip"
                                    value="{{ old('zip', @$lastOrder->shipping_postcode) }}" placeholder="" required="">
                                @error('zip')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <h4 class="mb-3">Payment</h4>
                        <div class="my-3">
                            @if($user->user_balance > 0)
                            <h6 class="mb-1" for="credit">Avilable Balance :
                                <span class="text-primary">
                                    <i class="fa fa-inr"></i>
                                    {{ $user->user_balance }}
                                </span>
                            </h6>
                            <h6 class="mb-1" for="credit">Wallet Use :
                                <span class="text-danger">
                                    <i class="fa fa-inr"></i>
                                    @if($summery['total'] > $user->user_balance )
                                    -{{ $user->user_balance }}
                                    @else
                                    -{{ $summery['total'] }}
                                    @endif
                                </span>
                            </h6>
                            @endif
                            @if($summery['total'] - $user->user_balance > 0)
                            <h6 class="mb-1" for="paypal"> Online Payment :
                                <span class="text-primary">
                                    <i class="fa fa-inr"></i>
                                    {{ $summery['total'] - $user->user_balance }}
                                </span>
                            </h6>
                            @endif
                        </div>
                        <button class="w-100 btn btn-primary btn-lg" type="submit">
                            Confirm & Pay
                            <i class="fa fa-arrows-right"></i>
                        </button>
                    </form>
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
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/custom-methods.js') }}"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $(function () {

        var city_id = "{{ old('city_id', @$lastOrder->shipping_city) }}";

        var totalOrderValue = "{{ $summery['total'] }}";
        var userBalance = "{{ $user->user_balance }}";

        function getCity(state_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('cities.list') }}",
                data: { state_id, city_id, _token: "{{ csrf_token() }}" },
                success: function (data) {
                    $('#city_id').html(data);
                },
            });
            return true;
        }

        function placeOrder(form) {
            $("#overlay").show();
            var formData = new FormData(form);
            $.ajax({
                url: "{{ route('customer.place-order') }}",
                data: formData,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function (data) {
                    if (data.status) {
                        $(form).trigger("reset")
                        swal({
                            title: "Thank you,",
                            text: data.message,
                            icon: "success",
                            timer: 15000,
                            button: "Okay",
                        }).then(() => window.location.href = "{{ route('customer.my-orders') }}")

                    } else {
                        $("#overlay").hide();
                        $(form).validate().showErrors(data.data);
                        toastr.error(data.message);
                    }
                }
            });
        }

        setTimeout(() => {
            $('#state_id').change();
            // getCity("{{ old('state_id') }}");
        }, 300);

        $('#state_id').on('change', function () {
            getCity(this.value)
        })

        $("#shippingAddress").validate({
            errorElement: "span",
            rules: {
                first_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                last_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                email: {
                    required: true,
                    email: true,
                    customEmail: true,
                    minlength: 2,
                    maxlength: 100
                },
                phone: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10
                },
                address: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                address2: {
                    minlength: 2,
                    maxlength: 100
                },
                state_id: {
                    required: true
                },
                city_id: {
                    required: true
                },
                zip: {
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 6
                }
            },
            messages: {
                first_name: {
                    required: "Please enter first name",
                },
                last_name: {
                    required: "Please enter last name",
                },
                email: {
                    required: "Please enter Email",
                },
                phone: {
                    required: "Please enter Mobile number",
                },
                address: {
                    required: "Please enter address.",
                },
                state_id: {
                    required: "Please select state.",
                },
                city_id: {
                    required: "Please select city.",
                },
                zip: {
                    required: "Please enter zip code.",
                }
            },
            submitHandler: function (form) {
                console.log(totalOrderValue, userBalance, parseFloat(totalOrderValue) < parseFloat(userBalance));
                if (parseFloat(totalOrderValue) < parseFloat(userBalance)) {
                    placeOrder(form)
                } else {
                    var amount = parseFloat(totalOrderValue) - parseFloat(userBalance)
                    if (amount > 0) {
                        $("#overlay").show();
                        $.post("{{ route('razorpay') }}", { amount }, function (data) {
                            if (data.status == true) {
                                $('#amount').val('');
                                $('#addModal').modal('hide');
                                var { key, amount, order_id, currency } = data.data;
                                var options = {
                                    key: key,
                                    amount: amount,
                                    currency: currency,
                                    name: "{{ $site_settings['application_name'] }}",
                                    description: "{{ 'Money Load For : '.$user['name'].' - Order Payment' }} ",
                                    image: "{{ asset('storage/' . $site_settings['logo']) }}",
                                    order_id: order_id,
                                    handler: function (response) {
                                        response.user_id = "{{ $user['id'] }}";
                                        response.user_type = "7";
                                        $.post("{{ route('update-wallet') }}", response, function (data) {
                                            $("#overlay").hide();
                                            if (data.status == true) {
                                                toastr.success(data.message);
                                                placeOrder(form)
                                            } else {
                                                toastr.error(data.message);
                                            }
                                        })
                                    },
                                    prefill: {
                                        name: "{{ $user['name'] }}",
                                        email: "{{ $user['email'] }}",
                                        contact: "{{ $user['mobile'] }}"
                                    },
                                    notes: {
                                        address: "Corporate Office"
                                    },
                                    theme: {
                                        color: "#3399cc"
                                    },
                                    modal: {
                                        ondismiss: function () {
                                            $("#overlay").hide();
                                        }
                                    }
                                };

                                var rzp1 = new Razorpay(options);
                                rzp1.on('payment.failed', function ({ error }) {
                                    if (error.description) {
                                        $("#overlay").hide();
                                        toastr.error(error.description);
                                    }
                                });
                                rzp1.open();
                            }
                            else {
                                $("#overlay").hide();
                                toastr.error(data.message);
                            }
                        })
                    }
                    else {
                        toastr.error('Please enter amount min 1');
                    }
                }
            }
        });
    })
</script>
@endsection