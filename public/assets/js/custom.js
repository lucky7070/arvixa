const config = JSON.parse($('input[name="config"]').val() || '{}');

function multiCheck(tb_var) {
    tb_var.on("change", ".chk-parent", function () {
        var e = $(this).closest("table").find("td:first-child .child-chk"), a = $(this).is(":checked");
        $(e).each(function () {
            a ? ($(this).prop("checked", !0), $(this).closest("tr").addClass("active")) : ($(this).prop("checked", !1), $(this).closest("tr").removeClass("active"))
        })
    }),
        tb_var.on("change", "tbody tr .new-control", function () {
            $(this).parents("tr").toggleClass("active")
        })
}

const getDistributors = (value = null, selected = null) => {
    $('#distributor_id').html('').removeClass('border-danger');
    if (value) {
        $.post(config?.distributors_list_url, { main_distributor_id: value }, function (data) {
            if (data.length > 0) {
                $.each(data, function (key, value) {
                    $('#distributor_id').append(`<option value="${value.id}" ${selected == value.id ? 'selected' : ''}>${value.name}</option>`);
                });
            } else {
                $('#distributor_id').addClass('border-danger').append(`<option value="">No Distributor Found </option>`);
            }
        });
    } else {
        $('#distributor_id').append(`<option value="">Admin</option>`);
    }
}

$('.form-control').on('change', function () {
    $(this).siblings('.invalid-feedback').remove();
})


$.extend(true, $.fn.dataTable.defaults, {
    searching: true,
    ordering: true,
    processing: true,
    serverSide: true,
    paging: true,
    info: true,
    searchDelay: 400,
    oLanguage: {
        sProcessing: "Processing...",
        sLengthMenu: "Show _MENU_ entries",
        sZeroRecords: '<b class="text-danger">No results found</b>',
        sEmptyTable: '<b class="text-danger">No data available in this table</b>',
        sInfo: "Showing _START_ to _END_ of _TOTAL_ entries.",
        sInfoEmpty: "Showing records from 0 to 0 of a total of 0 records",
        sInfoFiltered: "(filtered from _MAX_ total entries)",
        sInfoPostFix: "",
        sSearch: 'Search :', //'<span class="text-secondary"><i class="fa-solid fa-search"></i></span>',
        sUrl: "",
        sInfoThousands: ",",
        sLoadingRecords: "Loading...",
        oPaginate: {
            sFirst: '<i class="fa-solid fa-chevrons-left"></i>',
            sLast: '<i class="fa-solid fa-chevrons-right"></i>',
            sNext: '<i class="fa fa-chevron-right" ></i>',
            sPrevious: '<i class="fa fa-chevron-left" ></i>'
        },
        sProcessing: "<div class='fa-3x text-secondary'><i class='fas fa-spinner fa-spin'></i></div>",
        oAria: {
            sSortAscending: "",
            sSortDescending: ""
        }
    }
});


jQuery.validator.setDefaults({
    // onkeyup: true,
    onfocusout: function (element) { $(element).valid() },
    debug: false,
    errorClass: "text-danger fs--1",
    errorElement: "span",
});

function makeid(length = 50) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

const slugify = (string, saprator = "-") => {
    const newText = string
        .toLowerCase()
        .replace(/ /g, saprator)
        .replace(/[^\w-]+/g, "");

    return newText
};

function copyToClipboard(textToCopy) {
    // navigator clipboard api needs a secure context (https)
    if (navigator.clipboard && window.isSecureContext) {
        // navigator clipboard api method'
        return navigator.clipboard.writeText(textToCopy);
    } else {
        // text area method
        let textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        // make the textarea out of viewport
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        return new Promise((res, rej) => {
            // here the magic happens
            document.execCommand('copy') ? res() : rej();
            textArea.remove();
        });
    }
}


$(function () {
    $('#openOnLoad').modal('show');
})


// Event listener for Enter key press on #amount input
$("#amount").keypress(function (event) {
    if (event.which == 13) { // Check if Enter key is pressed
        event.preventDefault(); // Prevent default behavior of Enter key (form submission)
        checkAndProcessInput();
    }
});


// Event listener for button click event
$("#addBalanceBtn").click(function () {
    checkAndProcessInput();
});

// Function to check input and process action
// function checkAndProcessInput() {
//     var amount = parseFloat($("#amount").val());
//     if (isNaN(amount) || amount <= 0) {
//         // Display error message if amount is not valid
//         $("#errorMsg").css("display", "block");
//     } else {
//         // Hide error message if amount is valid
//         $("#errorMsg").css("display", "none");

//         // Perform AJAX request
//         $.ajax({
//             url: $("#addBalanceBtn").attr('url'), // Get the URL from the button's 'url' attribute
//             type: "POST",
//             // data: { amount: amount }, // Send the amount to the server

