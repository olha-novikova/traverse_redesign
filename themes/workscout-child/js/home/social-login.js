(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/tr_TR/sdk.js#xfbml=1&version=v2.4&appId=***********";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

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
};
( function( $ ) {
    $( document ).ready( function() {

        function authorizeMe(){
            FB.api('/me?fields=email', function(response) {
                if ( response.email !== ''){

                    var data = {
                        action: 'aj_fb_login',
                        security: $('.login #security').val(),
                        email: response.email
                    };

                    jQuery.ajax({
                        type: 'POST',
                        url:  ws.ajaxurl,
                        data: data,
                        dataType: 'json',
                        success: function( response ) {
                            if( response.loggedin === true ) {
                                if ( response.redirect )
                                    window.location.href = response.redirect;
                                else
                                    window.location.href = window.location.href;
                            }else{
                                $.magnificPopup.open({
                                    items: {
                                        src:'<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                                            '<div class="small-dialog-headline"><h2><?php esc_html_e("Sorry!","workscout"); ?></h2></div>'+
                                            '<div class="small-dialog-content"><p>We couldn\'t find user matched your profile. Please, make sure you have an account with same email</p></div>'+
                                            '</div>',
                                        type: 'inline'
                                    }
                                });
                            }
                        }
                    });
                }else{
                    $.magnificPopup.open({
                        items: {
                            src:'<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                                '<div class="small-dialog-headline"><h2><?php esc_html_e("Sorry!","workscout"); ?></h2></div>'+
                                '<div class="small-dialog-content"><p>We couldn\'t get your email from the Facebook.com. Maybe, you need to grant the permissions for getting an email from your account</p></div>'+
                                '</div>',
                            type: 'inline'
                        }
                    });
                }
            });
        }

        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                authorizeMe();
            } else {
                FB.login(
                    function( response ) {
                        if ( response.authResponse ) {
                            authorizeMe();
                        } else {
                            $.magnificPopup.open({
                                items: {
                                    src:'<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                                        '<div class="small-dialog-headline"><h2><?php esc_html_e("Sorry!","workscout"); ?></h2></div>'+
                                        '<div class="small-dialog-content"><p>User cancelled login or did not fully authorize.</p></div>'+
                                        '</div>',
                                    type: 'inline'
                                }
                            });
                        }
                    },
                    {scope:'public_profile,email'}
                );
            }
        }

        $('#fb-login-button').click( function( e){
            e.preventDefault();

            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });

        });
    });
} )( jQuery );