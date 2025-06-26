@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/genealogy.css') }}">
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Users :: Users Tree </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('dashboard')  }}" class="btn btn-outline-secondary me-4">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="genealogy-body genealogy-scroll">
            <div class="genealogy-tree">
                <ul>
                    <li data-id="{{ $admin['id'] }}" data-type="admin"
                        data-details="{{ htmlspecialchars(json_encode($admin)) }}">
                        <a href="javascript:void(0);">
                            <div class="member-view-box" style="min-width: 140px;">
                                <div class="member-image position-relative">
                                    <!-- <span class="badge badge-primary eye">
                                        <i class="fa fa-eye"></i>
                                    </span> -->
                                    <img class="border border-4" src="{{ asset('storage/' . $admin['image']) }}"
                                        alt="Member">
                                </div>
                                <div class="member-details">
                                    <h3 class="text-white">{{ $admin['name'] }}</h3>
                                    <p class="text-white">( Admin )</p>
                                </div>
                            </div>
                        </a>
                        <ul class="active">
                            @foreach($main_distributors as $key => $main_distributor)
                            <li data-id="{{ $main_distributor['id'] }}" data-type="main_distributor"
                                data-details="{{ htmlspecialchars(json_encode($main_distributor)) }}">
                                <a href="javascript:void(0);">
                                    <div class="member-view-box">
                                        <div class="member-image position-relative">
                                            <!-- <span class="badge badge-primary eye">
                                                <i class="fa fa-eye"></i>
                                            </span> -->
                                            <img class="border border-4"
                                                src="{{ asset('storage/' . $main_distributor['image']) }}" alt="Member">
                                        </div>
                                        <div class="member-details">
                                            <h3 class="text-white">{{ $main_distributor['name'] }}</h3>
                                            <p class="text-white">(Main Distributor)</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

<div id="detailsModal" class="modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light-primary py-2">
                <h5 class="modal-title text-primary fw-bold">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Name :
                        <span class="name">14</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Balance :
                        <span class="balance">2</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Mobile No. :
                        <span class="mobile">1</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Email :
                        <span class="email">1</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<script src='https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.3/js/jquery.orgchart.min.js'></script>
<script>
    $(function () {
        $('.genealogy-tree ul').hide();
        $('.genealogy-tree>ul').show();
        $('.genealogy-tree ul.active').show();
        $(document).on('click', '.genealogy-tree li', function (e) {
            if ($(this).find('> ul').length === 0) {
                var selector = this;
                var vData = $(selector).data()
                $.post("{{ route('user-chart') }}", vData, function (data) {
                    if (data.html) {
                        $(selector).append(data.html);
                        $(selector).find('.member-image img').first().addClass('border-success');
                    }
                    else {
                        $(selector).find('.member-image img').first().addClass('border-danger');
                    }
                });
                e.stopPropagation();
            }
            else {
                var children = $(this).find('> ul');
                if (children.is(":visible")) {
                    children.hide('fast').removeClass('active');
                }
                else {
                    children.show('fast').addClass('active');
                }
                e.stopPropagation();
            }
        });

        $(document).on('click', '.eye', function (e) {
            var data = $(this).parents().eq(3).data('details');
            if (data) {
                data = JSON.parse(data);
                console.log(data);
            }

            // $('#detailsModal').modal('show')
            e.stopPropagation();
        })
    });
</script>
@endsection