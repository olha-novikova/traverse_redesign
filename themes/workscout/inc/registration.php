<?php 
/*login */
// user registration login form
// 
function workscout_registration_form() {
 
    // only show the registration form to non-logged-in members
    if(!is_user_logged_in()) {

        // check to make sure user registration is enabled
        $registration_enabled = get_option('users_can_register');
 
        // only show the registration form if allowed
        if($registration_enabled) { ?>
            <form method="post" class="register workscout_form">

                <?php do_action( 'woocommerce_register_form_start' ); ?>
               
                <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                    <p class="form-row form-row-wide">
                        <label for="reg_username"><?php _e( 'Username', 'workscout' ); ?> <span class="required">*</span>
                        <i class="ln ln-icon-Male"></i>
                        <input type="text" class="input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
                        </label>
                    </p>
                <?php endif; ?>

                <p class="form-row form-row-wide">
                    <label for="reg_email"><?php _e( 'Email address', 'workscout' ); ?> <span class="required">*</span>
                    <i class="ln ln-icon-Mail"></i><input type="email" class="input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
                    </label>
                </p>

                <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

                    <p class="form-row form-row-wide">
                        <label for="reg_password"><?php _e( 'Password', 'workscout' ); ?> <span class="required">*</span>
                        <i class="ln ln-icon-Lock-2"></i><input type="password" class="input-text" name="password" id="reg_password" />
                        </label>
                    </p>

                <?php endif; ?>

                <!-- Spam Trap -->
                <div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'workscout' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

                <?php do_action( 'woocommerce_register_form' ); ?>
                <?php do_action( 'register_form' ); ?>

                <p class="form-row">
                    <?php wp_nonce_field( 'woocommerce-register' ); ?>
                    <input type="submit" class="button" name="register" value="<?php esc_attr_e( 'Register', 'workscout' ); ?>" />
                </p>

                <?php do_action( 'woocommerce_register_form_end' ); ?>

            </form>
        <?php } else {
           _e('User registration is not enabled','workscout');
        }
   
    }
}
add_shortcode('workscout_register_form', 'workscout_registration_form');

function workscout_login_form() {
 
    if(!is_user_logged_in()) { ?>
        <form method="post" class="login workscout_form">

                <?php do_action( 'woocommerce_login_form_start' ); ?>

                <p class="form-row form-row-wide">
                    <label for="username"><?php _e( 'Username or email address', 'workscout' ); ?> <span class="required">*</span>
                    <i class="ln ln-icon-Male"></i><input type="text" class="input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
                    </label>
                </p>
                <p class="form-row form-row-wide">
                    <label for="password"><?php _e( 'Password', 'workscout' ); ?> <span class="required">*</span>
                    <i class="ln ln-icon-Lock-2"></i><input class="input-text" type="password" name="password" id="password" />
                    </label>
                </p>

                <?php do_action( 'woocommerce_login_form' ); ?>

                <p class="form-row">
                    <?php wp_nonce_field( 'woocommerce-login' ); ?>
                    <input type="submit" class="button" name="login" value="<?php esc_attr_e( 'Login', 'workscout' ); ?>" />
                    <label for="rememberme" class="inline">
                        <input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'workscout' ); ?>
                    </label>
                </p>
                <p class="lost_password">
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'workscout' ); ?></a>
                </p>

                <?php do_action( 'woocommerce_login_form_end' ); ?>

            </form>
    <?php }
       
   
}
add_shortcode('workscout_login_form', 'workscout_login_form');



function wc_custom_user_redirect( $redirect, $user ) {
    // Get the first of all the roles assigned to the user
    //$role = $user->roles[0];
    $dashboard = admin_url();
    $myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );
    
    if( $role == 'employer' ) {
        
        if(get_option( 'job_manager_job_dashboard_page_id')) { 
            $redirect_to = get_permalink(get_option( 'job_manager_job_dashboard_page_id')); 
        } else {
            $redirect_to= home_url();
        };
    } elseif ( $role == 'candidate' ) {
        if(get_option( 'resume_manager_candidate_dashboard_page_id')) { 
            $redirect_to = get_permalink(get_option( 'resume_manager_candidate_dashboard_page_id')); 
        } else {
            $redirect_to= home_url();
        };
    } elseif ( $role == 'customer' || $role == 'subscriber' ) {
        //Redirect customers and subscribers to the "My Account" page
        $redirect = $myaccount;
    } else {
        //Redirect any other role to the previous visited page or, if not available, to the home
        $redirect = wp_get_referer() ? wp_get_referer() : home_url();
    }
    return $redirect;
}
add_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2 );


