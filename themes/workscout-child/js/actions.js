/**
 * Created with JetBrains PhpStorm.
 * User: olga
 * Date: 12.10.17
 * Time: 16:35
 * To change this template use File | Settings | File Templates.
 */
( function( $ ) {
    $(document).on( 'submit', '.newhomapage_register', function(e) {

        e.preventDefault();
        var form = $(this);
        var error = false;

        var base = $(this).serialize();
        var button = $(this).find( 'input[type=submit]' );

        $(button).css('backgroundColor','#ddd');
        var data = base + '&' + button.attr("name") + "=" + button.val();
        var action = 'custom_redirect_newhomepage';
        data = data+'&action='+action;

        var request = $.ajax({
            url: ws.ajaxurl,
            data: data,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            success: function(response) {
                form.find( $( '.woocommerce-error' ) ).remove();
                $(button).css('backgroundColor',ws.theme_color);
                if (response.success == true) {
                    window.location.href = response.redirect;
                } else {
                    var output = "";
                    for (var i=0; i < response.error.length; i++) {
                        output += "<div class='woocommerce-error'>"+response.error[i]+"</div>";
                    }
                    $( '.form__erorrs' ).append(output );
                }
            }
        });

    });
} )( jQuery );

