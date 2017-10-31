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

    $(document).on( 'submit', '.small-dialog-content.woo-reg-box form.register', function(e) {
        e.preventDefault();
        var form = $(this);
        var error = false;

        var base = $(this).serialize();
        var button = $(this).find( 'input[type=submit]' );

        $(button).css('backgroundColor','#ddd');
        var data = base + '&' + button.attr("name") + "=" + button.val();
        var action = 'custom_redirect';
            data = data+'&action='+action;
        var $response = $( '#ajax-response' );

        var request = $.ajax({
            url: ws.ajaxurl,
            data: data,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            success: function(response) {

                form.find( $( '.woocommerce-error' ) ).remove();

                var $response = response;
                $(button).css('backgroundColor',ws.theme_color);

                if (response.success == true){
                    window.location.href = response.redirect;
                }else{
                    var output = "";

                    for (var i=0; i < response.error.length; i++){
                         output += "<div class='woocommerce-error'>"+response.error[i]+"</div>";
                    }
                    form.prepend(output );
                }

            }
        });

    });

    $(document).on( 'submit', '.small-dialog-content form#custom-campaign-form', function(e) {
        var magnificPopup = $.magnificPopup.instance;
        e.preventDefault();
        var form = $(this);
        var error = false;

        var base = $(this).serialize();
        var button = $(this).find( 'input[type=submit]' );

        $(button).css('backgroundColor','#ddd');

        var data = base + '&' + button.attr("name") + "=" + button.val();
        var action = 'create_custom_campaign';
        data = data+'&action='+action;
        form.find( $( '.woocommerce-error' ) ).remove();

        var request = $.ajax({
            url: ws.ajaxurl,
            data: data,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            success: function(response) {

                form.find( $( '.woocommerce-error' ) ).remove();

                var $response = response;
                $(button).css('backgroundColor',ws.theme_color);

                if (response.success == true){

                    form.find("input[type=text], textarea").val("");

                    $.magnificPopup.open({
                        items: {
                            src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup"><div class="small-dialog-headline">Success!</h2></div><div class="small-dialog-content"><p class="margin-reset">Thank you, one of our representatives will be back to you in 24 hours.</p></div></div>',
                            type: 'inline'
                        }
                    });
                    setTimeout( function(){
                        $.magnificPopup.close();
                    }, 2000);

                }else{
                    var output = "";

                    for (var i=0; i < response.error.length; i++){
                        output += "<div class='woocommerce-error'>"+response.error[i]+"</div>";
                    }
                    form.prepend(output );
                }

            }
        });

    });

    $( document ).ready( function() {

        var
            sendMsgBtn = $('.wp_job_manager_message_to_application'),
            sendOnReview = $('.wp_job_manager_review_application'),
            messageText = $('.job-manager-application-message-text');

        messageText.keyup(function(){

            var $this = $(this),
                targetForm = $this.closest('form.job-manager-application-message-form'),
                targetButton = targetForm.find('.wp_job_manager_message_to_application');

            if($this.val().length !=0){
                targetButton.removeAttr( "disabled" );
                targetButton.prop('disabled', false);
            } else{
                targetButton.attr('disabled','disabled');
                targetButton.prop('disabled', true);
            }
        });


        sendMsgBtn.click( function (e){

            var $this = $(this),
                targetForm = $this.closest('form.job-manager-application-message-form'),
                targetMessageText = $(targetForm).find('.job-manager-application-message-text'),
                msgList = $this.closest('.msg_part').find('.msg_set');

            e.preventDefault();

            var data = {
                action: 'send_message_to_candidate',
                message: targetForm.serialize()
            };

            jQuery.ajax({
                type: 'POST',
                url:  ws.ajaxurl,
                data: data,
                dataType: 'json',
                success: function( result ) {
                    if (result.success == true){
                        msgList.append( '<div class="msg msg-'+result.message_id+'"><span class="msg_meta"><i class="fa fa-commenting-o"></i> ' +result.from+'<span class="msg_data"> '+result.date+'</span></span><div class="msg_text">'+result.text+'</div></div>');
                        msgList.show();
                    }
                    targetMessageText.text('');
                    targetMessageText.val('');
                    $this.attr('disabled',true);
                    $this.prop('disabled', true);
                },
                error:	function( ) {

                }
            });
        });

        sendOnReview.click(function(e){

            e.preventDefault();
            var $this = $(this),
                targetForm = $this.closest('form.job-manager-application-review-form'),
                targetMessageText = $(targetForm).find('.application-review-msg'),
                action = 'send_on_review';

            targetForm.find( $( '.woocommerce-error' ) ).remove();

            if(typeof targetMessageText.val() != 'undefined' && targetMessageText.val().length !=0){
                var data = targetForm.serialize();

                data = data+'&action='+action;

                jQuery.ajax({
                    type: 'POST',
                    url:  ws.ajaxurl,
                    data: data,
                    dataType: 'json',
                    success: function( result ) {
                        window.location.href = window.location.href;
                    },
                    error:	function( ) {

                    }
                });
            }else{
                targetForm.prepend("<div class='woocommerce-error'>Please, add review message</div>");
            }

        });
    });
} )( jQuery );
