@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Service Commission Slots - <span class="text-primary">{{ $service->name }}</span> </h5>
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

        <form class="row" id="edit" method="POST" action="{{ route('services.commission_slots', $service['slug']) }}">
            @csrf
            @if (in_array($service->id, config('constant.commission-slab-services', [])))
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th scope="col" rowspan="2">Start Amount</th>
                                <th scope="col" rowspan="2">End Amount</th>
                                <th colspan="4" class="text-center">Commission Precent</th>
                            </tr>
                            <tr>
                                <th scope="col">Main Distributor</th>
                                <th scope="col">Distributor </th>
                                <th scope="col">Retailer</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($service['commission_slots'] ?? config('constant.bill-slab', [])) as $key => $value )
                            <tr>
                                <td>
                                    <input type="number" name="commission_slots[{{ $key }}][start]" class="form-control text-dark" value="{{ $value['start'] }}" placeholder="From" min="1" required readonly>
                                </td>
                                <td>
                                    <input type="number" name="commission_slots[{{ $key }}][end]" class="form-control text-dark" value="{{ $value['end'] }}" placeholder="To" min="1" required readonly>
                                </td>
                                <td>
                                    <input type="number" name="commission_slots[{{ $key }}][commission_main_distributor]"
                                        class="form-control commission_main_distributor" placeholder="Commission" value="{{ $value['commission_main_distributor'] ?? 0 }}" step="0.001" min="0" max="100" required>
                                </td>
                                <td>
                                    <input type="number" name="commission_slots[{{ $key }}][commission_distributor]"
                                        class="form-control commission_distributor" placeholder="Commission" value="{{ $value['commission_distributor'] ?? 0 }}" step="0.001" min="0" max="100" required>
                                </td>
                                <td>
                                    <input type="number" name="commission_slots[{{ $key }}][commission]"
                                        class="form-control commission" placeholder="Commission" value="{{ $value['commission'] ?? 0 }}" step="0.001" min="0" max="100" required>
                                </td>
                                <td>
                                    <input type="number" name="commission_slots[{{ $key }}][total_commission]"
                                        class="form-control text-dark total_commission" placeholder="Commission" value="{{ ($value['commission_main_distributor'] ?? 0) + ($value['commission_distributor'] ?? 0 ) + ($value['commission'] ?? 0) }}" step="0.001" min="0" max="100" required readonly>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="col-lg-6 mt-2 @if(config('constant.service_ids.gas_payment') === $service->id) d-none @endif">
                <label class="form-label" for="purchase_rate">Purchase Rate <span class="required">*</span></label>
                <input class="form-control" id="purchase_rate" placeholder="Purchase Rate" name="purchase_rate"
                    type="number" step="0.01" value="{{ old('purchase_rate', $service['purchase_rate']) }}" />
                @error('purchase_rate')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2 @if(config('constant.service_ids.gas_payment') === $service->id) d-none @endif">
                <label class="form-label" for="sale_rate">Default Retailer Sale Rate <span
                        class="required">*</span></label>
                <input class="form-control" id="sale_rate" placeholder="Default Retailer Sale Rate" name="sale_rate"
                    type="number" step="0.01" value="{{ old('sale_rate', $service['sale_rate']) }}" />
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
                    name="default_d_commission" type="number" step="0.001"
                    value="{{ old('default_d_commission', $service['default_d_commission']) }}" />
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
                    name="default_md_commission" type="number" step="0.001"
                    value="{{ old('default_md_commission', $service['default_md_commission']) }}" />
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
                    name="default_r_commission" type="number" step="0.001"
                    value="{{ old('default_r_commission', $service['default_r_commission']) }}" />
                @error('default_r_commission')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            @endif
            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                <button class="btn btn-primary submitbtn" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
@if (in_array($service->id, config('constant.commission-slab-services', [])))
<script>
    $(document).ready(function() {
        $("#edit").validate({
            ignore: [],
            errorPlacement: function(error, element) {
                if (element.parent().hasClass('input-group')) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }

            },
        });

        $('#edit tbody .commission_main_distributor, #edit tbody .commission_distributor, #edit tbody .commission').on('input', function(e) {
            const parent = $(this).parents().eq(1);
            const commission_main_distributor = parseFloat(parent.find('.commission_main_distributor').val() || 0);
            const commission_distributor = parseFloat(parent.find('.commission_distributor').val() || 0);
            const commission = parseFloat(parent.find('.commission').val() || 0);
            parent.find('.total_commission').val(commission_main_distributor + commission_distributor + commission)
        });
    });
</script>
@else
<script>
    $(document).ready(function() {
        $("#edit").validate({
            ignore: [],
            rules: {
                purchase_rate: {
                    required: true,
                    min: 0
                },
                sale_rate: {
                    required: true,
                    min: 0.01
                },
                default_d_commission: {
                    required: true,
                    min: 0,
                    step: 0.0001
                },
                default_md_commission: {
                    required: true,
                    min: 0,
                    step: 0.0001
                },
                default_r_commission: {
                    required: true,
                    min: 0,
                    step: 0.0001
                },
            },
            messages: {
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
                if (sale_rate >= (purchase_rate + default_d_commission + default_md_commission)) {
                    form.submit()
                } else {
                    toastr.error("Sale Rate can't be less then sum of 'Distributor commission', 'MainDistributor commission', 'Retailer Commission' and 'Purchase Rate'.");
                    return false;
                }
            }
        });
    });
</script>
@endif
@endsection