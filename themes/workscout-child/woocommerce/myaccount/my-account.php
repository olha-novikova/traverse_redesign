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

                jQuery.magnificPopup.open({
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

                jQuery.magnificPopup.open({
                    items: {
                        src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                            '<div class="small-dialog-content"><p class="margin-reset"><?php esc_html_e("First, we need your account details, then you'll be able to create a listing and browse influencers.","workscout"); ?></p><p style="text-align: right;"><a class="button" href="<?php echo get_home_url()?>/my-account/edit-account">Next</a></p></div>'+
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

                jQuery.magnificPopup.open({
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
    }

    ?>
</div>
