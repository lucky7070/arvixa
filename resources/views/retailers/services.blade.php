@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/light/forms/switches.css') }}">
<style>
    .table thead tr th {
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
            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
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
                            <input class="form-control" name="sale_rate" id="sale_rate" type="number" step="0.01" />
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
@endsection

@section('js')
<script type="text/javascript">
    $(function () {
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

        $(document).on('change', '.switch-input', function () {
            var service_id = $(this).data('service-id');
            var selector = this;
            $.ajax({
                url: "{{ route('retailers.services', [ 'slug' => $user['slug'] ]) }}",
                data: { service_id },
                type: 'POST',
                success: function (data) {
                    if (data.status == true) {
                        table.draw();
                        toastr.success(data.message);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        })

        $(document).on('click', ".edit", function () {
            var data = $(this).data('all')
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
            submitHandler: function (form) {
                var formData = new FormData(form);
                $("#overlay").show();
                const formDataObj = {};
                formData.forEach((value, key) => (formDataObj[key] = parseFloat(value)));
                var { sale_rate, purchase_rate, main_distributor_commission, distributor_commission } = formDataObj
                if (sale_rate > (purchase_rate + main_distributor_commission + distributor_commission)) {
                    $.ajax({
                        url: "{{ route('retailers.commission.services') }}",
                        data: formDataObj,
                        type: 'PUT',
                        success: function (data) {
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
                }
                else {
                    toastr.error("Sale Rate can't be less then sum of 'Distributor commission', 'MainDistributor commission' and 'Purchase Rate'.")
                }
            }
        });
    });

</script>
@endsection