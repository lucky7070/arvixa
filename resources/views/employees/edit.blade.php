@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Employees :: Employee Edit </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('employees')  }}" class="btn btn-outline-secondary me-4"> <i
                            class="fa fa-arrow-left me-1"></i> Go
                        Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="ediUser" method="POST" action="{{ route('employees.edit', $employee['id']) }}"
            enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="designation_id">Designation</label>
                <select name="designation_id" class="form-select" id="designation_id">
                    <option value="">Please Select Designation</option>
                    @foreach (config('constant.designation_list', []) as $key => $designation)
                    <option name="designation_id" value="{{ $key }}" 
                    {{ (old('designation_id')==$key || $employee['designation_id']==$key) ? 'selected' : '' }}> {{ $designation }}
                    </option>
                    @endforeach
                </select>
                @error('designation_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="name">First Name</label>
                <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                    value="{{ old('name',$employee['name']) }}" />
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" placeholder="Enter Email" type="email" name="email"
                    value="{{ old('email', $employee['email']) }}" />
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="mobile">Mobile</label>
                <input class="form-control" id="mobile" placeholder="Enter Mobile Number" name="mobile" type="text"
                    value="{{ old('mobile', $employee['mobile']) }}" />
                @error('mobile')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" {{ (old('status', $employee['status'])==1) ? 'selected' : '' }}> Active
                    </option>
                    <option value="0" {{ (old('status', $employee['status'])==0) ? 'selected' : '' }}> Inactive
                    </option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="image">Image</label>
                <input class="form-control" id="image" name="image" type="file" value="" />
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="password">New Password</label>
                <input class="form-control" placeholder="Enter Password" name="password" id="new-password"
                    type="password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input class="form-control" placeholder="Enter Confirm Password" name="password_confirmation"
                    id="password_confirmation" type="password">
                @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                <button class="btn btn-primary submitbtn" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $("#ediUser").validate({
        rules: {
            designation_id: {
                required: true
            },
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
                required: false,
                minlength: 8,
                maxlength: 50
            },
            password_confirmation: {
                required: false,
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
            designation_id: {
                required: "Please Select Designation",
            },
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

</script>
@endsection