//             data: {
//                 amount: amount,
//                 // payment_gateway: paymentGateway, // Send the payment gateway value to the server
//                 // _token: "{{ csrf_token() }}" // Include CSRF token
//             },

//             success: function (response) {
//                 if (response.success) {

//                     // if (paymentGateway === "EQRK_UPI_GATEWAY" && response.data.payment_url) {
//                     //     // Redirect to the payment URL in a new tab if the gateway is EQRK_UPI_GATEWAY
//                     //     window.open(response.data.payment_url, '_blank');
//                     //     return;
//                     // }

//                     // If success, hide input and button
//                     $("#amount,#rs_btn,#span_informantion, #addBalanceBtn, #errorMsg").hide();

//                     // Show QR code image
//                     showQRCode(response.data.result.qrImage);

//                     // Check if status is Pending to start checking status
//                     if (response.status === "Pending") {
//                         // Periodically check status every 5 seconds (adjust as needed)
//                         var checkStatusInterval = setInterval(function () {
//                             $.ajax({
//                                 url: response.check_url,
//                                 type: 'get',
//                                 success: function (response) {
//                                     if (response.success == "Yes") {

//                                         if (response.data && typeof response.data === 'object') {
//                                             // Retrieve 'updated_balance' and 'ledger_status' from 'data'
//                                             const updatedBalance = response.data.updated_balance ?? null;
//                                             const ledgerStatus = response.data.ledger_status ?? null;

//                                             // Ensure 'ledgerStatus' is a string and handle accordingly
//                                             if (typeof ledgerStatus === 'string') {
//                                                 if (ledgerStatus === 'Approved') {
//                                                     // Payment was approved
//                                                     clearInterval(checkStatusInterval); // Stop checking status

//                                                     // Update the balance on the page
//                                                     $("#myBalance").text(updatedBalance);

//                                                     // Show success message
//                                                     Swal.fire({
//                                                         title: "Good job!",
//                                                         text: 'Your payment was successfully Done.',
//                                                         icon: "success"
//                                                     });

//                                                     // Redirect to wallet page or any other page after a delay (if needed)
//                                                     setTimeout(function () {
//                                                         window.location.href = response.data.redirect_url;
//                                                     }, 2000);

//                                                 } else if (ledgerStatus === 'Pending') {

//                                                     // Show pending message (optional)
//                                                     console.log("Payment is still pending...");

//                                                 } else {
//                                                     // Handle unexpected 'ledger_status' value
//                                                     console.error('Unexpected payment status:', ledgerStatus);
//                                                     clearInterval(checkStatusInterval); // Stop checking status

//                                                     Swal.fire({
//                                                         icon: "error",
//                                                         title: "Oops...",
//                                                         text: "Unexpected payment status: " + ledgerStatus,
//                                                         footer: '<a href="#">Unexpected payment status.</a>'
//                                                     });
//                                                 }
//                                             } else {
//                                                 // Handle case where 'ledger_status' is not a string
//                                                 console.error('Invalid payment status type:', ledgerStatus);
//                                                 clearInterval(checkStatusInterval); // Stop checking status

//                                                 Swal.fire({
//                                                     icon: "error",
//                                                     title: "Oops...",
//                                                     text: "Invalid payment status type.",
//                                                     footer: '<a href="#">Invalid payment status.</a>'
//                                                 });
//                                             }
//                                         }

//                                     } else {
//                                         // Error response from server
//                                         console.error('Error:', response.message);
//                                         clearInterval(checkStatusInterval); // Stop checking status
//                                     }
//                                 },
//                                 error: function (xhr, status, error) {
//                                     // AJAX request error
//                                     console.error('Error:', error);
//                                     clearInterval(checkStatusInterval); // Stop checking status
//                                 }
//                             });
//                         }, 5000); // Check status every 5 seconds (adjust interval as needed)

//                     }
//                 } else {
//                     // If status is false, handle the error
//                     console.error(response.message);
//                 }
//             },
//             error: function (xhr, status, error) {
//                 console.error('Error:', error);
//                 // Handle the error response as needed
//             }
//         });
//     }
// }


