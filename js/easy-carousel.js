/*jslint browser: true*/
/*global $, jQuery, document, setTimeout*/

function carousel_width($parent) {
    "use strict";
    var width = 0;
    $parent.find('.item').each(function () {
        width = Math.max(width, jQuery(this).width());
    });
    //$parent.css('max-width', width + 'px');
    return;
}

jQuery(document).ready(function ($) {
    "use strict";
    setTimeout(function () {
        carousel_width($('.easy-responsive-carousel'));

    }, 1);

});


