<?php
/**
 * Account Functions live here
 */

remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'process_registration' ), 20 );               /* remove registration process */
add_action('wp_ajax_custom_redirect_newhomepage', 'traverse_redirect_newhomepage');                   /* new registration process */
add_action('wp_ajax_nopriv_custom_redirect_newhomepage', 'traverse_redirect_newhomepage');

remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'process_login'), 20 );                       /* remove login process */
   add_action( 'wp_loaded',  'traverse_process_login'  );                                             /* add new login process */

remove_action( 'woocommerce_edit_account_form', 'my_woocommerce_edit_account_form' );               /* remove edit account process */
   add_action( 'woocommerce_edit_account_form', 'traverse_woocommerce_edit_account_form' );         /* add new edit account process */

remove_action( 'woocommerce_save_account_details', 'my_woocommerce_save_account_details' );         /* remove save account process */
   add_action( 'woocommerce_save_account_details', 'traverse_my_woocommerce_save_account_details' );   /* add new save account process */
   add_action( 'woocommerce_save_account_details_errors','traverse_validate_custom_field', 10, 1 );     /*add validation for new save account process*/

remove_action( 'template_redirect', array( 'WC_Form_Handler', 'save_account_details' ) );           /* remove WC redirect after save account */
   add_action( 'template_redirect',  'traverse_save_account_details'  );                              /* add new WC redirect after save account */

