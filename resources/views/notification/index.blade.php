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
                        <h5 class="mb-0" id="table-example">Send Notification :: Send Notifications </h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div style="min-height: 300px;">
                    <form action="{{ route('notification') }}" method="post" id="sendMessage"
                        enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="d-flex justify-content-center flex-column align-items-center">
                                    <h4>Send To</h4>
                                    @csrf
                                    <div
                                        class="switch switch-inline form-switch-primary form-switch-custom-big inner-label-toggle">
                                        <div class="input-checkbox">
                                            <span class="switch-chk-label label-left">All</span>
                                            <input class="switch-input" type="checkbox" role="switch" name="sendType"
                                                id="sendType">
                                            <span class="switch-chk-label label-right">Selected</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2 retailers" style="display: none;">
                                <label for="send_to" class="form-label">Select Ratailers</label>
                                <select multiple class="form-select" name="send_to[]" id="send_to"></select>
                                @error('send_to')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="title" class="form-label">Enter Title</label>
                                <input class="form-control" type="text" name="title" id="title">
                                @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="message" class="form-label">Enter Message</label>
                                <input class="form-control" type="text" name="message" id="message">
                                @error('message')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="image" class="form-label">Select Image</label>
                                <input class="form-control" type="file" name="image" id="image">
                                @error('image')
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
</div>

@endsection

@section('js')
<script src="{{ asset('assets/js/tom-select.base.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $('#sendType').on('change', function () {
            if ($(this).is(":checked")) {
                $('.retailers').show()
                $(this).closest('.inner-label-toggle').addClass('show')
            } else {
                $('.retailers').hide()
                $(this).closest('.inner-label-toggle').removeClass('show')
            }
        })
        const tomAdd = new TomSelect("#send_to", {
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            placeholder: "Type Retailer Name or Mobile Here...",
            // fetch remote data
            load: function (query, callback) {
                $.get("{{ route('get_user_list_filter') }}", { user_type: 4, filter: query }, function (data) {
                    callback(data);
                })
            },
        });
        $("#sendMessage").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                image: {
                    extension: "jpg|jpeg|png",
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
                    maxlength: 200
                },
                "send_to[]": {
                    required: function (element) {
                        return $('#sendType').is(":checked");
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
                image: {
                    extension: "Please select image file.",
                },
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