<?php

get_template_part('template-parts/page-header');

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="content">

    <section class="section section_settings">

        <div class="section__container settings__menu">
                <?php
            /**
             * My Account navigation.
             * @since 2.6.0
             */
            do_action( 'woocommerce_account_navigation' );
            ?>
        </div>

        <div class="section__container settings__workarea">
            <?php
            wc_print_notices();
            ?>
            <?php
                /**
                 * My Account content.
                 * @since 2.6.0
                 */
                do_action( 'woocommerce_account_content' );
            ?>
        </div>

    </section>
    <?php
    if ( is_user_logged_in() ) $user = new WP_User(get_current_user_id());
    if(isset($_GET['success']) && $_GET['success'] == 1 && is_user_logged_in())  {

    if($user->roles[0]=="candidate"){
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                $.magnificPopup.open({
                    items: {
                        src:'<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                            '<div class="small-dialog-content"><p>You are registered and logged in. Now we’ll set up your profile and portfolio. Click NEXT to get started. </p><p style="text-align: right;"><a class="button" href="<?php echo get_home_url()?>/my-account/edit-account">Next</a></p></div>'+
                            '</div>', // can be a HTML string, jQuery object, or CSS selector
                        type: 'inline'
                    }
                });
            });
        </script>
    <?php
    }elseif ($user->roles[0] == 'employer'){
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                $.magnificPopup.open({
                    items: {
                        src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                            '<div class="small-dialog-content"><p class="margin-reset"><?php esc_html_e("Next, we’ll capture some information about your company, so that we can match you with the best influencers!","workscout"); ?></p></div>'+
                            '</div>', // can be a HTML string, jQuery object, or CSS selector
                        type: 'inline'
                    }
                });
            });
        </script>
    <?php } else{
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                $.magnificPopup.open({
                    items: {
                        src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                            '<div class="small-dialog-content"><p class="margin-reset"><?php esc_html_e("Account details changed successfully. Thank you!","woocommerce"); ?></p></div>'+
                            '</div>', // can be a HTML string, jQuery object, or CSS selector
                        type: 'inline'
                    }
                });
            });
        </script>
    <?php }
    }elseif(isset($_GET['success']) && $_GET['success'] == 2 && is_user_logged_in())  {

    $myaccount = wc_get_page_permalink( 'myaccount' ) ;
    if($user->roles[0]=="employer") {
    $redirect =  home_url().'/brandhome';
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                $.magnificPopup.open({
                    items: {
                        src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                            '<div class="small-dialog-content"><p class="margin-reset"><?php esc_html_e( 'Account details changed successfully.', 'woocommerce' ); ?></p></div>'+
                            '</div>', // can be a HTML string, jQuery object, or CSS selector
                        type: 'inline'
                    }
                });
                setTimeout( function (){$.magnificPopup.close(); window.location.href = "<?php echo $redirect?>"}, 2000);
            });
        </script>

    <?php
    }
    elseif($user->roles[0] == 'candidate') {
    $redirect = home_url().'/influencer-php';
    $resume_page = get_option( 'resume_manager_submit_resume_form_page_id' );

    ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $.magnificPopup.open({
                    items: {
                        src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e('Great! Now let’s set up your portfolio.', 'woocommerce'); ?></h2></div>'+
                            '<div class="small-dialog-content">'+
                            '<p class="margin-reset">This is where you will create specific portfolios for your blog, photography, social accounts, and more. The sky’s the limit! <br>*Remember: the more descriptive you are, the more brands will see how perfect a fit you can be.  You can create multiple portfolios for different accounts.</p>'+
                            '<p class="margin-top-20 margin-bottom-20"><a class="button" href="<?php echo esc_url( get_permalink( $resume_page ) ); ?>"><?php esc_html_e( 'Let&#39;s Go', 'workscout' ); ?></a></p>'+
                            "<p class='margin-reset'><a href ='<?php echo $redirect; ?>'>No thanks. I’ll set up my portfolio later.</a></p>"+
                            '</div>'+
                            '</div>', // can be a HTML string, jQuery object, or CSS selector
                        type: 'inline'
                    }
                });
            });
        </script>

    <?php
    }
    }

    ?>
</div>