/*
*
* Old WorkScout registration system (pre1.2.3)
*
*/


function workscout_old_registration_form() {
 
    // only show the registration form to non-logged-in members
    if(!is_user_logged_in()) {

        // check to make sure user registration is enabled
        $registration_enabled = get_option('users_can_register');
 
        // only show the registration form if allowed
        if($registration_enabled) {
            $output = workscout_registration_form_fields();
        } else {
            $output = __('User registration is not enabled','workscout');
        }
        return $output;
    }
}
add_shortcode('workscout_old_register_form', 'workscout_old_registration_form');

function workscout_old_login_form() {
 
    if(!is_user_logged_in()) {
         $output = workscout_login_form_fields();
    } else {
       $output = '';
    }
    return $output;
}
add_shortcode('workscout_old_login_form', 'workscout_old_login_form');


if ( ! function_exists( 'workscout_registration_form_fields' ) ) :
function workscout_registration_form_fields() {
 
    ob_start(); ?>  
        <div class="entry-header">
            <h3 class="headline margin-bottom-20"><?php esc_html_e('Register','workscout'); ?></h3>
        </div>
 
        <?php 
        // show any error messages after form submission
        workscout_show_error_messages(); ?>
 
        <form id="workscout_registration_form" class="workscout_form" action="" method="POST">
            <p class="status"></p>
            <fieldset>
                <p>
                    <label for="workscout_user_login"><?php _e('Username','workscout'); ?>
                    <i class="ln ln-icon-Male"></i><input name="workscout_user_login" id="workscout_user_login" class="required" type="text"/>
                    </label>
                </p>
                <p>
                    <label for="workscout_user_email"><?php _e('Email','workscout'); ?>
                    <i class="ln ln-icon-Mail"></i><input name="workscout_user_email" id="workscout_user_email" class="required" type="email"/>
                    </label>
                </p>
                <p>
                <?php 
                    echo '<label for="workscout_user_role">'.esc_html__('I\'m looking..','workscout').'</label>';
                    echo '<select name="workscout_user_role" id="workscout_user_role" class="input chosen-select">';
                        echo '<option selected="selected" value="candidate">'.esc_html__(".. for a job","workscout").'</option>';
                        echo '<option value="employer">'.esc_html__("..to hire","workscout").'</option>';
                    echo '</select>';
                ?>
                </p>
                <?php if( function_exists( 'gglcptch_display' ) ) { echo gglcptch_display(); } ; ?>
                <p style="display:none">
                    <label for="confirm_email"><?php esc_html_e('Please leave this field empty','workscout'); ?></label>
                    <input type="text" name="confirm_email" id="confirm_email" class="input" value="">
                </p>
                <p>
                    <input type="hidden" name="workscout_register_nonce" value="<?php echo wp_create_nonce('workscout-register-nonce'); ?>"/>
                    <input type="hidden" name="workscout_register_check" value="1"/>
                    <?php  wp_nonce_field( 'ajax-register-nonce', 'security' );  ?>
                    <input type="submit" value="<?php _e('Register Your Account','workscout'); ?>"/>
                </p>
            </fieldset>
        </form>
    <?php
    return ob_get_clean();
}
endif;

