@extends('layouts.retailer_app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create FSSAI Certificate</h5>
            <a href="{{ route('pan-card') }}" class="btn btn-primary">
                <i class="fa fa-arrow-left me-1"></i>
                Go Back
            </a>
        </div>
    </div>
    <div class="card-body">


        <form action="{{ request()->url() }}" id="add" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">

                <div class="col-md-12 mb-2">
                    <div class="form-check ps-4">
                        <input class="form-check-input" type="checkbox" name="declaration" id="declaration">
                        <label class="form-check-label mb-0" for="declaration">
                            <p>I hereby deciare that information given above are true to the best of my knowledge.
                                for any information, that may be required to be verified, proof/evidence shall be
                                produced immediately before the concerned authority.</p>
                            <p>
                                मैं एतद्द्वारा घोषणा करता हूं कि ऊपर दी गई जानकारी मेरी सर्वोत्तम जानकारी के अनुसार सत्य
                                है। किसी भी जानकारी के लिए, जिसे सत्यापित करने की आवश्यकता हो सकती है, संबंधित
                                प्राधिकारी के समक्ष तुरंत सबूत/साक्ष्य प्रस्तुत किया जाएगा।
                            </p>
                        </label>
                    </div>

                    <div class="col-lg-12 mt-3 d-flex justify-content-start">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>

    function getCity(state_id, selector, city_id = null) {
        $.ajax({
            type: "POST",
            url: "{{ route('cities.list') }}",
            data: { state_id, city_id },
            success: function (data) {
                $(selector).html(data);
                return true;
            },
            error: function (jqXHR, exception) {
                $(selector).html('<option value="">Select City</option>');
                return false;
            }
        });
    }

    $(function () {

        $("#add").validate({
            ignore: [],
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50
                },
                email: {
                    required: true,
                    email: true,
                    customEmail: true,
                    minlength: 2,
                    maxlength: 50
                },
                phone: {
                    required: true,
                    digits: true,
                    exactlength: 10,
                    indiaMobile: true
                },
                aadharcard: {
                    required: true,
                    digits: true,
                    exactlength: 12,
                    aadharcard: true,
                },
                aadhar_file: {
                    required: true,
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 1
                },
                pancard_has: {
                    required: true,
                },
                pancard_type: {
                    required: true,
                    digits: true
                },
                pancard: {
                    required: function () {
                        return $('input[name="pancard_has"]:checked').val() == 1;
                    },
                    pancard: true,
                    minlength: 10,
                    maxlength: 10
                },
                pancard_file: {
                    extension: "jpg|jpeg|png|pdf",
                    filesize: 1
                },
                category: {
                    required: true,
                    digits: true
                },
                gender: {
                    required: true,
                    digits: true
                },
                special_abled: {
                    required: true,
                    digits: true
                },
                name_enterprise: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                name_plant: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                flat_plant: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                building_plant: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                block_plant: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                street_plant: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                village_plant: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                city: {
                    required: true,
                    digits: true
                },
                state: {
                    required: true,
                    digits: true
                },
                pincode: {
                    required: true,
                    digits: true,
                    exactlength: 6
                },
                official_flat: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                official_building: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                official_block: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                official_street: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                official_village: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                official_city: {
                    required: true,
                    digits: true
                },
                official_state: {
                    required: true,
                    digits: true
                },
                official_pincode: {
                    required: true,
                    digits: true,
                    exactlength: 6
                },
                uam_register: {
                    required: true,
                },
                uam_number: {
                    required: function () {
                        return $('input[name="uam_register"]:checked').val() == 'EM - II' || $('input[name="uam_register"]:checked').val() == 'Previous UAM';
                    },
                    minlength: 2,
                    maxlength: 100
                },
                enterprise_registration: {
                    required: true,
                    date: true
                },
                enterprise_production: {
                    required: true,
                },
                enterprise_date: {
                    required: function () {
                        return $('input[name="enterprise_production"]:checked').val() == 1;
                    },
                    date: true
                },
                bank_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                bank_ifsc: {
                    required: true,
                    ifsc: true,
                    minlength: 2,
                    maxlength: 100,
                },
                bank_account: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                unit_type: {
                    required: true,
                },
                nic_description: {
                    required: true,
                    minlength: 20,
                    maxlength: 500
                },
                emp_male: {
                    required: true,
                    digits: true
                },
                emp_female: {
                    required: true,
                    digits: true
                },
                emp_other: {
                    required: true,
                    digits: true
                },
                emp_total: {
                    required: true,
                    digits: true
                },
                inv_wdv_a: {
                    required: true,
                },
                inv_wdv_b: {
                    required: true,
                },
                inv_wdv_total: {
                    required: true,
                    min: 0
                },
                turnover_a: {
                    required: true,
                },
                turnover_b: {
                    required: true,
                    min: function () {
                        return $('#turnover_a').val();
                    }
                },
                turnover_total: {
                    required: true,
                    min: 0
                },
                get_register: {
                    required: true,
                },
                treds_rgister: {
                    required: true,
                },
                district_center: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                declaration: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Please enter name.",
                },
                email: {
                    required: "Please enter email.",
                },
                phone: {
                    required: "Please enter phone.",
                },
                aadharcard: {
                    required: "Please enter AadharCard Number.",
                },
                aadhar_file: {
                    required: "Please select aadhar card file.",
                },
                pancard_has: {
                    required: "Are you have pancard.?",
                },
                pancard_type: {
                    required: "Please select pancard type",
                },
                pancard: {
                    required: "Please enter pancard number",
                },
                category: {
                    required: "Please select Category.",
                },
                gender: {
                    required: "Please select gender.",
                },
                special_abled: {
                    required: "Are you special abled.?",
                },
                name_enterprise: {
                    required: "Please enter enterprise name.",
                },
                name_plant: {
                    required: "Please enter plant name.",
                },
                flat_plant: {
                    required: "Please enter flat.",
                },
                building_plant: {
                    required: "Please enter building.",
                },
                block_plant: {
                    required: "Please enter block.",
                },
                street_plant: {
                    required: "Please enter street name.",
                },
                village_plant: {
                    required: "Please enter village name.",
                },
                city: {
                    required: "Please select city.",
                },
                state: {
                    required: "Please select state.",
                },
                pincode: {
                    required: "Please enter pincode.",
                },
                official_flat: {
                    required: "Please enter flat / house number.",
                },
                official_building: {
                    required: "Please enter building name.",
                },
                official_block: {
                    required: "Please enter block.",
                },
                official_street: {
                    required: "Please enter street name.",
                },
                official_village: {
                    required: "Please enter village name.",
                },
                official_city: {
                    required: "Please select city.",
                },
                official_state: {
                    required: "Please select state.",
                },
                official_pincode: {
                    required: "Please enter pincode.",
                },
                uam_register: {
                    required: "Please select, are you registered with UAM.?",
                },
                uam_number: {
                    required: "Please enter UAM number.",
                },
                enterprise_registration: {
                    required: "Please select enterprise registration date.",
                },
                enterprise_production: {
                    required: "Is enterprise/business Start.?",
                },
                enterprise_date: {
                    required: "Please select enterprise start date.",
                },
                bank_name: {
                    required: "Please enter bank name.",
                },
                bank_ifsc: {
                    required: "Please enter IFSC code.",
                },
                bank_account: {
                    required: "Please enter bank account number.",
                },
                unit_type: {
                    required: "Please select unit type.",
                },
                nic_description: {
                    required: "Please select unit type description.",
                },
                emp_male: {
                    required: "Please enter male employee count.",
                },
                emp_female: {
                    required: "Please enter female employee count.",
                },
                emp_other: {
                    required: "Please enter other employee count.",
                },
                emp_total: {
                    required: "Please enter total employee count.",
                },
                inv_wdv_a: {
                    required: "Please enter amount in rupees.",
                },
                inv_wdv_b: {
                    required: "Please enter amount in rupees.",
                },
                inv_wdv_total: {
                    required: "Please enter amount in rupees.",
                },
                turnover_a: {
                    required: "Please enter amount in rupees.",
                },
                turnover_b: {
                    required: "Please enter amount in rupees.",
                },
                turnover_total: {
                    required: "Please enter amount in rupees.",
                },
                district_center: {
                    required: "Please enter District Industries Centre.",
                },
                declaration: {
                    required: "You must confirm Declaration.",
                }
            },
            errorPlacement: function (error, element) {
                if ($(element).hasClass('form-check-input')) {
                    error.insertAfter($(element).parent());
                } else if ($(element).parent().hasClass('custom-control') && $(element).parents().eq(1).hasClass('form-control')) {
                    error.insertAfter($(element).parents().eq(1));
                } else if ($(element).parent().hasClass('input-group')) {
                    error.insertAfter($(element).parent());
                } else if ($(element).parents().eq(1).hasClass('custom-check-group')) {
                    error.insertAfter($(element).parents().eq(1));
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $('[name="pancard_has"]').on('change', function () {
            if ($(this).val() == 1) {
                $('.panBlock').fadeIn()
            } else {
                $('.panBlock').fadeOut()
            }
        });

        $('[name="uam_register"]').on('change', function () {
            if ($(this).val() == 'EM-II' || $(this).val() == 'Previous UAM') {
                $('.uamBlock').fadeIn()
            } else {
                $('.uamBlock').fadeOut()
            }
        });

        $('[name="enterprise_production"]').on('change', function () {
            if ($(this).val() == 1) {
                $('.startDate').fadeIn()
            } else {
                $('.startDate').fadeOut()
            }
        });

        $('#turnover_a, #turnover_b').on('input', function () {
            var value = $('#turnover_a').val() - $('#turnover_b').val();
            $('#turnover_total').val(value)
        });

        $('#inv_wdv_a, #inv_wdv_b').on('input', function () {
            var value = $('#inv_wdv_a').val() - $('#inv_wdv_b').val();
            $('#inv_wdv_total').val(value)
        });

        $('#sameAsAbove').on('change', function () {
            if ($(this).prop('checked')) {
                $('#official_flat').val($('#flat_plant').val());
                $('#official_building').val($('#building_plant').val());
                $('#official_block').val($('#block_plant').val());
                $('#official_street').val($('#street_plant').val());
                $('#official_village').val($('#village_plant').val());
                $('#official_state').val($('#state').val());
                $('#official_country').val($('#country').val());
                $('#official_pincode').val($('#pincode').val());
                getCity($('#state').val(), '#official_city', $('#city').val());
            }
        })

        $('#pancard, #bank_ifsc').on('input', function () {
            this.value = this.value.toString().toUpperCase().replaceAll(' ', '');
        });

        $('#emp_male, #emp_female, #emp_other').on('input', function () {
            var value = parseInt($('#emp_male').val()) + parseInt($('#emp_female').val()) + parseInt($('#emp_other').val());
            $('#emp_total').val(value)
        });

    });
</script>

@endsection