<!-- ===============================================-->
<!--    JavaScripts-->
<!-- ===============================================-->

@if(!auth()->guard('web')->check())
<!-- Modal -->
<div class="modal fade" id="loadMoney" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm modal-dialog-scrollable-no" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="exampleModalLabel">Load Money</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="{{ asset('storage/'.$site_settings['load_money_qr_code']) }}" class="img-fluid" alt="QR Code">
            </div>
        </div>
    </div>
</div>
@endif

<div id="overlay">
    <div class="cv-spinner">
        <span class="spinner"></span>
    </div>
</div>
<input name="config" type="hidden" value="{{ json_encode($config) }}">
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/custom-methods.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/waves.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
@yield('js')
@include('partial.toastr')