if ( ! function_exists( 'workscout_login_form_fields' ) ) :
function workscout_login_form_fields() {
 
    ob_start(); ?>
        <div class="entry-header">
            <h3 class="headline margin-bottom-20"><?php esc_html_e('Login','workscout'); ?></h3>
        </div>
        <?php  $loginpage = Kirki::get_option( 'workscout', 'pp_login_workscout_page' );  ?>

        <?php
        // show any error messages after form submission
        workscout_show_error_messages(); ?>
 
        <form id="workscout_login_form"  class="workscout_form" action="" method="post">
            <p class="status"></p>
            <fieldset>
                <p>
                    <label for="workscout_user_Login"><?php _e('Username','workscout'); ?>
                    <i class="ln ln-icon-Male"></i><input name="workscout_user_login" id="workscout_user_login" class="required" type="text"/>
                    </label>
                </p>
                <p>
                    <label for="workscout_user_pass"><?php _e('Password','workscout'); ?>
                    <i class="ln ln-icon-Lock-2"></i><input name="workscout_user_pass" id="workscout_user_pass" class="required" type="password"/>
                    </label>
                </p>
                <p>
                    <input type="hidden" id="workscout_login_nonce" name="workscout_login_nonce" value="<?php echo wp_create_nonce('workscout-login-nonce'); ?>"/>
                    <input type="hidden" name="workscout_login_check" value="1"/>
                    <?php  wp_nonce_field( 'ajax-login-nonce', 'security' );  ?>
                    <input id="workscout_login_submit" type="submit" value="<?php esc_attr_e('Login','workscout'); ?>"/>
                </p>
                <p><?php esc_html_e('Don\'t have an account?','workscout'); ?> <a href="<?php echo get_permalink($loginpage); ?>?action=register"><?php esc_html_e('Sign up now','workscout'); ?></a>!</p>
                <p><a href="<?php echo wp_lostpassword_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e('Lost Password?','workscout'); ?>"><?php esc_html_e('Lost Password?','workscout'); ?></a></p>
    
            </fieldset>
        </form>
    <?php
    return ob_get_clean();
}
endif;


// logs a member in after submitting a form
function workscout_login_member() {
    $login_system = Kirki::get_option( 'workscout', 'pp_login_form_system' );

    if($login_system == 'workscout') {
        if(isset($_POST['workscout_login_check'])  && wp_verify_nonce($_POST['workscout_login_nonce'], 'workscout-login-nonce')) {
     
            // this returns the user ID and other info from the user name
            $user =  get_user_by('login',$_POST['workscout_user_login']);
     
            if(!$user) {
                // if the user name doesn't exist
                workscout_form_errors()->add('empty_username', __('Invalid username','workscout'));

            }
     
            if(!isset($_POST['workscout_user_pass']) || $_POST['workscout_user_pass'] == '') {
                // if no password was entered
                workscout_form_errors()->add('empty_password', __('Please enter a password','workscout'));
            }
     
            if(isset($_POST['workscout_user_pass']) && !empty($_POST['workscout_user_pass'])){
                // check the user's login with their password
                if(!wp_check_password($_POST['workscout_user_pass'], $user->user_pass, $user->ID)) {
                    // if the password is incorrect for the specified user
                    workscout_form_errors()->add('empty_password', __('Incorrect password','workscout'));
                }
            }
     
            // retrieve all error messages
            $errors = workscout_form_errors()->get_error_messages();
     
            // only log the user in if there are no errors
            if(empty($errors)) {
     
                $creds = array();
                $creds['user_login'] = $_POST['workscout_user_login'];
                $creds['user_password'] = $_POST['workscout_user_pass'];
                $creds['remember'] = true;

                $user = wp_signon( $creds, false );
                // send the newly created user to the home page after logging them in
                if ( is_wp_error($user) ){
                    echo $user->get_error_message();
                } else {
                    $oUser = get_user_by( 'login', $creds['user_login'] );
                    $aUser = get_object_vars( $oUser );
                    $sRole = $aUser['roles'][0];

                    if(get_option( 'job_manager_job_dashboard_page_id')) { 
                        $job_url = get_permalink(get_option( 'job_manager_job_dashboard_page_id')); 
                    } else {
                        $job_url= home_url();
                    }; 
                    if(get_option( 'resume_manager_candidate_dashboard_page_id')) { 
                        $resume_url = get_permalink(get_option( 'resume_manager_candidate_dashboard_page_id')); 
                    } else {
                        $resume_url= home_url();
                    };
                    switch ($sRole) {
                        case 'candidate':
                            $redirect_url = $resume_url;
                            break;      
                        case 'employer':
                            $redirect_url = $job_url;
                            break;
                        
                        default:
                            $redirect_url = home_url( '/' );
                            break;
                    }
                    wp_safe_redirect( $redirect_url);
                    
                }
                exit;
            }
        }
    }
}
add_action('init', 'workscout_login_member');



