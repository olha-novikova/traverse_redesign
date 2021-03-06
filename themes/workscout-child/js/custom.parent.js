! function(e) {
    "use strict";
    e(document).ready(function() {
        function t() {
            var t = e(window).width();
            t < 973 ? (e("#navigation").removeClass("menu"), e("#navigation li").removeClass("dropdown"), e("header#main-header").removeClass("full-width"), e("#navigation").superfish("destroy")) : (e("#navigation").addClass("menu"), "yes" === e("header#main-header").data("full") && e("header#main-header").addClass("full-width"), e("#navigation").superfish({
                delay: 300,
                animation: {
                    opacity: "show"
                },
                speed: 200,
                speedOut: 50
            })), t < 1272 ? e("header#main-header").addClass("alternative").removeClass("full-width") : "yes" === e("header#main-header").data("alt") || e("header#main-header").removeClass("alternative")
        }

        function a() {
            var t = e("body").height();
            e(".parallax-content").each(function(a) {
                e(this).innerHeight() > t && e(this).closest(".fullscreen").addClass("overflow")
            })
        }

        function n() {
            var t = e(window).height(),
                a = e(window).width(),
                n = navigator.userAgent || navigator.vendor || window.opera,
                o = !1;
            (n.match(/iPad/i) || n.match(/iPhone/i) || n.match(/iPod/i)) && (o = !0), a > 1023 || 0 == o ? e(".background").each(function(a) {
                var n = e(this);
                e(this).removeClass("mobilebg");
                var o = n.width(),
                    i = n.height(),
                    s = n.attr("data-img-width"),
                    r = n.attr("data-img-height"),
                    l = s / r,
                    d = parseFloat(n.attr("data-diff"));
                d = d ? d : 0;
                var c = 0;
                if (n.hasClass("parallax") && !e("html").hasClass("touch")) {
                    c = t - i
                }
                r = i + c + d, s = r * l, o > s && (s = o, r = s / l), n.data("resized-imgW", s), n.data("resized-imgH", r), n.css("background-size", s + "px " + r + "px")
            }) : e(".background").each(function(t) {
                e(this).addClass("mobilebg")
            })
        }

        function o(t) {
            var a = e(window).width(),
                n = navigator.userAgent || navigator.vendor || window.opera,
                o = !1;
            if ((n.match(/iPad/i) || n.match(/iPhone/i) || n.match(/iPod/i)) && (o = !0), a > 1023 || 0 == o) {
                var i = e(window).height(),
                    s = e(window).scrollTop(),
                    r = s + i,
                    l = (s + r) / 2;
                e(".parallax").each(function(t) {
                    var a = e(this),
                        n = a.height(),
                        o = a.offset().top,
                        d = o + n;
                    if (r > o && s < d) {
                        var c = (a.data("resized-imgW"), a.data("resized-imgH")),
                            h = 0,
                            p = -c + i,
                            v = n < i ? c - n : c - i;
                        o -= v, d += v;
                        var f = -100 + h + (p - h) * (l - o) / (d - o),
                            u = a.attr("data-oriz-pos");
                        u = u ? u : "50%", e(this).css("background-position", u + " " + f + "px")
                    }
                })
            }
        }
        e(":checkbox").attr("autocomplete", "off"), e("#login-tabs a").click(function(t) {
            return t.preventDefault(), e("#login-tabs li").removeClass("active"), e(this).parent().addClass("active"), e(".tab-content").hide(), e(e(this).attr("href")).show(), !1
        }), e(".cart-in-header").hoverIntent({
            sensitivity: 3,
            interval: 60,
            over: function() {
                console.log("teeest"), e(".cart-list", this).fadeIn(200), e(".cart-btn a.button", this).addClass("hovered")
            },
            timeout: 220,
            out: function() {
                e(".cart-list", this).fadeOut(100), e(".cart-btn a.button", this).removeClass("hovered")
            }
        }), e(".search_keywords #search_keywords").change(function() {
            e(".job_filters #search_keywords").val(e(this).val())
        }), e("header#main-header").hasClass("full-width") && e("header#main-header").attr("data-full", "yes"), e("header#main-header").hasClass("alternative") && e("header#main-header").attr("data-alt", "yes"), e(window).resize(function() {
            t()
        }), t(), e(window).load(function() {
            var t = e(".recent-blog-posts.masonry, .woo_pricing_tables");
            t.isotope({
                itemSelector: ".recent-blog, .plan",
                layoutMode: "fitRows"
            })
        });
        var i = e.jPanelMenu({
            menu: "#responsive",
            animated: !1,
            duration: 200,
            keyboardShortcuts: !1,
            closeOnContentClick: !0
        });
        e(".menu-trigger").on("click", function() {
            var t = e(this);
            return t.hasClass("active") ? (i.off(), t.removeClass("active")) : (i.on(), i.open(), t.addClass("active")), !1
        }), e("#jPanelMenu-menu").removeClass("sf-menu"), e("#jPanelMenu-menu li ul").removeAttr("style"), e(window).resize(function() {
            var t = e(window).width(),
                a = e(".menu-trigger");
            t > 990 && (i.off(), a.removeClass("active"))
        });
        var s = window.devicePixelRatio ? window.devicePixelRatio : 1;
        e(window).on("load", function() {
            s > 1 ? (ws.retinalogo && e("header:not(.transparent) #logo img").attr("src", ws.retinalogo), ws.transparentretinalogo && e("header.transparent:not(.cloned) #logo img").attr("src", ws.transparentretinalogo)) : (e("header:not(.transparent) #logo img").attr("src", ws.logo), e("header.transparent:not(.cloned) #logo img").attr("src", ws.transparentlogo))
        }), e(".shop_table,.responsive-table").stacktable(), e(".small-only input.input-text.qty.text").on("change", function() {
            var t = e(this).val(),
                a = e(this).attr("name");
            e(".large-only").find(".quantity.buttons_added .qty[name*='" + a + "']").val(t)
        });
        var r = 400,
            l = 400,
            d = 400,
            c = 400;
        e(window).scroll(function() {
            e(window).scrollTop() >= r ? e("#backtotop").fadeIn(l) : e("#backtotop").fadeOut(d)
        }), e("#backtotop a").on("click", function() {
            return e("html, body").animate({
                scrollTop: 0
            }, c), !1
        }), e(".job-spotlight-car").each(function(t, a) {
            var n = e(this).data("visible"),
                o = e(this).data("autoplay"),
                i = e(this).data("delay");
            e(this).showbizpro({
                dragAndScroll: "off",
                visibleElementsArray: n,
                carousel: "on",
                entrySizeOffset: 0,
                allEntryAtOnce: "off",
                rewindFromEnd: "off",
                autoPlay: o,
                delay: i,
                speed: 400,
                easing: "easeOut"
            })
        }), e(".related-job-spotlight-car").each(function(t, a) {
            var n = e(this).data("visible"),
                o = e(this).data("autoplay"),
                i = e(this).data("delay");
            e(this).showbizpro({
                dragAndScroll: "off",
                visibleElementsArray: n,
                carousel: "off",
                entrySizeOffset: 0,
                allEntryAtOnce: "off",
                rewindFromEnd: "off",
                autoPlay: o,
                delay: i,
                speed: 400,
                easing: "easeOut"
            })
        }), e(".our-clients-run").each(function(t, a) {
            var n = e(this).data("autoplay"),
                o = e(this).data("delay");
            e(this).showbizpro({
                dragAndScroll: "off",
                visibleElementsArray: [5, 4, 3, 1],
                carousel: "on",
                entrySizeOffset: 0,
                allEntryAtOnce: "off",
                autoPlay: n,
                delay: o,
                speed: 400
            })
        }), e(".testimonials-slider").flexslider({
            animation: "fade",
            controlsContainer: e(".custom-controls-container"),
            customDirectionNav: e(".custom-navigation a")
        }), e(".counter").counterUp({
            delay: 10,
            time: 800
        });
        var h = {
            ".chosen-select": {
                disable_search_threshold: 10,
                width: "100%"
            },
            ".chosen-select-deselect": {
                allow_single_deselect: !0,
                width: "100%"
            },
            ".chosen-select-no-single": {
                disable_search_threshold: 10,
                width: "100%"
            },
            ".chosen-select-no-results": {
                no_results_text: "Oops, nothing found!"
            },
            ".chosen-select-width": {
                width: "95%"
            }
        };
        for (var p in h) e(p).chosen(h[p]);
        e("body").magnificPopup({
            type: "image",
            delegate: "a.mfp-gallery",
            fixedContentPos: !0,
            fixedBgPos: !0,
            overflowY: "auto",
            closeBtnInside: !0,
            preloader: !0,
            removalDelay: 0,
            mainClass: "mfp-fade",
            gallery: {
                enabled: !0
            },
            callbacks: {
                buildControls: function() {
                    this.contentContainer.append(this.arrowLeft.add(this.arrowRight))
                }
            }
        }),  e(".popup-with-zoom-anim").magnificPopup({
            type: "inline",
            fixedContentPos: !1,
            fixedBgPos: !0,
            overflowY: "auto",
            closeBtnInside: !0,
            preloader: !1,
            midClick: !0,
            removalDelay: 300,
            mainClass: "my-mfp-zoom-in"
        }), e(".mfp-image").magnificPopup({
            type: "image",
            closeOnContentClick: !0,
            mainClass: "mfp-fade",
            image: {
                verticalFit: !0
            }
        }), e(".popup-youtube, .popup-vimeo, .popup-gmaps").magnificPopup({
            disableOn: 700,
            type: "iframe",
            mainClass: "mfp-fade",
            removalDelay: 160,
            preloader: !1,
            fixedContentPos: !1
        }), e("#contactform input, #contactform textarea").keyup(function() {
            e("#contactform input, #contactform textarea").removeClass("error"), e("#result").slideUp()
        });
        var v = e(".accordion");
        v.each(function() {
            e(this).find("div").hide().first().show(), e(this).find("h3").first().addClass("active-acc")
        });
        var f = v.find("h3");
        f.on("click", function(t) {
            var a = e(this).parent();
            if (e(this).next().is(":hidden")) {
                var n = e("h3", a);
                n.removeClass("active-acc").next().slideUp(300), e(this).addClass("active-acc").next().slideDown(300)
            }
            t.preventDefault()
        });
        var u = e(".app-link");
        e(".close-tab").hide(), e(".app-tabs div.app-tab-content").hide(), u.on("click", function(t) {
            if (t.preventDefault(), e(this).parents("div.application").find(".close-tab").fadeOut(), e(this).hasClass("opened")) e(this).parents("div.application").find(".app-tabs div.app-tab-content").slideUp("fast"), e(this).parents("div.application").find(".close-tab").fadeOut(10), e(this).removeClass("opened");
            else {
                e(this).parents("div.application").find(".app-link").removeClass("opened"), e(this).addClass("opened");
                var a = e(this).attr("href");
                e(this).parents("div.application").find(a).slideDown("fast").removeClass("closed").addClass("opened"), e(this).parents("div.application").find(".close-tab").fadeIn(10)
            }
            e(this).parents("div.application").find(".app-tabs div.app-tab-content").not(a).slideUp("fast").addClass("closed").removeClass("opened")
        }), e(".close-tab").on("click", function(t) {
            e(this).fadeOut(), t.preventDefault(), e(this).parents("div.application").find(".app-link").removeClass("opened"), e(this).parents("div.application").find(".app-tabs div.app-tab-content").slideUp("fast").addClass("closed").removeClass("opened")
        }), e(".box-to-clone").hide(), e(".add-box").on("click", function(t) {
            t.preventDefault();
            var a = e(this).parent().find(".box-to-clone:first").clone();
            a.find("input").val(""), a.prependTo(e(this).parent()).show();
            var n = e(this).prev(".box-to-clone").outerHeight(!0);
            e("html, body").stop().animate({
                scrollTop: e(this).offset().top - n
            }, 600)
        }), e("body").on("click", ".remove-box", function(t) {
            t.preventDefault(), e(this).parent().remove()
        }), e(".stars a").on("click", function() {
            e(".stars a").removeClass("prevactive"), e(this).prevAll().addClass("prevactive")
        }).hover(function() {
                e(".stars a").removeClass("prevactive"), e(this).addClass("prevactive").prevAll().addClass("prevactive")
            }, function() {
                e(".stars a").removeClass("prevactive"), e(".stars a.active").prevAll().addClass("prevactive")
            });
        var m = e(".tabs-nav,.vc_tta-tabs-list"),
            w = m.children("li");
        m.each(function() {
            var t = e(this);
            t.next().children(".tab-content").stop(!0, !0).hide().first().show(), t.children("li").first().addClass("active").stop(!0, !0).show()
        }), w.on("click", function(t) {
            var a = e(this);
            a.siblings().removeClass("active").end().addClass("active"), a.parent().next().children(".tab-content").stop(!0, !0).hide().siblings(a.find("a").attr("href")).fadeIn(), t.preventDefault()
        });
        var g = window.location.hash,
            b = e('.tabs-nav-o a[href="' + g + '"]');
        if (0 === b.length || (e(".tab-content").hide(), b.trigger("click"), e(g + ".tab-content").show()), e("#login-tabs a").click(function(t) {
            return t.preventDefault(), e("#login-tabs li").removeClass("active"), e(this).parent().addClass("active"), e(" .tab-content").hide(), e(e(this).attr("href")).show(), !1
        }), e("p").each(function() {
            var t = e(this);
            0 === t.html().replace(/\s|&nbsp;/g, "").length && t.addClass("pfix").html("")
        }), e(".ws-file-upload").change(function() {
            var t = [];
            e.each(e(this).prop("files"), function(e, a) {
                t.push('<span class="job-manager-uploaded-file-name">' + a.name + "</span> ")
            }), e(this).prev(".job-manager-uploaded-files").html(t)
        }), e(window).bind("load resize scroll", function(t) {
            var a = e(".parallax .search-container");
            e(a).css({
                transform: "translateY(" + e(window).scrollTop() / -9 + "px)"
            })
        }), "ontouchstart" in window && (document.documentElement.className = document.documentElement.className + " touch"), e("html").hasClass("touch") || e(".parallax").css("background-attachment", "fixed"), e(window).resize(a), a(), e(window).resize(n), e(window).focus(n), n(), e("html").hasClass("touch") || (e(window).resize(o), e(window).scroll(o), o()), e("header#main-header").hasClass("sticky-header")) {
            e(".sticky-header").clone(!0).addClass("cloned").insertAfter(".sticky-header"), e(".sticky-header.cloned.transparent #logo a img").attr("src", ws.logo), e(".sticky-header.cloned.alternative").removeClass("alternative"), e(".sticky-header.cloned .popup-with-zoom-anim").magnificPopup({
                type: "inline",
                fixedContentPos: !1,
                fixedBgPos: !0,
                overflowY: "auto",
                closeBtnInside: !0,
                preloader: !1,
                midClick: !0,
                removalDelay: 300,
                mainClass: "my-mfp-zoom-in"
            });
            var y = document.querySelector(".sticky-header.cloned"),
                C = new Headroom(y, {
                    offset: e(".sticky-header").height(),
                    tolerance: 0
                });
            e(window).bind("load resize", function(t) {
                e(".sticky-header.cloned").removeClass("transparent alternative");
                var a = e(window).width();
                a > 1290 ? C.init() : a < 1290 && C.destroy()
            })
        }
        e(".small-only #coupon_code").on("change", function() {
            var t = e(this).val(),
                a = e(this).attr("name");
            e(".large-only").find("input[name*='" + a + "']").val(t)
        }), e(".large-only #coupon_code").on("change", function() {
            var t = e(this).val(),
                a = e(this).attr("name");
            e(".small-only").find("input[name*='" + a + "']").val(t)
        });
        var k = e(window).width();
        k < 768 && e("#related-job-container").detach().appendTo("#job-details")
    })
}(this.jQuery),
    function(e) {
        e.fn.hoverIntent = function(t, a, n) {
            var o = {
                interval: 50,
                sensitivity: 7,
                timeout: 0
            };
            o = "object" == typeof t ? e.extend(o, t) : e.isFunction(a) ? e.extend(o, {
                over: t,
                out: a,
                selector: n
            }) : e.extend(o, {
                over: t,
                out: t,
                selector: a
            });
            var i, s, r, l, d = function(e) {
                    i = e.pageX, s = e.pageY
                },
                c = function(t, a) {
                    return a.hoverIntent_t = clearTimeout(a.hoverIntent_t), Math.abs(r - i) + Math.abs(l - s) < o.sensitivity ? (e(a).off("mousemove.hoverIntent", d), a.hoverIntent_s = 1, o.over.apply(a, [t])) : (r = i, l = s, a.hoverIntent_t = setTimeout(function() {
                        c(t, a)
                    }, o.interval), void 0)
                },
                h = function(e, t) {
                    return t.hoverIntent_t = clearTimeout(t.hoverIntent_t), t.hoverIntent_s = 0, o.out.apply(t, [e])
                },
                p = function(t) {
                    var a = jQuery.extend({}, t),
                        n = this;
                    n.hoverIntent_t && (n.hoverIntent_t = clearTimeout(n.hoverIntent_t)), "mouseenter" == t.type ? (r = a.pageX, l = a.pageY, e(n).on("mousemove.hoverIntent", d), 1 != n.hoverIntent_s && (n.hoverIntent_t = setTimeout(function() {
                        c(a, n)
                    }, o.interval))) : (e(n).off("mousemove.hoverIntent", d), 1 == n.hoverIntent_s && (n.hoverIntent_t = setTimeout(function() {
                        h(a, n)
                    }, o.timeout)))
                };
            return this.on({
                "mouseenter.hoverIntent": p,
                "mouseleave.hoverIntent": p
            }, o.selector)
        }
    }(jQuery);