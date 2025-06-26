@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Testimonial :: Testimonial Add </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('testimonials')  }}" class="btn btn-outline-secondary me-4">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="add" method="POST" action="{{ route('testimonials.add') }}" enctype='multipart/form-data'>
            @csrf

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="name">Name <span class="required">*</span></label>
                <input class="form-control" id="name" placeholder="Name" name="name" type="text"
                    value="{{ old('name') }}" />
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="designation">Designation</label>
                <input class="form-control" id="designation" placeholder="Designation" name="designation" type="text"
                    value="{{ old('designation') }}" />
                @error('designation')
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


            <div class="col-lg-12 mt-2">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
                @error('description')
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
            name: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            designation: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            description: {
                required: true,
                minlength: 2,
                maxlength: 1000
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
            designation: {
                required: "Please enter designation",
            },
            description: {
                required: "Please enter Description",
            },
            image: {
                extension: "Supported Format Only : jpg, jpeg, png"
            }
        },
    });

</script>
@endsection