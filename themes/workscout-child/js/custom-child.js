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

} )( jQuery );
