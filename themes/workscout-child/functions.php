<?php

add_action( 'wp_enqueue_scripts', 'workscout_enqueue_styles' );
function workscout_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css',array('workscout-base','workscout-responsive','workscout-font-awesome') );

}

function workscout_child_dequeue_script() {
    wp_dequeue_script( 'workscout-custom' );
}

add_action( 'wp_print_scripts', 'workscout_child_dequeue_script', 100 );

function workscout_child_scripts(){
    wp_enqueue_script( 'workscout-custom-parent', get_stylesheet_directory_uri() . '/js/custom.parent.js', array('jquery'), '20150705', true );
    wp_enqueue_script( 'workscout-custom-child', get_stylesheet_directory_uri() . '/js/custom-child.js', array('jquery' ,'workscout-custom-parent'), '20150705', true );

    $ajax_url = admin_url( 'admin-ajax.php' );

    wp_localize_script( 'workscout-custom-child', 'ws',
        array(
            'ajaxurl' 			=> $ajax_url,
            'woo_account_page'	=> get_permalink(get_option('woocommerce_myaccount_page_id')),
            'theme_url'			=> get_template_directory_uri(),
        )
    );

}

add_action( 'wp_enqueue_scripts', 'workscout_child_scripts' );

function overwrite_shortcode() {
    include_once get_stylesheet_directory() . '/inc/spotlight_jobs_custom.php';
    include_once get_stylesheet_directory() . '/inc/spotlight_resumes_custom.php';
    include_once get_stylesheet_directory() . '/inc/jobs_custom.php';
    include_once get_stylesheet_directory() . '/inc/resume-output.php';

    remove_shortcode('spotlight_jobs');
    remove_shortcode('spotlight_resumes');
    remove_shortcode('jobs');

    add_shortcode( 'spotlight_jobs', 'spotlight_jobs_custom' );
    add_shortcode( 'spotlight_resumes', 'spotlight_resumes_custom' );
    add_shortcode( 'jobs', 'jobs_custom' );


}

add_action( 'wp_loaded', 'overwrite_shortcode' );
add_action( 'manage_job_listing_posts_custom_column', 'job_custom_columns', 2 );

include_once get_stylesheet_directory() . '/inc/brand-functions.php';

//remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

function remove_parent_theme_features() {
    remove_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2 );
    remove_action( 'wp_loaded', array( 'WP_Job_Manager_Applications_Dashboard', 'edit_handler' ) );
}

add_action( 'after_setup_theme', 'remove_parent_theme_features', 10 );

function workscout_child_setup() {

    register_nav_menus( array(
        'without_login_menu' => esc_html__( 'Without Login Menu', 'workscout' ),

    ) );

}

add_action( 'after_setup_theme', 'workscout_child_setup' );



function get_the_resume_categories( $post = null ) {
    $post = get_post( $post );
    if ( $post->post_type !== 'resume' )
        return '';

    if ( ! get_option( 'resume_manager_enable_categories' ) )
        return '';

    $categories = wp_get_object_terms( $post->ID, 'resume_category', array( 'fields' => 'names' ) );

    if ( is_wp_error( $categories ) ) {
        return '';
    }

    return $categories;
}

add_action( 'wp_loaded',  'application_edit_handler'  );

function application_edit_handler() {
    if ( ! empty( $_POST['wp_job_manager_edit_application'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'edit_job_application' ) ) {
        global $wp_post_statuses;

        $application_id = absint( $_POST['application_id'] );

        $application = get_post( $application_id );

        if ( ! $application ) {
            return false;
        }

        $job = get_post( $application->post_parent );

        // Permissions
        if ( ! $job || ! $application || $application->post_type !== 'job_application' || $job->post_type !== 'job_listing' || !job_manager_user_can_edit_job( $job->ID ) ) {
            return false;
        }

        $application_status = sanitize_text_field( $_POST['application_status'] );

        if ( array_key_exists( $application_status, $wp_post_statuses ) ) {
            wp_update_post( array(
                'ID'          => $application_id,
                'post_status' => $application_status
            ) );
        }

        if ( $application_status == 'completed' ){
            $candidate_application_author  =  get_post_meta( $application_id, '_candidate_user_id', true );

            /* ______________________ update total count of completed applications ___________________________*/

            $resume_id = get_post_meta( $application_id, '_resume_id', true );
            $complete_application_count = get_post_meta( $resume_id, '_finished_companies', true );
            $complete_application_count = intval( $complete_application_count ) ? intval( $complete_application_count ):0;

            update_post_meta( $resume_id, '_finished_companies', $complete_application_count +1 );

            /* ______________________ update available amount for user ___________ ___________________________*/

            $job_price          = get_post_meta(  $job->ID , '_targeted_budget', true );
            if ( !$job_price ) $job_price = get_post_meta($job->ID, 'Budget_for_the_influencer', true );

            if ( $candidate_application_author && $job_price ){
                $current_deposit = get_user_meta($candidate_application_author, '_available_money', true );

                if ( !get_user_meta( $candidate_application_author, '_available_money_for_'. $application_id, true ) ){

                    update_user_meta( $candidate_application_author, '_available_money_for_'. $application_id, $job_price );
                    update_user_meta( $candidate_application_author, '_available_money', $current_deposit + $job_price );

                }

            }
        }

    }
}

add_action( 'after_setup_theme', 'theme_register_nav_menu' );

function theme_register_nav_menu() {

    register_nav_menu( 'footer', 'Main Footer Menu' );
}


function get_company_meta_logo( $post, $size = 'thumbnail' ){
    $post = get_post( $post );

    if ( $post->post_type !== 'job_listing' ) {
        echo '';
    }

    $user_id = $post->post_author;

    $logo =  get_user_meta( $user_id, 'logo', true );

    $dir = wp_get_upload_dir();

    $file_logo = $dir['baseurl']."/users/".$logo;

    if ( ! empty( $file_logo ) && ( strstr( $file_logo, 'http' ) || file_exists( $file_logo ) ) ) {
        echo '<img class="company_logo profile__background__image" src="' . esc_attr( $file_logo ) . '" alt="Company logo" />';
    } else  {
        echo '<img class="company_logo profile__background__image" src="' . esc_attr( apply_filters( 'job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png' ) ) . '" alt="Company logo" />';
    }
}

function get_company_logo_url( $post, $size = 'thumbnail' ){
    $post = get_post( $post );

    if ( $post->post_type !== 'job_listing' ) {
        return;
    }

    $user_id = $post->post_author;

    $logo =  get_user_meta( $user_id, 'logo', true );

    $dir = wp_get_upload_dir();

    $file_logo = $dir['baseurl']."/users/".$logo;

    if ( ! empty( $file_logo ) && ( strstr( $file_logo, 'http' ) || file_exists( $file_logo ) ) ) {
        return  $file_logo;
    }

    return;
}
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

add_action('init', 'myStartSession', 1);
function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}

function clear_cart() {
    if( function_exists('WC') ){
        WC()->cart->empty_cart();
    }
}
add_action('wp_logout', 'clear_cart');

/**********************************Adding status to the job applicatioins ***********************************/


add_filter( 'job_application_statuses', 'add_new_job_application_status' );

function add_new_job_application_status( $statuses ) {
    $statuses['hired'] = _x( 'In Progress', 'job_application', 'wp-job-manager-applications' );
    $statuses['completed'] = _x( 'Completed', 'job_application', 'wp-job-manager-applications' );
    $statuses['in_review'] = _x( 'In review', 'job_application', 'wp-job-manager-applications' );

    unset($statuses['interviewed']);
    unset($statuses['offer']);

    return $statuses;
}
/***********************************************************************************************************/


/******************** shortcode for the applications with status In Progress / Completed *******************/

function get_candidate_projects()
{
    global $wpdb;
    // If user is not logged in, abort
    if ( ! is_user_logged_in() ) {
        do_action( 'job_manager_job_applications_past_logged_out' );
        return;
    }

    $args = apply_filters( 'job_manager_job_applications_past_args', array(
        'post_type'           => 'job_application',
        'post_status'         => array_keys( get_job_application_statuses() ),//array_keys( get_job_application_statuses() )array('In Progress','Completed')
        'posts_per_page'      => 25,
        'offset'              => ( max( 1, get_query_var('paged') ) - 1 ) * 25,
        'ignore_sticky_posts' => 1,
        'meta_key'            => '_candidate_user_id',
        'meta_value'          => get_current_user_id(),
    ) );

    $applications = new WP_Query( $args );

    ob_start();

    if ( $applications->have_posts() ) {
        get_job_manager_template( 'project-applications.php', array( 'applications' => $applications->posts, 'max_num_pages' => $applications->max_num_pages ), 'wp-job-manager-applications', JOB_MANAGER_APPLICATIONS_PLUGIN_DIR . '/templates/' );
    } else {
        get_job_manager_template( 'past-applications-none-projects.php', array(), 'wp-job-manager-applications', JOB_MANAGER_APPLICATIONS_PLUGIN_DIR . '/templates/' );
    }

    //return ob_get_clean();
}

add_shortcode('candidate_projects','get_candidate_projects');

/*************************************************************************************************************/

//// Send an email to the employer when a job listing is approved

function listing_published_send_email($post_id) {
    if( 'job_listing' != get_post_type( $post_id ) ) {
        return;
    }
    $post = get_post($post_id);
    $author = get_userdata($post->post_author);

    $message = "
	  Hi ".$author->display_name.",
	  Your listing, ".$post->post_title." has just been approved at ".get_permalink( $post_id ).". Well done!
	";
    @wp_mail($author->user_email, "Your job listing is online", $message);
}
add_action('pending_to_publish', 'listing_published_send_email');
add_action('pending_payment_to_publish', 'listing_published_send_email');

//////// Send an email to the employer when a job listing expires

function listing_expired_send_email($post_id) {
    $post = get_post($post_id);
    $author = get_userdata($post->post_author);

    $message = "
      Hi ".$author->display_name.",
      Your listing, ".$post->post_title." has now expired: ".get_permalink( $post_id );
    @wp_mail($author->user_email, "Your job listing has expired", $message);
}
add_action('expired_job_listing', 'listing_expired_send_email');

/////////// Send an email to the candidate when their resume is approved

function resume_published_send_email($post_id) {
    if( 'resume' != get_post_type( $post_id ) ) {
        return;
    }
    $post = get_post($post_id);
    $author = get_userdata($post->post_author);

    $message = "
      Hi ".$author->display_name.",
      Your resume, ".$post->post_title." has just been approved at ".get_permalink( $post_id ).". Well done!
   ";
    @wp_mail($author->user_email, "Your resume is online", $message);
}
add_action('pending_to_publish', 'resume_published_send_email');
add_action('pending_payment_to_publish', 'resume_published_send_email');