function traverse_redirect_newhomepage(){ //add_action('wp_ajax_custom_redirect_newhomepage', 'traverse_redirect_newhomepage');

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
                $error[] = __('Please accept our Terms of Service');
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


function traverse_process_login() { //add_action( 'wp_loaded',  'traverse_process_login'  );

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
                    /*
                     * block update _audience and social links
                     */
                    @update_audience_for_user($user->ID);
                    @update_finished_companies_for_user( $user->ID );

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


function traverse_woocommerce_edit_account_form() {  //add_action( 'woocommerce_edit_account_form', 'traverse_woocommerce_edit_account_form_child' );

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
                        <option value="<?php echo $portfolio_type->term_id; ?>" <?php if ( $traveler_type && in_array( $portfolio_type->term_id, $traveler_type) ) echo "selected" ; ?>><?php echo $portfolio_type->name; ?></option>
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
            <label class="form__input__label" for="fb">YOUR FACEBOOK URL<span id="face_book_login"></span></label>
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

        <div class="input__block newsletterr_block">
            <p>DOES YOUR WEBSITE HAVE A NEWSLETTER?</p>
            <label for="newsletter_yes"><input type="radio" id="newsletter_yes" name="newsletter" value="yes" <?php if ( $newsletter == 'yes') echo 'checked = "checked"';?>>
                <span class="circle"></span>
                <span class="check"></span>YES
            </label>
            <label for="newsletter_no"><input type="radio" id="newsletter_no" name="newsletter" value="no" <?php if ($newsletter == 'no') echo 'checked = "checked"';?>>
                <span class="circle"></span>
                <span class="check"></span>NO
            </label>
        </div>

        <div class="input__block newsletter_conditional <?php if ($newsletter != 'yes') echo 'invisible';?>" >
            <input class="input-text form__input <?php if (!empty($newsletter_subscriber_count)) echo 'has-value';?>"    type="text" name="newsletter_subscriber" value="<?php echo esc_attr( $newsletter_subscriber_count ); ?>"   />
            <label style="z-index: 99" class="form__input__label" for="newsletter_subscriber">IF YES, HOW MANY SUBSCRIBERS?</label>
        </div>

        <div class="input__block panel__search panel__search fieldset-header_image">
            <input class="input-text panel__search__input <?php if (!empty($logo)) echo 'has-value';?>"    type="file" name="logo"  id = "logo_img" value="<?php echo esc_attr( $logo ); ?>"   />
            <label class="panel__search__input panel__search__input__label" for="logo_img">YOUR PROFILE PHOTO <div class="upload-btn button_search"></div></label>

        </div>

        <div class="input__block">
            <div id="logo_im" class="logo_im">
                <?php
                if (!empty($logo)){?>
                    <img class="user_logo" src="<?php echo $logo ?>" alt="Photo">
                <?php }else{ ?>
                    <img class="user_logo" src="<?php echo get_template_directory_uri().'/images/candidate.png'?>" alt="Photo">
                <?php } ?>
            </div>
        </div>
        <?php
        $user_resumes = get_posts( array(
            'post_type'           => 'resume',
            'post_status'         => 'all',
            'ignore_sticky_posts' => 1,
            'posts_per_page'      => -1,
            'author'              => $user_id

        ) );

        if ( $user_resumes ){
            foreach ( $user_resumes as $user_resumes ){
                $photo_samples = get_post_meta( $user_resumes->ID, '_photo_sample', true);
                $videos = get_post_meta( $user_resumes->ID, '_video_sample_embed', true);

                preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $videos, $video);
            }
        }
        ?>
        <div class="input__block full_width">
            <h5>
                Add examples of your photo/video work to show brands what you are capable of
            </h5>
        </div>

        <div class="full_width panel__search fieldset-header_image">
            <input class="form__input input-text panel__search__input" type="file" name="samples[]" id="samples" multiple/>
            <label class="panel__search__input panel__search__input__label" for="samples">Add photography samples: <div class="upload-btn button_search"></div></label>
        </div>

        <div id="photos" class="photos">
            <?php if ($photo_samples ){
                $margin = 2;
                $width = (100 - 2*$margin*count ($photo_samples))/count ($photo_samples);
                foreach ($photo_samples as $photo_sample){?>
                    <div class="im_wr">
                        <span class="remove exists" data-resume_id="<?php echo $user_resumes->ID; ?>">remove</span>
                        <img src="<?php echo $photo_sample ?>" alt="Photo Sample" >
                    </div>
                <?php }} ?>
        </div>


        <div class="input__block full_width ">
            <input class="form__input input-text <?php if (!empty($videos)) echo 'has-value';?>" type="url" name="video" value="<?php echo esc_attr( $videos ); ?>"/>
            <label class="form__input__label" for="video">Add a sample video (YouTube Embed Link): </label>
        </div>

        <?php if ( $video ){ ?>
            <div class="video">
                <div data-embed="<?= $video[0] ?>" class="youtube">
                    <div class="play-button "></div>
                </div>
            </div>
        <?php } ?>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.remove').click(function(){
                        $(this).parent(".im_wr").remove();
                        if ( $(this).hasClass('exists')){

                            var data = {
                                id: $(this).data('resume_id'),
                                value: $(this).parent(".im_wr").find('img').attr('src'),
                                action: 'remove_assets_from_resume'
                            }

                            $.ajax({
                                url: ws.ajaxurl,
                                data: data,
                                type: 'POST',
                                dataType: 'JSON',
                                cache: false,
                                success: function(response) {

                                }
                            });
                        }
                    }
                );

                var imagesPreview = function(input, placeToInsertImagePreview) {
                    if (input.files) {
                        var filesAmount = input.files.length;
                        for (var i = 0; i < filesAmount; i++) {
                            var file = input.files[i];
                            var reader = new FileReader();
                            reader.onload = function(event) {

                                var item = "<div class=\"im_wr\"><span class=\"remove\">remove</span>" +
                                    "<img src=\"" + event.target.result + "\" title=\"" + file.name + "\"/>"+
                                    "</div>";

                                $(placeToInsertImagePreview).append(item);

                                $(".remove").click(function(){
                                    $(this).parent(".im_wr").remove();
                                });

                            }
                            reader.readAsDataURL(input.files[i]);
                        }
                    }
                };

                $("#samples").change(function() {
                    imagesPreview(this, 'div.photos');
                });

                $("#logo_img").change(function() {
                    $('.user_logo').remove();
                    imagesPreview(this, 'div.logo_im');
                });

                $('input[name="newsletter"]').on('change', function(){
                    var val = $(this).val();
                    if ( val == 'yes'){
                        $('.newsletter_conditional').removeClass('invisible');
                    }else{
                        $('.newsletter_conditional').addClass('invisible');
                        $('input[name="newsletter_subscriber"]').val('');
                    }
                });
            });
        </script>


    <?php
    }

    if( $str->roles[0] == "employer" || $str->roles[0] == "administrator"){
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

        <div class="input__block">
            <textarea class="input-text <?php if (!empty($shortbio)) echo 'has-value';?>" name="shortbio"><?php echo esc_attr( $shortbio ); ?></textarea>
            <label class="form__input__label" for="birthdate">SHORT BIO</label>
        </div>

        <div class="input__block panel__search panel__search fieldset-header_image">
            <input class="input-text panel__search__input <?php if (!empty($logo)) echo 'has-value';?>"    type="file" name="logo"  id = "logo_img" value="<?php echo esc_attr( $logo ); ?>"   />
            <label class="panel__search__input panel__search__input__label" for="logo_img">YOUR PROFILE PHOTO  <div class="upload-btn button_search"></div></label>

        </div>

        <div class="input__block">

            <input class="form__input input-text <?php if (!empty($website)) echo 'has-value';?>"    type="text" name="website" value="<?php echo esc_attr( $website ); ?>"   />
            <label class="form__input__label" for="website">WEBSITE</label>
        </div>

        <div class="input__block">
            <div id="logo_im" class="logo_im">
                <?php
                if (!empty($logo)){?>
                    <img class="user_logo" src="<?php echo $logo ?>" alt="Photo">
                <?php }else{ ?>
                    <img class="user_logo" src="<?php echo get_template_directory_uri().'/images/candidate.png'?>" alt="Photo">
                <?php } ?>
            </div>
        </div>


        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                var imagesPreview = function(input, placeToInsertImagePreview) {
                    if (input.files) {
                        var filesAmount = input.files.length;
                        for (var i = 0; i < filesAmount; i++) {
                            var file = input.files[i];
                            var reader = new FileReader();
                            reader.onload = function(event) {

                                var item = "<div class=\"im_wr\"><span class=\"remove\">remove</span>" +
                                    "<img src=\"" + event.target.result + "\" title=\"" + file.name + "\"/>"+
                                    "</div>";

                                $(placeToInsertImagePreview).append(item);

                                $(".remove").click(function(){
                                    $(this).parent(".im_wr").remove();
                                });

                            }
                            reader.readAsDataURL(input.files[i]);
                        }
                    }
                };


                $("#logo_img").change(function() {
                    $('.user_logo').remove();
                    imagesPreview(this, 'div.logo_im');
                });

            });
        </script>
    <?php
    }
} // end func