function checkAndProcessInput() {
    var amount = parseFloat($("#amount").val());
    var paymentGateway = $("#payment_gateway").val(); // Get paymentGateway value from hidden input

    if (isNaN(amount) || amount <= 0) {
        $("#errorMsg").css("display", "block");
    } else {
        $("#errorMsg").css("display", "none");

        var requestData = {
            amount: amount,
            _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token from meta tag
        };

        if (paymentGateway === "EQRK_UPI_GATEWAY") {
            requestData.payment_gateway = paymentGateway;
        }

        $.ajax({
            url: $("#addBalanceBtn").attr('url'),
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token from meta tag
            },
            data: requestData,
            success: function (response) {
                console.log(response.success);
                if (response.success) {

                    if (paymentGateway === "qkqr_upi_gateway" && response.data.payment_url) {
                        // Trigger window.open with a direct user action
                        window.location.href = response.data.payment_url;
                        return;
                    }

                    $("#amount,#rs_btn,#span_informantion, #addBalanceBtn, #errorMsg").hide();

                    showQRCode(response.data.result.qrImage);

                    if (response.status === "Pending") {
                        var checkStatusInterval = setInterval(function () {
                            $.ajax({
                                url: response.check_url,
                                type: 'GET',
                                success: function (response) {
                                    if (response.success === "Yes") {
                                        if (response.data && typeof response.data === 'object') {
                                            const updatedBalance = response.data.updated_balance ?? null;
                                            const ledgerStatus = response.data.ledger_status ?? null;

                                            if (typeof ledgerStatus === 'string') {
                                                if (ledgerStatus === 'Approved') {
                                                    clearInterval(checkStatusInterval);
                                                    $("#myBalance").text(updatedBalance);

                                                    Swal.fire({
                                                        title: "Good job!",
                                                        text: 'Your payment was successfully Done.',
                                                        icon: "success"
                                                    });

                                                    setTimeout(function () {
                                                        window.location.href = response.data.redirect_url;
                                                    }, 2000);

                                                } else if (ledgerStatus === 'Pending') {
                                                    console.log("Payment is still pending...");
                                                } else {
                                                    console.error('Unexpected payment status:', ledgerStatus);
                                                    clearInterval(checkStatusInterval);

                                                    Swal.fire({
                                                        icon: "error",
                                                        title: "Oops...",
                                                        text: "Unexpected payment status: " + ledgerStatus,
                                                        footer: '<a href="#">Unexpected payment status.</a>'
                                                    });
                                                }
                                            } else {
                                                console.error('Invalid payment status type:', ledgerStatus);
                                                clearInterval(checkStatusInterval);

                                                Swal.fire({
                                                    icon: "error",
                                                    title: "Oops...",
                                                    text: "Invalid payment status type.",
                                                    footer: '<a href="#">Invalid payment status.</a>'
                                                });
                                            }
                                        }

                                    } else {
                                        console.error('Error:', response.message);
                                        clearInterval(checkStatusInterval);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error:', error);
                                    clearInterval(checkStatusInterval);
                                }
                            });
                        }, 5000);
                    }
                } else {
                    console.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });

        // $.ajax({
        //     url: $("#addBalanceBtn").attr('url'),
        //     type: "POST",
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token from meta tag
        //     },
        //     data: requestData,
        //     success: function (response) {
        //         console.log(response.success);
        //         if (response.success) {
        //             if (paymentGateway === "qkqr_upi_gateway" && response.data.payment_url) {
        //                 // Trigger window.open with a direct user action
        //                 window.location.href = response.data.payment_url;
        //                 return;
        //             }

        //             $("#amount,#rs_btn,#span_informantion, #addBalanceBtn, #errorMsg").hide();

        //             showQRCode(response.data.result.qrImage);

        //             if (response.data.ledger_status === "Pending") {
        //                 var checkStatusInterval = setInterval(function () {
        //                     $.ajax({
        //                         url: response.check_url,
        //                         type: 'GET',
        //                         success: function (response) {
        //                             if (response.success) {
        //                                 const updatedBalance = response.data.updated_balance ?? null;
        //                                 const ledgerStatus = response.data.ledger_status ?? null;

        //                                 if (ledgerStatus === 'Approved') {
        //                                     clearInterval(checkStatusInterval);
        //                                     $("#myBalance").text(updatedBalance);

        //                                     Swal.fire({
        //                                         title: "Good job!",
        //                                         text: 'Your payment was successfully Done.',
        //                                         icon: "success"
        //                                     });

        //                                     setTimeout(function () {
        //                                         window.location.href = response.data.redirect_url;
        //                                     }, 2000);

        //                                 } else if (ledgerStatus === 'Pending') {
        //                                     console.log("Payment is still pending...");
        //                                 } else {
        //                                     console.error('Unexpected payment status:', ledgerStatus);
        //                                     clearInterval(checkStatusInterval);

        //                                     Swal.fire({
        //                                         icon: "error",
        //                                         title: "Oops...",
        //                                         text: "Unexpected payment status: " + ledgerStatus,
        //                                         footer: '<a href="#">Unexpected payment status.</a>'
        //                                     });
        //                                 }
        //                             } else {
        //                                 console.error('Error:', response.message);
        //                                 clearInterval(checkStatusInterval);
        //                             }
        //                         },
        //                         error: function (xhr, status, error) {
        //                             console.error('Error:', error);
        //                             clearInterval(checkStatusInterval);
        //                         }
        //                     });
        //                 }, 5000);
        //             }
        //         } else {
        //             console.error(response.message);
        //         }
        //     },
        //     error: function (xhr, status, error) {
        //         console.error('Error:', error);
        //     }
        // });
    }
}

function showQRCode(qrImage) {
    // Create img element for QR code
    var qrImageElement = $("<img>").addClass("img-fluid mx-auto").attr("alt", "QR Code").css({
        "max-width": "300px",
        "width": "100%"
    });

    // Set src attribute with base64 encoded image data
    qrImageElement.attr("src", qrImage);

    // Get QR code container element and append QR code image
    $("#qrCodeContainer").empty().append(qrImageElement).show();
}

// $("#addBalanceBtn").click(function () {
//     var amount = parseFloat($("#amount").val());
//     if (isNaN(amount) || amount <= 0) {
//         $("#errorMsg").css("display", "block");
//         return;
//     } else {
//         $("#errorMsg").css("display", "none");

//         // Disable the button to prevent multiple clicks
//         $(this).prop('disabled', true);

//         $.ajax({
//             url: $(this).attr('url'), // Get the URL from the button's 'url' attribute
//             type: "POST",
//             data: { amount: amount }, // Send the amount to the server
//             success: function (response) {
//                 if (response.success) {
//                     // If success, hide input and button
//                     $("#amount, #addBalanceBtn, #errorMsg").hide();

//                     // Show QR code image
//                     showQRCode(response.data.result.qrImage);

//                     // Check if status is Pending to start checking status
//                     if (response.status === "Pending") {
//                         // Periodically check status every 5 seconds (adjust as needed)
//                         var checkStatusInterval = setInterval(function () {
//                             $.ajax({
//                                 url: response.check_url,
//                                 type: 'get',
//                                 success: function (response) {
//                                     if (response.success == "Yes") {

//                                         if (response.data && typeof response.data === 'object') {
//                                             // Retrieve 'updated_balance' and 'ledger_status' from 'data'
//                                             const updatedBalance = response.data.updated_balance ?? null;
//                                             const ledgerStatus = response.data.ledger_status ?? null;

//                                             // Ensure 'ledgerStatus' is a string and handle accordingly
//                                             if (typeof ledgerStatus === 'string') {
//                                                 if (ledgerStatus === 'Approved') {
//                                                     // Payment was approved
//                                                     clearInterval(checkStatusInterval); // Stop checking status

//                                                     // Update the balance on the page
//                                                     $("#myBalance").text(updatedBalance);

//                                                     // Show success message
//                                                     Swal.fire({
//                                                         title: "Good job!",
//                                                         text: 'Your payment was successfully Done.',
//                                                         icon: "success"
//                                                     });

//                                                     // Redirect to wallet page or any other page after a delay (if needed)
//                                                     setTimeout(function () {
//                                                         window.location.href = response.data.redirect_url;
//                                                     }, 2000);

//                                                 } else if (ledgerStatus === 'Pending') {

//                                                     // Show pending message (optional)
//                                                     console.log("Payment is still pending...");

//                                                 } else {
//                                                     // Handle unexpected 'ledger_status' value
//                                                     console.error('Unexpected payment status:', ledgerStatus);
//                                                     clearInterval(checkStatusInterval); // Stop checking status

//                                                     Swal.fire({
//                                                         icon: "error",
//                                                         title: "Oops...",
//                                                         text: "Unexpected payment status: " + ledgerStatus,
//                                                         footer: '<a href="#">Unexpected payment status.</a>'
//                                                     });
//                                                 }
//                                             } else {
//                                                 // Handle case where 'ledger_status' is not a string
//                                                 console.error('Invalid payment status type:', ledgerStatus);
//                                                 clearInterval(checkStatusInterval); // Stop checking status

//                                                 Swal.fire({
//                                                     icon: "error",
//                                                     title: "Oops...",
//                                                     text: "Invalid payment status type.",
//                                                     footer: '<a href="#">Invalid payment status.</a>'
//                                                 });
//                                             }
//                                         }

//                                     } else {
//                                         // Error response from server
//                                         console.error('Error:', response.message);
//                                         clearInterval(checkStatusInterval); // Stop checking status
//                                     }
//                                 },
//                                 error: function (xhr, status, error) {
//                                     // AJAX request error
//                                     console.error('Error:', error);
//                                     clearInterval(checkStatusInterval); // Stop checking status
//                                 }
//                             });
//                         }, 5000); // Check status every 5 seconds (adjust interval as needed)

//                     }
//                 } else {
//                     // If status is false, handle the error
//                     console.error(response.message);
//                 }
//             },
//             error: function (xhr, status, error) {
//                 console.error('Error:', error);
//                 // Handle the error response as needed
//             }
//         });
//     }
// });








