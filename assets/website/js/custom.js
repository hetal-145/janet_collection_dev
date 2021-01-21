$(document).ready(function () {
    $(".top_picks_carousel").owlCarousel({
        center: false,
        items: 5,
        loop: true,
        dots: false,
        margin: 25,
        nav: false,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: false,
        navText: ["<img src='https://Janet-Collection.com/assets/website/img/icons/left_arrow_icon.svg' alt=''> <label>Prev</label>", "<label>Next</label> <img src='https://Janet-Collection.com/assets/website/img/icons/right_arrow_icon.svg' alt=''>"],
        responsive: {
            320: {
                items: 1
            },
            576: {
                items: 1
            },
            767: {
                items: 3
            },
            768: {
                items: 3
            },
            992: {
                items: 3
            },
            1200: {
                items: 5
            }
        }
    });
    $(".categories_carousel").owlCarousel({
        center: false,
        items: 5,
        loop: true,
        dots: false,
        margin: 25,
        nav: false,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: false,
        navText: ["<img src='https://Janet-Collection.com/assets/website/img/icons/left_arrow_icon.svg' alt=''> <label>Prev</label>", "<label>Next</label> <img src='https://Janet-Collection.com/assets/website/img/icons/right_arrow_icon.svg' alt=''>"],
        responsive: {
            320: {
                items: 1
            },
            576: {
                items: 1
            },
            767: {
                items: 3
            },
            768: {
                items: 3
            },
            992: {
                items: 3
            },
            1200: {
                items: 5
            }
        }
    });

    $(".similar_drinks_carousel").owlCarousel({
        center: false,
        items: 5,
        loop: true,
        margin: 25,
        dots: false,
        nav: false,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        navText: ["<img src='https://Janet-Collection.com/assets/website/img/icons/left_arrow_icon.svg' alt=''> <label>Prev</label>", "<label>Next</label> <img src='https://Janet-Collection.com/assets/website/img/icons/right_arrow_icon.svg' alt=''>"],
        responsive: {
            320: {
                items: 1
            },
            576: {
                items: 1
            },
            767: {
                items: 3
            },
            768: {
                items: 3
            },
            992: {
                items: 3
            },
            1200: {
                items: 5
            }
        }
    });

    $(".testimonials_carousel").owlCarousel({
        center: false,
        loop: true,
        margin: 25,
        dots: false,
        nav: true,
        slideBy: 4,
        autoplay: false,
        autoplayTimeout: 2500,
        autoplayHoverPause: true,
        navText: ["<img src='https://Janet-Collection.com/assets/website/img/icons/left_arrow_icon.svg' alt=''> <label>Prev</label>", "<label>Next</label> <img src='https://Janet-Collection.com/assets/website/img/icons/right_arrow_icon.svg' alt=''>"],
        responsive: {
            320: {
                items: 1
            },
            576: {
                items: 1
            },
            767: {
                items: 4
            },
            768: {
                items: 1
            },
            1200: {
                items: 3
            }
        }
    });
});
$('.dropdown-toggle').dropdown();

$(document).ready(function () {
    $('.menu_open_close').click(function () {
        $('.side_menu').toggleClass('side_menu_close side_menu_open');
        $('.bg-menu-overlay').show();
    });
    $('.close_menu').click(function () {
        $('.side_menu').addClass('side_menu_close');
        $('.side_menu').removeClass('side_menu_open');
        $('.bg-menu-overlay').hide();
    });
});
//$(document).click(function (event) {
//    if (!$(event.target).closest(".side_menu,.menu_open_close").length) {
//        $("body").find(".side_menu").toggleClass('side_menu_open side_menu_close');
//    }
//});

$(document).ready(function () {
    setTimeout(function () {
        $("#cookieConsent").fadeIn(200);
    }, 2000);
    $("#closeCookieConsent, .cookieConsentOK").click(function () {
        $("#cookieConsent").fadeOut(200);
    });
}); 