@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/light/forms/switches.css') }}">
<style>
    .table.colored thead tr th {
        border: 1px solid #ebedf2 !important;
        background: #eaeaec !important;
        padding: 10px 21px 10px 21px;
        vertical-align: middle;
        font-weight: 500;
    }
</style>
@endsection

@section('content')

@include('partial.common.user_detail')

<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Retailers :: Services</h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('retailers')  }}" class="btn btn-outline-secondary me-4"> <i
                            class="fa fa-arrow-left me-1"></i>
                        Go Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive scrollbar">
            <table class="table colored custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th scope="col" class="fw-bold"> Select</th>
                        <th scope="col" class="fw-bold">Service Name</th>
                        <th width="100px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" City="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="tabsModalLabel">Edit Commission & Sale Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-none">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3">
                        <div class="mb-2">
                            <label class="col-form-label" for="purchase_rate">Purchase Rate :</label>
                            <input class="form-control text-dark" name="purchase_rate" id="purchase_rate" type="number"
                                step="0.01" readonly />
                        </div>
                        <div class="mb-2">
                            <label class="col-form-label" for="sale_rate">Sale Rate for Retailer :</label>
                            <input class="form-control" name="sale_rate" id="sale_rate" type="number" step="0" />
                            <input class="form-control" name="id" id="id" type="hidden" />
                        </div>
                        <div class="mb-2">
                            <label class="col-form-label" for="main_distributor_commission">
                                Main Distributor Commission :
                                @if(!empty($user->main_distributor->name))
                                <span class="text-secondary fw-bold"> ({{ $user->main_distributor->name }})</span>
                                @else
                                <span class="text-danger fw-bold">(Not Available)</span>
                                @endif
                            </label>
                            <div class="input-group mb-3">
                                <input class="form-control" name="main_distributor_commission"
                                    id="main_distributor_commission" type="number" step="0.01" />
                                <span class="input-group-text fs--1" id="default_md_commission">
                                    Default Commission : <b class="mx-1"></b>
                                </span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="col-form-label" for="distributor_commission">
                                Distributor Commission :
                                @if(!empty($user->distributor->name))
                                <span class="text-secondary fw-bold">({{ $user->distributor->name }})</span>
                                @else
                                <span class="text-danger fw-bold">(Not Available)</span>
                                @endif
                            </label>
                            <div class="input-group mb-3">
                                <input class="form-control" name="distributor_commission" id="distributor_commission"
                                    type="number" step="0.01" />
                                <span class="input-group-text fs--1" id="default_d_commission">
                                    Default Commission : <b class="mx-1"></b>
                                </span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="col-form-label" for="retailer_commission">
                                Retailer Commission :
                                @if(!empty($user->retailer->name))
                                <span class="text-secondary fw-bold">({{ $user->retailer->name }})</span>
                                @else
                                <span class="text-danger fw-bold">(Not Available)</span>
                                @endif
                            </label>
                            <div class="input-group mb-3">
                                <input class="form-control" name="retailer_commission" id="retailer_commission"
                                    type="number" step="0.01" />
                                <span class="input-group-text fs--1" id="default_r_commission">
                                    Retailer Commission : <b class="mx-1"></b>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light-dark" type="reset" data-bs-dismiss="modal">Discard</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal2" tabindex="-1" City="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content position-relative">

            <div class="modal-header">
                <h5 class="modal-title" id="tabsModalLabel">Edit Commission & Sale Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-none">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <form id="editForm2">
                    <div class="table-responsive" id="commission_slots">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th class="bg-white" scope="col" rowspan="2">Start Amount</th>
                                    <th class="bg-white" scope="col" rowspan="2">End Amount</th>
                                    <th class="bg-white" colspan="4" class="text-center">Commission Precent</th>
                                </tr>
                                <tr>
                                    <th class="bg-white" scope="col">Main Distributor</th>
                                    <th class="bg-white" scope="col">Distributor </th>
                                    <th class="bg-white" scope="col">Retailer</th>
                                    <th class="bg-white" scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light-dark" type="reset" data-bs-dismiss="modal">Discard</button>
                        <input class="form-control" name="id" id="id" type="hidden" />
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(function() {
        var table = $('.table-datatable').DataTable({
            searching: false,
            paging: false,
            ordering: false,
            info: false,
            ajax: "{{ request()->url() }}",
            order: [
                [1, 'desc']
            ],
            columns: [{
                    data: 'check',
                    name: 'check',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $(document).on('change', '.switch-input', function() {
            var service_id = $(this).data('service-id');
            var selector = this;
            $.ajax({
                url: "{{ route('retailers.services', [ 'slug' => $user['slug'] ]) }}",
                data: {
                    service_id
                },
                type: 'POST',
                success: function(data) {
                    if (data.status == true) {
                        table.draw();
                        toastr.success(data.message);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        })

        $(document).on('click', ".edit", function() {
            var data = $(this).data('all')
            if (data.openSlots) {
                if (data?.commission_slots && Array.isArray(data.commission_slots) && data.commission_slots.length > 0) {
                    reinit(data.commission_slots, data.services_log_id);
                }
            } else {
                $('[name="id"]').val(data.services_log_id)
                document.forms['editForm']['purchase_rate'].value = data.purchase_rate;
                document.forms['editForm']['sale_rate'].value = data.sale_rate_unique;
                document.forms['editForm']['main_distributor_commission'].value = data.main_distributor_commission;
                document.forms['editForm']['distributor_commission'].value = data.distributor_commission;
                document.forms['editForm']['retailer_commission'].value = data.retailer_commission;

                $('#default_md_commission b').text(data.default_md_commission)
                $('#default_d_commission b').text(data.default_d_commission)
                $('#default_r_commission b').text(data.default_r_commission)
                $('#editModal').modal('show');
            }
        })

        $("#editForm").validate({
            debug: false,
            errorClass: "text-danger fs--1",
            errorElement: "span",
            rules: {
                sale_rate: {
                    required: true,
                    min: 0.01
                },
                main_distributor_commission: {
                    required: true,
                    min: 0.00
                },
                distributor_commission: {
                    required: true,
                    min: 0.00
                },
            },
            messages: {
                sale_rate: {
                    required: "Please enter sale rate",
                },
                default_d_commission: {
                    required: "Please enter default Distributor commission",
                },
                default_md_commission: {
                    required: "Please enter default MainDistributor commission",
                },
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $("#overlay").show();
                const formDataObj = {};
                formData.forEach((value, key) => (formDataObj[key] = parseFloat(value)));
                var {
                    sale_rate,
                    purchase_rate,
                    main_distributor_commission,
                    distributor_commission
                } = formDataObj
                if (sale_rate > (purchase_rate + main_distributor_commission + distributor_commission)) {
                    $.ajax({
                        url: "{{ route('retailers.commission.services') }}",
                        data: formDataObj,
                        type: 'PUT',
                        success: function(data) {
                            if (data.status) {
                                toastr.success(data?.message);
                                $('#editModal').modal('hide');
                                table.draw();
                                $("#overlay").hide();
                            } else {
                                toastr.error(data?.message);
                                $("#overlay").hide();
                            }
                        }
                    });
                } else {
                    toastr.error("Sale Rate can't be less then sum of 'Distributor commission', 'MainDistributor commission' and 'Purchase Rate'.")
                }
            }
        });

        $(document).on('input', '#editForm2 tbody .commission_main_distributor, #editForm2 tbody .commission_distributor, #editForm2 tbody .commission', function(e) {
            const parent = $(this).parents().eq(1);
            const commission_main_distributor = parseFloat(parent.find('.commission_main_distributor').val() || 0);
            const commission_distributor = parseFloat(parent.find('.commission_distributor').val() || 0);
            const commission = parseFloat(parent.find('.commission').val() || 0);
            parent.find('.total_commission').val(commission_main_distributor + commission_distributor + commission)
        });

        $(document).on('input', '#editForm tbody .commission_main_distributor', function() {
            console.log($(this));

        })

        function reinit(commission_slots, services_log_id) {
            let html = '';
            commission_slots.forEach((row, i) => {
                html += `<tr>
                    <td>
                        <input type="number" name="commission_slots[${i}][start]" class="form-control text-dark" value="${row.start}" placeholder="From" min="1" required="" readonly="">
                    </td>
                    <td>
                        <input type="number" name="commission_slots[${i}][end]" class="form-control text-dark" value="${row.end}" placeholder="To" min="1" required="" readonly="">
                    </td>
                    <td>
                        <input type="number" name="commission_slots[${i}][commission_main_distributor]" class="form-control commission_main_distributor" placeholder="Commission" value="${row.commission_main_distributor}" step="0.01" min="0" max="100" required="">
                    </td>
                    <td>
                        <input type="number" name="commission_slots[${i}][commission_distributor]" class="form-control commission_distributor" placeholder="Commission" value="${row.commission_distributor}" step="0.01" min="0" max="100" required="">
                    </td>
                    <td>
                        <input type="number" name="commission_slots[${i}][commission]" class="form-control commission" placeholder="Commission" value="${row.commission}" step="0.01" min="0" max="100" required="">
                    </td>
                    <td>
                        <input type="number" name="commission_slots[${i}][total_commission]" class="form-control text-dark total_commission" placeholder="Commission" value="${row.total_commission}" step="0.01" min="0" max="100" required="" readonly="">
                    </td>
                </tr>`
            });

            $('[name="id"]').val(services_log_id)
            $('#commission_slots tbody').html(html);

            $("#editForm2").validate({
                ignore: [],
                errorClass: "text-danger fs--3",
                errorElement: "small",
                errorPlacement: function(error, element) {
                    if (element.parent().hasClass('input-group')) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    formData.append('_method', 'PUT')
                    $("#overlay").show();
                    $.ajax({
                        url: "{{ route('retailers.commission.services') }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data) {
                            if (data.status) {
                                toastr.success(data?.message);
                                $('#editModal2').modal('hide');
                                table.draw();
                                $("#overlay").hide();
                            } else {
                                toastr.error(data?.message);
                                $("#overlay").hide();
                                console.log(data?.data);
                            }
                        }
                    });
                }
            });

            $('#commission_slots tbody tr').each(function(index) {
                const rules = {
                    required: true,
                    min: 0,
                    max: 100,
                    step: 0.01
                }

                $('[name="commission_slots[' + index + '][commission_main_distributor]"]').rules('add', rules);
                $('[name="commission_slots[' + index + '][commission_distributor]"]').rules('add', rules);
                $('[name="commission_slots[' + index + '][commission]"]').rules('add', rules);
            });

            $('#editModal2').modal('show');
        }
    });
</script>
@endsection