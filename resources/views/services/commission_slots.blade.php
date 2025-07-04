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
                                        class="form-control commission_main_distributor" placeholder="Commission" value="{{ $value['commission_main_distributor'] ?? 0 }}" step="0.01" min="0" max="100" required>
                                </td>
                                <td>
                                    <input type="number" name="commission_slots[{{ $key }}][commission_distributor]"
                                        class="form-control commission_distributor" placeholder="Commission" value="{{ $value['commission_distributor'] ?? 0 }}" step="0.01" min="0" max="100" required>
                                </td>
                                <td>
                                    <input type="number" name="commission_slots[{{ $key }}][commission]"
                                        class="form-control commission" placeholder="Commission" value="{{ $value['commission'] ?? 0 }}" step="0.01" min="0" max="100" required>
                                </td>
                                <td>
                                    <input type="number" name="commission_slots[{{ $key }}][total_commission]"
                                        class="form-control text-dark total_commission" placeholder="Commission" value="{{ ($value['commission_main_distributor'] ?? 0) + ($value['commission_distributor'] ?? 0 ) + ($value['commission'] ?? 0) }}" step="0.01" min="0" max="100" required readonly>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                <button class="btn btn-primary submitbtn" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
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
@endsection