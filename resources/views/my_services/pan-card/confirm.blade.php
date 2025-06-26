@extends('layouts.retailer_app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Check Details </h5>
            <a href="{{ route('pan-card') }}" class="btn btn-primary">
                <i class="fa fa-arrow-left me-1"></i>
                Go Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ $submit_url }}" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Name :
                            <span class="">{{ $pan_card['name'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Middle Name :
                            <span class="">{{ $pan_card['middle_name'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Last Name :
                            <span class="">{{ $pan_card['last_name'] }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Email :
                            <span class="">{{ $pan_card['email'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Phone :
                            <span class="">{{ $pan_card['phone'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Gender :
                            <span class="">{{ $pan_card['gender'] }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-12 mt-3 d-flex justify-content-start">
                    <input type="hidden" name="req" value="{{ json_encode($requestData) }}" />
                    <button class="btn btn-primary" type="submit">Confirm</button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection