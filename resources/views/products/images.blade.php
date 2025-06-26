@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-auto align-self-center">
                        <h5 class="mb-0">Products :: Product Image -
                            <span class="text-primary">{{ $product['name'] }}</span>
                        </h5>
                    </div>
                    <div class="col-auto ms-auto">
                        <div class="nav nav-pills nav-pills-falcon">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('products') }}">
                                <i class="fa fa-arrow-left me-1"></i> Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="dropzone dropzone-single p-0">
                    <div class="fallback">
                        <input type="file" id="dropZone" class="d-none" name="file" multiple />
                    </div>
                    <label class="dz-message w-100 cursor-pointer" data-dz-message="data-dz-message" for="dropZone">
                        <div class="dz-message-text text-center">
                            <i class="fa-regular fa-upload fa-3x"></i>
                            <span>Drop your file here</span>
                            <p class="mb-0 text-primary">-- Or --</p>
                            <p class="mb-0 text-primary btn btn-outline-primary">Click Here</p>
                        </div>
                    </label>
                </div>

                <h5 class="text-primary mt-4">
                    You can change image order by clicking on image then drag.
                </h5>
                <div class="row filelist">
                    @if($product['images'])
                    @foreach($product['images'] as $image)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4" data-id="{{$image->id}}">
                        <div class="position-relative rounded-top">
                            <div class="d-block">
                                <img src="{{ $image['image'] }}" alt=""
                                    class="w-100 h-100 aspect-ratio-1 fit-cover rounded">
                            </div>
                            <span role="button" data-id="{{ $image['id'] }}"
                                class="badge rounded-pill bg-danger position-absolute z-index-2 top-0 end-0 delete">
                                <i class="fa fa-close"></i>
                            </span>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui-shortable.min.js') }}"></script>
<script type="text/javascript">
    $(function () {

        const uploadImage = (file) => {
            var reader = new FileReader();
            reader.onload = function (e) {

                if (!['image/gif', 'image/png', 'image/jpeg', 'image/jpg'].includes(file.type)) {
                    return toastr.error('Only Image files are allow.')
                }

                if (file.size > 2048000) {
                    return toastr.error('File size must be below 2mb');
                }

                var data = new FormData();
                data.append('image', file);
                jQuery.ajax({
                    url: "{{ route('products.images', $product['slug']) }}",
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data.message)
                            $('.filelist').append(`
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                                        <div class="position-relative rounded-top">
                                            <div class="d-block">
                                                <img src="${e.target.result}" alt=""
                                                    class="w-100 aspect-ratio-1 h-100 fit-cover rounded">
                                            </div>
                                            <span role="button" data-id="${data.data.id}"
                                                class="badge rounded-pill bg-danger position-absolute z-index-2 top-0 end-0 delete">
                                                <i class="fa fa-close"></i>
                                            </span>
                                        </div>
                                    </div>`);
                        }
                        else {
                            toastr.error(data.message)
                        }
                    }
                });
            }
            reader.readAsDataURL(file);
        }

        $(".filelist").sortable({
            cursor: "move",
            revert: true,
            opacity: 0.6,
            update: function () {
                var shortOrder = $(this).sortable('toArray', { attribute: 'data-id' });
                $.ajax({
                    url: "{{ request()->url() }}",
                    type: 'PUT',
                    data: { shortOrder },
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data.message)
                        } else {
                            toastr.error(data.message)
                        }
                    }
                });
            }
        });

        $('.dropzone').on('dragover', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $('.dz-message').addClass('active')
            $('.dz-message-text span').text("Release to Upload File")
        });

        $('.dropzone').on('dragleave', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $('.dz-message').removeClass('active')
            $('.dz-message-text span').text("Drag & Drop to Upload File")
        });

        $('.dropzone').on('drop', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let files = event.originalEvent.dataTransfer.files;
            Array.from(files).forEach(file => uploadImage(file));
        });


        $('#dropZone').on('change', function () {
            Array.from(this.files).forEach(file => uploadImage(file));
        });

        $(document).on('click', ".delete", function () {
            var id = $(this).data('id');
            var selector = this

            swal(deleteSweetAlertConfig).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('products.images', $product['slug']) }}",
                        data: { 'id': id },
                        type: 'DELETE',
                        success: function (data) {
                            if (data.status) {
                                swal(data?.message, { icon: "success" });
                                $(selector).parents().eq(1).remove()
                            } else {
                                toastr.error(data?.message);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection