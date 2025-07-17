@extends('layouts.app')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote-0.8.18-dist/summernote.min.css') }}">
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Services :: Service Add </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('services')  }}" class="btn btn-outline-secondary me-4">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="add" method="POST" action="{{ route('services.add') }}" enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-12 mt-2">
                <label class="form-label" for="name">Service Name <span class="required">*</span></label>
                <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                    value="{{ old('name') }}" />
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="purchase_rate">Purchase Rate <span class="required">*</span></label>
                <input class="form-control" id="purchase_rate" placeholder="Purchase Rate" name="purchase_rate"
                    type="number" step="0.01" value="{{ old('purchase_rate') }}" />
                <p class="form-text text-primary mb-1">
                    It is the cost, What we are charged.
                </p>
                @error('purchase_rate')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="sale_rate">Default Retailer Sale Rate <span
                        class="required">*</span></label>
                <input class="form-control" id="sale_rate" placeholder="Default Retailer Sale Rate" name="sale_rate"
                    type="number" step="0.01" value="{{ old('sale_rate') }}" />
                <p class="form-text text-primary mb-1">
                    It is the price, What we will charge from retailers.
                </p>
                @error('sale_rate')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="default_d_commission">Default Distributor Commission <span
                        class="required">*</span></label>
                <input class="form-control" id="default_d_commission" placeholder="Default Distributor Commission"
                    name="default_d_commission" type="number" step="0.01" value="{{ old('default_d_commission') }}" />
                <p class="form-text text-primary mb-1">
                    It is the price amount, What Distributor get, when their retailer uses this service.
                </p>
                @error('default_d_commission')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="default_md_commission">Default MainDistributor Commission <span
                        class="required">*</span></label>
                <input class="form-control" id="default_md_commission" placeholder="Default MainDistributor Commission"
                    name="default_md_commission" type="number" step="0.01" value="{{ old('default_md_commission') }}" />
                <p class="form-text text-primary mb-1">
                    It is the price amount, What Main Distributor get, when their retailer uses this service.
                </p>
                @error('default_md_commission')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="default_r_commission">Default Retailer Commission <span
                        class="required">*</span></label>
                <input class="form-control" id="default_r_commission" placeholder="Default Retailer Commission"
                    name="default_r_commission" type="number" step="0.01" value="{{ old('default_r_commission') }}" />
                <p class="form-text text-primary mb-1">
                    It is the price amount, What Retailer get, when they use this service.
                </p>
                @error('default_r_commission')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="default_assign">Default Assign to Retailer</label>
                <select name="default_assign" class="form-select" id="default_assign">
                    <option value="1" {{ old('default_assign')==1 ? 'selected' : '' }}> Yes </option>
                    <option value="0" {{ old('default_assign')==0 ? 'selected' : '' }}> No </option>
                </select>
                <p class="form-text text-primary mb-1">
                    If this option is yes, then this service is automatically assigned to retailers when they are
                    registered.
                </p>
                @error('default_assign')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="btn_text">Button text <span class="required">*</span></label>
                <input class="form-control" id="btn_text" placeholder="Enter Button text" name="btn_text" type="text"
                    value="{{ old('btn_text') }}" />
                @error('btn_text')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="is_feature">Is Feature</label>
                <select name="is_feature" class="form-select" id="is_feature">
                    <option value="1" {{ old('is_feature', 1)==1 ? 'selected' : '' }}> Yes </option>
                    <option value="0" {{ old('is_feature', 1)==0 ? 'selected' : '' }}> No </option>
                </select>
                @error('is_feature')
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

            <div class="col-lg-12 mt-2">
                <label class="form-label" for="notice">Notice</label>
                <textarea name="notice" id="notice" class="form-control">{{ old('notice') }}</textarea>
                @error('notice')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="image">Image </label>
                <input class="form-control" id="image" name="image" type="file" value="" />
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="banner">Banner </label>
                <input class="form-control" id="banner" name="banner" type="file" value="" />
                @error('banner')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-12 mt-2">
                <label class="form-label" for="description">Description </label>
                <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
                @error('description')
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
<script src="{{ asset('assets/plugins/summernote-0.8.18-dist/summernote.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#description').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview', 'help']],
            ]
        });

        let buttons = $('.note-editor button[data-toggle="dropdown"]');
        buttons.each((key, value) => {
            $(value).on('click', function(e) {
                $(this).attr('data-bs-toggle', 'dropdown')

            })
        })

        $("#add").validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                purchase_rate: {
                    required: true,
                    min: 0.01
                },
                sale_rate: {
                    required: true,
                    min: 0.01
                },
                default_d_commission: {
                    required: true,
                    min: 0.01
                },
                default_md_commission: {
                    required: true,
                    min: 0.01
                },
                default_r_commission: {
                    required: true,
                    min: 0.01
                },
                description: {
                    required: true,
                    minlength: 2,
                },
                image: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                },
                banner: {
                    extension: "jpg|jpeg|png",
                    filesize: 2
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                purchase_rate: {
                    required: "Please enter purchase rate",
                },
                sale_rate: {
                    required: "Please enter sale rate",
                },
                default_d_commission: {
                    required: "Please enter default Distributor commission",
                },
                default_md_commission: {
                    required: "Please enter default MainDistributor commission",
                },
                default_r_commission: {
                    required: "Please enter default Retailer commission",
                },
                description: {
                    required: "Please enter Description",
                },
                image: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                },
                banner: {
                    extension: "Supported Format Only : jpg, jpeg, png"
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                const data = new FormData(form);
                const objData = {};
                data.forEach((value, key) => (objData[key] = parseFloat(value)));
                var {
                    purchase_rate,
                    sale_rate,
                    default_d_commission,
                    default_md_commission
                } = objData
                if (sale_rate > (purchase_rate + default_d_commission + default_md_commission)) {
                    form.submit()
                } else {
                    toastr.error("Sale Rate can't be less then sum of 'Distributor commission', 'MainDistributor commission' and 'Purchase Rate'.");
                    return false;
                }
            }
        });
    })
</script>
@endsection