@extends('front.layouts.main')

@section('main_content')

<div class="container-fluid bg-light-theme text-center py-4 text-theme">
    <h2 class="mb-1 fw-bolder">Testimonial</h2>
    <p class="mb-1">
        <a href="" class="text-decoration-none text-theme">Home</a> / Testimonial
    </p>
</div>

<div class="container-xl my-5">
    <div class="card">
        <div class="card-body testimonial-page">
            <!-- <h2 class="text-center border-bottom border-5 pb-3 fw-bold text-theme">⬽ Testimonial ⤘</h2> -->
            <div class="row">
                @if($testimonials->count())
                @foreach($testimonials as $key => $row)
                <div class="col-lg-4 col-md-6 my-3">
                    <div class="testimonial-card">
                        <div class="d-flex border-bottom border-2 align-items-center gap-2 mb-2 pb-2">
                            <img src="{{ asset('storage/'.$row->image) }}" alt="">
                            <div class="d-flex flex-column align-items-start justify-content-center">
                                <h6 class="mb-0 fw-bold text-theme">{{ $row->name }}</h6>
                                <p class="fs-small text-theme-secondary mb-0">{{ $row->designation }}</p>
                            </div>
                        </div>
                        <p class="fs-7 text-justify text-muted mb-0">{{ Str::limit($row->description, 450) }}</p>
                    </div>
                </div>
                @endforeach
                <div class="col-md-12">
                    {{ $testimonials->links() }}
                </div>
                @else
                <div class="col-md-12 mb-3">
                    <p class="mb-0 text-danger text-center">No Testimonials Found.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection