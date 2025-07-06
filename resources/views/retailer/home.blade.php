@extends('layouts.retailer_app')
@section('content')
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        <div class="alert alert-icon-left alert-arrow-left alert-light-primary alert-dismissible fade show" role="alert">
            <p class="mb-2 text-primary"><strong>ध्यान दें..!! </strong> सरकार के नए नियमानुसार MFS L0 को अब MFS L1 110
                में अपग्रेड किया गया है। बिना रुकावट के पैनकार्ड बनाने के लिए आज ही अपनी डिवाइस को अपग्रेड करे। डिवाइस
                लेने के लिए संपर्क करे।</p>
            <i class="fa-regular fa-bell"></i>
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
            <!--  -->
            @endif

            {{-- Service 4 :: ITR Service --}}
            @if(config('constant.service_ids.income_tax_return') == $row->service_id)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-blue">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h5 class="card-title text-white">
                                <img src="{{ asset('assets/img/itr-tax.png') }}" style="height: 36px"
                                    alt="">
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

            {{-- Service 4 :: NSDL Payment bank --}}
            @if(config('constant.service_ids.income_tax_return') == $row->service_id)
            <!--  -->
            @endif

            {{-- Service 5 :: Electricity Bill --}}
            @if(config('constant.service_ids.electricity_bill') == $row->service_id)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-blue">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h5 class="card-title text-white">
                                <img src="{{ asset('assets/img/electricity.png') }}" style="height: 36px" alt="">
                                {{ $row->service_name }}
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
                                <a href="{{ route('retailer.electricity-bill-list') }}" class="btn btn-lg btn-outline-dark w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-duotone fa-list fs-4"></i>
                                    History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Service 7 :: Water Bill --}}
            @if(config('constant.service_ids.water_bill') == $row->service_id)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-blue">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h5 class="card-title text-white">
                                <img src="{{ asset('assets/img/water.png') }}" style="height: 36px" alt="">
                               {{ $row->service_name }}
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
                                <a href="{{ route('retailer.water-bill-list') }}" class="btn btn-lg btn-outline-dark w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-duotone fa-list fs-4"></i>
                                    History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @endif

            {{-- Service 8 :: LIC Premium --}}
            @if(config('constant.service_ids.lic_premium') == $row->service_id)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-blue">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h5 class="card-title text-white">
                                <img src="{{ asset('assets/img/lic.png') }}" style="height: 36px" alt="">
                                {{ $row->service_name }}
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
                                <a href="{{ route('retailer.lic-bill-list') }}" class="btn btn-lg btn-outline-dark w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-duotone fa-list fs-4"></i>
                                    History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Service 9 :: Gas Payment --}}
            @if(config('constant.service_ids.gas_payment') == $row->service_id)
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-blue">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h5 class="card-title text-white">
                                <img src="{{ asset('assets/img/gas.png') }}" style="height: 36px" alt="">
                                {{ $row->service_name }}
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
                                <a href="{{ route('retailer.gas-bill-list') }}" type="button" class="btn btn-lg btn-outline-dark w-100 py-1 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-duotone fa-list fs-4"></i>
                                    History
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

@endsection