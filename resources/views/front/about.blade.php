@extends('front.layouts.main')

@section('main_content')
<div class="container-fluid bg-light-theme text-center py-4 text-theme">
    <h2 class="mb-1 fw-bolder">{{ $data->title }}</h2>
    <p class="mb-1">
        <a href="" class="text-decoration-none text-theme">Home</a> / {{ $data->title }}
    </p>
</div>
<div class="container my-5">
    <div class="card">
        <div class="card-body">
            <div class="w-100">
                {!! $data->description !!}
            </div>
        </div>
    </div>
</div>
@endsection