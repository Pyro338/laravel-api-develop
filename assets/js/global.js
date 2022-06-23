/**
 * @file
 * Global utilities.
 *
 */
jQuery(function ($) {

    //------------------------------------------------------------------------
    //				Preloader
    //------------------------------------------------------------------------
    if ($("#preloader").length) {
        $(document).ready(function () {
            // Remove loading indicator
            setTimeout(function () {
                $('#preloader .preloader-content').fadeOut(400, function () {
                    $('#preloader').fadeOut(800);
                });
            }, 400);
        });
    }

    //------------------------------------------------------------------------
    //				register toggler
    //------------------------------------------------------------------------
    $('.register-toggler').click(function (e) {
        e.preventDefault();
        $(".slider-menu").toggleClass("slider-active");
    });


}); // end function
