@extends('layouts.app')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote-0.8.18-dist/summernote.min.css') }}">
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Services :: Service Edit </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('services')  }}" class="btn btn-outline-secondary me-4">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="ediUser" method="POST" action="{{ route('services.edit', $service['id']) }}"
            enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-12 mt-2">
                <label class="form-label" for="name">Service Name</label>
                <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                    value="{{ old('name', $service['name']) }}" />
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="default_assign">Default Assign to Retailer</label>
                <select name="default_assign" class="form-select" id="default_assign">
                    <option value="1" {{ (old('default_assign',$service['default_assign'])==1) ? 'selected' : '' }}> Yes
                    </option>
                    <option value="0" {{ (old('default_assign',$service['default_assign'])==0) ? 'selected' : '' }}> No
                    </option>
                </select>
                @error('default_assign')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="btn_text">Button text <span class="required">*</span></label>
                <input class="form-control" id="btn_text" placeholder="Enter Button text" name="btn_text" type="text"
                    value="{{ old('btn_text', $service['btn_text']) }}" />
                @error('btn_text')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="is_feature">Is Feature</label>
                <select name="is_feature" class="form-select" id="is_feature">
                    <option value="1" {{ old('is_feature', $service['is_feature'])==1 ? 'selected' : '' }}> Yes
                    </option>
                    <option value="0" {{ old('is_feature', $service['is_feature'])==0 ? 'selected' : '' }}> No </option>
                </select>
                @error('is_feature')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" {{ (old('status',$service['status'])==1) ? 'selected' : '' }}> Active
                    </option>
                    <option value="0" {{ (old('status',$service['status'])==0) ? 'selected' : '' }}> Inactive
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
                <div class="img-group mb-2">
                    <img class="" src="{{ asset('storage/' . $service['image']) }}" alt="">
                </div>
                <input class="form-control" id="image" name="image" type="file" value="" />
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="banner">Banner</label>
                <div class="img-group-show mb-2">
                    <img class="" src="{{ asset('storage/' . $service['banner']) }}" alt="">
                </div>
                <input class="form-control" id="banner" name="banner" type="file" value="" />
                @error('banner')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-12 mt-2">
                <label class="form-label" for="description">Description </label>
                <textarea class="form-control" id="description"
                    name="description">{{ old('description', $service['description']) }}</textarea>
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
<script src="{{ asset('assets/plugins/summernote-0.8.18-dist/summernote.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#description').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview', 'help']],
            ]
        });

        let buttons = $('.note-editor button[data-toggle="dropdown"]');
        buttons.each((key, value) => {
            $(value).on('click', function(e) {
                $(this).attr('data-bs-toggle', 'dropdown')

            })
        })

        $("#ediUser").validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                description: {
                    required: true,
                    minlength: 2,
                },
                image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                banner: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                description: {
                    required: "Please enter Description",
                },
                image: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                },
                banner: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                }
            },
        });
    })
</script>
@endsection