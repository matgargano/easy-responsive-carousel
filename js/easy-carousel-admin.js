/*jslint browser: true*/
/*global $, jQuery, stop*/

jQuery(function ($) {
    "use strict";
    $("#slideshow-sortable").sortable({
        stop: function () {

            var hidden_values = "";

            $(this).find('li').each(function () {

                if (hidden_values !== "") {
                    hidden_values += ';';
                }
                hidden_values = hidden_values + $(this).data('post-id');
            });
            $("#post-order").val(hidden_values);
        }
    });
    $("#slideshow-sortable").disableSelection();
    $(document).ready(function ($) {
        if (typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function') {
            $("#content_color").wpColorPicker();
        }
    });
});

