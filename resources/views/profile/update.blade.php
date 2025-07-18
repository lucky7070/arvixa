@extends('layouts.'.($user['route'] != 'web' ? $user['route'].'_': '').'app')

@section('css')
<link href="{{ asset('assets/css/tom-select.default.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom-tomSelect.css') }}" rel="stylesheet" type="text/css" />
@endsection


@section('content')

@include('partial.common.user_box')
<div class="row g-0">
    <div class="col-lg-8 pe-lg-2">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Profile Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" id="profileUpdate" action="{{ request()->url() }}"
                    enctype='multipart/form-data'>
                    <fieldset class="row g-3" @disabled(in_array($user['route'], ['distributor', 'main_distributor' , 'retailer' ]))>
                        @csrf
                        <div class="col-lg-6">
                            <label class="form-label" for="name">First Name</label>
                            <input class="form-control text-dark" id="name" name="name" type="text"
                                value="{{ old('name', $user->name) }}" />
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control text-dark" id="email" type="email" name="email"
                                value="{{ old('email', $user->email) }}" />
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="mobile">Mobile</label>
                            <input class="form-control text-dark" id="mobile" name="mobile" type="text"
                                value="{{ old('mobile', $user->mobile) }}" />
                            @error('mobile')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label" for="state_id">State</label>
                            <select name="state_id" onchange="getCity(this.value)" class="form-select" id="state_id">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                <option value="{{ $state['id'] }}" {{ old('state_id', $user['state_id'])==$state['id']
                                ? 'selected' : '' }}>
                                    {{ $state['name'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('state_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="city_id">City</label>
                            <select name="city_id" class="form-select" id="city_id">
                                <option value="">Select City</option>
                            </select>
                            @error('city_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="image">Image</label>
                            <input class="form-control" id="image" name="image" type="file" value="" />
                            @error('image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4 ps-lg-2">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" id="passUpdate"
                    action="{{ route(($user['route'] != 'web' ? $user['route'].'.' : '' ).'update-password') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="old_password">Old Password</label>
                        <input class="form-control" name="old_password" id="old_password" type="password">
                        @error('old_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">New Password</label>
                        <input class="form-control" name="password" id="new-password" type="password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <input class="form-control" name="password_confirmation" id="password_confirmation"
                            type="password">
                        @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <button class="btn btn-primary d-block w-100" type="submit">Update Password </button>
                </form>
            </div>
        </div>
    </div>

    @if($user['route'] == 'retailer')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Default Board</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('retailer.default-board-save') }}">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="default_electricity_board" class="form-label">Select Electricity Board</label>
                            @csrf
                            <select class="form-control" name="default_electricity_board" id="default_electricity_board">
                                <option value=""> -- Select Board --</option>
                                @foreach($providers['electricity'] ?? [] as $provider)
                                <option value="{{ $provider->id }}" @selected($user->default_electricity_board == $provider->id)>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label for="default_water_board" class="form-label">Select Water Board</label>
                            <select class="form-control" name="default_water_board" id="default_water_board">
                                <option value=""> -- Select Board --</option>
                                @foreach($providers['water'] ?? [] as $provider)
                                <option value="{{ $provider->id }}" @selected($user->default_water_board == $provider->id)>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="default_gas_board" class="form-label">Select Gas Board</label>
                            <select class="form-control" name="default_gas_board" id="default_gas_board">
                                <option value=""> -- Select Board --</option>
                                @foreach($providers['gas'] ?? [] as $provider)
                                <option value="{{ $provider->id }}" @selected($user->default_gas_board == $provider->id)>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="default_lic_board" class="form-label">Select LIC Board</label>
                            <select class="form-control" name="default_lic_board" id="default_lic_board">
                                <option value=""> -- Select Board --</option>
                                @foreach($providers['lic'] ?? [] as $provider)
                                <option value="{{ $provider->id }}" @selected($user->default_lic_board == $provider->id)>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary px-4">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection


@section('js')

@if($user['route'] == 'retailer')
<script src="{{ asset('assets/js/tom-select.base.js') }}"></script>
<script>
    $(function() {

        new TomSelect("#default_electricity_board");
        new TomSelect("#default_water_board");
        new TomSelect("#default_gas_board");
        new TomSelect("#default_lic_board");
    });
</script>
@endif

<script>
    var city_id = "{{ old('city_id', $user['city_id']) }}";

    function getCity(state_id) {
        $.ajax({
            type: "POST",
            url: "{{ route('cities.list') }}",
            data: {
                state_id,
                city_id
            },
            success: function(data) {
                $('#city_id').html(data);
            },
        });
        return true;
    }


    $(function() {
        $('#profile-image').change(function() {
            var formData = new FormData();
            formData.append('image', this.files[0]);
            $.ajax({
                url: "{{ route(($user['route'] != 'web' ? $user['route'].'.' : '' ).'update-image') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    if (result.success) {
                        toastr.success(result?.message);
                        $('.profile-img').attr('src', result?.image);
                    } else {
                        toastr.error(result?.message);
                    }
                }
            });
        });

        $("#profileUpdate").validate({
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                email: {
                    required: true,
                    email: true,
                    minlength: 2,
                    maxlength: 100
                },
                mobile: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10
                },
                image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                email: {
                    required: "Please enter Email",
                },
                mobile: {
                    required: "Please enter Mobile number",
                },
                image: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                }
            },
        });

        $("#passUpdate").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                old_password: {
                    required: true,
                    minlength: 8,
                    maxlength: 50
                },
                password: {
                    required: true,
                    minlength: 8,
                    maxlength: 50
                },
                password_confirmation: {
                    required: true,
                    minlength: 8,
                    maxlength: 50,
                    equalTo: "#new-password"
                },

            },
            messages: {
                old_password: {
                    required: "Please enter old password",
                },
                password: {
                    required: "Please enter new password",
                },
                password_confirmation: {
                    required: "Please enter confirm password",
                },
            },
        });

        setTimeout(() => {
            getCity("{{ old('state_id', $user['state_id']) }}");
        }, 500);
    });
</script>
@endsection