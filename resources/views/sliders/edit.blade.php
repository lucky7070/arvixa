@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Sliders :: Slider Edit </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('sliders')  }}" class="btn btn-outline-secondary me-4">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="ediUser" method="POST" action="{{ route('sliders.edit', $slider['id']) }}"
            enctype='multipart/form-data'>
            @csrf

            <div class="col-lg-6 mb-3">
                <label class="form-label" for="image">Image</label>
                <input class="form-control" id="image" name="image" type="file" value="" />
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
                <div class="img-group mb-2">
                    <img class="" src="{{ asset('storage/' . $slider['image']) }}" alt="">
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="is_special" class="form-label">Is Special Banner</label>
                <select class="form-select" name="is_special" id="is_special">
                    <option value="0" {{ (old('is_special',$slider['is_special'])==0) ? 'selected' : '' }}>No</option>
                    <option value="1" {{ (old('is_special',$slider['is_special'])==1) ? 'selected' : '' }}>Yes</option>
                </select>
                @error('is_special')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mb-3">
                <label class="form-label" for="url">URL</label>
                <input class="form-control" id="url" placeholder="Url" name="url" type="text"
                    value="{{ old('url',$slider['url']) }}" />
                @error('url')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mb-3">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" {{ (old('status',$slider['status'])==1) ? 'selected' : '' }}> Active
                    </option>
                    <option value="0" {{ (old('status',$slider['status'])==0) ? 'selected' : '' }}> Inactive
                    </option>
                </select>
                @error('status')
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
            image: {
                extension: "jpg|jpeg|png",
                filesize: 2
            }
        },
        messages: {
            image: {
                extension: "Supported Format Only : jpg, jpeg, png"
            }
        },
    });
</script>
@endsection