/**
 * Snippet Name: Redirect to homepage after logout
 * Snippet URL: http://www.wpcustoms.net/snippets/redirect-homepage-logout/
 */
function wpc_auto_redirect_after_logout(){
    wp_redirect( home_url() );
    exit();
}
add_action('wp_logout','wpc_auto_redirect_after_logout');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

add_action( 'job_manager_update_job_data', 'update_employer_woocommerce_fields', 100, 2 );

function update_employer_woocommerce_fields( $job_id, $values ){
    $user_id = get_current_user_id();
    $user = get_userdata( $user_id );

    $job_company_name = isset( $_POST['job_company_name'] ) ? sanitize_text_field( $_POST['job_company_name'] ) : false;

    if( $job_company_name ) update_post_meta( $job_id, '_company_name', $job_company_name );

}

add_action( 'transition_post_status',  'transition_post_status_for_multiply_job' , 20, 3 );

function transition_post_status_for_multiply_job( $new_status, $old_status, $post ) {
    if ( 'job_application' !== $post->post_type ) {
        return;
    }

    $job_id = wp_get_post_parent_id( $post->ID );

    $nubmer_influencer_possible = get_post_meta($job_id, '_applications_number', true );

    if ('hired' === $new_status ){

        $args = array(
            'post_type'      => 'job_application',
            'post_status'    => 'hired',
            'posts_per_page' => -1,
            'post_parent'    => $job_id
        );

        $existing_applications = get_posts($args);

        if ( count ($existing_applications) >= $nubmer_influencer_possible ){
            update_post_meta( $job_id, '_filled', 1 );
        }else{
            update_post_meta( $job_id, '_filled', 0 );
        }
    }
}


function get_employer_account_balance_info($user_id){

    $args = array(
        'post_type'     => 'job_listing',
        'post_status'   => 'any',
        'posts_per_page'   => -1,
        'author'        => $user_id,
        'fields'        => 'ids'
    );

    $jobs_query = new WP_Query( $args );

    if ($jobs_query -> have_posts())  $jobs = $jobs_query -> posts; else __return_false();

    $args_1 = apply_filters( 'job_manager_job_applications_past_args', array(
        'post_type'           => 'job_application',
        'post_status'         => 'completed',
        'posts_per_page'      => -1,
        'ignore_sticky_posts' => 1,
        'post_parent__in'     => $jobs
    ) );


    $applications = new WP_Query( $args_1 );

    $applications_list = array();

    if ( $applications ->have_posts()){
        while ( $applications ->have_posts() ) {
            $applications -> the_post();

            $application_id     = $applications->post ->ID;
            $application_status = get_post_status( $application_id );

            $job_id             = wp_get_post_parent_id( $application_id );
            $job                = get_post( $job_id );

            if ( get_post_status($job_id) == 'publish' ){
                $job_link = get_permalink($job_id);
            }
            $job_name           = get_post_meta( $application_id, '_job_applied_for', true );
            $job_author_id      = get_post_meta( $application_id, '_job_author', true );
            $job_title          = ( $job_link )?( '<a href="'.$job_link.'">'.$job_name.'</a>' ):$job_name;
            $job_price          = get_post_meta( $job_id, '_targeted_budget', true );
            $resume_id          = get_job_application_resume_id( $application_id );
            $resume_title       = get_the_title( $resume_id );

            if ( !$job_price ) $job_price = get_post_meta($job_id, 'Budget_for_the_influencer', true );

            $current_apl = array();

            $current_apl['application_id']      = $application_id;
            $current_apl['application_status']  = $application_status;
            $current_apl['job_status']          = get_post_status($job_id);
            $current_apl['job_id']              = $job_id;
            $current_apl['job_title']           = $job_title;
            $current_apl['job_price']           = $job_price;
            $current_apl['influencer_id']       = $resume_id;
            $current_apl['currency']            = get_woocommerce_currency_symbol();

            $applications_list[] = $current_apl;

            unset($application_id);
            unset($job_id);
            unset($job_link);

        }
        wp_reset_postdata();
    }
    return $applications_list;

}

function get_candidate_account_balance_info($user_id){

    $args = apply_filters( 'job_manager_job_applications_past_args', array(
        'post_type'           => 'job_application',
        'post_status'         => array_keys( get_job_application_statuses() ),
        'posts_per_page'      => -1,
        'ignore_sticky_posts' => 1,
        'meta_key'            => '_candidate_user_id',
        'meta_value'          => $user_id,
    ) );
    $applications_list = array();
    $applications = new WP_Query( $args );
    if ( $applications ->have_posts()){
        while ( $applications ->have_posts() ) {
            $applications -> the_post();

            $application_id     = $applications->post ->ID;
            $application_status = get_post_status( $application_id );

            $job_id             = wp_get_post_parent_id( $application_id );
            $job                = get_post( $job_id );

            if ( get_post_status($job_id) == 'publish' ){
                $job_link = get_permalink($job_id);
            }
            $job_name           = get_post_meta( $application_id, '_job_applied_for', true );
            $job_author_id      = get_post_meta( $application_id, '_job_author', true );
            $job_title          = ( $job_link )?( '<a href="'.$job_link.'">'.$job_name.'</a>' ):$job_name;
            $job_price          = get_post_meta( $job_id, '_targeted_budget', true );

            if ( !$job_price ) $job_price = get_post_meta($job_id, 'Budget_for_the_influencer', true );

            $current_apl = array();

            $current_apl['application_id']      = $application_id;
            $current_apl['application_status']  = $application_status;
            $current_apl['job_status']          = get_post_status($job_id);
            $current_apl['job_id']              = $job_id;
            $current_apl['job_title']           = $job_title;
            $current_apl['job_price']           = $job_price;

            $applications_list[] = $current_apl;

            unset($application_id);
            unset($job_id);
            unset($job_link);

        }
        wp_reset_postdata();
    }
    return $applications_list;

}


