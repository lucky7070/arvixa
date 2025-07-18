@extends('layouts.employee_app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Retailers :: Retailer Add </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('employee.retailers')  }}" class="btn btn-outline-secondary"> <i
                            class="fa fa-arrow-left me-1"></i> Go
                        Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="addUser" method="POST" action="{{ route('employee.retailers.add') }}"
            enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="main_distributor_id">Main Distributor</label>
                <select name="main_distributor_id" class="form-select" id="main_distributor_id"
                    onchange="getDistributors(this.value)">
                    <option value="">Admin</option>
                    @foreach ($main_distributors as $main_distributor)
                    <option value="{{ $main_distributor['id'] }}" {{ old('main_distributor_id')==$main_distributor['id']
                        ? 'selected' : '' }}>
                        {{ $main_distributor['name'] }}
                    </option>
                    @endforeach
                </select>
                @error('main_distributor_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="distributor_id">Distributor</label>
                <select name="distributor_id" class="form-select" id="distributor_id">
                    <option value="">Admin</option>
                </select>
                @error('distributor_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="name">First Name <span class="required">*</span></label>
                <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                    value="{{ old('name') }}" />
                @error('name')
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
                <label class="form-label" for="image">Image </label>
                <input class="form-control" id="image" name="image" type="file" value="" />
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="password">New Password <span class="required">*</span></label>
                <input class="form-control" placeholder="Enter Password" name="password" id="new-password"
                    type="password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="password_confirmation">Confirm Password <span
                        class="required">*</span></label>
                <input class="form-control" placeholder="Enter Confirm Password" name="password_confirmation"
                    id="password_confirmation" type="password">
                @error('password_confirmation')
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

    $("#addUser").validate({
        rules: {
            name: {
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
            password: {
                required: "Please enter New Password",
            },
            password_confirmation: {
                required: "Please enter Confirm Password",
            },
            image: {
                extension: "Supported Format Only : jpg, jpeg, png"
            }
        },

    });

</script>
@endsection