window.addEventListener("load", function () {

    // Remove Loader
    if (typeof $ === "undefined") {
        var load_screen = document.getElementById("load_screen");
        if (load_screen) {
            // document.body.removeChild(load_screen);
            load_screen.style.display = 'none';
        }

    }
    else {
        var $loading = $('#load_screen');
        if ($loading) {
            $loading.hide()
        }

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $(document)
            // .ajaxStart(function () {
            //     $loading.show();
            // })
            // .ajaxStop(function () {
            //     $loading.hide();
            // })
            .ajaxError(function (jqXHR, exception, settings) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (exception.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (exception.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + exception.responseText;
                }
                toastr.error(msg);
            })
    }
});