function get_candidate_cash_out_sum($user_id){

    $args = apply_filters( 'job_manager_job_applications_past_args', array(
        'post_type'           => 'job_application',
        'post_status'         => array('hired','in_review','completed'),
        'posts_per_page'      => -1,
        'ignore_sticky_posts' => 1,
        'meta_key'            => '_candidate_user_id',
        'meta_value'          => $user_id,
    ) );

    $applications = new WP_Query( $args );
    $available_cash = 0;
    if ( $applications ->have_posts()){
        while ( $applications ->have_posts() ) {
            $applications -> the_post();

            $application_id     = get_the_ID();
            $application        = get_post( $application_id );
	          $application_status = $application->post_status;
            $job_id             = wp_get_post_parent_id( $application_id );
            $job                = get_post( $job_id );

            //if ( !$job ) return false;
            $job_price                    = get_post_meta( $job_id, '_targeted_budget', true );
            if ( !$job_price ) $job_price = get_post_meta($job_id, 'Budget_for_the_influencer', true );

            if ($application_status != 'completed') {
	            $available_cash += $job_price * HIRED_PERCENTAGE;
            } else {
	            $available_cash += $job_price * INFLUENCER_CUT;
            }

        }
        wp_reset_postdata();

    }

    update_user_meta( $user_id, '_available_money', $available_cash );

    $sum_in_log = get_user_meta( $user_id, '_available_money', true );

    return $sum_in_log;

}

if ( ! function_exists( 'get_application_id_user_has_applied_for_job' ) ) {

    function get_application_id_user_has_applied_for_job( $user_id, $job_id ) {
        if ( ! $user_id ) {
            return false;
        }
        $application=  get_posts( array(
            'post_type'      => 'job_application',
            'post_status'    => array_merge( array_keys( get_job_application_statuses() ), array( 'publish' ) ),
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'post_parent'    => $job_id,
            'meta_query'     => array(
                array(
                    'key' => '_candidate_user_id',
                    'value' => absint( $user_id )
                )
            )
        ) ) ;

        if ( count($application)>0 ){
            return $application[0];
        }
    }
}



if ( ! function_exists( 'brand_has_listing' ) ) {

    function brand_has_listing(  ) {

        $current_user_id = get_current_user_id();

        $user_meta = get_userdata($current_user_id);

        $user_roles = $user_meta->roles[0];

        if( $user_roles != "candidate" && $user_roles != "guest" ){

            $query_args = array(
                'post_type'              => 'job_listing',
                'post_status'            => 'publish',
                'ignore_sticky_posts'    => 1,
                'posts_per_page'         => -1,
                'author'                 => $current_user_id
            );

            $result = new WP_Query( $query_args );

            if ( $result -> have_posts() ) return $result->post_count;

        } else
            return false;

    }
}


if ( ! function_exists( 'get_brand_listings_list' ) ) {

    function get_brand_listings_list( $select_view = false ) {
        global $post;

        $current_resume_id = $post->ID;
        $candidate_id     = get_post_field( 'post_author', $current_resume_id );

        $current_user_id = get_current_user_id();

        $user_meta = get_userdata($current_user_id);

        $user_roles = $user_meta->roles[0];

        if( $user_roles != "candidate" && $user_roles != "guest" ){

            $query_args = array(
                'post_type'              => 'job_listing',
                'post_status'            => 'publish',
                'ignore_sticky_posts'    => 1,
                'posts_per_page'         => -1,
                'author'                 => $current_user_id
            );

            $result = new WP_Query( $query_args );

            if ( $result -> have_posts()  ){

                if ( !$select_view ) return $result;

                else {

                    $select_view = '<select name="job_id">';

                    while ( $result ->have_posts()  ) :

                        $result->the_post();

                        $theid = get_the_ID();
                        $thename = get_the_title();

                        $select_view .= '<option value="'.$theid.'">'.$thename;
                        if (user_has_applied_for_job( $candidate_id, $theid ) ) $select_view .=' <b> - applied for this job</b>';
                        $select_view .='</option>';

                    endwhile;

                    wp_reset_postdata();

                    $select_view .= '</select>';

                    return $select_view;
                }
            }

        }

        return false;

    }
}

include_once get_stylesheet_directory() . '/inc/socilal-apis.php';
include_once get_stylesheet_directory() . '/inc/job-listing-handler.php';
include_once get_stylesheet_directory() . '/inc/account-settings.php';

