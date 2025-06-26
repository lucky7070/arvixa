@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0">Customer :: Documents -
                            <span class="text-primary">{{ $customer['name'] }}</span>
                        </h5>
                    </div>
                    <div class="col-auto ms-auto">
                        <div class="nav nav-pills nav-pills-falcon">
                            <a class="btn btn-outline-secondary me-4 add" href="{{ route('customers') }}">
                                <i class="fa fa-arrow-left me-1"></i> Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="document" action="{{ request()->url() }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="doc_type" class="form-label">Document Type</label>
                                    <select class="form-select" name="doc_type" id="doc_type">
                                        <option value="" selected>Select one</option>
                                        @foreach(config('constant.documents_type_list', []) as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('doc_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="doc_number" class="form-label">Document Number</label>
                                    <input type="text" class="form-control" name="doc_number" id="doc_number"
                                        placeholder="Document Number">
                                    @error('doc_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label for="doc_img_front" class="form-label">Front Side</label>
                                    <input type="file" class="form-control" name="doc_img_front" id="doc_img_front">
                                    @error('doc_img_front')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label for="doc_img_back" class="form-label">Back Side</label>
                                    <input type="file" class="form-control" name="doc_img_back" id="doc_img_back">
                                    @error('doc_img_back')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label for="submit" class="form-label d-block"><br /></label>
                                    <input type="submit" class="btn btn-primary w-100" id="submit" value="Submit" />
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-12 mt-4">
                        <h5 class="my-2 text-primary fw-bold">Uploaded Documents</h5>
                        <hr class="my-2">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Document Type</th>
                                        <th scope="col">Document Number</th>
                                        <th class="text-center" scope="col">Image</th>
                                        <th class="text-center" scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->documents as $document)
                                    <tr>
                                        <td><b>{{ getDocumentType($document->doc_type) }}</b></td>
                                        <td><b>{{ $document->doc_number }}</b></td>
                                        <td class="text-center">
                                            @if(!empty($document->doc_img_front))
                                            <a href="{{ asset('storage/'.$document->doc_img_front) }}" target="_blank"
                                                class="btn btn-outline-secondary btn-icon btn-sm mx-1">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            @endif
                                            @if(!empty($document->doc_img_back))
                                            <a href="{{ asset('storage/'.$document->doc_img_back) }}" target="_blank"
                                                class="btn btn-outline-primary btn-icon btn-sm mx-1">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button data-info="{{ json_encode($document) }}"
                                                class="btn btn-outline-warning btn-icon btn-sm edit">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="{{ request()->url().'?delete='.$document->id }}"
                                                class="btn btn-outline-danger btn-icon btn-sm delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if(count($customer->documents) == 0)
                                    <tr>
                                        <td colspan="4" class="text-center text-danger my-2 fw-bold">
                                            Not Document Found
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $("#document").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                doc_type: {
                    required: true
                },
                doc_number: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                doc_img_front: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2
                },
                doc_img_back: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 2
                }
            },
            messages: {
                doc_type: {
                    required: "Please Select Document Type.",
                },
                doc_number: {
                    required: "Please enter Document Number.",
                },
                doc_img_front: {
                    extension: "Supported Format Only : jpg, jpeg, png, pdf"

                },
                doc_img_back: {
                    extension: "Supported Format Only : jpg, jpeg, png, pdf"
                }
            },
        });


        $(document).on('click', ".delete", function (e) {
            e.preventDefault()
            swal({
                title: "Are you Sure..!!",
                text: "What do you want to do.?",
                buttons: {
                    cancel: "Cancel",
                    defeat: "Yes..!!",
                },
            }).then((value) => {
                if (value) {
                    window.location.href = $(this).attr('href');
                }
            });
        });

        $('.edit').on('click', function () {
            var data = $(this).data('info');
            $('#doc_type').val(data?.doc_type).prop('disabled', true)
            $('#doc_number').val(data?.doc_number).focus()
        })
    });
</script>
@endsection