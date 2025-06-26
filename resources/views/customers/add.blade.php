@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Customers :: Customer Add </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('customers')  }}" class="btn btn-outline-secondary me-4"> <i
                            class="fa fa-arrow-left me-1"></i> Go
                        Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="add" method="POST" action="{{ route('customers.add') }}" enctype='multipart/form-data'>
            @csrf

            <div class="col-lg-4 mt-2">
                <label class="form-label" for="first_name">First Name <span class="required">*</span></label>
                <input class="form-control" id="first_name" placeholder="Enter First Name" name="first_name" type="text"
                    value="{{ old('first_name') }}" />
                @error('first_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 mt-2">
                <label class="form-label" for="middle_name">Middle Name</label>
                <input class="form-control" id="middle_name" placeholder="Enter Middle Name" name="middle_name"
                    type="text" value="{{ old('middle_name') }}" />
                @error('middle_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 mt-2">
                <label class="form-label" for="last_name">Last Name</label>
                <input class="form-control" id="last_name" placeholder="Enter Last Name" name="last_name" type="text"
                    value="{{ old('last_name') }}" />
                @error('last_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="email">Email <span class="required">*</span></label>
                <input class="form-control" id="email" placeholder="Enter Email" type="email" name="email"
                    value="{{ old('email') }}" />
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="mobile">Mobile <span class="required">*</span></label>
                <input class="form-control" id="mobile" placeholder="Enter Mobile Number" name="mobile" type="text"
                    value="{{ old('mobile') }}" />
                @error('mobile')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="dob">Date of Birth</label>
                <input class="form-control" id="dob" placeholder="Select Date of Birth" name="dob" type="date"
                    value="{{ old('dob') }}" />
                @error('dob')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="gender">Gender</label>
                <select class="form-control" id="gender" name="gender" value="{{ old('gender') }}">
                    <option value="N">Please Select</option>
                    @foreach(config('constant.gender_list') as $key => $gender)
                    <option value="{{ $key }}" {{ old('gender')==$key ? 'selected' : '' }}>{{
                        $gender }}</option>
                    @endforeach
                </select>

                @error('gender')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" {{ old('status', 1)==1 ? 'selected' : '' }}> Active </option>
                    <option value="0" {{ old('status', 1)==0 ? 'selected' : '' }}> Inactive </option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="state_id">State</label>
                <select name="state_id" onchange="getCity(this.value)" class="form-select" id="state_id">
                    <option value="">Select State</option>
                    @foreach ($states as $state)
                    <option value="{{ $state['id'] }}" {{ old('state_id')==$state['id'] ? 'selected' : '' }}>
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
            <div class="col-lg-6 mt-2">
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

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="image">Image </label>
                <input class="form-control" id="image" name="image" type="file" value="" />
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                <button class="btn btn-primary submitbtn" type="submit">Add</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">

    $("#add").validate({
        rules: {
            first_name: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            email: {
                required: true,
                email: true
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
            first_name: {
                required: "Please enter first name",
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

    var city_id = "{{ old('city_id') }}";
    function getCity(state_id) {
        $.ajax({
            type: "POST",
            url: "{{ route('cities.list') }}",
            data: { state_id, city_id },
            success: function (data) {
                $('#city_id').html(data);
            },
        });
        return true;
    }

    $(window).on('load', function () {
        getCity("{{ old('state_id') }}");
    })

</script>
@endsection