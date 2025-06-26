@extends('layouts.retailer_app')

@section('css')
<!-- <link rel="stylesheet" href="{{ asset('assets/css/light/components/accordions.css') }}"> -->
<style>
    .check-success span.icon {
        background-color: #00ab55;
    }

    .check-error span.icon {
        background-color: #e7515a;
    }

    .check-success span.icon:not(.first):not(.second):not(.third):not(.forth)::before {
        content: "\f00c";
    }

    .check-error span.icon:not(.first):not(.second):not(.third):not(.forth)::before {
        content: "\f00d";
    }

    .custom-check .first::before {
        content: "1";
    }

    .custom-check .second::before {
        content: "2";
    }

    .custom-check .third::before {
        content: "3";
    }

    .custom-check .forth::before {
        content: "4";
    }

    .accordion .card-header section>div[aria-expanded="true"] .icons i {
        transform: rotate(180deg);
    }

    .accordion-icons .accordion-icon-custom {
        margin-right: 10px;
        width: 40px;
        min-width: 40px;
        height: 40px;
        background-color: #e3e8dab2;
        display: flex !important;
        justify-content: center;
        align-items: center;
        border-radius: 50px;
        border: 1px solid gray;
    }

    .accordion .card {
        border: 1px solid #d3d3d3;
        border-radius: 6px;
        margin-bottom: 4px;
        background: #fff;
    }

    .accordion .card-header {
        background-color: transparent;
        color: #f8538d;
        border-radius: 0;
        padding: 0;
        position: relative;
        border-bottom: none;
    }

    .accordion .card-header section>div {
        padding: 13px 19px;
        cursor: pointer;
        display: block;
        font-size: 18px;
        letter-spacing: 1px;
    }

    .accordion .card-header section>div .icons {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        padding: 9px;
    }

    .accordion .card-header section>div.collapsed,
    .accordion .card-header section>div.collapsed * {
        color: #f8538d !important;
    }

    .accordion .card-header section>div:not(.collapsed) {
        color: #805dca;
        border-bottom: 1px solid #757575;
        font-weight: 600;
        background-color: #cfcfcf71;
    }

    .accordion .card-header section>div:not(.collapsed) * {
        color: #805dca;
    }

    small.text-muted {
        display: block;
    }

    .form-group label {
        margin-bottom: 0;
    }

    .form-control:disabled:not(.flatpickr-input),
    .form-control[readonly]:not(.flatpickr-input) {
        color: #3d3939;
    }

    .widget {
        color: #fff;
        background-image: linear-gradient(315deg, rgba(30, 154, 254, 0.9215686275) 0%, rgba(61, 56, 225, 0.8705882353) 74%);
    }
</style>
@endsection

@section('content')
<div class="card">

    <div class="card-body">
        <div class="row">
            <!-- <div class="col-12">
                <div class="alert alert-icon-left alert-arrow-left alert-light-primary alert-dismissible fade show"
                    role="alert">
                    <strong>Note : </strong>
                    E-filings for AY 2023-24 will be live soon. In the meanwhile, you can add your details and keep your
                    tax-returns ready to be submitted.
                    <i class="fa-regular fa-bell"></i>
                </div>
            </div> -->

            <div class="col-12 mb-3">
                <div class="row">
                    <div class="col-lg-auto col-sm-12 mb-2">
                        <a href="{{ route('itr-list') }}" class="btn btn-icon btn-outline-secondary">
                            <i class="fa fa-list"></i>
                        </a>
                    </div>
                    <div class="col-auto mb-2">
                        <div class=" d-flex justify-content-center align-items-center gap-2">
                            <a href="{{ route('file-itr', ['step' => 'personal-info', 'slug' => $itr->slug ]) }}" class="custom-check btn btn-rounded {{ $itr->is_step_1_complete == 1 ?
                                'btn-outline-success check-success' : 'btn-outline-danger check-error' }}">
                                <span class="{{ $itr->is_step_1_complete == 1 ? 'icon' : 'icon first' }}"></span>
                                Personal Info
                            </a>
                            <i class="fa fa-arrow-right fs-6 d-none d-md-block"></i>
                        </div>
                    </div>
                    <div class="col-auto mb-2">
                        <div class=" d-flex justify-content-center align-items-center gap-2">
                            <a href="{{ route('file-itr', ['step' => 'income-sources', 'slug' => $itr->slug ]) }}"
                                class="{{ $itr->slug == null ? 'disabled' : '' }} custom-check btn btn-rounded {{ $itr->is_step_2_complete == 1 ? 'btn-outline-success check-success' : 'btn-outline-danger check-error' }}"
                                @disabled($itr->slug == null)>
                                <span class="{{ $itr->is_step_2_complete == 1 ? 'icon' : 'icon second' }}"></span>
                                Income Sources
                            </a>
                            <i class="fa fa-arrow-right fs-6 d-none d-md-block"></i>
                        </div>
                    </div>
                    <div class="col-auto mb-2">
                        <div class=" d-flex justify-content-center align-items-center gap-2">
                            <a href="{{ route('file-itr', ['step' => 'tax-saving', 'slug' => $itr->slug ]) }}"
                                class="{{ ($itr->slug == null || $itr->is_step_2_complete == 0) ? 'disabled' : '' }} custom-check btn btn-rounded  {{ $itr->is_step_3_complete == 1 ? 'btn-outline-success check-success' : 'btn-outline-danger check-error' }}"
                                @disabled($itr->slug == null || $itr->is_step_2_complete == 0)>
                                <span class="{{ $itr->is_step_3_complete == 1 ? 'icon' : 'icon third' }}"></span>
                                Tax Saving
                            </a>
                            <i class="fa fa-arrow-right fs-6 d-none d-md-block"></i>
                        </div>
                    </div>
                    <div class="col-auto mb-2">
                        <div class=" d-flex justify-content-center align-items-center gap-2">
                            <a href="{{ route('file-itr', ['step' => 'tax-summary', 'slug' => $itr->slug ]) }}"
                                class="{{ ($itr->slug == null || $itr->is_step_3_complete == 0) ? 'disabled' : '' }} custom-check btn btn-rounded  {{ $itr->is_step_4_complete == 1 ? 'btn-outline-success check-success' : 'btn-outline-danger check-error' }}"
                                @disabled($itr->slug == null || $itr->is_step_3_complete == 0)>
                                <span class="{{ $itr->is_step_4_complete == 1 ? 'icon' : 'icon forth' }}"></span>
                                Tax Summary
                            </a>
                        </div>
                    </div>

                    @if(config('ayt.nsdl.itr_service_remote_use', false) && $itr->is_step_4_complete == 1)
                    <div class="col-auto mb-2">
                        <a href="{{ route('itr-files.sync', $itr->slug ) }}" class="btn btn-outline-primary me-1">
                            <i class="fa fa-refresh me-1"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-12">
                @yield('sub_section')
            </div>
        </div>
    </div>
</div>

@endsection