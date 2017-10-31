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


    $( document ).ready( function() {
        update_yotTube();
        update_instagram();
        update_twitter();

        $('#youtube_link').on('change', function(e){ update_yotTube();});
        $('#instagram_link').on('change',  function(e){update_instagram(); } );
        $('#witter_link').on('change',  function(e){update_twitter(); } );

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
