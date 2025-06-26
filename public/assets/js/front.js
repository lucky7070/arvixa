$(function () {

    $(window).scroll(function (event) {
        var scroll = $(window).scrollTop();
        if (scroll > 46) {
            $('#top-bar-custom').removeClass('fixed-top-custom').addClass('fixed-top');
        } else {
            $('#top-bar-custom').removeClass('fixed-top').addClass('fixed-top-custom');
        }
    });

    $('.owl-carousel-banners').owlCarousel({
        loop: true,
        margin: 10,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        nav: true,
        responsive: {
            0: {
                items: 1
            },
            850: {
                items: 2
            }
        }
    })

    $('.owl-carousel-testimonail').owlCarousel({
        loop: true,
        margin: 25,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        nav: true,
        responsive: {
            0: {
                items: 1
            },
            850: {
                items: 2
            }
        }
    })

    $(window).scroll(function () {
        if ($(this).scrollTop()) {
            $('.back-to-top').fadeIn();
        } else {
            $('.back-to-top').fadeOut();
        }
    });

    $(".back-to-top").click(function () {
        $("html, body").animate({ scrollTop: 0 }, 1000);
    });
})