// register a new user
// 
if ( ! function_exists( 'workscout_add_new_member' ) ) :
function workscout_add_new_member() {
    
    $login_system = Kirki::get_option( 'workscout', 'pp_login_form_system' );
    
    if($login_system == 'workscout') {
        if (isset( $_POST["workscout_register_check"] ) && wp_verify_nonce($_POST['workscout_register_nonce'], 'workscout-register-nonce')) {

            if ( !isset($_POST['confirm_email']) || $_POST['confirm_email'] !== '' ) {
                home_url( '/' );
                exit;
            }
            $user_login     = $_POST["workscout_user_login"];  
            $user_email     = $_POST["workscout_user_email"];
            $user_role      = $_POST["workscout_user_role"];
            
     
            if(username_exists($user_login)) {
                // Username already registered
                workscout_form_errors()->add('username_unavailable', __('Username already taken','workscout'));
            }
            if(!validate_username($user_login)) {
                // invalid username
                workscout_form_errors()->add('username_invalid', __('Invalid username','workscout'));
            }
            if($user_login == '') {
                // empty username
                workscout_form_errors()->add('username_empty', __('Please enter a username','workscout'));
            }
            if(!is_email($user_email)) {
                //invalid email
                workscout_form_errors()->add('email_invalid', __('Invalid email','workscout'));
            }
            if(email_exists($user_email)) {
                //Email address already registered
                workscout_form_errors()->add('email_used', __('Email already registered','workscout'));
            }
            
            $password = wp_generate_password();
            $password_generated = true;

            if(empty($user_role)) {
                $user_role = 'candidate';
            }
     
            $errors = workscout_form_errors()->get_error_messages();
            
            // only create the user in if there are no errors
            if(empty($errors)) {
     
                $new_user_id = wp_insert_user(array(
                        'user_login'        => $user_login,
                        'user_pass'         => $password,
                        'user_email'        => $user_email,
                        'user_registered'   => date('Y-m-d H:i:s'),
                        'role'              => $user_role,
                    )
                );
                if($new_user_id) {
                    // send an email to the admin alerting them of the registration
                    // 
                    if (function_exists('sb_we_init')) {
                        wp_new_user_notification( $new_user_id, null, 'both' );
                    } else {
                        workscout_wp_new_user_notification($new_user_id,$password);
                    }
     
                    // log the new user in
                    $creds = array();
                    $creds['user_login'] = $user_login;
                    $creds['user_password'] = $password;
                    $creds['remember'] = true;

                    $user = wp_signon( $creds, false );
                    // send the newly created user to the home page after logging them in
                    if ( is_wp_error($user) ){
                        echo $user->get_error_message();
                    } else {
                        wp_safe_redirect(  add_query_arg( 'success', 1,  home_url( '/' ) )  );
                    }
                    
                    exit;
                }
     
            }
     
        }
    }
}
endif;
add_action('init', 'workscout_add_new_member');


// used for tracking error messages
function workscout_form_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

function workscout_show_error_messages() {
    if($codes = workscout_form_errors()->get_error_codes()) {
        echo '<div class="notification closeable error">';
            // Loop error codes and display errors
           foreach($codes as $code){
                $message = workscout_form_errors()->get_error_message($code);
                echo '<span class="error">' . $message . '</span><br/>';
            }
        echo '</div>';
    }   
}

if ( ! function_exists( 'workscout_wp_new_user_notification' ) ) :
 function workscout_wp_new_user_notification($user_id, $plaintext_pass) {

    global $wpdb;
    $user = get_userdata( $user_id );

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $loginpage = Kirki::get_option( 'workscout', 'pp_login_workscout_page' );


    $message  = sprintf(__('New user registration on your site %s:','workscout'), $blogname) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s','workscout'), $user->user_login) . "\r\n\r\n";
    $message .= sprintf(__('E-mail: %s','workscout'), $user->user_email) . "\r\n";

    @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration','workscout'), $blogname), $message);

    
    $message = sprintf(__('Username: %s','workscout'), $user->user_login) . "\r\n\r\n";
    $message .= sprintf(__('Password: %s','workscout'), $plaintext_pass) . "\r\n\r\n";

    if(!empty($loginpage)){
        $message .= __('You can access your account area and change your password here ','workscout') . get_permalink($loginpage) . "\r\n\r\n";
    } else {
        $message .= __('To log into the admin area please us the following address ','workscout') . wp_login_url() . "\r\n\r\n";
        
    }
    $message .= sprintf( __('If you have any problems, please contact us at %s. ','workscout'), get_option('admin_email') ) . "\r\n\r\n";
    $message .= __('Thank you!','workscout') . "\r\n\r\n";

    wp_mail($user->user_email, sprintf(__('[%s] Your username and password info','workscout'), $blogname), $message);
    }
