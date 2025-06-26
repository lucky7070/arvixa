@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">MSME Certificate Details</h5>
            <div>
                @if(config('ayt.nsdl.msme_service_remote_use', false))
                <a href="{{ route('reports.msme-certificate.sync',$data->txn_id ) }}" class="btn btn-primary me-1">
                    <i class="fa fa-refresh me-1"></i> Sync
                </a>
                @endif
                <a href="{{ route('reports.msme-certificate') }}" class="btn btn-secondary me-1">
                    <i class="fa fa-arrow-left me-1"></i> Go Back
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-3">
                <ul class="list-group">
                    <li class="list-group-item active">
                        <h6 class="mb-0 text-white fw-bold">Certificate Details</h6>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Transaction ID</span>
                        <span class="">{{ $data->txn_id }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Date Submitted</span>
                        <span class="">{{ $data->created_at->format('d F, Y h:i A') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Status</span>
                        @switch($data->status)
                        @case(0)
                        <small class="badge fw-semi-bold rounded-pill status badge-light-secondary"> Pending</small>
                        @break
                        @case(1)
                        <small class="badge fw-semi-bold rounded-pill status badge-light-success"> Submitted</small>
                        @break
                        @case(2)
                        <small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Rejected</small>
                        @break
                        @endswitch
                    </li>

                    @if($data->certificate)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Certificate File </span>
                        <a class="text-primary" href="{{ asset('storage/'.$data->certificate) }}" target="_blank">
                            Download
                        </a>
                    </li>
                    @endif

                    @if($data->comment)
                    <li class="list-group-item">
                        <span>Comments</span>
                        <p class="">{{ $data->comment }}</p>
                    </li>
                    @endif

                    @if($data->error_message)
                    <li class="list-group-item">
                        <span>Comments</span>
                        <p class="">{{ $data->error_message }}</p>
                    </li>
                    @endif
                </ul>
            </div>
            <div class="col-md-6 mb-3">
                <ul class="list-group">
                    <li class="list-group-item active">
                        <h6 class="mb-0 text-white fw-bold">Personal Details</h6>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Name</span>
                        <span class="">{{ $data->name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Email </span>
                        <span class="">{{ $data->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Mobile </span>
                        <span class="">{{ $data->phone }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Aadhaar Card </span>
                        <span class="">{{ $data->aadharcard }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Aadhaar File </span>
                        <a class="text-primary" href="{{ asset('storage/'.$data->aadhar_file) }}" target="_blank">
                            Download
                        </a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>PanCard Number </span>
                        <span class="">{{ $data->pancard }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>PanCard File </span>
                        <a class="text-primary" href="{{  asset('storage/'.$data->pancard_file) }}" target="_blank">
                            Download
                        </a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Social Category </span>
                        <span class="">{{ config('constant.social_category_list.'.$data->category, '') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Gender </span>
                        <span class="">{{ config('constant.gender_list.'.$data->gender) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Special Abled </span>
                        <span class="">{{ yesNo($data->special_abled) }}</span>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 mb-3">
                <ul class="list-group">
                    <li class="list-group-item active">
                        <h6 class="mb-0 text-white fw-bold">Enterprise Details</h6>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Enterprise Name </span>
                        <span class="">{{ $data->name_enterprise }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Plant Name </span>
                        <span class="">{{ $data->name_plant }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Flat </span>
                        <span class="">{{ $data->flat_plant }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Building </span>
                        <span class="max-w-60 text-end">{{ $data->building_plant }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Block /Street </span>
                        <span class="">{{ $data->block_plant }}, {{ $data->street_plant }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Village </span>
                        <span class="">{{ $data->village_plant }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>City</span>
                        <span class="">{{ $data->city }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>State</span>
                        <span class="">{{ $data->state }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Country</span>
                        <span class="">{{ $data->country }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>PinCode</span>
                        <span class="">{{ $data->pincode }}</span>
                    </li>
                </ul>
            </div>

            <div class="col-md-6">
                <ul class="list-group mb-3">
                    <li class="list-group-item active">
                        <h6 class="mb-0 text-white fw-bold">Bank Details</h6>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Bank Name </span>
                        <span class="">{{ $data->bank_name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>IFSC Code </span>
                        <span class="">{{ $data->bank_ifsc }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Account Number </span>
                        <span class="">{{ $data->bank_account }}</span>
                    </li>
                </ul>

                <ul class="list-group">
                    <li class="list-group-item  d-flex justify-content-between align-items-center active py-1">
                        <h6 class="mb-0 text-white fw-bold">
                            Employee Details
                        </h6>
                        <span class="badge bg-white text-primary">{{ $data->emp_total }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Male Employee </span>
                        <span class="">{{ $data->emp_male }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Female Employee </span>
                        <span class="">{{ $data->emp_female }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Other Employee </span>
                        <span class="">{{ $data->emp_other }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Total Investment </span>
                        <span class="">₹ {{ $data->inv_wdv_a }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Total Turnover </span>
                        <span class="">₹ {{ $data->turnover_a }}</span>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 mb-3">
                <ul class="list-group mb-2">
                    <li class="list-group-item active">
                        <h6 class="mb-0 text-white fw-bold">Office Address</h6>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Registration Date </span>
                        <span class="">{{ $data->enterprise_registration->format('d F, Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Unit Start Date </span>
                        <span class="">
                            {{ $data->enterprise_date ? $data->enterprise_date->format('d F, Y') : null }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Unit Type </span>
                        <span class="">{{ $data->unit_type }}</span>
                    </li>
                    <li class="list-group-item">
                        <span>Unit NIC Description </span>
                        <p class="text-justify small">{{ $data->nic_description }}</p>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 mb-3"></div>

            @if(userCan(130, 'can_edit') && $data->status == 0 && !config('ayt.nsdl.msme_service_remote_use',
            false))
            <div class="col-md-12 mb-3">
                <form id="updateStatus" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="mb-2">
                                <label for="status" class="form-label">Update Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="1">Submitted</option>
                                    <option value="2">Rejected</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-lg" id="submit" type="submit">Update</button>
                        </div>

                        <div class="col-md-6 mb-2 certificate">
                            <label for="certificate" class="form-label">Certificate File</label>
                            <input type="hidden" name="id" value="{{ $data->id }}">
                            <input type="file" class="form-control" name="certificate" id="certificate" name="3" />
                        </div>

                        <div class="col-md-6 mb-2 reject_message" style="display: none;">
                            <label for="reject_message" class="form-label">Reject Reason</label>
                            <textarea class="form-control" name="reject_message" id="reject_message"
                                rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script type="text/javascript">

    $(function () {
        $('#status').on('change', function () {
            if ($(this).val() == 1) {
                $('.certificate').show();
                $('.reject_message').hide();
            } else {
                $('.certificate').hide();
                $('.reject_message').show();
            }
        });

        $("#updateStatus").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                status: {
                    required: true,
                },
                certificate: {
                    required: () => {
                        return $('#status').val() == 1;
                    },
                    extension: "pdf",
                },
                reject_message: {
                    required: () => {
                        return $('#status').val() == 2;
                    },
                    minlength: 2,
                    maxlength: 100
                },
            },
            messages: {
                status: {
                    required: "Please select status.",
                },
                certificate: {
                    required: "Please select certificate file.",
                    extension: "Supported Format Only : pdf"
                },
                reject_message: {
                    required: "Please select reject reason.",
                },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                $('#overlay').show();
                $.ajax({
                    url: "{{ route('reports.msme-certificate') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (data) {
                        if (data.status) {
                            toastr.success(data.message);
                            $(form).trigger("reset")
                            location.reload();
                            $('#overlay').hide();
                        } else {
                            $(form).validate().showErrors(data.data);
                            toastr.error(data.message);
                            $("#overlay").hide();
                        }
                    }
                });
            }
        });



    });
</script>
@endsection