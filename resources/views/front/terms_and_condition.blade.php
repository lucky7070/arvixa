@extends('front.layouts.main')

@section('main_content')
<div class="container-fluid bg-light-theme text-center py-4 text-theme">
    <h2 class="mb-1 fw-bolder">Testimonial</h2>
    <p class="mb-1">
        <a href="" class="text-decoration-none text-theme">Home</a> / Testimonial
    </p>
</div>

<div class="container my-5">
    <div class="card">
        <div class="card-body">
            <!-- <h2 class="text-center border-bottom border-5 pb-3 fw-bold text-theme">⬽ {{ $data->title }} ⤘</h2> -->
            <div class="w-100">
                {!! $data->description !!}
            </div>
        </div>
    </div>
</div>
@endsection