function send_on_review (){

    if ( ! empty( $_POST['wp_job_manager_review_application'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'edit_job_application' ) ) {

        global $wp_post_statuses;
        $response = array();
        $response['success'] = false;

        $application_id = absint( $_POST['application_id'] );

        $application = get_post( $application_id );

        if ( $application && $application->post_type == 'job_application') {
            $application_status = sanitize_text_field( $_POST['application_status'] );

            if ( array_key_exists( $application_status, $wp_post_statuses ) ) {
                wp_update_post( array(
                    'ID'          => $application_id,
                    'post_status' => $application_status
                ) );
            }

            $review_text = sanitize_text_field(trim($_POST['application-review-msg']));

            update_post_meta($application_id, '_review_msg', $review_text);
            $response['test'] = $review_text;
            $response['success'] = true;

        }

        echo json_encode($response);
        wp_die();

    }
}

add_action('wp_ajax_send_on_review', 'send_on_review');

add_action('init', 'paypal_request_payment');

function paypal_request_payment(){

    if(isset($_POST['action']) && $_POST['action'] == 'send_payment_request' && wp_verify_nonce($_POST['r_nonce'], 'r-nonce')) {

        $paypal_email = $_POST['payout_destination'];
        $paypal_amount = $_POST['amount'];
        $success = 1;

        $_SESSION['error'] = array();

        if ( !preg_match('/\$?((\d{1,4}(,\d{1,3})*)|(\d+))(\.\d{2})?\$?/', $paypal_amount)) {

            $_SESSION['error'][] = 'Please verify amount';
            $success = 0;

        }

        $paypal_amount = preg_replace('/\$/', '', $paypal_amount);


        if ( !is_numeric($paypal_amount) ){
            $_SESSION['error'][] = 'Please verify amount';
            $success = 0;
        }

        $user = wp_get_current_user();
        $currency = get_woocommerce_currency_symbol();

        if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) {

            $available_cash = get_candidate_cash_out_sum($user->ID);

        } else {
            $_SESSION['error'][] .= 'You don\'t have permission for this operation\n';
            $success = 0;
        }


        if ( $available_cash < $paypal_amount) {
            $_SESSION['error'][] .= 'Available amount is less then Requested amount';
            $success = 0;
        }

        if ( $success  ){
            $message = "Hi,\n
	        Your have new cash request from ".$user->first_name." ".$user->last_name.".\n
            Available amount is ".$currency.$available_cash.".\n
            Requested amount is ".$currency.$paypal_amount.".\n
            PayPal email is ".$paypal_email.".\n
            Date of request is ".date('F j, Y, g:i a')."\n
	        ";

            $headers = "Content-Type: text/html; charset=UTF-8\n";
            @wp_mail('admin@jrrny.com', "New Cash Request", $message);
           // @wp_mail('olha.novikova@gmail.com', "New Cash Request", $message, $headers);

            $_SESSION['success'] = "OK";
        }

    }
}

function workscout_manage_action_icons_custom($val){
    switch ($val) {

        case 'view':
            $icon = '<i class="fa fa-check-circle-o"></i> ';
            break;
        case 'email':
            $icon = '<i class="fa fa-envelope"></i> ';
            break;
        case 'toggle_status':
            $icon = '<i class="fa fa-eye-slash"></i> ';
            break;
        case 'delete':
            $icon = '<i class="fa fa-remove"></i> ';
            break;
        case 'hide':
            $icon = '<i class="fa fa-eye-slash"></i> ';
            break;
        case 'edit':
            $icon = '<i class="fa fa-pencil"></i> ';
            break;
        case 'mark_filled':
            $icon = '<i class="fa  fa-check "></i> ';
            break;
        case 'publish':
            $icon = '<i class="fa  fa-eye "></i> ';
            break;
        case 'mark_not_filled':
            $icon = '<i class="fa  fa-minus "></i> ';
            break;
        case 'reexpiries':
            $icon = '<i class="fa fa-clock-o "></i> ';
            break;
        default:
            $icon = '';
            break;
    }
    return $icon;
}

add_action( 'job_manager_job_dashboard_do_action_reexpiries', 're_expiries_listing', 10, 1);

function re_expiries_listing(){
    if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'job_manager_my_job_actions' ) ) {

        $action = sanitize_title( $_REQUEST['action'] );
        $job_id = absint( $_REQUEST['job_id'] );

        try {
            // Get Job
            $job    = get_post( $job_id );
            // Check ownership
            if ( ! job_manager_user_can_edit_job( $job_id ) ) {
                throw new Exception( __( 'Invalid ID', 'wp-job-manager' ) );
            }

            $duration = get_post_meta( $job_id, '_job_duration', true );                // Get duration from the product if set...

            if ( ! $duration ) {
                $duration = absint( get_option( 'job_manager_submission_duration' ) );  // ...otherwise use the global option
            }

            $current_date = date('Y-m-d');

            $job_expires = get_post_meta($job_id, '_job_expires', true);                // Listing Duration is 30 days for all listings be default

            $job_deadline = get_post_meta($job_id, '_job_deadline', true);              // Deadline for applicants. The listing will end automatically after this date.

            $count = get_job_application_count( $job->ID );                             // get count of all applications for this job

            if ( $count > 0 ) return;                                                   // if jab already has an application - drop it

            if ( strtotime($job_expires) > strtotime($current_date) ) return;           // if listing doesn't expired yet - drop it

            if ( $duration && $job_expires) {
                $new_job_expires = date( 'Y-m-d', strtotime($job_expires. "+{$duration} days" ) ); // add new job duration to this job expires date
                update_post_meta($job_id, '_job_expires', $new_job_expires);
                update_post_meta($job_id, '_application_deadline', $new_job_expires);

                wp_update_post( array(
                    'ID'          => $job_id,
                    'post_status' => 'publish'
                ) );

            }else return;

            if (  strtotime($job_deadline) < strtotime($current_date) ){
                update_post_meta($job_id, '_job_deadline', $new_job_expires);
            }

            remove_query_arg(
                array( 'action', 'job_id', '_wpnonce' )
            );

          /*  get_job_manager_template( 'job_manager/job-prolng.php',
                array(
                    'form'               => 'job-prolong',
                    'job_id'             => absint( $job_id ) ,
                    'action'             => 'job_prolong',
                    'submit_button_text' =>  __( 'Change Expiries', 'wp-job-manager' )
                )
            );*/
        }
        catch ( Exception $e ) {

        }


    }
    wp_redirect(get_permalink());
}


