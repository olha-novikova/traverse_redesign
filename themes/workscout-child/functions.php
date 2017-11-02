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
    wp_register_script('message-by-job',get_stylesheet_directory_uri() . '/js/message-by-job.js', array('jquery'), '20150705', true);

    $ajax_url = admin_url( 'admin-ajax.php' );

    wp_localize_script( 'workscout-custom-child', 'ws',
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

remove_action( 'woocommerce_edit_account_form', 'my_woocommerce_edit_account_form' );

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

add_action( 'woocommerce_edit_account_form', 'my_woocommerce_edit_account_form_child' );

function my_woocommerce_edit_account_form_child() {

    $user_id = get_current_user_id();
    $user = get_userdata( $user_id );

    if ( !$user )
        return;

    $str = get_userdata($user_id);

    if($str->roles[0] == "candidate"){

        $number =  get_user_meta($user_id,'phone_number',true);
        $logo = get_user_meta( $user_id, 'photo', true );

        $website = get_user_meta( $user_id, 'website', true );
        $monthlyvisit = get_user_meta( $user_id, 'monthlyvisit', true );

        $insta = get_user_meta( $user_id, 'insta', true );
        $fb = get_user_meta( $user_id, 'fb', true );
        $twitter = get_user_meta( $user_id, 'twitter', true );

        $youtube = get_user_meta( $user_id, 'youtube', true );
        $shortbio = get_user_meta( $user_id, 'shortbio', true );

        $newsletter = get_user_meta( $user_id, 'newsletter', true );
        $newsletter_subscriber_count = get_user_meta( $user_id, 'newsletter_subscriber_count', true );

        $traveler_type = get_user_meta( $user_id, 'traveler_type', true );
        $location = get_user_meta( $user_id, 'location', true );

        $jrrny_link_auto = get_user_meta($user_id,'_jrrny_link', true);
        $jrrny_link_own = get_user_meta($user_id,'jrrny_link', true);
        ?>

        <div class="input__block">
            <textarea type="textfield"  name="shortbio"  class="input-text <?php if (!empty($shortbio)) echo 'has-value';?>" /><?php echo esc_attr( $shortbio ); ?></textarea>
            <label class="form__input__label" for="shortbio">SHORT BIO</label>
        </div>

        <?php
        $args = array(
            'taxonomy' => 'resume_category',
            'hide_empty' => false,
        );
        $portfolio_types = get_terms( $args );

        ?>
        <?php wp_enqueue_script( 'wp-job-manager-multiselect' ); ?>

        <div class="input__block">
            <p style="margin-bottom: 8px;">Select Category</p>
            <select name="traveler_type[]" class="input-text job-manager-multiselect" multiple="multiple" data-no_results_text="<?php _e( 'No results match', 'wp-job-manager' ); ?>" data-multiple_text="<?php _e( 'Select Some Options', 'wp-job-manager' ); ?>">
                <?php
                if( $portfolio_types && ! is_wp_error($portfolio_types) ){
                    foreach ($portfolio_types as $portfolio_type){ ?>
                        <option value="<?php echo $portfolio_type->term_id; ?>" <?php if ( in_array( $portfolio_type->term_id, $traveler_type) ) echo "selected" ; ?>><?php echo $portfolio_type->name; ?></option>
                    <?php }
                }
                ?>
            </select>
        </div>

        <div class="input__block full_width">
            <input class="form__input input-text <?php if (!empty($location)) echo 'has-value';?>"    type="text"  name="location" value="<?php echo esc_attr( $location ); ?>"   />
            <label class="form__input__label" for="location">LOCATIONS YOU KNOW BEST</label>
        </div>

        <div class="input__block">
            <input class="form__input input-text <?php if (!empty($number)) echo 'has-value';?>"    type="text"  value="<?php echo esc_attr( $number ); ?>" />
            <label class="form__input__label" for="number">YOUR PHONE NUMBER</label>

        </div>

         <div class="input__block">
             <input class="form__input input-text <?php if (!empty($insta)) echo 'has-value';?>"    type="text"  name="insta" id = "instagram_link" value="<?php echo esc_attr( $insta ); ?>"   />
             <label class="form__input__label" for="insta">YOUR INSTAGRAM URL</label>
         </div>

         <div class="input__block">
             <input class="form__input input-text <?php if (!empty($fb)) echo 'has-value';?>"    type="text"  name="fb" id = "fb_link" value="<?php echo esc_attr( $fb ); ?>"   />
             <label class="form__input__label" for="fb">YOUR FACEBOOK URL</label>
         </div>

         <div class="input__block">
             <input class="form__input input-text <?php if (!empty($twitter)) echo 'has-value';?>"    type="text"  name="twitter"  id ="twitter_link" value="<?php echo esc_attr($twitter); ?>"   />
             <label class="form__input__label" for="birthdate">YOUR TWITTER URL</label>
         </div>

         <div class="input__block">
             <input class="form__input input-text <?php if (!empty($youtube)) echo 'has-value';?>"    type="text"  name="youtube" id = "youtube_link" value="<?php echo esc_attr( $youtube ); ?>"   />
             <label class="form__input__label" for="youtube">YOUR YOUTUBE URL</label>
         </div>

         <div class="input__block">
            <input class="form__input input-text <?php if (!empty($jrrny_link_own)||!empty($jrrny_link_auto)) echo 'has-value';?>"    type="text" name="jrrny_link" id = "jrrny_link" value="<?php echo esc_attr( $jrrny_link_own ? $jrrny_link_own: $jrrny_link_auto); ?>"   />
                <label class="form__input__label" for="youtube">YOUR JRRNY.COM PROFILE <?php if (!$jrrny_link_own && $jrrny_link_auto) echo "(A new JRRNY account was created automatically, but you can use your own if you have one.)";?></label>
         </div>

         <div class="input__block">
            <input class="form__input input-text <?php if (!empty($website)) echo 'has-value';?>"    type="text"  name="website" value="<?php echo esc_attr( $website ); ?>"   />
             <label class="form__input__label" for="website">YOUR WEBSITE URL</label>
         </div>

         <div class="input__block">
             <input class="form__input input-text <?php if (!empty($monthlyvisit)) echo 'has-value';?>"    type="text" name="monthlyvisit" value="<?php echo esc_attr( $monthlyvisit ); ?>"   />
             <label class="form__input__label" for="monthlyvisit">YOUR WEBSITE'S ESTIMATED NUMBER OF MONTHLY VISITS</label>
         </div>

         <div class="input__block">
            <p>DOES YOUR WEBSITE HAVE A NEWSLETTER?</p>
            <input type="radio" name="newsletter" value="yes" <?php if ( $newsletter == 'yes') echo 'checked = "checked"';?>> YES
            <input type="radio" name="newsletter" value="no" <?php if ($newsletter == 'no') echo 'checked = "checked"';?>> NO
         </div>

         <div class="input__block newsletter_conditional <?php if ($newsletter != 'yes') echo 'hide';?>" >

            <input class="input-text form__input <?php if (!empty($newsletter_subscriber_count)) echo 'has-value';?>"    type="text" name="newsletter_subscriber" value="<?php echo esc_attr( $newsletter_subscriber_count ); ?>"   />
             <label style="z-index: 99" class="form__input__label" for="newsletter_subscriber">IF YES, HOW MANY SUBSCRIBERS?</label>
         </div>

        <div class="input__block full_width">
            <input class="form__input input-text <?php if (!empty($logo)) echo 'has-value';?>"    type="file" name="logo" value="<?php echo esc_attr( $logo ); ?>"   />
            <label class="form__input__label" for="logo">YOUR PROFILE PHOTO</label>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('input[name="newsletter"]').on('change', function(){
                    var val = $(this).val();
                    if ( val == 'yes'){
                        $('.newsletter_conditional').removeClass('hide');
                    }else{
                        $('.newsletter_conditional').addClass('hide');
                        $('input[name="newsletter_subscriber"]').val('');
                    }
                });
            });
        </script>

    <?php
    }
    if($str->roles[0] == "employer")
    {
        $company_name= get_user_meta( $user_id, 'company_name', true );
        $number = get_user_meta( $user_id, 'number', true );
        $logo = get_user_meta( $user_id, 'logo', true );
        $website = get_user_meta( $user_id, 'website', true );
        $insta = get_user_meta( $user_id, 'insta', true );
        $fb = get_user_meta( $user_id, 'fb', true );
        $twitter = get_user_meta( $user_id, 'twitter', true );
        $youtube = get_user_meta( $user_id, 'youtube', true );
        $newsletter = get_user_meta( $user_id, 'newsletter', true );
        $shortbio = get_user_meta( $user_id, 'shortbio', true );
        $traveler_type = get_user_meta( $user_id, 'traveler_type', true );
        ?>
         
         <div class="input__block">

            <input class="form__input input-text <?php if (!empty($company_name)) echo 'has-value';?>"    type="text" name="company_name" value="<?php echo esc_attr( $company_name ); ?>" class="input-text sdjhjksdhk" />
             <label class="form__input__label" for="company_name">COMPANY</label>
         </div>

         <div class="input__block">

            <input class="form__input input-text <?php if (!empty($number)) echo 'has-value';?>"    type="text" name="number" value="<?php echo esc_attr( $number ); ?>" class="input-text sdjhjksdhk" />
             <label class="form__input__label" for="number">PHONE NUMBER</label>
         </div>

         <div class="input__block <?php if (!empty($shortbio)) echo 'has-value';?>">
             <textarea class="input-text" name="shortbio"><?php echo esc_attr( $shortbio ); ?></textarea>
             <label class="form__input__label" for="birthdate">SHORT BIO</label>
         </div>

         <div class="input__block">
            <?php if( $logo ) {
                $dir = wp_get_upload_dir();?>
                <img class="img-responsive" src="<?php echo $dir['baseurl'].'/users/'.$logo; ?>" />
            <?php } ?>

            <input class="form__input input-text <?php if (!empty($logo)) echo 'has-value';?>"    type="file" name="logo" value="<?php echo esc_attr( $logo ); ?>"   />
             <label class="form__input__label" for="logo">LOGO</label>
         </div>

         <div class="input__block">

            <input class="form__input input-text <?php if (!empty($website)) echo 'has-value';?>"    type="text" name="website" value="<?php echo esc_attr( $website ); ?>"   />
             <label class="form__input__label" for="website">WEBSITE</label>
         </div>

    <?php
    }

    if($str->roles[0] == "administrator") {
        //Brand
        $company_name= get_user_meta( $user_id, 'company_name', true );
        $number = get_user_meta( $user_id, 'number', true );
        ?>

         
         <div class="input__block">

             <input class="form__input input-text <?php if (!empty($company_name)) echo 'has-value';?>"    type="text" name="company_name" value="<?php echo esc_attr( $company_name ); ?>" class="input-text sdjhjksdhk" />
             <label class="form__input__label" for="company_name">COMPANY</label>
         </div>

         <div class="input__block">

            <input class="form__input input-text <?php if (!empty($number)) echo 'has-value';?>"    type="text" name="number" value="<?php echo esc_attr( $number ); ?>" class="input-text sdjhjksdhk" />
             <label class="form__input__label" for="number">PHONE NUMBER</label>
         </div>

        <?php
        //Candidate
        $number =  get_user_meta($user_id,'phone_number',true);
        $monthlyvisit = get_user_meta( $user_id, 'monthlyvisit', true );
        $newsletter = get_user_meta( $user_id, 'newsletter', true );
        $newsletter_subscriber_count = get_user_meta( $user_id, 'newsletter_subscriber_count', true );
        $traveler_type = get_user_meta( $user_id, 'traveler_type', true );
        $location = get_user_meta( $user_id, 'location', true );
        $jrrny_link_auto = get_user_meta($user_id,'_jrrny_link', true);
        $jrrny_link_own = get_user_meta($user_id,'jrrny_link', true);

        ?>

         <div class="input__block">

            <input class="form__input input-text <?php if (!empty($monthlyvisit)) echo 'has-value';?>"    type="text" name="monthlyvisit" value="<?php echo esc_attr( $monthlyvisit ); ?>"   />
             <label class="form__input__label" for="monthlyvisit">ESTIMATED MONTHLY VISIT</label>
         </div>

         <div class="input__block">

                <input class="form__input input-text <?php if (!empty($jrrny_link_own) || !empty($jrrny_link_auto)) echo 'has-value';?>"    type="text" name="jrrny_link" id = "jrrny_link" value="<?php echo esc_attr( $jrrny_link_own ? $jrrny_link_own: $jrrny_link_auto); ?>"   />
             <label class="form__input__label" for="youtube">JRRNY.COM <?php if (!$jrrny_link_own && $jrrny_link_auto) echo "(account was created automatically, you can use your own if you have)";?></label>
         </div>

         <div class="input__block">
            <p>DO YOU HAVE A NEWSLETTER?</p>
            <input type="radio" name="newsletter" value="yes" <?php if ($newsletter == 'yes') echo 'checked = "checked"';?>> YES
            <input type="radio" name="newsletter" value="no" <?php if ($newsletter == 'no') echo 'checked = "checked"';?>> NO
         </div>

        <div class="input__block newsletter_conditional <?php if ($newsletter != 'yes') echo 'hide';?>" >
          <input class="input-text form__input <?php if (!empty($newsletter_subscriber_count)) echo 'has-value';?>"    type="text" name="newsletter_subscriber" value="<?php echo esc_attr( $newsletter_subscriber_count ); ?>"   />
          <label class="form__input__label" for="newsletter_subscriber">IF YES, HOW MANY SUBSCRIBERS?</label>
         </div>
        <?php
        global $wpdb;
        $sql = $wpdb->get_results("SELECT * FROM travler_type");
        ?>
        <?php wp_enqueue_script( 'wp-job-manager-multiselect' ); ?>
         
         <div class="input__block">
           <p>TARGET AUDIENCE</p>
            <select name="traveler_type[]" class="job-manager-multiselect" multiple="multiple" data-no_results_text="<?php _e( 'No results match', 'wp-job-manager' ); ?>" data-multiple_text="<?php _e( 'Select Some Options', 'wp-job-manager' ); ?>">
                <?php
                foreach ($sql as $result){ ?>
                    <option value="<?php echo $result->travler_type; ?>" <?php if ( in_array( $result->travler_type, $traveler_type) ) echo "selected" ; ?>><?php echo $result->travler_type; ?></option>
                <?php }
                ?>
            </select>

         </div>

         
         <div class="input__block">

            <input class="form__input input-text <?php if (!empty($location)) echo 'has-value';?>"    type="text" name="location" value="<?php echo esc_attr( $location ); ?>"   />
             <label class="form__input__label" for="location">LOCATIONS YOU HAVE ACCESS TO</label>
         </div>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('input[name="newsletter"]').on('change', function(){
                    var val = $(this).val();
                    if ( val == 'yes'){
                        $('.newsletter_conditional').removeClass('hide');
                    }else{
                        $('.newsletter_conditional').addClass('hide');
                        $('input[name="newsletter_subscriber"]').val('');
                    }
                });
            });
        </script>
        <?php
        //Both
        $website = get_user_meta( $user_id, 'website', true );
        $logo = get_user_meta( $user_id, 'photo', true );
        $insta = get_user_meta( $user_id, 'insta', true );
        $fb = get_user_meta( $user_id, 'fb', true );
        $twitter = get_user_meta( $user_id, 'twitter', true );
        $youtube = get_user_meta( $user_id, 'youtube', true );
        $shortbio = get_user_meta( $user_id, 'shortbio', true );

        ?>

         <div class="input__block">
             <textarea class="form__input input-text <?php if (!empty($shortbio)) echo 'has-value';?>" name="shortbio" /><?php echo esc_attr( $shortbio ); ?></textarea>
             <label class="form__input__label" for="birthdate">SHORT BIO</label>
         </div>


         <div class="input__block">

            <input class="form__input input-text <?php if (!empty($website)) echo 'has-value';?>"    type="text" name="website" value="<?php echo esc_attr( $website ); ?>"   />
             <label class="form__input__label" for="website">WEBSITE</label>
         </div>

         <div class="input__block">
                <input class="form__input input-text <?php if (!empty($insta)) echo 'has-value';?>"    type="text" name="insta" id = "instagram_link" value="<?php echo esc_attr( $insta ); ?>"   />
             <label class="form__input__label" for="insta">INSTAGRAM</label>
         </div>

         <div class="input__block">

            <input class="form__input input-text <?php if (!empty($fb)) echo 'has-value';?>"    type="text" name="fb" id = "fb_link" value="<?php echo esc_attr( $fb ); ?>"   />
             <label class="form__input__label" for="fb">FACEBOOK</label>
         </div>


         <div class="input__block">

             <input class="form__input input-text <?php if (!empty($twitter)) echo 'has-value';?>"    type="text" name="twitter"  id ="twitter_link" value="<?php echo esc_attr($twitter); ?>"   />
             <label class="form__input__label" for="birthdate">TWITTER</label>
         </div>

         <div class="input__block">

                <input class="form__input input-text <?php if (!empty($youtube)) echo 'has-value';?>"    type="text" name="youtube" id = "youtube_link" value="<?php echo esc_attr( $youtube ); ?>"   />
             <label class="form__input__label" for="youtube">YOUTUBE</label>
         </div>

        <div class="input__block">
            <?php if( $logo ) {
                $dir = wp_get_upload_dir();?>
                <img class="img-responsive" src="<?php echo $dir['baseurl'].'/users/'.$logo; ?>" />
            <?php } ?>

            <input class="form__input input-text <?php if (!empty($logo)) echo 'has-value';?>"    type="file" name="logo" value="<?php echo esc_attr( $logo ); ?>"   />
            <label class="form__input__label" for="logo">PHOTO/LOGO</label>
        </div>

    <?php
    }
} // end func