add_action('wp_ajax_remove_assets_from_resume', 'remove_assets_from_resume');

function remove_assets_from_resume(){
    $id = $_POST['id'];
    $meta_value = $_POST['value'];

    if ( $photos = get_post_meta ($id, '_photo_sample', true)){
        foreach ($photos as $key => $photo){
            if ( $photo == $meta_value) unset ($photos[$key]);

        }
        update_post_meta( $id, '_photo_sample', $photos);
    }

    die;
}


function traverse_validate_custom_field( $args )
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

function traverse_my_woocommerce_save_account_details( $user_id ) { //add_action( 'woocommerce_save_account_details', 'traverse_woocommerce_save_account_details_child' );
    $str = get_userdata($user_id);

    $errors = false;

    if( $str->roles[0] == "candidate" )
    {
        /* save to uploads/users/2017/10/ */
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

                $path = $dir['basedir']."/users/";
                $url = $dir['baseurl']."/users/";

                @move_uploaded_file($file_tmp, $path."/".$new_name);

                update_user_meta( $user_id, 'photo', $url.$new_name );
            }
        }

        if (isset($_FILES['samples']['name']) && !empty($_FILES['samples']['name']) ){
            $error = array();
            $extension=array("jpeg","jpg","png","gif");

            /* save to uploads/job-manager-uploads/asset_upload/2017/10/ */
            $samples = array();
            foreach($_FILES["samples"]["tmp_name"] as $key=>$tmp_name){
                $filename = $_FILES["samples"]["name"][$key];
                $file_tmp  = $_FILES["samples"]["tmp_name"][$key];

                $ext = pathinfo( $filename, PATHINFO_EXTENSION );

                $dir = wp_get_upload_dir();

                $path = $dir['basedir']."/job-manager-uploads/asset_upload".$dir['subdir'];
                $url = $dir['baseurl']."/job-manager-uploads/asset_upload".$dir['subdir'];

                if( in_array( $ext,$extension) ){
                    $new_name = $str->user_login."_".time()."_".$key.".".$ext;
                    @move_uploaded_file( $file_tmp = $_FILES["samples"]["tmp_name"][$key],$path."/".$new_name);
                    $samples[] = $url."/".$new_name  ;
                }else {
                    array_push( $error, "$filename, " );
                }

            }
        }


        foreach ( $_POST as $key => $value ){
            if ( isset($_POST[ $key ]) && !empty ($_POST[ $key ]))
                update_user_meta( $user_id, $key, htmlentities( $_POST[ $key ] ) );
            elseif ( isset($_POST[ $key ]) && empty ($_POST[ $key ])){
                delete_user_meta( $user_id, $key);
            }
        }


        $user_resumes =  get_posts( array(
            'post_type'           => 'resume',
            'post_status'         => 'all',
            'ignore_sticky_posts' => 1,
            'posts_per_page'      => -1,
            'author'              => $user_id

        ) );

        if ( $user_resumes ){
            foreach ( $user_resumes as $user_resumes ){

                if ( get_user_meta($user_id, 'photo', true) )
                    update_post_meta( $user_resumes->ID, '_candidate_photo', get_user_meta($user_id, 'photo', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_candidate_photo' );

                if ( get_user_meta($user_id, 'website', true) )
                    update_post_meta( $user_resumes->ID, '_influencer_website',get_user_meta($user_id, 'website', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_influencer_website' );


                if ( get_user_meta($user_id, 'jrrny_link', true) )
                    update_post_meta( $user_resumes->ID, '_jrrny_link', get_user_meta($user_id, 'jrrny_link', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_jrrny_link' );


                if ( get_user_meta($user_id, 'monthlyvisit', true) )
                    update_post_meta( $user_resumes->ID, '_estimated_monthly_visitors', get_user_meta($user_id, 'monthlyvisit', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_estimated_monthly_visitors' );


                if ( get_user_meta($user_id, 'insta', true) )
                    update_post_meta( $user_resumes->ID, '_instagram_link', get_user_meta($user_id, 'insta', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_instagram_link' );


                if ( get_user_meta($user_id, 'fb', true) )
                    update_post_meta( $user_resumes->ID, '_facebook_link', get_user_meta($user_id, 'fb', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_facebook_link' );

                if ( get_user_meta($user_id, 'twitter', true) )
                    update_post_meta( $user_resumes->ID, '_twitter_link',get_user_meta($user_id, 'twitter', true) );
                else{
                    delete_post_meta( $user_resumes->ID, '_twitter_link' );
                }

                if ( get_user_meta($user_id, 'youtube', true) )
                    update_post_meta( $user_resumes->ID, '_youtube_link',get_user_meta($user_id, 'youtube', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_youtube_link' );


                if ( get_user_meta($user_id, 'newsletter', true) )
                    update_post_meta( $user_resumes->ID, '_newsletter', get_user_meta($user_id, 'newsletter', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_newsletter' );


                if ( get_user_meta($user_id, 'newsletter_subscriber_count', true) )
                    update_post_meta( $user_resumes->ID, '_newsletter_total', get_user_meta($user_id, 'newsletter_subscriber_count', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_newsletter_total' );


                if ( get_user_meta($user_id, 'shortbio', true) )
                    update_post_meta( $user_resumes->ID, '_portfolio_description', get_user_meta($user_id, 'shortbio', true));
                else
                    delete_post_meta( $user_resumes->ID, '_portfolio_description' );


                if ( get_user_meta($user_id, 'shortbio', true) )
                    update_post_meta( $user_resumes->ID, '_short_influencer_bio', get_user_meta($user_id, 'shortbio', true));
                else
                    delete_post_meta( $user_resumes->ID, '_short_influencer_bio' );


                if ( get_user_meta($user_id, 'traveler_type', true) ) {
                    $cat_ids = array_map( 'intval', get_user_meta($user_id, 'traveler_type', true) );
                    $cat_ids = array_unique( $cat_ids );
                    wp_set_object_terms( $user_resumes->ID, $cat_ids, 'resume_category' );
                }else{
                    wp_set_object_terms( $user_resumes->ID, NULL, 'resume_category' );
                }

                if ( get_user_meta($user_id, 'location', true) )
                    update_post_meta( $user_resumes->ID, '_resume_locations', get_user_meta($user_id, 'location', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_resume_locations' );


                if ( get_user_meta($user_id, 'phone_number', true) )
                    update_post_meta( $user_resumes->ID, '_influencer_number', get_user_meta($user_id, 'phone_number', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_influencer_number' );


                if ( get_user_meta($user_id, 'video', true) )
                    update_post_meta( $user_resumes->ID, '_video_sample_embed', get_user_meta($user_id, 'video', true) );
                else
                    delete_post_meta( $user_resumes->ID, '_video_sample_embed' );



                if ( sizeof( $samples ) ) {

                    $current_samples = get_post_meta( $user_resumes->ID, '_photo_sample', true);

                    if ( $current_samples ) $samples = array_merge( $current_samples, $samples);

                    update_post_meta( $user_resumes->ID, '_photo_sample', $samples );
                }

                if ( $user_resumes -> post_status == 'preview'){
                    wp_update_post( array(
                        'ID'            => $user_resumes->ID,
                        'post_status'   => 'publish',
                        'post_title'    => $str-> first_name.' '.$str-> last_name

                    ) );
                }

                @update_audience_for_user( $user_id );
                @update_finished_companies_for_user( $user_id );

            }
        }

    }

    if( $str->roles[0] == "employer" || $str->roles[0] == "administrator" ) {

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

                $path = $dir['basedir']."/users/";
                $url = $dir['baseurl']."/users/";

                @move_uploaded_file($file_tmp, $path."/".$new_name);

                update_user_meta( $user_id, 'logo', $url.$new_name );
            }
        }

        foreach ( $_POST as $key => $value ){
            if ( isset($_POST[ $key ]) && !empty ($_POST[ $key ]))
                update_user_meta( $user_id, $key, htmlentities( $_POST[ $key ] ) );
            elseif ( isset($_POST[ $key ]) && empty ($_POST[ $key ]))
                delete_user_meta( $user_id, $key);
        }

    }
}// end func

function traverse_save_account_details() { //add_action( 'template_redirect',  'traverse_save_account_details'  );

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
            wp_safe_redirect( $myaccount );
            exit;
        }

        if( $current_user->roles[0] == 'employer' ) {
            wp_safe_redirect(home_url().'/job-dashboard');
            exit;
        } else {
            wp_safe_redirect( $myaccount );
            exit;
        }
    }
}





