@php
    use Illuminate\Support\Facades\DB;

    $providers = DB::table('rproviders')->get();
@endphp


@extends('layouts.retailer_app')

@section('content')

<style>
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .card-title {
      font-weight: bold;
    }
  </style>

<div class="row">
    <div class="col-md-12 mb-3">
        @if(count($banners))
        <div class="card rounded-2">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded-2">
                    @foreach($banners as $key => $row)
                    <div class="carousel-item {{ $key == 0 ? 'active' : ''}}">
                        <img class="d-block w-100 object-fit-cover" src="{{ asset('storage/'.$row->image) }}"
                            alt="First slide" style="max-height: 175px;">
                    </div>
                    @endforeach
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </a>
            </div>
        </div>
        @endif
    </div>
    
       
          
          <div class="col-md-12">
            <div class="row">
        
                {{-- Electricity Bill --}}
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-blue">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h5 class="card-title text-white">
                                    <img src="{{ asset('assets/img/electricity.png') }}" style="height: 36px" alt="">
                                    Electricity Bill
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <a href="{{ route('retailer.electricity-bill') }}" class="btn btn-lg btn-outline-danger w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                        <i class="fa-solid fa-bolt fs-4"></i>
                                        Pay Now
                                    </a>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn btn-lg btn-outline-dark w-100 py-1 d-flex align-items-center justify-content-center gap-2"
                                            data-bs-toggle="modal" data-bs-target="#historyModal">
                                        <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                                        History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
                {{-- Water Bill --}}
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-blue">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h5 class="card-title text-white">
                                    <img src="{{ asset('assets/img/water.png') }}" style="height: 36px" alt="">
                                    Water Bill Payment
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <a href="{{ route('retailer.water-bill') }}" class="btn btn-lg btn-outline-danger w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                        <i class="fa-solid fa-bolt fs-4"></i>
                                        Pay Now
                                    </a>
                                </div>
                               <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn btn-lg btn-outline-dark w-100 py-1 d-flex align-items-center justify-content-center gap-2"
                                            data-bs-toggle="modal" data-bs-target="#waterbill">
                                        <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                                        History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
                {{-- LIC Premium --}}
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-blue">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h5 class="card-title text-white">
                                    <img src="{{ asset('assets/img/lic.png') }}" style="height: 36px" alt="">
                                    LIC Premium Payment
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                               <div class="col-sm-6 mb-2">
                                    <a href="{{ route('retailer.lic-bill') }}" class="btn btn-lg btn-outline-danger w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                        <i class="fa-solid fa-bolt fs-4"></i>
                                        Pay Now
                                    </a>
                                </div>
                              <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn btn-lg btn-outline-dark w-100 py-1 d-flex align-items-center justify-content-center gap-2"
                                            data-bs-toggle="modal" data-bs-target="#licbill">
                                        <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                                        History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
                {{-- Gas Payment --}}
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-blue">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h5 class="card-title text-white">
                                    <img src="{{ asset('assets/img/gas.png') }}" style="height: 36px" alt="">
                                    Gas Payment
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <a href="{{ route('retailer.gas-bill') }}" class="btn btn-lg btn-outline-danger w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                        <i class="fa-solid fa-bolt fs-4"></i>
                                        Pay Now
                                    </a>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn btn-lg btn-outline-dark w-100 py-1 d-flex align-items-center justify-content-center gap-2"
                                            data-bs-toggle="modal" data-bs-target="#gasbill">
                                        <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                                        History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
            </div>
          </div>



    <div class="col-md-12">
        <div class="row">
            @foreach($servicesLog as $row)

            {{-- Service 1 :: Physical PanCard --}}
            @if(config('constant.service_ids.pan_cards_add') == $row->service_id ||
            config('constant.service_ids.pan_cards_edit') == $row->service_id)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-blue">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h5 class="card-title text-white">
                                <img src="{{ asset('assets/img/physical.png') }}" style="width: 50px" alt="" srcset="">
                                {{ $row->service_name }}
                            </h5>
                            <a class="list-icon" href="{{ route('pan-card', 'physical') }}">
                                <i class="fa-solid fa-list fs-5"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(config('constant.service_ids.pan_cards_add') == $row->service_id)
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('create-pan-card', 'physical') }}" type="button"
                                    class="btn btn-lg btn-outline-danger w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-light fa-address-card fs-4"></i>
                                    New PanCard
                                </a>
                            </div>
                            @endif

                            @if(config('constant.service_ids.pan_cards_edit') == $row->service_id)
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('update-pan-card', 'physical') }}"
                                    class="btn btn-lg btn-outline-dark w-100 px-2 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-light fa-pen fs-4"></i>
                                    Correction PanCard
                                </a>
                            </div>
                            @endif

                            @if(config('constant.service_ids.pan_cards_add') == $row->service_id)
                            <div class="col-sm-12 mb-2">
                                <a href="{{ route('create-pan-card',['card_type' => 'physical', 'type' => 'esign']) }}"
                                    class="btn btn-lg btn-outline-secondary w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-light fa-folder-medical fs-4"></i>
                                    New Pan with Photo & Signture (Upload)
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Service 2 :: Digital PanCard --}}
            @if(config('constant.service_ids.pan_cards_add_digital') == $row->service_id ||
            config('constant.service_ids.pan_cards_edit_digital') == $row->service_id)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-blue">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h5 class="card-title text-white">
                                <img src="{{ asset('assets/img/digital.png') }}" style="height: 36px" alt="" srcset="">
                                {{ $row->service_name }}
                            </h5>
                            <a class="list-icon" href="{{ route('pan-card', 'digital') }}">
                                <i class="fa-solid fa-list fs-5"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(config('constant.service_ids.pan_cards_add_digital') == $row->service_id)
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('create-pan-card', 'digital') }}" type="button"
                                    class="btn btn-lg btn-outline-danger w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-light fa-address-card fs-4"></i>
                                    New PanCard
                                </a>
                            </div>
                            @endif

                            @if(config('constant.service_ids.pan_cards_edit_digital') == $row->service_id)
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('update-pan-card', 'digital') }}" type="button"
                                    class="btn btn-lg  btn-outline-dark w-100 px-2 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-light fa-pen fs-4"></i>
                                    Correction PanCard
                                </a>
                            </div>
                            @endif

                            @if(config('constant.service_ids.pan_cards_add_digital') == $row->service_id)
                            <div class="col-sm-12 mb-2">
                                <a href="{{ route('create-pan-card',[ 'card_type' => 'digital', 'type' => 'esign']) }}"
                                    class="btn btn-lg btn-outline-secondary w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-light fa-folder-medical fs-4"></i>
                                    New Pan with Photo & Signture (Upload)
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Service 3 :: MSME Certificate --}}
            @if(config('constant.service_ids.msme_certificate') == $row->service_id)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-blue">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h5 class="card-title text-white">
                                <img src="{{ asset('assets/img/msme-certificate.png') }}" style="height: 36px" alt="">
                                {{ $row->service_name }}
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('msme-certificate') }}" type="button"
                                    class="btn btn-lg  btn-outline-danger w-100 px-2 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-duotone fa-sparkles fs-4"></i>
                                    New Application
                                </a>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('msme-certificate.list') }}"
                                    class="btn btn-lg btn-outline-secondary w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-duotone fa-list fs-4"></i>
                                    Certificate List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Service 5 :: ITR Service --}}
            @if(config('constant.service_ids.income_tax_return') == $row->service_id)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-blue">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h5 class="card-title text-white">
                                <img src="{{ asset('assets/img/itr-tax.png') }}" style="height: 36px" alt="">
                                {{ $row->service_name }}
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('file-itr', ['step' => 'personal-info']) }}" type="button"
                                    class="btn btn-lg  btn-outline-danger w-100 px-2 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-duotone fa-sparkles fs-4"></i>
                                    New Application
                                </a>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('itr-list') }}"
                                    class="btn btn-lg btn-outline-secondary w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-duotone fa-list fs-4"></i>
                                    Filed ITR List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @endforeach
        </div>
    </div>
</div>
<div id="electricity-bill-details"></div>




<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Latest History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>transaction_id</th>
                                    <th>Retailer</th>
                                    <th>Board</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bills as $index => $bill)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $bill->transaction_id }}</td>
                                    <td>{{ $bill->retailer->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->board->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->bill_amount }}</td>
                                    <td>{{ $bill->created_at->format('d-m-Y h:i A') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="waterbill" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Latest History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>transaction_id</th>
                                    <th>Retailer</th>
                                    <th>Board</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($waterbills as $index => $bill)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $bill->transaction_id }}</td>
                                    <td>{{ $bill->retailer->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->board->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->bill_amount }}</td>
                                    <td>{{ $bill->created_at->format('d-m-Y h:i A') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gasbill" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Latest History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>transaction_id</th>
                                    <th>Retailer</th>
                                    <th>Board</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gasbills as $index => $bill)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $bill->transaction_id }}</td>
                                    <td>{{ $bill->retailer->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->board->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->bill_amount }}</td>
                                    <td>{{ $bill->created_at->format('d-m-Y h:i A') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="licbill" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Latest History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>transaction_id</th>
                                    <th>Retailer</th>
                                    <th>Board</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($licbills as $index => $bill)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $bill->transaction_id }}</td>
                                    <td>{{ $bill->retailer->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->board->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->bill_amount }}</td>
                                    <td>{{ $bill->created_at->format('d-m-Y h:i A') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                
            </div>
        </div>
    </div>
</div>





@endsection
@section('js')
<script>
    $(document).ready(function(){
        $('.electricity-btn').on('click', function(e){
            e.preventDefault();

            var board_id = $('.board-id').val();
            var consumer_no = $('.consumer-no').val();
            if(board_id === "" || consumer_no === ""){
                alert("Please fill in all required fields.");
                return;
            }

            $.ajax({
                url: '{{ route("retailer.electricity-bill") }}',
                type: 'GET',
                data: {
                    operator: board_id,
                    tel: consumer_no,
                    offer: 'roffer'
                },
                success: function(response) {
                    $('#electricity-bill-details').html('<div class="text-center my-3">Loading...</div>');
                    $("#electricity-bill-details").html(response);

                    var billModal = new bootstrap.Modal(document.getElementById('electricityBillModal'));
                    billModal.show();
                },
                error: function(xhr) {
                    alert("Failed to fetch bill details.");
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function(){
        $('.water-btn').on('click', function(e){
            e.preventDefault();

            var board_id = $('.board-id').val();
            var cnnection_no = $('.connection-no').val();
            if(board_id === "" || consumer_no === ""){
                alert("Please fill in all required fields.");
                return;
            }

            $.ajax({
                url: '{{ route("retailer.electricity-bill-details") }}',
                type: 'GET',
                data: {
                    operator: board_id,
                    tel: consumer_no,
                    offer: 'roffer'
                },
                success: function(response) {
                    $('#electricity-bill-details').html('<div class="text-center my-3">Loading...</div>');
                    $("#electricity-bill-details").html(response);

                    var billModal = new bootstrap.Modal(document.getElementById('electricityBillModal'));
                    billModal.show();
                },
                error: function(xhr) {
                    alert("Failed to fetch bill details.");
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endsection