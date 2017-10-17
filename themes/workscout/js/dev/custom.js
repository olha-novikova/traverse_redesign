/* ----------------- Start Document ----------------- */
(function($){
    "use strict";

    $(document).ready(function(){

    $(":checkbox").attr("autocomplete", "off");


    $('#login-tabs a').click(function (e) {
        e.preventDefault();
       
        // add class to tab
        $('#login-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        // show the right tab
        $('.tab-content').hide();
        $( $(this).attr('href')).show();
        return false;
    });


    $(".cart-in-header").hoverIntent({
        sensitivity: 3,
        interval: 60,
        over: function () {
            console.log('teeest');
            $('.cart-list', this).fadeIn(200);
            $('.cart-btn a.button', this).addClass('hovered');
        },
        timeout: 220,
        out: function () {
            $('.cart-list', this).fadeOut(100);
            $('.cart-btn a.button', this).removeClass('hovered');
        }
    });


    $('.search_keywords #search_keywords').change(function() {
      
        $('.job_filters #search_keywords').val($(this).val());
    });
    /*----------------------------------------------------*/
    /*  Navigation
    /*----------------------------------------------------*/
    if($('header#main-header').hasClass('full-width')) {
        $('header#main-header').attr('data-full', 'yes');
    }  
    if($('header#main-header').hasClass('alternative')) {
        $('header#main-header').attr('data-alt', 'yes');
    }
    function menumobile(){
        var winWidth = $(window).width();

        if( winWidth < 973 ) {
            $('#navigation').removeClass('menu');
            $('#navigation li').removeClass('dropdown');
            $('header#main-header').removeClass('full-width');
            $('#navigation').superfish('destroy');
        } else {
            $('#navigation').addClass('menu');
            if($('header#main-header').data('full') === "yes" ) {
                 $('header#main-header').addClass('full-width');
            }
            $('#navigation').superfish({
                delay:       300,                               // one second delay on mouseout
                animation:   {opacity:'show'},   // fade-in and slide-down animation
                speed:       200,                               // animation speed
                speedOut:    50                                 // out animation speed
            });
        }
        if( winWidth < 1272 ) {
            $('header#main-header').addClass('alternative').removeClass('full-width');
        } else {
            if($('header#main-header').data('alt') === "yes" ) {} else {
                $('header#main-header').removeClass('alternative');
            }
        }
    }

    $(window).resize(function (){
        menumobile();
    });
    menumobile();


    $(window).load(function(){
        var $mascontainer = $('.recent-blog-posts.masonry, .woo_pricing_tables');
        $mascontainer.isotope({ itemSelector: '.recent-blog, .plan',layoutMode: 'fitRows' });
    });

    /*----------------------------------------------------*/
    /*  Mobile Navigation
    /*----------------------------------------------------*/
        var jPanelMenu = $.jPanelMenu({
          menu: '#responsive',
          animated: false,
          duration: 200,
          keyboardShortcuts: false,
          closeOnContentClick: true
        });


      // desktop devices
        $('.menu-trigger').on('click',function(){
          var jpm = $(this);

          if( jpm.hasClass('active') )
          {
            jPanelMenu.off();
            jpm.removeClass('active');
          }
          else
          {
            jPanelMenu.on();
            jPanelMenu.open();
            jpm.addClass('active');
          }
          return false;
        });


        // Removes SuperFish Styles
        $('#jPanelMenu-menu').removeClass('sf-menu');
        $('#jPanelMenu-menu li ul').removeAttr('style');


        $(window).resize(function (){
          var winWidth = $(window).width();
          var jpmactive = $('.menu-trigger');
          if(winWidth>990) {
            jPanelMenu.off();
            jpmactive.removeClass('active');
          }
        });

    var pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1;
      $(window).on("load", function() {
        if (pixelRatio > 1) {
          if(ws.retinalogo) {
            $('header:not(.transparent) #logo img').attr('src',ws.retinalogo);
          }
         if(ws.transparentretinalogo) {
            $('header.transparent:not(.cloned) #logo img').attr('src',ws.transparentretinalogo);
          }

        } else {
              $('header:not(.transparent) #logo img').attr('src',ws.logo);
              $('header.transparent:not(.cloned) #logo img').attr('src',ws.transparentlogo);
        }
      });



    /*----------------------------------------------------*/
    /*  Stacktable / Responsive Tables Plug-in
    /*----------------------------------------------------*/
    $('.shop_table,.responsive-table').stacktable();
    
    $(".small-only input.input-text.qty.text").on( "change", function() {
        var value = $(this).val();
        var name = $(this).attr('name');
        $(".large-only").find(".quantity.buttons_added .qty[name*='"+name+"']").val(value);
    });

    /*----------------------------------------------------*/
    /*  Back to Top
    /*----------------------------------------------------*/
        var pxShow = 400; // height on which the button will show
        var fadeInTime = 400; // how slow / fast you want the button to show
        var fadeOutTime = 400; // how slow / fast you want the button to hide
        var scrollSpeed = 400; // how slow / fast you want the button to scroll to top.

        $(window).scroll(function(){
          if($(window).scrollTop() >= pxShow){
            $("#backtotop").fadeIn(fadeInTime);
          } else {
            $("#backtotop").fadeOut(fadeOutTime);
          }
        });

        $('#backtotop a').on('click',function(){
          $('html, body').animate({scrollTop:0}, scrollSpeed);
          return false;
        });
    


    /*----------------------------------------------------*/
    /*  Showbiz Carousel
    /*----------------------------------------------------*/
    $( ".job-spotlight-car" ).each( function( index, element ){
        var visible = $(this).data('visible');
        var autoplay = $(this).data('autoplay');
        var delay = $(this).data('delay');
       
        $( this ).showbizpro({
            dragAndScroll:"off",
            visibleElementsArray:visible,
            carousel:"on",
            entrySizeOffset:0,
            allEntryAtOnce:"off",
            rewindFromEnd:"off",
            autoPlay:autoplay,
            delay:delay,
            speed:400,
            easing:'easeOut'
        });
    });  
    $( ".related-job-spotlight-car" ).each( function( index, element ){
        var visible = $(this).data('visible');
        var autoplay = $(this).data('autoplay');
        var delay = $(this).data('delay');
       
        $( this ).showbizpro({
            dragAndScroll:"off",
            visibleElementsArray:visible,
            carousel:"off",
            entrySizeOffset:0,
            allEntryAtOnce:"off",
            rewindFromEnd:"off",
            autoPlay:autoplay,
            delay:delay,
            speed:400,
            easing:'easeOut'
        });
    });
        

    $('.our-clients-run').each( function( index, element ){
        var autoplay = $(this).data('autoplay');
        var delay = $(this).data('delay');
        $( this ).showbizpro({
            dragAndScroll:"off",
            visibleElementsArray:[5,4,3,1],
            carousel:"on",
            entrySizeOffset:0,
            allEntryAtOnce:"off",
            autoPlay:autoplay,
            delay:delay,
            speed:400,
        });

    });




    /*----------------------------------------------------*/
    /*  Flexslider
    /*----------------------------------------------------*/
        $('.testimonials-slider').flexslider({
            animation: "fade",
            controlsContainer: $(".custom-controls-container"),
            customDirectionNav: $(".custom-navigation a")
        });



    /*----------------------------------------------------*/
    /*  Counters
    /*----------------------------------------------------*/

        $('.counter').counterUp({
            delay: 10,
            time: 800
        });



    /*----------------------------------------------------*/
    /*  Chosen Plugin
    /*----------------------------------------------------*/

        var config = {
          '.chosen-select'           : {disable_search_threshold: 10, width:"100%"},
          '.chosen-select-deselect'  : {allow_single_deselect:true, width:"100%"},
          '.chosen-select-no-single' : {disable_search_threshold:10, width:"100%"},
          '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
          '.chosen-select-width'     : {width:"95%"}
        };
        for (var selector in config) {
          $(selector).chosen(config[selector]);
        }




    /*----------------------------------------------------*/
    /*  Magnific Popup
    /*----------------------------------------------------*/   
        
            $('body').magnificPopup({
                type: 'image',
                delegate: 'a.mfp-gallery',

                fixedContentPos: true,
                fixedBgPos: true,

                overflowY: 'auto',

                closeBtnInside: true,
                preloader: true,

                removalDelay: 0,
                mainClass: 'mfp-fade',

                gallery:{enabled:true},

                callbacks: {
                    buildControls: function() {
                         this.contentContainer.append(this.arrowLeft.add(this.arrowRight));
                    }
                }
            });

            
        $(document).on( 'submit', '.small-dialog-content.woo-reg-box form.login, .small-dialog-content.woo-reg-box form.register', function(e) {
            var form = $(this);
            var error = false;

            var base = $(this).serialize();
            var button = $(this).find( 'input[type=submit]' );

            $(button).css('backgroundColor','#ddd');
            var data = base + '&' + button.attr("name") + "=" + button.val();

            var $response = $( '#ajax-response' );

            var request = $.ajax({
                url: ws.woo_account_page,
                data: data,
                type: 'POST',
                cache: false,
                async: false,
                success: function(response) {
                    form.find( $( '.woocommerce-error' ) ).remove();

                    var $response = $( '#ajax_response' );
                    var html = $.parseHTML(response);


                    $response.append(html);
                    error = $response.find( $( '.woocommerce-error' ) );

                    $(button).css('backgroundColor',ws.theme_color);
                    if ( error.length > 0 ) {
                        form.prepend( error.clone() );
                        $response.html('');
                        e.preventDefault();
                    } else {
                        if(form.hasClass('register')) {
                            window.location.href = ws.woo_account_page;
                            e.preventDefault();
                            return false;
                        } else {
                            document.location.href = ws.woo_account_page;
                        }
                    }
                }
            });

        });

            $('.popup-with-zoom-anim').magnificPopup({
                type: 'inline',

                fixedContentPos: false,
                fixedBgPos: true,

                overflowY: 'auto',

                closeBtnInside: true,
                preloader: false,

                midClick: true,
                removalDelay: 300,
                mainClass: 'my-mfp-zoom-in'
            });


            $('.mfp-image').magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                mainClass: 'mfp-fade',
                image: {
                    verticalFit: true
                }
            });


            $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
                disableOn: 700,
                type: 'iframe',
                mainClass: 'mfp-fade',
                removalDelay: 160,
                preloader: false,

                fixedContentPos: false
            });


     /*---------------------------------------------------*/
    /*  Contact Form
    /*---------------------------------------------------*/
    

    //reset previously set border colors and hide all comment on .keyup()
    $("#contactform input, #contactform textarea").keyup(function() {
      $("#contactform input, #contactform textarea").removeClass('error');
      $("#result").slideUp();
    });




    /*----------------------------------------------------*/
    /*  Accordions
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
    /*  Application Tabs
    /*----------------------------------------------------*/   
        // Get all the links.
        var link = $(".app-link");
        $('.close-tab').hide();

        $('.app-tabs div.app-tab-content').hide();
        // On clicking of the links do something.
        link.on('click', function(e) {

            e.preventDefault();
            $(this).parents('div.application').find('.close-tab').fadeOut();
            if($(this).hasClass('opened')) {
                $(this).parents('div.application').find(".app-tabs div.app-tab-content").slideUp('fast');
                $(this).parents('div.application').find('.close-tab').fadeOut(10);
                $(this).removeClass('opened');
            } else {
                $(this).parents('div.application').find(".app-link").removeClass('opened');
                $(this).addClass('opened');
                var a = $(this).attr("href");
                $(this).parents('div.application').find(a).slideDown('fast').removeClass('closed').addClass('opened');
                $(this).parents('div.application').find('.close-tab').fadeIn(10);
            }

            $(this).parents('div.application').find(".app-tabs div.app-tab-content").not(a).slideUp('fast').addClass('closed').removeClass('opened');
            
        });

        $('.close-tab').on('click',function(e){
            $(this).fadeOut();
            e.preventDefault();
            $(this).parents('div.application').find(".app-link").removeClass('opened');
            $(this).parents('div.application').find(".app-tabs div.app-tab-content").slideUp('fast').addClass('closed').removeClass('opened');
        });


    /*----------------------------------------------------*/
    /*  Add Resume 
    /*----------------------------------------------------*/   
        $('.box-to-clone').hide();
        $('.add-box').on('click', function(e) {
            e.preventDefault();
            var newElem = $(this).parent().find('.box-to-clone:first').clone();
            newElem.find('input').val('');
            newElem.prependTo($(this).parent()).show();
            var height = $(this).prev('.box-to-clone').outerHeight(true);
            
            $("html, body").stop().animate({ scrollTop: $(this).offset().top-height}, 600);
        });

        $('body').on('click','.remove-box', function(e) {
            e.preventDefault();
            $(this).parent().remove();
        });



        $('.stars a').on( "click", function() {
            $('.stars a').removeClass('prevactive');
            $(this).prevAll().addClass('prevactive');
        }).hover(
          function() {
            $('.stars a').removeClass('prevactive');
            $(this).addClass('prevactive').prevAll().addClass('prevactive');
          }, function() {
            $('.stars a').removeClass('prevactive');
            $('.stars a.active').prevAll().addClass('prevactive');
          }
        );

        
    /*----------------------------------------------------*/
    /*  Tabs
    /*----------------------------------------------------*/ 
        var $tabsNav    = $('.tabs-nav,.vc_tta-tabs-list'),
        $tabsNavLis = $tabsNav.children('li');
        // $tabContent = $('.tab-content');

        $tabsNav.each(function() {
            var $this = $(this);
            
                $this.next().children('.tab-content').stop(true,true).hide().first().show();

                $this.children('li').first().addClass('active').stop(true,true).show();
            
        });

        $tabsNavLis.on('click', function(e) {
            var $this = $(this);

            $this.siblings().removeClass('active').end()
            .addClass('active');

            $this.parent().next().children('.tab-content').stop(true,true).hide()
            .siblings( $this.find('a').attr('href') ).fadeIn();

            e.preventDefault();
        });
    
    var hash = window.location.hash;
    var anchor = $('.tabs-nav-o a[href="' + hash + '"]');
    if (anchor.length === 0) {
      
    } else {
        $(".tab-content").hide();
        anchor.trigger( "click" );
        $(hash+".tab-content").show();
    }


    $('#login-tabs a').click(function (e) {
        
        e.preventDefault();
        // add class to tab
        $('#login-tabs li').removeClass('active');
       $(this).parent().addClass('active');
        // show the right tab
        $(' .tab-content').hide();
        $( $(this).attr('href')).show();
        return false;
    });

    /*remove empty tags*/
    $('p').each(function() {
        var $this = $(this);
        if($this.html().replace(/\s|&nbsp;/g, '').length === 0)
        $this.addClass('pfix').html(''); 
    }); 


    
    $('.ws-file-upload').change(function(){
        
            var filename = [];
            $.each($(this).prop("files"), function(k,v){
                filename.push('<span class="job-manager-uploaded-file-name">'+v['name']+'</span> ');
            });
        
            $(this).prev('.job-manager-uploaded-files').html(filename);
      
    });

    
    /*----------------------------------------------------*/
    /*  Sliding In-Out Content
    /*----------------------------------------------------*/

    $(window).bind("load resize scroll",function(e){
        var headerElem = $('.parallax .search-container');

        // flying out and fading for header content
        $(headerElem).css({  'transform': 'translateY(' + (  $(window).scrollTop() / -9 ) + 'px)', });
        // $(headerElem).css({ 'opacity': 1 - $(window).scrollTop() / 600 });  
    });



    /*----------------------------------------------------*/
    /*  Parallax
    /*----------------------------------------------------*/
    /* detect touch */
    if("ontouchstart" in window){
        document.documentElement.className = document.documentElement.className + " touch";
    }
    if(!$("html").hasClass("touch")){
        /* background fix */
        $(".parallax").css("background-attachment", "fixed");
    }

    /* fix vertical when not overflow
    call fullscreenFix() if .fullscreen content changes */
    function fullscreenFix(){
        var h = $('body').height();
        // set .fullscreen height
        $(".parallax-content").each(function(i){
            if($(this).innerHeight() > h){ $(this).closest(".fullscreen").addClass("overflow");
            }
        });
    }
    $(window).resize(fullscreenFix);
    fullscreenFix();



    /* resize background images */
    function backgroundResize(){
        var windowH = $(window).height();
        var winWidth = $(window).width();
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;
        var ios = false;
        if( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) ) {
            ios = true;
        }
        if(winWidth>1023 || ios == false) {
            $(".background").each(function(i){
                var path = $(this);
                $(this).removeClass('mobilebg');
                // variables
                var contW = path.width();
                var contH = path.height();
                var imgW = path.attr("data-img-width");
                var imgH = path.attr("data-img-height");
                var ratio = imgW / imgH;
                // overflowing difference
                var diff = parseFloat(path.attr("data-diff"));
                diff = diff ? diff : 0;
                // remaining height to have fullscreen image only on parallax
                var remainingH = 0;
                if(path.hasClass("parallax") && !$("html").hasClass("touch")){
                    var maxH = contH > windowH ? contH : windowH;
                    remainingH = windowH - contH;
                }
                // set img values depending on cont
                imgH = contH + remainingH + diff;
                imgW = imgH * ratio;
                // fix when too large
                if(contW > imgW){
                    imgW = contW;
                    imgH = imgW / ratio;
                }
                //
                path.data("resized-imgW", imgW);
                path.data("resized-imgH", imgH);
                path.css("background-size", imgW + "px " + imgH + "px");
            });
        } else {
            $(".background").each(function(i){
               $(this).addClass('mobilebg');
            });
        }
    }
    $(window).resize(backgroundResize);
    $(window).focus(backgroundResize);
    backgroundResize();



    /* set parallax background-position */
    function parallaxPosition(e){
         var winWidth = $(window).width();
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;
        var ios = false;
        if( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) ) {
            ios = true;
        }
        if(winWidth>1023 || ios == false) {
            var heightWindow = $(window).height();
            var topWindow = $(window).scrollTop();
            var bottomWindow = topWindow + heightWindow;
            var currentWindow = (topWindow + bottomWindow) / 2;
                $(".parallax").each(function(i){
                    var path = $(this);
                    var height = path.height();
                    var top = path.offset().top;
                    var bottom = top + height;
                    // only when in range
                    if(bottomWindow > top && topWindow < bottom){
                        var imgW = path.data("resized-imgW");
                        var imgH = path.data("resized-imgH");
                        // min when image touch top of window
                        var min = 0;
                        // max when image touch bottom of window
                        var max = - imgH + heightWindow;
                        // overflow changes parallax
                        var overflowH = height < heightWindow ? imgH - height : imgH - heightWindow; // fix height on overflow
                        top = top - overflowH;
                        bottom = bottom + overflowH;
                        // value with linear interpolation
                        var value = -100 + min + (max - min) * (currentWindow - top) / (bottom - top);
                        // set background-position
                        var orizontalPosition = path.attr("data-oriz-pos");
                        orizontalPosition = orizontalPosition ? orizontalPosition : "50%";
                        $(this).css("background-position", orizontalPosition + " " + value + "px");

                    }
                });
            }
        }
    if(!$("html").hasClass("touch")){
        $(window).resize(parallaxPosition);
        //$(window).focus(parallaxPosition);
        $(window).scroll(parallaxPosition);
        parallaxPosition();
    }


    /*----------------------------------------------------*/
    /*  Sticky Header 
    /*----------------------------------------------------*/
    if ($('header#main-header').hasClass('sticky-header')) { 
    
        $(".sticky-header" ).clone(true).addClass('cloned').insertAfter( ".sticky-header" );
        $(".sticky-header.cloned.transparent #logo a img").attr('src',ws.logo);
        $(".sticky-header.cloned.alternative").removeClass('alternative');
        $('.sticky-header.cloned .popup-with-zoom-anim').magnificPopup({
                type: 'inline',

                fixedContentPos: false,
                fixedBgPos: true,

                overflowY: 'auto',

                closeBtnInside: true,
                preloader: false,

                midClick: true,
                removalDelay: 300,
                mainClass: 'my-mfp-zoom-in'
            });

        var stickyHeader = document.querySelector(".sticky-header.cloned");

        var headroom = new Headroom(stickyHeader, {
          "offset": $(".sticky-header").height(),
          "tolerance": 0
        });

        // disabling on mobile
        $(window).bind("load resize",function(e){
            $( ".sticky-header.cloned" ).removeClass('transparent alternative');

            var winWidth = $(window).width();

            if(winWidth>1290) {
                headroom.init();
                }

                else if(winWidth<1290) {
                    headroom.destroy();
                }
        });

    }

        $(".small-only #coupon_code").on( "change", function() {
                var value = $(this).val();
                var name = $(this).attr('name');
                $(".large-only").find("input[name*='"+name+"']").val(value);
            }); 

        $(".large-only #coupon_code").on( "change", function() {
                var value = $(this).val();
                var name = $(this).attr('name');
                $(".small-only").find("input[name*='"+name+"']").val(value);
            });

    /* move related jobs after job details*/
    var winWidth = $(window).width();
    if(winWidth < 768){
        $("#related-job-container").detach().appendTo('#job-details')
    } 