/* New Homepage 02.10.2017 */

function remove_all_styles() {
    global $wp_styles;
	if (isset($GLOBALS["header_type"]) && $GLOBALS["header_type"]=="newhomepage" )
	{
        global $wp_styles;
        if ( is_front_page() )
            $wp_styles->queue = array(0 => "admin-bar",1 => "newhomepage-vendor", 2 => "newhomepage-main" );
        else{
            foreach ( $wp_styles->queue as $num => $name ){
                if ( $name == 'workscout-base' ||  $name == 'workscout-style' || $name == 'workscout-woocommerce' )    unset ($wp_styles->queue[$num]);
            }
        }
	}
}
add_action('wp_print_styles', 'remove_all_styles', 100);


add_action('wp_ajax_aj_do_estimate', 'aj_do_estimate');

function aj_do_estimate(){
    $user = wp_get_current_user();
    $response = array();

    $budget = $_POST['target_budget'];
    $meta_query = array('relation' => 'AND');

    $meta_use = false;

    if ( isset($_POST['fb_channel']) && $_POST['fb_channel'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_fb_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( isset($_POST['ig_channel']) && $_POST['ig_channel'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_instagram_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( isset($_POST['yt_channel']) && $_POST['yt_channel'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_youtube_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( isset($_POST['tw_channel']) && $_POST['tw_channel'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_twitter_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( isset($_POST['micro_exclude']) && $_POST['micro_exclude'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_audience',
            'compare'   => '>=',
            'value'     => '50000',
            'type'      => 'NUMERIC'
        );
        $meta_use = true;
    }

    if ( isset($_POST['growth_exclude']) && $_POST['growth_exclude'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_audience',
            'compare'   => '>=',
            'value'     => '500000',
            'type'      => 'NUMERIC'
        );
        $meta_use = true;
    }

    if ( isset($_POST['pro_exclude']) && $_POST['pro_exclude'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_audience',
            'compare'   => '<',
            'value'     => '500000',
            'type'      => 'NUMERIC'
        );
        $meta_use = true;
    }

    $args = array(
        'post_type'           => 'resume',
        'post_status'         => array( 'publish'),
        'ignore_sticky_posts' => 1,
        'orderby'             => 'ASC',
        'order'               => 'date',
        'posts_per_page'      => -1
    );

    if ( isset($_POST['traveler_type']) && !empty($_POST['traveler_type']) )
        $categories = $_POST['traveler_type'];

    if ( $categories ){
        $args['tax_query'][] = array(
            'taxonomy'         => 'resume_category',
            'field'            => 'slug',
            'terms'            => array_values( $categories ),
            'include_children' => false,
            'operator'         => 'IN'
        );
    }

    if ( $meta_use ) {
        $args['meta_query'] = $meta_query;
    }

    $resumes = new WP_Query($args);
    $count =  $resumes -> post_count;

    $company_name = ( get_user_meta( $user->ID, 'company_name', true) )?get_user_meta($user->ID, 'company_name', true)
        :(  (get_user_meta($user->ID, 'first_name', true) && get_user_meta($user->ID, 'last_name', true)) ? get_user_meta($user->ID, 'first_name', true)." ". get_user_meta($user->ID, 'last_name', true) : ($user->display_name[0]) );

    ?>
    <section class="section section_listing">
        <div class="listing__wrapper">
            <p class="listing__view__header">
                <span class="company-name"><?php echo $company_name; ?></span> campaign <span class="company-campaign"> estimate</span>
            </p>
        </div>
    </section>
    <section class="section orange_section">
        <h2>Campaign Estimate</h2>
    </section>
    <section class="section section_listing">
        <div class="listing__wrapper">
            <p>
                Lorem ipsum dolor sit amet, congue postea erroribus et his, vim te putant quaeque. An noster doctus nusquam pro. Stet choro pericula est ut, tale expetendis scribentur ei per. Ponderum sapientem in his, habemus principes intellegam eu eos. Nam prima labore at
            </p>
            <div class="list__options">
                <?php
                $possible_products = array('pro_inf', 'growth_inf', 'micro_inf');
                $text = "";
                foreach( $possible_products as $possible_product){
                    $product_id = wc_get_product_id_by_sku( $possible_product );
                    $product = new WC_Product( $product_id );
                    $price = $product -> get_price();

                    if ( floor( $budget/$price ) > 0){
                        if ( $possible_product == 'pro_inf' )       $can_pro = true;
                        if ( $possible_product == 'growth_inf' )    $can_growth = true;
                        if ( $possible_product == 'micro_inf' )     $can_micro = true;
                    }
                    ?>
                    <input type="button" class="button button_orange add_prod_to_job" data-prod_id = "<?php echo $product_id; ?>" data-prod_count = "<?php echo floor( $budget/$price );?>" value="<?php _e( floor( $budget/$price )." ".$product ->get_name(). _n(" influencer"," influencers",floor( $budget/$price )) , 'wp-job-manager' ); ?>" />

                <?php }
                if ( !isset($can_pro) && $can_growth && $can_micro)
                    $text = "For a chance to use a PRO influencer, please add more budget or check out how many GROW or MICRO influencers you can have." ;

                if ( !isset($can_pro) && !isset($can_growth) && $can_micro)
                    $text = "For a chance to use a PRO or a GROW influencer, please add more budget or check out how many  MICRO influencers you can have." ;

                if ( !isset($can_pro) && !isset($can_growth) && !$can_micro){
                    $text = "For a chance to use an influencer, please add more budget." ;?>
                <?php }
                ?>
            </div>
            <p class="listing__wrapper">
                <?php echo $text;
                if ( !isset($can_pro) && !$can_growth && !isset($can_micro) ){ ?>
                    <input type="submit" name="edit_job" class="button job-manager-button-edit-listing button_grey" value="<?php _e( 'Edit listing', 'wp-job-manager' ); ?>" />
                <?php } ?>
            </p>
            <p class="listing__wrapper">
                <?php if ( !$resumes->have_posts() ) :?>
                    Sorry, no influencers for your request found
                <?php endif; ?>
            </p>
            <?php if ( $resumes->have_posts() ) :?>
            <div class="listing__wrapper">
                <p class="list__number"><span>Number of influencers: </span> <span> <?php echo $count?></span></p>
                <p>
                    Example of influencers
                </p>
            </div>
            <?php endif; ?>
        </div>
        <section class="section section_browse">
            <div class="section__container">
                <div class="carousel">
                    <?php
                    $possible_reach = 0;
                    if ( $resumes->have_posts() ) :?>

                        <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>

                            <?php get_template_part('template-parts/content', 'influencer')?>
                            <?php
                            $resume_id = get_the_ID();
                            $possible_reach += get_influencer_audience($resume_id);
                            ?>

                        <?php endwhile; wp_reset_postdata();?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php if ( $resumes->have_posts() ) :?>
        <div class="listing__wrapper">
            <p class="list__number"><span>Possible Reach: </span><span><?php echo $possible_reach; ?></span></p>
            <p>Average   audience   size   of   all   influencers   in   the   category selected   by   the   brand.</p>
        </div>
        <div class="listing__wrapper">//.5%-2.5%
            <p class="list__number"><span>Estimated Engagement: </span><span><?php echo round($possible_reach*0.005)." - ".round($possible_reach*0.025)?> </span></p>
            <p>Average   possible   reach</p>
        </div>
        <?php $submit_job_page = get_permalink(get_option('job_manager_submit_job_form_page_id')); ?>
        <a href="<?php echo $submit_job_page; ?>" class="button button_green">Let's Build My Campaign</a>
        <?php endif; ?>

    </section>
    <?php
    exit;
}

add_action('wp_ajax_aj_preview_estimate_influencers', 'aj_preview_estimate_influencers');

add_action('wp_ajax_aj_preview_estimate_summary', 'aj_preview_estimate_summary');

function aj_preview_estimate_influencers(){

    $budget = $_POST['target_budget'];
    $meta_query = array('relation' => 'AND');

    $meta_use = false;

    if ( isset($_POST['fb_channel']) && $_POST['fb_channel'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_fb_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( isset($_POST['ig_channel']) && $_POST['ig_channel'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_instagram_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( isset($_POST['yt_channel']) && $_POST['yt_channel'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_youtube_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( isset($_POST['tw_channel']) && $_POST['tw_channel'] == 'on' ){
        $meta_query[] = array(
            'key'       => '_twitter_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( isset($_POST['include']) && $_POST['include'] == 'pro_inf' ){
        $meta_query[] = array(
            'key'       => '_audience',
            'compare'   => '>=',
            'value'     => '500000',
            'type'      => 'NUMERIC'
        );
        $meta_use = true;
    }

    if ( isset($_POST['include']) && $_POST['include'] == 'growth_inf' ){
        $meta_query[] = array(
            'key'       => '_audience',
            'compare'   => 'BETWEEN',
            'value'     => array(50000, 499999),
            'type'      => 'NUMERIC'
        );
        $meta_use = true;
    }

    if ( isset($_POST['include']) && $_POST['include'] == 'micro_inf' ){
        $meta_query[] = array(
            'key'       => '_audience',
            'compare'   => '<',
            'value'     => '50000',
            'type'      => 'NUMERIC'
        );
        $meta_use = true;
    }

    $names = array(
        'micro_inf'     => "Micro",
        'growth_inf'    => "Growth",
        'pro_inf'       => "Pro",
    );

    $args = array(
        'post_type'           => 'resume',
        'post_status'         => array( 'publish'),
        'ignore_sticky_posts' => 1,
        'orderby'             => 'ASC',
        'order'               => 'date',
        'posts_per_page'      => -1
    );

    if ( isset($_POST['traveler_type']) && !empty($_POST['traveler_type']) )
        $categories = $_POST['traveler_type'];

    if ( $categories ){
        $args['tax_query'][] = array(
            'taxonomy'         => 'resume_category',
            'field'            => 'slug',
            'terms'            => array_values( $categories ),
            'include_children' => false,
            'operator'         => 'IN'
        );
    }

    if ( $meta_use ) {
        $args['meta_query'] = $meta_query;
    }

    $resumes = new WP_Query($args);

    if ( $resumes->have_posts() ) :?>
        <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>
            <?php get_template_part('template-parts/content', 'influencer')?>
        <?php endwhile; wp_reset_postdata();?>
        <?php else: echo "Sorry, We can't find any ".$names[$_POST['include']]." influencers now"; ?>
    <?php endif;

    exit;
}

function aj_preview_estimate_summary(){

    $response = array();

    $number = $_POST['number'];

    $koef = 1;


    if ( isset($_POST['include']) && $_POST['include'] == 'pro_inf' ){
        $koef = 5000000;
    }

    if ( isset($_POST['include']) && $_POST['include'] == 'growth_inf' ){
        $koef = 500000;
    }

    if ( isset($_POST['include']) && $_POST['include'] == 'micro_inf' ){
        $koef = 50000;
    }

    $possible_reach = $koef*$number;
    if ( $possible_reach != 0 ){
        $response['possible_reach'] = $possible_reach;
        $response['possible_engagement'] = (round(($koef*$number)*0.0001)." - ".round(($koef*$number)*0.005));
    }else {
        $response['found'] = false;
    }

    echo json_encode($response);

    exit;
}

/*------------------------------------   Dashboard Init -----------------------------------------------*/
include_once get_stylesheet_directory() . '/inc/term-walker.php';
require_once 'inc/dashboard-init.php';

function scrape_insta($username) {
	$baseUrl = 'http://instagram.com/'. $username . '/?__a=1';
	$url = $baseUrl;
	$data = [];
	for($i = 0; $i < 2; $i++) {
		$json = json_decode(file_get_contents($url));
		$nodes = $json->user->media->nodes;

		foreach ($nodes as $node) {
			array_push($data, $node);
		}

		if(!$json->user->media->page_info->has_next_page) break;
		$url = $baseUrl.'&max_id=' . $json->user->media->page_info->end_cursor;
	}

	return array_slice($data, 0, 20) ;

}

require_once 'settings.php';

function job_custom_columns( $column ) {
    global $post;

    switch ( $column ) {
        case "job_listing_type" :
            $post = get_post( $post );

            if ( $post->post_type !== 'job_listing' ) {
                return;
            }

            $types = wp_get_post_terms( $post->ID, 'job_listing_type' );

            if ( $types ) {
                $type_current = current( $types );
                $output = '';
                foreach ($types as $type){
                    if ( $type != $type_current ) $output .=  '<span class="job-type ' . $type->slug . '">' . $type->name . '</span>';
                }
            } else {
                $type = false;
            }

            if ( $type )
                echo $output;
            break;
    }
}

add_action('wp_ajax_aj_search', 'aj_search');

function aj_search(){
    $str = addslashes($_POST['search']);

    $arg = array(
        'post_status'         => array( 'publish'),
        'ignore_sticky_posts' => 1,
        'orderby'             => 'ASC',
        'order'               => 'date',
        'posts_per_page'      => -1
    );

    $user = wp_get_current_user();

    $post_type = 'resume';

    if ( in_array( 'candidate', (array) $user->roles ) ) {
        $post_type = 'job_listing';
    }


    $arg['post_type'] = $post_type;

    $arg['s'] = $str;

    $result = new WP_Query($arg);

    if($result->have_posts()):
        while ($result->have_posts()) : $result->the_post(); ?>
            <div class="inline-items" data-selectable="" data-value="<?php the_title()?>">
                <div class="author-thumb">
                    <a href="<?php the_permalink()?>" >
                        <?php if ($post_type == 'resume'){?>
                            <img src="<?php echo get_the_candidate_photo(get_the_ID())?>" alt="avatar">
                    <?php }?>
                    </a>
                </div>
                <div class="notification-event">
                    <span class="title-result"><a href="<?php the_permalink()?>" ><?php the_title()?></a></span>
                    <?php if ($post_type == 'resume'){?>
                        <?php $tags = explode(', ', get_the_resume_category(get_the_ID()));?>
                    <?php }elseif( $post_type == 'job_listing'){?>
                        <?php $tags = wp_get_object_terms( get_the_ID(), 'job_listing_category', array('fields'=>'names') );?>
                    <?php }?>
                    <span class="chat-message-item">
                        <?php foreach($tags as $tag) : if ( $tag ):?>
                            <p class="tag-gray"><?php esc_html_e($tag) ?></p>
                        <?php endif; endforeach; ?>
                    </span>
                </div>
            </div>
        <?php endwhile;
    else: ?>
        <div class="inline-items">
            No matches
        </div>
    <?php endif;

    die;
}
