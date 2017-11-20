jQuery(document).ready(function($) {


    // Perform AJAX login on form submit
    $('#login-dialog form#workscout_login_form').on('submit', function(e){
        $('form#workscout_login_form p.status').show().text(ajax_login_object.loadingmessage);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_login_object.ajaxurl,
            data: { 
                'action': 'workscoutajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                'username': $('form#workscout_login_form #workscout_user_login').val(), 
                'password': $('form#workscout_login_form #workscout_user_pass').val(), 
                'security': $('form#workscout_login_form #security').val() },
            success: function(data){
                $('form#workscout_login_form p.status').text(data.message).removeClass('success error notification ').addClass(data.type);
                if (data.loggedin == true){
                    switch (data.role){
                        case 'employer': document.location.href = ajax_login_object.redirect_job_dashboard;
                        break;
                        case 'candidate': document.location.href = ajax_login_object.redirect_candidate_dashboard;
                        break;
                        default: document.location.href = ajax_login_object.redirecturl;
                    }
                }
            }
        });
        e.preventDefault();
    });

    // Perform AJAX register on form submit
    $('#signup-dialog form#workscout_registration_form').on('submit', function(e){
        $('form#workscout_registration_form p.status').show().text(ajax_login_object.loadingmessage);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_login_object.ajaxurl,
            data: { 
                'action': 'workscoutajaxregister', //calls wp_ajax_nopriv_ajaxlogin
                'username': $('form#workscout_registration_form #workscout_user_login').val(), 
                'email':    $('form#workscout_registration_form #workscout_user_email').val(), 
                'password': $('form#workscout_registration_form #password').val(), 
                'password_again': $('form#workscout_registration_form #password_again').val(), 
                'role':     $('form#workscout_registration_form #workscout_user_role').val(), 
                'security': $('form#workscout_registration_form #security').val() },
            success: function(data){
                $('form#workscout_registration_form p.status').text(data.message).removeClass('success error notification ').addClass(data.type);
                if (data.loggedin == true){
                    switch (data.role){
                        case 'employer': document.location.href = ajax_login_object.redirect_job_dashboard;
                        break;
                        case 'candidate': document.location.href = ajax_login_object.redirect_candidate_dashboard;
                        break;
                        default: document.location.href = ajax_login_object.redirecturl;
                    }
                }
            }
        });
        e.preventDefault();
    });

});