// ------------------ End Document ------------------ //
});

})(this.jQuery);

  
  /**
 * hoverIntent is similar to jQuery's built-in "hover" method except that
 * instead of firing the handlerIn function immediately, hoverIntent checks
 * to see if the user's mouse has slowed down (beneath the sensitivity
 * threshold) before firing the event. The handlerOut function is only
 * called after a matching handlerIn.
 *
 * hoverIntent r7 // 2013.03.11 // jQuery 1.9.1+
 * http://cherne.net/brian/resources/jquery.hoverIntent.html
 *
 * You may use hoverIntent under the terms of the MIT license. Basically that
 * means you are free to use hoverIntent as long as this header is left intact.
 * Copyright 2007, 2013 Brian Cherne
 *
 * // basic usage ... just like .hover()
 * .hoverIntent( handlerIn, handlerOut )
 * .hoverIntent( handlerInOut )
 *
 * // basic usage ... with event delegation!
 * .hoverIntent( handlerIn, handlerOut, selector )
 * .hoverIntent( handlerInOut, selector )
 *
 * // using a basic configuration object
 * .hoverIntent( config )
 *
 * @param  handlerIn   function OR configuration object
 * @param  handlerOut  function OR selector for delegation OR undefined
 * @param  selector    selector OR undefined
 * @author Brian Cherne <brian(at)cherne(dot)net>
 **/
(function($) {
    $.fn.hoverIntent = function(handlerIn,handlerOut,selector) {

        // default configuration values
        var cfg = {
            interval: 50,
            sensitivity: 7,
            timeout: 0
        };

        if ( typeof handlerIn === "object" ) {
            cfg = $.extend(cfg, handlerIn );
        } else if ($.isFunction(handlerOut)) {
            cfg = $.extend(cfg, { over: handlerIn, out: handlerOut, selector: selector } );
        } else {
            cfg = $.extend(cfg, { over: handlerIn, out: handlerIn, selector: handlerOut } );
        }

        // instantiate variables
        // cX, cY = current X and Y position of mouse, updated by mousemove event
        // pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
        var cX, cY, pX, pY;

        // A private function for getting mouse position
        var track = function(ev) {
            cX = ev.pageX;
            cY = ev.pageY;
        };

        // A private function for comparing current and previous mouse position
        var compare = function(ev,ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            // compare mouse positions to see if they've crossed the threshold
            if ( ( Math.abs(pX-cX) + Math.abs(pY-cY) ) < cfg.sensitivity ) {
                $(ob).off("mousemove.hoverIntent",track);
                // set hoverIntent state to true (so mouseOut can be called)
                ob.hoverIntent_s = 1;
                return cfg.over.apply(ob,[ev]);
            } else {
                // set previous coordinates for next time
                pX = cX; pY = cY;
                // use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
                ob.hoverIntent_t = setTimeout( function(){compare(ev, ob);} , cfg.interval );
            }
        };

        // A private function for delaying the mouseOut function
        var delay = function(ev,ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            ob.hoverIntent_s = 0;
            return cfg.out.apply(ob,[ev]);
        };

        // A private function for handling mouse 'hovering'
        var handleHover = function(e) {
            // copy objects to be passed into t (required for event object to be passed in IE)
            var ev = jQuery.extend({},e);
            var ob = this;

            // cancel hoverIntent timer if it exists
            if (ob.hoverIntent_t) { ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t); }

            // if e.type == "mouseenter"
            if (e.type == "mouseenter") {
                // set "previous" X and Y position based on initial entry point
                pX = ev.pageX; pY = ev.pageY;
                // update "current" X and Y position based on mousemove
                $(ob).on("mousemove.hoverIntent",track);
                // start polling interval (self-calling timeout) to compare mouse coordinates over time
                if (ob.hoverIntent_s != 1) { ob.hoverIntent_t = setTimeout( function(){compare(ev,ob);} , cfg.interval );}

                // else e.type == "mouseleave"
            } else {
                // unbind expensive mousemove event
                $(ob).off("mousemove.hoverIntent",track);
                // if hoverIntent state is true, then call the mouseOut function after the specified delay
                if (ob.hoverIntent_s == 1) { ob.hoverIntent_t = setTimeout( function(){delay(ev,ob);} , cfg.timeout );}
            }
        };

        // listen for mouseenter and mouseleave
        return this.on({'mouseenter.hoverIntent':handleHover,'mouseleave.hoverIntent':handleHover}, cfg.selector);
    };
})(jQuery);