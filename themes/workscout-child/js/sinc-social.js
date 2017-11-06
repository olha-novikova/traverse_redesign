( function( $ ) {

    $('#save_account_details').click(  function(e) {
        e.preventDefault();

        var form = $(this).closest('.woocommerce-EditAccountForm.edit-account');
        var inputs =  form.find("input[type='text']");

        inputs.each(function(){
            var key = $(this).attr('id');
            var val = $(this).val();
            if ( val ){
                $(this).val(val.replace(/(^\w+:|^)\/\//, ''));
            }

        });

        form.submit();

    });

    function update_facebook() {

        var userProfileLink = $('#fb_link'),
            count = "<span class='count_subcr'><i class='ln  ln-icon-Boy'></i></span>",
            error = "<span class='error_msg'></span>";

        if ( userProfileLink.val().length ){

            userProfileLink.closest('.input__block').find('.count_subcr').remove();
            userProfileLink.closest('.input__block').find('.error_msg').remove();

            var  $url = userProfileLink.val();

            FB.getLoginStatus(function(response) {

                if (response.status === 'connected') {

                    var data = {
                        action: 'aj_get_fb_users_count',
                        link: $url
                    }

                    jQuery.ajax({
                        type: 'POST',
                        url:  ws.ajaxurl,
                        data: data,
                        dataType: 'json',
                        success: function( response ) {
                            if(response.success) {
                                $.each(response.data, function(key, value){
                                    userProfileLink.closest('.input__block').append(count);
                                    userProfileLink.closest('.input__block').find('.count_subcr').append(value.toString());
                                });
                            }
                            else {
                                $.each(response.data, function(key, value){
                                    userProfileLink.addClass('error');
                                    userProfileLink.before(error);
                                    userProfileLink.prev('.error_msg').text(value.toString());
                                });
                            }
                        },
                        error:	function( ) {
                        }
                    });
                }else{
                    document.getElementById('face_book_login').innerHTML = 'Please log into this app to update data';

                    $('#face_book_login').click( function(){
                        FB.login(function(response) { }, {scope: 'public_profile,user_friends'});
                        return false;
                    });
                }
            })
        }

    }

    $( document ).ready( function() {
        update_yotTube();
        update_instagram();
        update_twitter();

        window.fbAsyncInit = function() {
            FB.init({
                appId            : '1886251131695070',
                autoLogAppEvents : true,
                xfbml            : true,
                version          : 'v2.10',
                status           : true,
                cookie           : true,
                oauth            : true
            });

            update_facebook();
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        $('#youtube_link').on('change', function(e){ update_yotTube();});
        $('#instagram_link').on('change',  function(e){update_instagram(); } );
        $('#witter_link').on('change',  function(e){update_twitter(); } );
        $('#fb_link').on('change',  function(e){ update_facebook(); } );

    });

    var update_yotTube = function() {

        var $this = $('#youtube_link'),
            $url = $this.val(),
            count = "<span class='count_subcr'><i class='ln  ln-icon-Boy'></i></span>",
            error = "<span class='error_msg'></span>";

        $this.closest('.input__block').find('.count_subcr').remove();
        $this.closest('.input__block').find('.error_msg').remove();

        if ( $url != '' ){
            var data = {
                link: $url,
                action: 'aj_get_youtube_subscriber_count'
            }

            jQuery.ajax({
                type: 'POST',
                url:  ws.ajaxurl,
                data: data,
                dataType: 'json',
                success: function( response ) {
                    if(response.success) {
                        $.each(response.data, function(key, value){
                            $this.closest('.input__block').append(count);
                            $this.closest('.input__block').find('.count_subcr').append(value.toString());
                        });
                    }
                    else {
                        $.each(response.data, function(key, value){
                            $this.addClass('error');
                            $this.before(error);
                            $this.prev('.error_msg').text(value.toString());
                        });
                    }

                },
                error:	function( ) {

                }
            });
        }
    }

    var update_twitter = function() {

        var $this = $('#twitter_link'),
            $url = $this.val(),
            count = "<span class='count_subcr'><i class='ln  ln-icon-Boy'></i></span>",
            error = "<span class='error_msg'></span>";

        $this.closest('.input__block').find('.count_subcr').remove();
        $this.closest('.input__block').find('.error_msg').remove();

        if ( $url != '' ){
            var data = {
                twit_link: $url,
                action: 'aj_get_twitter_followers_count'
            }

            jQuery.ajax({
                type: 'POST',
                url:  ws.ajaxurl,
                data: data,
                dataType: 'json',
                success: function( response ) {
                    if(response.success) {
                        $.each(response.data, function(key, value){
                            $this.closest('.input__block').append(count);
                            $this.closest('.input__block').find('.count_subcr').append(value.toString());
                        });
                    }
                    else {
                        $.each(response.data, function(key, value){
                            $this.addClass('error');
                            $this.before(error);
                            $this.prev('.error_msg').text(value.toString());
                        });
                    }

                },
                error:	function( ) {

                }
            });
        }
    }

    var update_instagram = function() {

        var $this = $('#instagram_link'),
            $url = $this.val(),
            count = "<span class='count_subcr'><i class='ln  ln-icon-Boy'></i></span>",
            error = "<span class='error_msg'></span>";

        $this.closest('.input__block').find('.count_subcr').remove();
        $this.closest('.input__block').find('.error_msg').remove();

        if ( $url != '' ){
            var data = {
                insta_link: $url,
                action: 'aj_get_instagram_followers_count'
            }

            jQuery.ajax({
                type: 'POST',
                url:  ws.ajaxurl,
                data: data,
                dataType: 'json',
                success: function( response ) {
                    if(response.success) {
                        $.each(response.data, function(key, value){
                            $this.closest('.input__block').append(count);
                            $this.closest('.input__block').find('.count_subcr').append(value.toString());
                        });
                    }
                    else {
                        $.each(response.data, function(key, value){
                            $this.addClass('error');
                            $this.before(error);
                            $this.prev('.error_msg').text(value.toString());
                        });
                    }

                },
                error:	function( ) {

                }
            });
        }
    }


} )( jQuery );
