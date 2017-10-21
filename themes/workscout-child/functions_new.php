<?php
/*------------------------------------ Turn off old scripts and Turn On New Ones -----------------------------------------------*/
function custom_load_scripts(){
    wp_dequeue_script( 'workscout-custom' );
    wp_deregister_script( 'workscout-custom' );

    wp_dequeue_script( 'workscout-responsive' );
    wp_deregister_script( 'workscout-responsive' );

    wp_dequeue_script( 'workscout-woocommerce');
    wp_deregister_script( 'workscout-woocommerce');

    wp_dequeue_script( 'workscout-base' );;
    wp_deregister_script( 'workscout-base' );

    wp_dequeue_script( 'workscout-style');
    wp_deregister_script( 'workscout-style');

    wp_enqueue_style('newhomepage-vendor', get_stylesheet_directory_uri().'/css/vendor.css');
    wp_enqueue_style('newhomepage-main', get_stylesheet_directory_uri().'/css/main.css');

   // wp_dequeue_script( 'jquery' );
    wp_enqueue_script( 'newhomepage-main', get_stylesheet_directory_uri() . '/js/main.min.js', array('jquery'), '1', true );
    wp_enqueue_script( 'actions', get_stylesheet_directory_uri() . '/js/actions.js', array('jquery'), '1', true );

    $ajax_url = admin_url( 'admin-ajax.php' );

    wp_localize_script( 'actions', 'ws',
        array(
            'logo'				=> Kirki::get_option( 'workscout','pp_logo_upload', ''),
            'retinalogo'		=> Kirki::get_option( 'workscout','pp_retina_logo_upload',''),
            'transparentlogo'			=> Kirki::get_option( 'workscout','pp_transparent_logo_upload', ''),
            'transparentretinalogo'		=> Kirki::get_option( 'workscout','pp_transparent_retina_logo_upload',''),
            'ajaxurl' 			=> $ajax_url,
            'theme_color' 		=> Kirki::get_option( 'workscout', 'pp_main_color' ),
            'woo_account_page'	=> get_permalink(get_option('woocommerce_myaccount_page_id')),
            'theme_url'			=> get_template_directory_uri(),

        )
    );
}

add_action('wp_enqueue_scripts', 'custom_load_scripts', 100);

function deregister_styles() {
    wp_dequeue_script( 'workscout-custom' );
    wp_deregister_script( 'workscout-custom' );

    wp_dequeue_script( 'workscout-responsive' );
    wp_deregister_script( 'workscout-responsive' );

    wp_dequeue_script( 'workscout-woocommerce');
    wp_deregister_script( 'workscout-woocommerce');

    wp_dequeue_script( 'workscout-base' );;
    wp_deregister_script( 'workscout-base' );

    wp_dequeue_script( 'workscout-style');
    wp_deregister_script( 'workscout-style');

    global $wp_styles;
    foreach ( $wp_styles->queue as $num => $name ){
        if ( $name == 'workscout-base' ||  $name == 'workscout-style' )    unset ($wp_styles->queue[$num]);
    }

}

add_action( 'wp_print_styles', 'deregister_styles', 100 );

/*------------------------------------     Registration Hook -----------------------------------------------*/
function custom_redirect_newhomepage(){

    $nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
    $nonce_value = isset( $_POST['woocommerce-register-nonce'] ) ? $_POST['woocommerce-register-nonce'] : $nonce_value;
    $response = array();
    $response['success'] = false;

    if ( wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {

        $username = '';
        $password = 'no' === get_option( 'woocommerce_registration_generate_password' ) ? $_POST['password'] : '';
        $email    = $_POST['email'];
        $firstname    = $_POST['firstname'];
        $lastname    = $_POST['lastname'];

        try {

            $validation_error = new WP_Error();
            $validation_error = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );

            $error = array();

            if ( $validation_error->get_error_code() ) {
                $error[] = $validation_error->get_error_message();;
            }
            // Anti-spam trap
            if ( ! empty( $_POST['email_2'] ) ) {
                $error[] = __( 'Anti-spam field was filled in.', 'woocommerce' ) ;
            }

            if ( !isset($_POST['role']) || $_POST['role']=='' ){
                $error[] = __('Please, select who you are');
            }

            if ( !isset($_POST['firstname']) || $_POST['firstname']=='' ){
                $error[] = __('The first name cannot be empty');
            }

            if ( !isset($_POST['lastname']) || $_POST['lastname']=='' ){
                $error[] = __('The last name cannot be empty');
            }

            if ( !isset($_POST['agreement']) || $_POST['agreement']=='' ){
                $error[] = __('You should agree to the Traverse Terms of Service');
            }

            if ( count($error ) == 0){
                $new_customer = wc_create_new_customer( sanitize_email( $email ), wc_clean( $username ), $password );

                wp_update_user(  array( 'ID' => $new_customer, 'first_name' => wc_clean($firstname),  'last_name' => wc_clean($lastname) ) );


                if ( is_wp_error( $new_customer ) ) {
                    $error[] =  $new_customer->get_error_message();
                }
                if ( apply_filters( 'woocommerce_registration_auth_new_customer', true, $new_customer ) ) {
                    wc_set_customer_auth_cookie( $new_customer );
                }

                if ( !is_wp_error( $new_customer ) ) {
                    $response['success'] = true;

                    $myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );

                    $role = $_POST['role'];

                    if( $role == 'employer' ) {

                        $redirect= $myaccount.'/edit-account?success=1';

                    } elseif ( $role == 'candidate' ) {
                        $redirect= $myaccount.'/edit-account?success=1';
                    } elseif ( $role == 'customer' || $role == 'subscriber' ) {
                        $redirect = $myaccount;
                    } else {

                        $redirect = wp_get_referer() ? wp_get_referer() : home_url();
                    }

                    $response['redirect'] = $redirect;
                }
            }
        } catch ( Exception $e ) {
            $error[] = $e->getMessage();
        }
    }

    $response['error'] = $error;
    echo json_encode($response);
    wp_die();
}

add_action('wp_ajax_custom_redirect_newhomepage', 'custom_redirect_newhomepage');
add_action('wp_ajax_nopriv_custom_redirect_newhomepage', 'custom_redirect_newhomepage');


