<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_roles;
$current_user = wp_get_current_user();

$roles = $current_user->roles[0];
$all_meta_for_user = get_user_meta( $current_user->ID );

$part = 'main';
if ( isset( $_GET['password']) && $_GET['password'] == 'change') $part = 'pass';
?>


    <?php do_action( 'woocommerce_before_edit_account_form' );?>

    <form class="woocommerce-EditAccountForm edit-account form" action="" enctype="multipart/form-data" method="post">
        <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

        <?php if ($part == 'main'){ ?>
        <p class="form__header">Account Settings</p>
            <div class="form__inputs inputs">
                <?php
                if( $roles=="candidate" || $roles=="employer"){?>
                    <div class="input__block">
                        <input type="email" class="woocommerce-Input woocommerce-Input--email form__input <?php if (isset($current_user->user_email)) echo 'has-value';?>" name="account_email" id="account_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" />
                        <label for="account_email" class="form__input__label"><?php _e( 'EMAIL ADDRESS', 'woocommerce' ); ?> <span class="required">*</span></label>
                    </div>
                    <div class="input__block">
                        <input type="text" class="woocommerce-Input woocommerce-Input--email form__input <?php if (isset($current_user->first_name)) echo 'has-value';?>" name="account_first_name" id="account_email" value="<?php echo esc_attr( $current_user->first_name ? $current_user->first_name : $current_user->display_name ); ?>" />
                        <label for="account_email" class="form__input__label"><?php _e( 'YOUR FIRST NAME', 'woocommerce' ); ?> <span class="required">*</span></label>
                    </div>
                    <div class="input__block">
                        <input type="text" class="form__input <?php if (isset($current_user->last_name)) echo 'has-value';?>" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $current_user->last_name ); ?>" />
                        <label for="account_last_name" class="form__input__label"><?php _e( 'YOUR LAST NAME', 'woocommerce' ); ?> <span class="required">*</span></label>
                    </div>


                <?php }

                if($roles=="administrator" ){?>

                    <input type="hidden" class="woocommerce-Input woocommerce-Input--email input-text" name="account_first_name" id="account_email" value="<?php echo esc_attr( $current_user->first_name ? $current_user->first_name : $current_user->display_name ); ?>" />
                    <input type="hidden" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $current_user->last_name ); ?>" />
                    <input type="hidden" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" />

                <?php } ?>

                <?php 	 do_action( 'woocommerce_edit_account_form' );  ?>
            </div>

            <div class="settings__button">
                <?php wp_nonce_field( 'save_account_details' ); ?>
                <input type="hidden" name="action" value="save_account_details" />
                <input type="submit" class="button button_orange"  id="save_account_details" name="save_account_details" value="<?php esc_attr_e( 'Save Account!', 'woocommerce' ); ?>" />
            </div>
        <?php } else {?>

            <input type="hidden" class="woocommerce-Input woocommerce-Input--email input-text" name="account_first_name" id="account_email" value="<?php echo esc_attr( $current_user->first_name ? $current_user->first_name : $current_user->display_name ); ?>" />
            <input type="hidden" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $current_user->last_name ); ?>" />
            <input type="hidden" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" />

            <p class="form__header">Change Password</p>
            <div class="form__inputs inputs">
                <div class="input__block">
                    <input id="first" type="password" class="form__input" name="password_current" id="password_current"/>
                    <label for="first" class="form__input__label">Confirm Current Password</label>
                </div>
                <div class="input__block">
                    <input id="second" type="password" class="form__input" name="password_1" id="password_1"/>
                    <label for="second" class="form__input__label">Your New Password</label>
                </div>
                <div class="input__block">
                    <input id="third" type="password" class="form__input"/>
                    <label for="third" class="form__input__label" name="password_2" id="password_2">Confirm New Password</label>
                </div>
            </div>
            <div class="password__settings">
                <div class="checkbox">
                    <input id="first-check" type="checkbox" class="form__checkbox"/>
                    <label for="first-check" class="checkbox__label">Remember New Password</label>
                </div><a href="#" class="checkbox__forget">Forgot my Password</a>
            </div>
            <div class="settings__button">
                <?php wp_nonce_field( 'save_account_details' ); ?>
                <input type="hidden" name="action" value="save_account_details" />
                <input type="submit" class="button button_orange"  id="save_account_details" name="save_account_details" value="<?php esc_attr_e( 'Change Password Now!', 'woocommerce' ); ?>" />
            </div>

        <?php } ?>
        <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
    </form>

    <?php do_action( 'woocommerce_after_edit_account_form1' ); ?>
    <?php do_action( 'woocommerce_after_edit_account_form' ); ?>