remove_action( 'woocommerce_save_account_details', 'my_woocommerce_save_account_details' );

add_action( 'woocommerce_save_account_details', 'my_woocommerce_save_account_details_child' );
add_action( 'woocommerce_save_account_details_errors','wooc_validate_custom_field', 10, 1 );


function wooc_validate_custom_field( $args )
{
    if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])){
        $errors= array();
        $file_name = $_FILES['logo']['name'];
        $file_size =$_FILES['logo']['size'];
        $file_tmp =$_FILES['logo']['tmp_name'];
        $file_type=$_FILES['logo']['type'];
        $file_ext=strtolower(end(explode('.',$_FILES['logo']['name'])));

        $expensions= array("jpeg","jpg","png","gif");

        if(in_array($file_ext,$expensions)=== false){
            $args->add( 'error', __( 'extension not allowed, please choose a JPEG or PNG file.', 'woocommerce' ),'');

        }

        if($file_size > 2097152){
            $args->add( 'error', __( 'File size should not be more then 2 MB', 'woocommerce' ),'');

        }

    }
}

function my_woocommerce_save_account_details_child( $user_id ) {
    $str = get_userdata($user_id);
    $errors = false;

    if($str->roles[0] == "candidate" || ($str->roles[0] =="administrator" ))
    {
        if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])){
            $errors= array();
            $file_name = $_FILES['logo']['name'];
            $file_size =$_FILES['logo']['size'];
            $file_tmp =$_FILES['logo']['tmp_name'];
            $file_type=$_FILES['logo']['type'];
            $pi = pathinfo($file_name);
            $ext = $pi['extension'];

            $file_ext=strtolower(end(explode('.',$_FILES['logo']['name'])));
            $new_name = $str->user_login."_".time().".".$ext;

            $expensions= array("jpeg","jpg","png","gif");

            if(in_array($file_ext,$expensions)=== false){
                $errors = true;;
            }

            if($file_size > 2097152){
                $errors = true;;
            }

            if( !$errors ){
                $dir = wp_get_upload_dir();
                if (move_uploaded_file($file_tmp, $dir['basedir']."/users/".$new_name)){
                    update_user_meta( $user_id, 'photo', $new_name );
                }
            }
        }

        update_user_meta( $user_id, 'website', htmlentities( $_POST[ 'website' ] ) );
        update_user_meta( $user_id, 'jrrny_link', htmlentities( $_POST[ 'jrrny_link' ] ) );
        update_user_meta( $user_id, 'monthlyvisit', htmlentities( $_POST[ 'monthlyvisit' ] ) );
        update_user_meta( $user_id, 'insta', htmlentities( $_POST[ 'insta' ] ) );
        update_user_meta( $user_id, 'fb', htmlentities( $_POST[ 'fb' ] ) );
        update_user_meta( $user_id, 'twitter', htmlentities( $_POST[ 'twitter' ] ) );
        update_user_meta( $user_id, 'youtube', htmlentities( $_POST[ 'youtube' ] ) );
        update_user_meta( $user_id, 'newsletter', htmlentities( $_POST[ 'newsletter' ] ) );
        update_user_meta( $user_id, 'newsletter_subscriber_count', htmlentities( $_POST[ 'newsletter_subscriber' ] ) );
        update_user_meta( $user_id, 'shortbio', htmlentities( $_POST[ 'shortbio'] ) );
        update_user_meta( $user_id, 'traveler_type',  $_POST['traveler_type' ] );
        update_user_meta( $user_id, 'location', htmlentities( $_POST['location'] ) );
        update_user_meta( $user_id, 'phone_number', htmlentities( $_POST[ 'phone_number' ] ) );

        $user_resumes = $resumes = get_posts( array(
            'post_type'           => 'resume',
            'post_status'         => 'all',
            'ignore_sticky_posts' => 1,
            'posts_per_page'      => -1,
            'author'              => $user_id

        ) );

        if ( $user_resumes ){
            foreach ( $user_resumes as $user_resumes ){
                update_post_meta( $user_resumes->ID, '_candidate_photo', get_user_meta($user_id, 'photo', true) );

                update_post_meta( $user_resumes->ID, '_influencer_website',get_user_meta($user_id, 'website', true) );
                update_post_meta( $user_resumes->ID, '_jrrny_link', get_user_meta($user_id, 'jrrny_link', true) );
                update_post_meta( $user_resumes->ID, '_estimated_monthly_visitors', get_user_meta($user_id, 'monthlyvisit', true) );
                update_post_meta( $user_resumes->ID, '_instagram_link', get_user_meta($user_id, 'insta', true) );
                update_post_meta( $user_resumes->ID, '_facebook_link', get_user_meta($user_id, 'fb', true) );
                update_post_meta( $user_resumes->ID, '_twitter_link',get_user_meta($user_id, 'twitter', true) );
                update_post_meta( $user_resumes->ID, '_youtube_link',get_user_meta($user_id, 'youtube', true) );
                update_post_meta( $user_resumes->ID, '_newsletter', get_user_meta($user_id, 'newsletter', true) );
                update_post_meta( $user_resumes->ID, '_newsletter_total', get_user_meta($user_id, 'newsletter_subscriber_count', true) );
                update_post_meta( $user_resumes->ID, '_portfolio_description', get_user_meta($user_id, 'shortbio', true));
                update_post_meta( $user_resumes->ID, '_short_influencer_bio', get_user_meta($user_id, 'shortbio', true));
                //TODO add resume_category save here
                update_post_meta( $user_resumes->ID, '_resume_locations', get_user_meta($user_id, 'location', true) );
                update_post_meta( $user_resumes->ID, '_influencer_number', get_user_meta($user_id, 'phone_number', true) );

                update_post_meta( $user_resumes->ID, '_video_sample_embed', get_user_meta($user_id, 'video', true) );

                if ( $user_resumes -> post_status == 'preview'){
                    wp_update_post( array(
                        'ID'            => $user_resumes->ID,
                        'post_status'   => 'publish',
                        'post_title'    => $str-> first_name.' '.$str-> last_name

                    ) );
                }

            }
        }

    }

    if($str->roles[0] == "employer") {

        if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])){
            $errors= array();
            $file_name = $_FILES['logo']['name'];
            $file_size =$_FILES['logo']['size'];
            $file_tmp =$_FILES['logo']['tmp_name'];
            $file_type=$_FILES['logo']['type'];
            $pi = pathinfo($file_name);
            $ext = $pi['extension'];

            $file_ext=strtolower(end(explode('.',$_FILES['logo']['name'])));
            $new_name = $str->user_login."_".time().".".$ext;

            $expensions= array("jpeg","jpg","png","gif");

            if(in_array($file_ext,$expensions)=== false){
                $errors = true;;
            }

            if($file_size > 2097152){
                $errors = true;;
            }

            if( !$errors ){
                $dir = wp_get_upload_dir();
                if (move_uploaded_file($file_tmp, $dir['basedir']."/users/".$new_name)){
                    update_user_meta( $user_id, 'logo', $new_name);
                }
            }
        }

        update_user_meta( $user_id, 'number', htmlentities( $_POST[ 'number' ] ) );
        update_user_meta( $user_id, 'company_name', htmlentities( $_POST[ 'company_name' ] ) );
        update_user_meta( $user_id, 'website', htmlentities( $_POST[ 'website' ] ) );
        update_user_meta( $user_id, 'shortbio', htmlentities( $_POST[ 'shortbio'] ) );

    }

  /*  if($str->roles[0] == "administrator") {

        if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])){
            $errors= array();
            $file_name = $_FILES['logo']['name'];
            $file_size =$_FILES['logo']['size'];
            $file_tmp =$_FILES['logo']['tmp_name'];
            $file_type=$_FILES['logo']['type'];
            $pi = pathinfo($file_name);
            $ext = $pi['extension'];

            $file_ext=strtolower(end(explode('.',$_FILES['logo']['name'])));
            $new_name = $str->user_login."_".time().".".$ext;

            $expensions= array("jpeg","jpg","png","gif");

            if(in_array($file_ext,$expensions)=== false){
                $errors = true;;
            }

            if($file_size > 2097152){
                $errors = true;;
            }

            if( !$errors ){
                $dir = wp_get_upload_dir();
                move_uploaded_file($file_tmp, $dir['basedir']."/users/".$new_name);
                update_user_meta( $user_id, 'photo', $new_name );
                update_user_meta( $user_id, 'logo', $new_name);

            }
        }

        update_user_meta( $user_id, 'number', htmlentities( $_POST[ 'number' ] ) );
        update_user_meta( $user_id, 'phone_number', htmlentities( $_POST[ 'number' ] ) );
        update_user_meta( $user_id, 'company_name', htmlentities( $_POST[ 'company_name' ] ) );
        update_user_meta( $user_id, 'website', htmlentities( $_POST[ 'website' ] ) );
        update_user_meta( $user_id, 'insta', htmlentities( $_POST[ 'insta' ] ) );
        update_user_meta( $user_id, 'fb', htmlentities( $_POST[ 'fb' ] ) );
        update_user_meta( $user_id, 'twitter', htmlentities( $_POST[ 'twitter' ] ) );
        update_user_meta( $user_id, 'pinterest', htmlentities( $_POST[ 'pinterest' ] ) );
        update_user_meta( $user_id, 'youtube', htmlentities( $_POST[ 'youtube' ] ) );
        update_user_meta( $user_id, 'shortbio', htmlentities( $_POST[ 'shortbio'] ) );
        update_user_meta( $user_id, 'jrrny_link', htmlentities( $_POST[ 'jrrny_link' ] ) );
        update_user_meta( $user_id, 'monthlyvisit', htmlentities( $_POST[ 'monthlyvisit' ] ) );
        update_user_meta( $user_id, 'newsletter', htmlentities( $_POST[ 'newsletter' ] ) );
        update_user_meta( $user_id, 'newsletter_subscriber_count', htmlentities( $_POST[ 'newsletter_subscriber' ] ) );
        update_user_meta( $user_id, 'traveler_type',  $_POST['traveler_type' ] );
        update_user_meta( $user_id, 'location', htmlentities( $_POST['location'] ) );

    }*/
}// end func

remove_action( 'template_redirect', array( 'WC_Form_Handler', 'save_account_details' ) );
add_action( 'template_redirect',  'custom_save_account_details'  );

function custom_save_account_details() {

    if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
        return;
    }

    if ( empty( $_POST['action'] ) || 'save_account_details' !== $_POST['action'] || empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_account_details' ) ) {
        return;
    }

    $errors       = new WP_Error();
    $user         = new stdClass();

    $user->ID     = (int) get_current_user_id();
    $current_user = get_user_by( 'id', $user->ID );

    if ( $user->ID <= 0 ) {
        return;
    }

    $account_first_name = ! empty( $_POST['account_first_name'] ) ? wc_clean( $_POST['account_first_name'] ) : '';
    $account_last_name  = ! empty( $_POST['account_last_name'] ) ? wc_clean( $_POST['account_last_name'] ) : '';
    $account_email      = ! empty( $_POST['account_email'] ) ? wc_clean( $_POST['account_email'] ) : '';
    $pass_cur           = ! empty( $_POST['password_current'] ) ? $_POST['password_current'] : '';
    $pass1              = ! empty( $_POST['password_1'] ) ? $_POST['password_1'] : '';
    $pass2              = ! empty( $_POST['password_2'] ) ? $_POST['password_2'] : '';
    $save_pass          = true;

    $user->first_name   = $account_first_name;
    $user->last_name    = $account_last_name;


    // Prevent emails being displayed, or leave alone.
    $user->display_name = is_email( $current_user->display_name ) ? $user->first_name : $current_user->display_name;

    if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
        wc_add_notice( __( 'Please fill out all password fields.', 'woocommerce' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
        wc_add_notice( __( 'Please enter your current password.', 'woocommerce' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
        wc_add_notice( __( 'Please re-enter your password.', 'woocommerce' ), 'error' );
        $save_pass = false;
    } elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
        wc_add_notice( __( 'New passwords do not match.', 'woocommerce' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
        wc_add_notice( __( 'Your current password is incorrect.', 'woocommerce' ), 'error' );
        $save_pass = false;
    }

    if ( $pass1 && $save_pass ) {
        // Handle required fields
        $required_fields = apply_filters( 'woocommerce_save_account_details_required_fields', array(
            'account_first_name' => __( 'First name', 'woocommerce' ),
            'account_email'      => __( 'Email address', 'woocommerce' ),
        ) );
    }else{
        $required_fields = apply_filters( 'woocommerce_save_account_details_required_fields', array(
            'account_first_name' => __( 'First name', 'woocommerce' ),
            'account_last_name'  => __( 'Last name', 'woocommerce' ),
            'account_email'      => __( 'Email address', 'woocommerce' ),
        ) );
    }

    foreach ( $required_fields as $field_key => $field_name ) {
        if ( empty( $_POST[ $field_key ] ) ) {
            wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_name ) . '</strong>' ), 'error' );
        }
    }

    if ( $account_email ) {
        $account_email = sanitize_email( $account_email );
        if ( ! is_email( $account_email ) ) {
            wc_add_notice( __( 'Please provide a valid email address.', 'woocommerce' ), 'error' );
        } elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
            wc_add_notice( __( 'This email address is already registered.', 'woocommerce' ), 'error' );
        }
        $user->user_email = $account_email;
    }



    if ( $pass1 && $save_pass ) {
        $user->user_pass = $pass1;
    }

    // Allow plugins to return their own errors.
    do_action_ref_array( 'woocommerce_save_account_details_errors', array( &$errors, &$user ) );

    if ( $errors->get_error_messages() ) {
        foreach ( $errors->get_error_messages() as $error ) {
            wc_add_notice( $error, 'error' );
        }
    }

    if ( wc_notice_count( 'error' ) === 0 ) {

        wp_update_user( $user );

        wc_add_notice( __( 'Account details changed successfully.', 'woocommerce' ) );
        do_action( 'woocommerce_save_account_details', $user->ID );

        $myaccount = wc_get_page_permalink( 'myaccount' ) ;

        if( $current_user->roles[0] == 'candidate'){

            $args = apply_filters( 'resume_manager_get_dashboard_resumes_args', array(
                'post_type'           => 'resume',
                'post_status'         => 'any',
                'ignore_sticky_posts' => 1,
                'posts_per_page'      => -1,
                'author'              => $user->ID
            ) );

            $resumes = new WP_Query( $args );
            wc_get_page_permalink( 'myaccount' );
            exit;
        }

        if( $current_user->roles[0] == 'employer' ) {
            wp_safe_redirect(home_url().'/job-dashboard');
            exit;
        } else {
            wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
            exit;
        }
    }
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
        'post_status'         => 'completed',
        'posts_per_page'      => -1,
        'ignore_sticky_posts' => 1,
        'meta_key'            => '_candidate_user_id',
        'meta_value'          => $user_id,
    ) );

    $applications_list = array();
    $applications = new WP_Query( $args );
    $available_cash = 0;
    if ( $applications ->have_posts()){
        while ( $applications ->have_posts() ) {
            $applications -> the_post();

            $application_id     = $applications->post ->ID;

            $job_id             = wp_get_post_parent_id( $application_id );

            $job                = get_post( $job_id );

            if ( !$job ) return false;


            $job_price          = get_post_meta( $job_id, '_targeted_budget', true );
            if ( !$job_price ) $job_price = get_post_meta($job_id, 'Budget_for_the_influencer', true );

            $available_cash += $job_price;
        }
        wp_reset_postdata();
    }

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

include_once get_stylesheet_directory() . '/inc/job-listing-handler.php';


add_action( 'register_form_child', 'workscout_register_form_child' );

remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'process_registration' ), 20 );
remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'process_login'), 20 );


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
add_action('wp_ajax_nopriv_send_on_review', 'send_on_review');


function aj_sync_social(){

    $user_id = get_current_user_id();

    if ( !$user_id )  wp_send_json_error( array('error'=> 'User not found') );

    $insta_link     = get_user_meta( $user_id, 'insta', true);
    $fb_link        = get_user_meta( $user_id, 'fb', true);
    $twitter_link   = get_user_meta( $user_id, 'twitter', true);
    $youtube_link   = get_user_meta( $user_id, 'youtube', true);
    $jrrny_link     = get_user_meta( $user_id, '_jrrny_link', true);

    $website        = get_user_meta( $user_id, 'website', true );
    $monthly_visitors = get_user_meta( $user_id, 'monthlyvisit', true);
    $influencer_number = get_user_meta( $user_id, 'phone_number', true);
    $influencer_location = get_user_meta( $user_id, 'location', true);
    $influencer_bio = get_user_meta( $user_id, 'shortbio', true);
    $logo           = get_user_meta( $user_id, 'photo', true );

    $response = array();
    if ( $insta_link )
        $response['insta_link'] = $insta_link;

    if ( $fb_link )
        $response['fb_link'] = $fb_link;

    if ( $twitter_link )
        $response['twitter_link'] = $twitter_link;

    if ( $youtube_link )
        $response['youtube_link'] = $youtube_link;

    if ( $jrrny_link )
        $response['jrrny_link'] = $jrrny_link;

    if ( $website )
        $response['influencer_website'] = $website;

    if ( $monthly_visitors )
        $response['estimated_monthly_visitors'] = $monthly_visitors;

    if ( $influencer_number )
        $response['influencer_number'] = $influencer_number;

    if ( $influencer_location )
        $response['influencer_location'] = $influencer_location;

    if ( $influencer_bio )
        $response['short_influencer_bio'] = $influencer_bio;

    if ( $logo ){
        $dir = wp_get_upload_dir();
        $response['candidate_photo'] = $dir['baseurl'].'/users/'.$logo;
    }

    wp_send_json_success( $response );

}

