@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Testimonials :: Testimonial Edit </h5>
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
        <form class="row" id="ediUser" method="POST" action="{{ route('testimonials.edit', $testimonial['id']) }}"
            enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" placeholder="Name" name="name" type="text"
                    value="{{ old('name', $testimonial['name']) }}" />
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="designation">Designation</label>
                <input class="form-control" id="designation" placeholder="Designation" name="designation" type="text"
                    value="{{ old('designation', $testimonial['designation']) }}" />
                @error('designation')
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

                <div class="img-group mb-2">
                    <img class="" src="{{ asset('storage/' . $testimonial['image']) }}" alt="">
                </div>
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" {{ (old('status',$testimonial['status'])==1) ? 'selected' : '' }}> Active
                    </option>
                    <option value="0" {{ (old('status',$testimonial['status'])==0) ? 'selected' : '' }}> Inactive
                    </option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>


            <div class="col-lg-12 mt-2">
                <label class="form-label" for="description">Description </label>
                <textarea class="form-control" id="description"
                    name="description">{{ $testimonial['description'] }}</textarea>
                @error('description')
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
                required: "Please enter Name",
            },
            designation: {
                required: "Please enter Designation",
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