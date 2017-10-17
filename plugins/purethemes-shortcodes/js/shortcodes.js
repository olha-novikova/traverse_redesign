/* ----------------- Start Document ----------------- */
(function($){
    $(document).ready(function(){

/*----------------------------------------------------*/
/*  Tabs
/*----------------------------------------------------*/

    var $tabsNav    = $('.tabs-nav'),
        $tabsNavLis = $tabsNav.children('li'),
        $tabContent = $('.tab-content');

    $tabsNav.each(function() {
        var $this = $(this);

        $this.next().children('.tab-content').stop(true,true).hide()
        .first().show();

        $this.children('li').first().addClass('active').stop(true,true).show();
    });

    $tabsNavLis.on('click', function(e) {
        var $this = $(this);

        if($tabsNavLis.length > 1 ) {
            $this.siblings().removeClass('active').end().addClass('active');
            $this.parent().next().children('.tab-content').stop(true,true).hide().siblings( $this.find('a').attr('href') ).fadeIn();
        }
        e.preventDefault();
    });


/*----------------------------------------------------*/
/*  Accordion
/*----------------------------------------------------*/
   var $accor = $('.accordion');

         $accor.each(function() {
            $(this).find("div").hide().first().show();
            $(this).find("h3").first().addClass('active-acc');
        });

        var $trigger = $accor.find('h3');

        $trigger.on('click', function(e) {
            var location = $(this).parent();

            if( $(this).next().is(':hidden') ) {
                var $triggerloc = $('h3',location);
                $triggerloc.removeClass('active-acc').next().slideUp(300);
                $(this).addClass('active-acc').next().slideDown(300);
            }
             e.preventDefault();
        });


/*----------------------------------------------------*/
/*  Toggle
/*----------------------------------------------------*/

  /*  $(".toggle-container").hide();
    $(".trigger").toggle(function(){
        $(this).addClass("active");
        }, function () {
        $(this).removeClass("active");
    });
    $(".trigger").click(function(){
        $(this).next(".toggle-container").slideToggle();
    });

    $(".trigger.opened").toggle(function(){
        $(this).removeClass("active");
        }, function () {
        $(this).addClass("active");
    });

    $(".trigger.opened").addClass("active").next(".toggle-container").show();*/



/*----------------------------------------------------*/
/*  Alert Boxes
/*----------------------------------------------------*/


        $(".notification a.close").removeAttr("href").click(function(){
            $(this).parent().fadeOut(200);
        });


/* ------------------ End Document ------------------ */
});

})(this.jQuery);