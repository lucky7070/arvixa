@extends('layouts.employee_app')

@section('css')
<link href="{{ asset('assets/css/tom-select.default.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom-tomSelect.css') }}" rel="stylesheet" type="text/css" />
<style>
    .my-timeline ul,
    ul li {
        list-style: none;
        padding: 0;
    }

    .my-timeline {
        background: #eaf6ff;
        padding: 0.5rem 2rem;
        border-radius: 15px;
    }

    .sessions {
        margin-top: 2rem;
        border-radius: 12px;
        position: relative;
    }

    .sessions p {
        color: #4f4f4f;
        line-height: 1.5;
        margin-top: 0.4rem;
    }

    .my-timeline li {
        padding-bottom: 1rem;
        border-left: 1px solid #abaaed;
        position: relative;
        padding-left: 20px;
        margin-left: 10px;
    }

    .my-timeline li:last-child {
        border: 0px;
        padding-bottom: 0;
    }

    .my-timeline li:before {
        content: '';
        width: 15px;
        height: 15px;
        background: white;
        border: 1px solid #4e5ed3;
        box-shadow: 3px 3px 0px #bab5f8;
        box-shadow: 3px 3px 0px #bab5f8;
        border-radius: 50%;
        position: absolute;
        left: -10px;
        top: 0px;
    }

    @media (max-width: 767.98px) {
        .my-timeline {
            padding: 0.5rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Retailers :: Notes</h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('employee.retailers')  }}" class="btn btn-outline-secondary me-4"> <i
                            class="fa fa-arrow-left me-1"></i>
                        Go Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-3">
                <form action="{{ request()->url() }}" method="post" id="addComment">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="date" class="form-label">Date</label>
                                <input class="form-control" type="date" name="date" id="date"
                                    value="{{ old('date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                                @error('date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="retailer_id" class="form-label">Retailer (Search)</label>
                                <select id="retailer_id" name="retailer_id" placeholder="Select a Retailer..."
                                    autocomplete="off" required>
                                    @if(!empty($user->id))
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->mobile }})</option>
                                    @else
                                    <option value="">Select Retailer</option>
                                    @foreach($retailers as $retailer)
                                    <option value="{{ $retailer->id }}" {{ old('retailer_id', @$user->id) ==
                                        $retailer->id ? 'selected' : '' }}>{{ $retailer->name }}
                                        ({{ $retailer->mobile }})
                                    </option>
                                    @endforeach
                                    @endif
                                </select>

                                @error('retailer_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <textarea class="form-control" placeholder="Enter Your Comments Here.." name="message"
                                    id="message" rows="2" required>{{ old('message') }}</textarea>
                                @error('message')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="submit" name="submit" class="btn btn-primary" value="Save Comment">
                        </div>
                    </div>
                </form>
                <hr class="mt-3 mb-0">
            </div>
            @if(count($notes))
            <div class="col-md-12 mb-3">
                <div class="my-timeline">
                    <ul class="sessions">
                        @foreach($notes as $note)
                        <li>
                            <div class="d-flex justify-content-between">
                                <a class="text-secondary fw-bold"
                                    href="{{ route('employee.retailers.notes', $note->retailer->slug ) }}">
                                    {{ $note->retailer->name }}
                                    ({{ $note->retailer->mobile }})
                                </a>
                                <span class="text-danger small">{{ $note->date->format('d M, Y') }}</span>
                            </div>
                            <p>{{ $note->message }}</p>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                {{ $notes->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/tom-select.base.js') }}"></script>
<script type="text/javascript">
    $(function () {

        new TomSelect("#retailer_id");
        $("#addComment").validate({
            ignore: [],
            rules: {
                date: {
                    required: true,
                },
                retailer_id: {
                    required: true,
                },
                message: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                }
            },
            messages: {
                date: {
                    required: "Please select Date.",
                },
                retailer_id: {
                    required: "Please select Retailer.",
                },
                message: {
                    required: "Please Enter Message.",
                }
            },
            errorPlacement: function (error, element) {
                if ($(element).hasClass('tomselected')) {
                    $(element).parent().append(error)
                } else {
                    error.insertAfter(element);
                }
            }
        });
    });
</script>
@endsection