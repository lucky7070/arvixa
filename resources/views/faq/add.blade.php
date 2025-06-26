@extends('layouts.app')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote-0.8.18-dist/summernote.min.css') }}">
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Faq :: Faq Add </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('faq')  }}" class="btn btn-outline-secondary me-4">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="add" method="POST" action="{{ route('faq.add') }}" enctype='multipart/form-data'>
            @csrf
            <div class="col-12 mt-2">
                <label class="form-label" for="question">Question <span class="required">*</span></label>
                <textarea placeholder="Add Your Question" name="question" id="question" cols="30" rows="3"
                    class="form-control">{{ old('question') }}</textarea>
                @error('question')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-12 mt-2">
                <label class="form-label" for="title">Answer <span class="required">*</span></label>

                <textarea placeholder="Add Your Answer" name="answer" id="answer" cols="30" rows="3"
                    class="form-control">{{ old('title') }}</textarea>

                @error('answer')
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
        ignore: ".ql-container *",
        rules: {
            question: {
                required: true,
                minlength: 2,
                maxlength: 500
            },
            answer: {
                required: true,
                minlength: 2,
                maxlength: 2000
            }
        },
        messages: {
            question: {
                required: "Please enter question",
            },
            answer: {
                required: "Please enter answer",
            }
        },
    });
</script>
@endsection