@extends('layouts.app')

@section('css')
<link href="{{ asset('assets/css/tom-select.default.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom-tomSelect.css') }}" rel="stylesheet" type="text/css" />
<style>
    .switch-inline.inner-label-toggle .input-checkbox {
        display: block;
        float: left;
        position: relative;
    }

    .switch-inline.inner-label-toggle .input-checkbox::before {
        content: "";
        position: absolute;
        height: 90%;
        width: 50%;
        background: #fff;
        top: 2px;
        z-index: 3;
        left: 2px;
        border-radius: 8px;
        transition: 0.5s;
        pointer-events: none;
    }

    .switch-inline.inner-label-toggle .input-checkbox span.label-left {
        z-index: 3;
        top: 28%;
        color: #000;
    }

    .switch-inline.inner-label-toggle .input-checkbox span.switch-chk-label {
        position: absolute;
        font-size: 17px;
        top: 10px;
        color: #000;
        pointer-events: none;
        border-radius: 8px !important;
        font-size: 14px;
        width: 50%;
        display: block;
        text-align: center;
    }

    .switch-inline.inner-label-toggle .switch-input {
        background-image: none;
    }

    .switch.inner-label-toggle .switch-input {
        min-width: 200px;
        height: 44px;
        border-radius: 8px !important;
        margin-left: 0;
    }

    .switch-inline.inner-label-toggle .input-checkbox span.label-right {
        right: 0;
        z-index: 3;
        top: 28%;
    }

    .switch-inline.inner-label-toggle.show .input-checkbox::before {
        left: 98px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0" id="table-example">Send Email :: Send Emails </h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('emails') }}" method="post" id="sendMessage" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="d-flex justify-content-center flex-column align-items-center">
                                <h4 class="text-primary fw-bold">Send To</h4>
                                <div
                                    class="switch switch-inline form-switch-primary form-switch-custom-big inner-label-toggle {{ old('sendType')=='on' ? 'show' : '' }}">
                                    <div class="input-checkbox">
                                        <span class="switch-chk-label label-left">All</span>
                                        <input class="switch-input" type="checkbox" role="switch" name="sendType"
                                            id="sendType" {{ old('sendType')=='on' ? 'checked' : '' }}>
                                        <span class="switch-chk-label label-right">With Excel</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2 selection">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="switch form-switch-custom">
                                        <input class="switch-input" type="checkbox" name="all_admins" role="switch"
                                            id="all_admins" {{ old('all_admins')=='on' ? 'checked' : '' }}>
                                        <label class="switch-label ms-3" for="all_admins">All Sub Admins</label>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="switch form-switch-custom">
                                        <input class="switch-input" type="checkbox" name="all_main_distributor"
                                            role="switch" id="all_main_distributor" {{ old('all_main_distributor')=='on'
                                            ? 'checked' : '' }}>
                                        <label class="switch-label ms-3" for="all_main_distributor">All Main
                                            Distributor</label>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="switch form-switch-custom">
                                        <input class="switch-input" type="checkbox" name="all_distributor" role="switch"
                                            id="all_distributor" {{ old('all_distributor')=='on' ? 'checked' : '' }}>
                                        <label class="switch-label ms-3" for="all_distributor">All Distributor</label>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="switch form-switch-custom">
                                        <input class="switch-input" type="checkbox" name="all_retailer" role="switch"
                                            id="all_retailer" {{ old('all_retailer')=='on' ? 'checked' : '' }}>
                                        <label class="switch-label ms-3" for="all_retailer">All Retailer</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2 excel" style="display: none">
                            <div class="row">
                                <div class="col-10">
                                    <label for="excel" class="form-label">Excel File with Name and Emails</label>
                                    <input type="file" class="form-control" name="excel" id="excel">
                                    <p class="text-secondary fs-small mb-0">
                                        Excel First Column will be User's Name and Second will be email address. (No
                                        Header
                                        required.)
                                    </p>
                                    @error('excel')
                                    <p class="invalid-feedback mb-0" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </p>
                                    @enderror
                                </div>
                                <div class="col-2">
                                    <br class="d-inline-block mb-2">
                                    <a href="{{ asset('assets/default/Send Email Template.xlsx') }}"
                                        class="btn btn-lg btn-success">
                                        <i class="fa fa-file-excel me-2"></i>
                                        Sample
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label for="title" class="form-label">Enter Title</label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ old('title') }}">
                            @error('title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="message" class="form-label">Enter Message</label>
                            <textarea class="form-control" type="text" name="message"
                                id="message">{{ old('message') }}</textarea>
                            @error('message')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="file" class="form-label">Select File</label>
                            <input class="form-control" type="file" name="file" id="file">
                            @error('file')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <button type="submit" class="btn btn-primary">
                                Send <i class="fa fa-send ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $('#sendType').on('change', function () {
            if ($(this).is(":checked")) {
                $('.excel').show()
                $('.selection').hide()
                $(this).closest('.inner-label-toggle').addClass('show')
            } else {
                $('.excel').hide()
                $('.selection').show()
                $(this).closest('.inner-label-toggle').removeClass('show')
            }
        })

        setTimeout(() => {
            if ("{{ old('sendType') }}" == 'on') {
                $('#sendType').change()
            }
        }, 200)

        $("#sendMessage").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                file: {
                    extension: "jpg|jpeg|png|pdf|docx|doc|xlsx|xls",
                    filesize: 2
                },
                title: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                message: {
                    required: true,
                    minlength: 2,
                    maxlength: 2000
                },
                excel: {
                    required: function (element) {
                        return $('#sendType').is(":checked");
                    },
                },
                all_admins: {
                    required: function (element) {
                        return $('#sendType').is(":checked") == false &&
                            $('#all_main_distributor').is(":checked") == false &&
                            $('#all_distributor').is(":checked") == false &&
                            $('#all_retailer').is(":checked") == false;
                    },
                },
            },
            messages: {
                title: {
                    required: 'Title is required field.',
                },
                message: {
                    required: 'Message is required field.',
                },
                file: {
                    extension: "Please select file with extention : jpg, jpeg, png, pdf, docx, doc, xlsx, xls.",
                },
                excel: {
                    required: "Please select excel file."
                },
                all_admins: {
                    required: "Please select atleast one of above."
                }
            },
            errorPlacement: function (error, element) {
                if ($(element).parent().hasClass('switch')) {
                    error.insertAfter($(element).parents('.switch'));
                } else {
                    error.insertAfter(element);
                }
            }
        });
    });
</script>
@endsection