add_action('wp_ajax_aj_sync_social', 'aj_sync_social');
add_action('wp_ajax_nopriv_aj_sync_social', 'aj_sync_social');


require_once 'socilal-apis.php';

add_action('init', 'paypal_request_payment');

function paypal_request_payment(){

    if(isset($_POST['action']) && $_POST['action'] == 'send_payment_request' && wp_verify_nonce($_POST['r_nonce'], 'r-nonce')) {

        $paypal_email = $_POST['payout_destination'];
        $paypal_amount = $_POST['amount'];
        $success = 1;


        if ( !preg_match('/\$?((\d{1,4}(,\d{1,3})*)|(\d+))(\.\d{2})?\$?/', $paypal_amount)) {

            $_SESSION['error'] = 'Please verify amount';
            $success = 0;

        }

        $paypal_amount = preg_replace('/\$/', '', $paypal_amount);


        if ( !is_numeric($paypal_amount) ){
            $_SESSION['error'] = 'Please verify amount';
            $success = 0;
        }

        $user = wp_get_current_user();
        $currency = get_woocommerce_currency_symbol();

        if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) {

            $available_cash = get_candidate_cash_out_sum($user->ID);

        } else {
            $_SESSION['error'] .= 'You don\'t have permission for this operation';
            $success = 0;
        }


        if ( $available_cash < $paypal_amount) {
            $_SESSION['error'] .= 'Available amount is less then Requested amount';
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


function custom_load_scripts(){
		if ($GLOBALS["header_type"]=="newhomepage"){
			//wp_dequeue_script( 'jquery' );

			//wp_enqueue_script('vendor', get_stylesheet_directory_uri() . '/js/vendor.min.js', array(), '1', true );
			//wp_enqueue_script('newhomepage-main', get_stylesheet_directory_uri() . '/js/main.min.js', array(), '1', true );

			wp_enqueue_script('actions', get_stylesheet_directory_uri() . '/js/actions.js', array(), '1', true );
		}
		
	}
add_action('wp_enqueue_scripts', 'custom_load_scripts', 200);


function process_login_custom() {

    $nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
    $nonce_value = isset( $_POST['woocommerce-login-nonce'] ) ? $_POST['woocommerce-login-nonce'] : $nonce_value;

    if ( ! empty( $_POST['login'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {

        try {
            $creds = array(
                'user_password' => $_POST['password'],
                'remember'      => isset( $_POST['rememberme'] ),
            );

            $username         = trim( $_POST['username'] );
            $validation_error = new WP_Error();
            $validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

            if ( $validation_error->get_error_code() ) {
                throw new Exception( '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . $validation_error->get_error_message() );
            }

            if ( empty( $username ) ) {
                throw new Exception( '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . __( 'Username is required.', 'woocommerce' ) );
            }

            if ( is_email( $username ) && apply_filters( 'woocommerce_get_username_from_email', true ) ) {
                $user = get_user_by( 'email', $username );

                if ( isset( $user->user_login ) ) {
                    $creds['user_login'] = $user->user_login;
                } else {
                    throw new Exception( '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . __( 'A user could not be found with this email address.', 'woocommerce' ) );
                }
            } else {
                $creds['user_login'] = $username;
            }

            // On multisite, ensure user exists on current site, if not add them before allowing login.
            if ( is_multisite() ) {
                $user_data = get_user_by( 'login', $username );

                if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
                    add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
                }
            }

            // Perform the login
            $user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

            if ( is_wp_error( $user ) ) {
                $message = $user->get_error_message();
                $message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $username ) . '</strong>', $message );
                throw new Exception( $message );
            } else {

                $role = $user->roles[0];

                $myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );

                if( $role == 'employer' || $role == 'administrator' ) {
                    if(get_option( 'job_manager_job_dashboard_page_id')) {
                        $redirect = home_url().'/job-dashboard';

                    } else {
                        $redirect= home_url();
                    };
                } elseif ( $role == 'candidate' ) {
                    if(get_option( 'resume_manager_candidate_dashboard_page_id')) {
                        $redirect = get_permalink(get_option( 'resume_manager_candidate_dashboard_page_id'));
                    } else {
                        $redirect= home_url();
                    };
                } elseif ( $role == 'customer' || $role == 'subscriber' ) {
                    //Redirect customers and subscribers to the "My Account" page
                    $redirect = $myaccount;
                } else {
                    //Redirect any other role to the previous visited page or, if not available, to the home
                    $redirect = wp_get_referer() ? wp_get_referer() : home_url();
                }
                wp_redirect( apply_filters( 'woocommerce_login_redirect', $redirect, $user ) );
                exit;
            }
        } catch ( Exception $e ) {
            wc_add_notice( apply_filters( 'login_errors', $e->getMessage() ), 'error' );
            do_action( 'woocommerce_login_failed' );
        }
    }
}

add_action( 'wp_loaded',  'process_login_custom'  );

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


                        $post_title = $firstname." ".$lastname;

                        $post_content = '';

                        $data = array(
                            'post_title'     => $post_title,
                            'post_content'   => $post_content,
                            'post_type'      => 'resume',
                            'comment_status' => 'closed',
                            'post_password'  => '',
                            'post_author'    => $new_customer
                        );

                        $data['post_status'] = 'preview';

                        $resume_id = wp_insert_post( $data );

                        update_post_meta( $resume_id, '_candidate_name',get_the_title( $resume_id) );
                        update_post_meta( $resume_id, '_candidate_email', $email );

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
        <div class="listing__wrapper">
            <p class="list__number"><span>Estimated Engagement: </span><span><?php echo round($possible_reach*0.03)." - ".round($possible_reach*0.07)?> </span></p>
            <p>Average   possible   reach</p>
        </div>
        <?php $submit_job_page = get_permalink(get_option('job_manager_submit_job_form_page_id')); ?>
        <a href="<?php echo $submit_job_page; ?>" class="button button_green">Let's Build My Campaign</a>
        <?php endif; ?>

    </section>
    <?php
    exit;
}

/*------------------------------------   Dashboard Init -----------------------------------------------*/
include_once get_stylesheet_directory() . '/inc/term-walker.php';
require_once 'inc/dashboard-init.php';
