@if($site_settings['notify_modal_show'] == 1)
<!-- Modal -->
<div class="modal fade" id="openOnLoad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-light-dark">
            <div class="modal-body">
                {!! $site_settings['notify_modal_content'] !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-decoration-none text-danger" data-bs-dismiss="modal"
                    aria-label="Close">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif