
(function($){

    "use strict";

    $(document).ready(function(){

        var link = $(".app-link");

        $('.app-tabs .app-tab-content:not(.opened)').hide();

        link.on('click', function(e) {
            e.preventDefault();
            $(this).parents('.tabs-content').find(".app-link").removeClass('active');
            $(this).addClass('active');
            var a = $(this).attr("href");
            $(this).parents('.tabs-content').find(a).slideDown('fast').removeClass('closed').addClass('opened');

            $(this).parents('.tabs-content').find(".app-tabs .app-tab-content").not(a).slideUp('fast').addClass('closed').removeClass('opened');

        });

        var pitchesToggles = $(".pitches_toggle");

        pitchesToggles.on('click', function(e) {
            e.preventDefault();
            var a = $(this).attr("href");

            if($(this).hasClass('opened')) {
                $(this).parents('.app-tab-content').find(a).slideUp('fast');
                $(this).removeClass('opened');
            } else {
                $(this).parents('.app-tab-content').find(".section__pitches .section__persons").not(a).slideUp('fast');
                $(this).parents('.app-tab-content').find(a).slideDown('fast');
                $(this).parents('.app-tab-content').find(".pitches_toggle").removeClass('opened');
                $(this).addClass('opened');

            }

        });

    });

})(jQuery);