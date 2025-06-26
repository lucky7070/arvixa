@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/choices-js/choices.min.css') }}">
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0">Products :: Product Edit </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('products')  }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="editProduct" method="POST" action="{{ route('products.edit', $product['slug']) }}"
            enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-12 mb-2">
                <label for="category_id">Category</label>
                <select class="form-select" id="category_id" name="category_id"
                    placeholder="Please Enter Category Name...">
                    <option value="">Select Category</option>
                </select>
                @error('category_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-4 mt-2">
                <label class="form-label" for="brand_id">Brand Name<span class="required">*</span></label>
                <select class="form-select js-choice" name="brand_id" id="brand_id" required="required">
                    <option value="">Select Brand</option>
                    @foreach ($brands as $brand)
                    <option value="{{ $brand['id'] }}" {{ old('brand_id', $product->brand_id)==$brand['id'] ? 'selected'
                        : '' }}>
                        {{ $brand['name'] }}
                    </option>
                    @endforeach
                </select>
                @error('brand_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-8 mt-2">
                <label class="form-label" for="name">Name <span class="required">*</span></label>
                <input class="form-control" id="name" placeholder="Enter Name" name="name" type="text"
                    value="{{ old('name', $product->name) }}" />
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-4 mt-2">
                <label class="form-label" for="sort_description">Sort Description <span
                        class="required">*</span></label>
                <textarea class="form-control" id="sort_description" placeholder="Enter Sort Description"
                    name="sort_description">{{ old('sort_description', $product->sort_description) }}</textarea>
                @error('sort_description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-8 mt-2">
                <label class="form-label" for="description">Description <span class="required">*</span></label>
                <textarea class="form-control" id="description" placeholder="Enter Description"
                    name="description">{{ old('description', $product->description) }}</textarea>
                @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-12 mt-2">
                <!-- Space -->
            </div>
            <div class="col-md-4 mt-2">
                <label class="form-label" for="weight">Weight (in Grams)</label>
                <input class="form-control" type="number" id="weight" name="weight" placeholder="Weight"
                    value="{{ old('brand_name', $product->weight) }}" />
                @error('weight')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-8 mt-2">
                <label class="form-label" for="mrp">Length * Width * Height (in Cms)</label>
                <div class="input-group">
                    <input class="form-control" type="number" name="length" id="length"
                        value="{{ old('length', $product->length) }}" placeholder="Length" />
                    <input class="form-control" type="number" name="width" id="width"
                        value="{{ old('width', $product->width) }}" placeholder="Width" />
                    <input class="form-control" type="number" name="height" id="height"
                        value="{{ old('height', $product->height) }}" placeholder="Height" />
                </div>
                @error('length')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-md-4 mt-2">
                <label class="form-label" for="hsn_code_id">HSN Code</label>
                <select name="hsn_code_id" class="form-select js-choice" id="hsn_code_id" required="required">
                    <option value="">Select HSN Code</option>
                    @foreach ($hsn_codes as $hsn_code)
                    <option value="{{ $hsn_code['id'] }}" {{ old('hsn_code_id',
                        $product['hsn_code_id'])==$hsn_code['id'] ? 'selected' : '' }}>
                        {{ $hsn_code['code'] }}
                    </option>
                    @endforeach
                </select>
                @error('hsn_code_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-md-4 mt-2">
                <label class="form-label" for="mrp">Max Retail Price <span class="required">*</span></label>
                <input class="form-control" id="mrp" placeholder="Enter MRP" name="mrp" type="number"
                    value="{{ old('mrp', $product->mrp) }}" step="0.01" />
                @error('mrp')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-md-4 mt-2">
                <label class="form-label" for="price">Sale Price <span class="required">*</span></label>
                <input class="form-control" id="price" placeholder="Enter Sale Price" name="price" type="number"
                    value="{{ old('price', $product->price) }}" step="0.01" />
                @error('price')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-md-4 mt-2">
                <label class="form-label" for="minimum">Min & Max Order Qty</label>
                <div class="input-group">
                    <input type="number" class="form-control" placeholder="Minimum" id="minimum"
                        value="{{ old('minimum', $product->minimum) }}" name="minimum">
                    <input type="number" class="form-control" placeholder="Maximim" id="maximum"
                        value="{{ old('maximum', $product->maximum) }}" name="maximum">
                </div>
                @error('minimum')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-md-4 mt-2">
                <label class="form-label" for="is_feature">Is Feature</label>
                <select name="is_feature" class="form-select" id="is_feature">
                    <option value="0" {{ old('is_feature', $product->is_feature)==0 ? 'selected' : '' }}> No </option>
                    <option value="1" {{ old('is_feature', $product->is_feature)==1 ? 'selected' : '' }}> Yes </option>
                </select>
                @error('is_feature')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-md-4 mt-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="0" {{ old('status', $product->status)==0 ? 'selected' : '' }}> Inactive </option>
                    <option value="1" {{ old('status', $product->status)==1 ? 'selected' : '' }}> Active </option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                <button class="btn btn-primary submitbtn" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('assets/plugins/choices-js/choices.min.js') }}"></script>
<script type="text/javascript">

    var category_choices = new Choices('#category_id', {
        "removeItemButton": true,
        "placeholder": true
    });

    var elements = document.querySelectorAll('.js-choice');
    elements.forEach(function (item) {
        var choices = new window.Choices(item, {
            "removeItemButton": true,
            "placeholder": true
        });
        return choices;
    });

    setTimeout(() => {
        var selected = "{{ old('category_id', $product->category_id) }}";
        $.get("{{ route('categories.list')  }}", function (data) {
            if (data.status) {
                category_choices.setChoices(data.data, 'id', 'name', true);
                category_choices.removeActiveItems().setChoiceByValue(parseInt(selected))
            }
        });
    }, 200)

    $("#editProduct").validate({
        ignore: [],
        rules: {
            category_id: {
                required: true,
            },
            name: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            sort_description: {
                required: true,
                minlength: 2,
                maxlength: 500
            },
            description: {
                required: true,
                minlength: 2,
                maxlength: 2000
            },
            brand_id: {
                required: true,
            },
            stock: {
                required: true,
            },
            price: {
                required: true,
            },
            mrp: {
                required: true,
            },
            weight: {
                required: true
            },
            length: {
                required: true
            },
            width: {
                required: true
            },
            height: {
                required: true
            },
            hsn_code_id: {
                required: true,
            },
            minimum: {
                required: true,
            },
            maximum: {
                required: true,
            },
        },
        messages: {
            category_id: {
                required: "Please select category.",
            },
            model: {
                required: "Please enter model",
            },
            name: {
                required: "Please enter name",
            },
            meta_title: {
                required: "Please enter Meta Title",
            },
            meta_keyword: {
                required: "Please enter Meta Keyword",
            },
            meta_description: {
                required: "Please enter Meta Description",
            },
            sort_description: {
                required: "Please enter Sort Description",
            },
            description: {
                required: "Please enter Description",
            },
            brand_id: {
                required: "Please select Brand",
            },
            stock: {
                required: "Please enter Stock",
            },
            price: {
                required: "Please enter Sale Price",
            },
            mrp: {
                required: "Please enter Max Retail Price.",
            },
            weight: {
                required: "Please enter weight.",
            },
            length: {
                required: "Please enter length.",
            },
            width: {
                required: "Please enter width.",
            },
            height: {
                required: "Please enter height.",
            },
            hsn_code_id: {
                required: "Please select HSN Code.",
            },
            minimum: {
                required: "Please enter min order qty.",
            },
            maximum: {
                required: "Please enter max order qty.",
            },
        },
    });
</script>
@endsection