endif;


//change password
if ( ! function_exists( 'workscout_change_password_form' ) ) :
function workscout_change_password_form() {
    global $post;   
 
    if (is_singular()) :
        $current_url = get_permalink($post->ID);
    else :
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $current_url = $pageURL;
    endif;      
    $redirect = $current_url;
 
    ob_start();
 
        // show any error messages after form submission
        workscout_show_error_messages(); ?>
 
        <?php if(isset($_GET['password-reset']) && $_GET['password-reset'] == 'true') { ?>
            <div class="workscout_message success">
                <span><?php _e('Password changed successfully', 'workscout'); ?></span>
            </div>
        <?php } ?>
        <form id="workscout_password_form" method="POST" action="<?php echo $current_url; ?>">
            <fieldset>
                <p>
                    <label for="workscout_user_pass"><?php _e('New Password', 'workscout'); ?></label>
                    <input name="workscout_user_pass" id="workscout_user_pass" class="required" type="password"/>
                </p>
                <p>
                    <label for="workscout_user_pass_confirm"><?php _e('Password Confirm', 'workscout'); ?></label>
                    <input name="workscout_user_pass_confirm" id="workscout_user_pass_confirm" class="required" type="password"/>
                </p>
                <p>
                    <input type="hidden" name="workscout_action" value="reset-password"/>
                    <input type="hidden" name="workscout_redirect" value="<?php echo $redirect; ?>"/>
                    <input type="hidden" name="workscout_password_nonce" value="<?php echo wp_create_nonce('workscout-password-nonce'); ?>"/>
                    <input id="workscout_password_submit" type="submit" value="<?php _e('Change Password', 'workscout'); ?>"/>
                </p>
            </fieldset>
        </form>
    <?php
    return ob_get_clean();  
}
endif;
 
// password reset form
function workscout_reset_password_form() {
    if(is_user_logged_in()) {
        return workscout_change_password_form();
    }
}
add_shortcode('workscout_old_password_form', 'workscout_reset_password_form');
 
 
function workscout_reset_password() {
    // reset a users password
    if(isset($_POST['workscout_action']) && $_POST['workscout_action'] == 'reset-password') {
 
        global $user_ID;
 
        if(!is_user_logged_in())
            return;
 
        if(wp_verify_nonce($_POST['workscout_password_nonce'], 'workscout-password-nonce')) {
 
            if($_POST['workscout_user_pass'] == '' || $_POST['workscout_user_pass_confirm'] == '') {
                // password(s) field empty
                workscout_form_errors()->add('password_empty', __('Please enter a password, and confirm it', 'workscout'));
            }
            if($_POST['workscout_user_pass'] != $_POST['workscout_user_pass_confirm']) {
                // passwords do not match
                workscout_form_errors()->add('password_mismatch', __('Passwords do not match', 'workscout'));
            }
 
            // retrieve all error messages, if any
            $errors = workscout_form_errors()->get_error_messages();
 
            if(empty($errors)) {
                // change the password here
                $user_data = array(
                    'ID' => $user_ID,
                    'user_pass' => $_POST['workscout_user_pass']
                );
                wp_update_user($user_data);
                // send password change email here (if WP doesn't)
                wp_redirect(add_query_arg('password-reset', 'true', $_POST['workscout_redirect']));
                exit;
            }
        }
    }   
}
add_action('init', 'workscout_reset_password');



/* AJAX LOGIN */
if ( ! function_exists( 'workscout_ajax_login_init' ) ) :
function workscout_ajax_login_init(){

    wp_register_script('workscout-ajax-login-script', get_template_directory_uri() . '/js/ajax-login-script.min.js', array('jquery') ); 
    wp_enqueue_script('workscout-ajax-login-script');
    
    if(get_option( 'job_manager_job_dashboard_page_id')) { 
        $job_url = get_permalink(get_option( 'job_manager_job_dashboard_page_id')); 
    } else {
        $job_url= home_url();
    }; 
    if(get_option( 'resume_manager_candidate_dashboard_page_id')) { 
        $resume_url = get_permalink(get_option( 'resume_manager_candidate_dashboard_page_id')); 
    } else {
        $resume_url= home_url();
    };

    wp_localize_script( 'workscout-ajax-login-script', 'ajax_login_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => home_url(),
        'redirect_job_dashboard' => $job_url, 
        'redirect_candidate_dashboard' => $resume_url, 
        'loadingmessage' => __('Sending user info, please wait...','workscout')
    ));

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_workscoutajaxlogin', 'workscout_ajax_login' );
    add_action( 'wp_ajax_nopriv_workscoutajaxregister', 'workscout_ajax_register' );
}
endif;

// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
    add_action('init', 'workscout_ajax_login_init');
}

if ( ! function_exists( 'workscout_ajax_login' ) ) :
function workscout_ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.','workscout'), 'type'=>"error notification "));
    } else {
        $oUser = get_user_by( 'login', $info['user_login'] );
        $aUser = get_object_vars( $oUser );
        $sRole = $aUser['roles'][0];
        
        echo json_encode(
            array(
                'loggedin'=>true, 
                'message'=>__('Login successful, redirecting...','workscout'),
                'type'=>"success notification ",
                'role'=> $sRole
            )
        );
    }

    die();
}
endif;

if ( ! function_exists( 'workscout_ajax_register' ) ) :
function workscout_ajax_register(){
    
    check_ajax_referer( 'ajax-register-nonce', 'security' );
        
    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = sanitize_user($_POST['username']);
    $info['user_email'] = sanitize_email( $_POST['email']);
    $info['user_role'] = sanitize_email( $_POST['role']);
    $valid = true;
    if(username_exists( $info['user_login'])) {
        // Username already registered
         echo json_encode(array('loggedin'=>false, 'message'=>__('Username already taken','workscout'), 'type'=>"error notification " ));
         $valid = false;
        die();
    }
    if(!validate_username( $info['user_login'])) {
        // invalid username
        echo json_encode(array('loggedin'=>false, 'message'=>__('Invalid username','workscout'), 'type'=>"error notification " ));
        $valid = false;
        die();
    }
    if( $info['user_login'] == '') {
        // empty username
        echo json_encode(array('loggedin'=>false, 'message'=>__('Please enter a username','workscout'), 'type'=>"error notification " ));
        $valid = false;
        die();
    }
    if(!is_email($info['user_email'])) {
        //invalid email
        echo json_encode(array('loggedin'=>false, 'message'=>__('Invalid email','workscout'), 'type'=>"error notification " ));
        $valid = false;
        die();
    }
    if(email_exists($info['user_email'])) {
        //Email address already registered
        echo json_encode(array('loggedin'=>false, 'message'=>__('Email already registered','workscout'), 'type'=>"error notification " ));
        $valid = false;
        die();
    }

    $password = wp_generate_password();
    $password_generated = true;
    $info['user_pass'] = $password;
    // Register the user
    if($valid) {
        $user_register = wp_insert_user( $info );
        if ( is_wp_error($user_register) ){ 
            $error  = $user_register->get_error_codes() ;
            
            if(in_array('empty_user_login', $error))
                echo json_encode(array('loggedin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login')), 'type'=>"error notification " ));
            elseif(in_array('existing_user_login',$error))
                echo json_encode(array('loggedin'=>false, 'message'=>__('This username is already registered.','workscout'), 'type'=>"error notification " ));
            elseif(in_array('existing_user_email',$error))
                echo json_encode(array('loggedin'=>false, 'message'=> __('This email address is already registered.','workscout'), 'type'=>"error notification " ));

        } else {
             if (function_exists('sb_we_init')) {
                wp_new_user_notification( $user_register, null, 'both' );
            } else {
                workscout_wp_new_user_notification($user_register,$password);
            }
            $creds = array();
            $creds['user_login'] = $info['user_login'];
            $creds['user_password'] = $password;
            $creds['remember'] = true;

            $user_signon = wp_signon( $creds, false );
             if ( is_wp_error($user_signon) ){
                echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.','workscout'), 'type'=>"error notification " ));
            } else {
                wp_set_current_user($user_signon->ID); 
                 $oUser = get_user_by( 'login', $creds['user_login'] );
                $aUser = get_object_vars( $oUser );
                $sRole = $aUser['roles'][0];
                echo json_encode(array('loggedin'=>true, 'message'=>__('Registration successful, redirecting...','workscout'), 'type'=>'success notification ', 'role'=> $sRole ));
            } 
        }
    }

    die();
}
endif;

?>