<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_roles;
$current_user = wp_get_current_user();

$roles = $current_user->roles[0];
$all_meta_for_user = get_user_meta( $current_user->ID );

$part = 'main';
if ( isset( $_GET['password']) && $_GET['password'] == 'change') $part = 'pass';

do_action( 'woocommerce_before_edit_account_form' );?>

<form class="woocommerce-EditAccountForm edit-account" action="" enctype="multipart/form-data" method="post">
    <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

    <?php if ($part == 'main'){ ?>
        <h2 class="my-acc-h2">Edit Account Fields</h2>
        <?php
        if( $roles=="candidate" || $roles=="employer"){?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="account_email"><?php _e( 'YOUR FIRST NAME', 'woocommerce' ); ?> <span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="account_first_name" id="account_email" value="<?php echo esc_attr( $current_user->first_name ? $current_user->first_name : $current_user->display_name ); ?>" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="account_last_name"><?php _e( 'YOUR LAST NAME', 'woocommerce' ); ?> <span class="required">*</span></label>
                <input type="text" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $current_user->last_name ); ?>" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="account_email"><?php _e( 'EMAIL ADDRESS', 'woocommerce' ); ?> <span class="required">*</span></label>
                <input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" />
            </p>

        <?php }

        if($roles=="administrator" ){?>

            <input type="hidden" class="woocommerce-Input woocommerce-Input--email input-text" name="account_first_name" id="account_email" value="<?php echo esc_attr( $current_user->first_name ? $current_user->first_name : $current_user->display_name ); ?>" />
            <input type="hidden" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $current_user->last_name ); ?>" />
            <input type="hidden" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" />

        <?php } ?>

        <?php 	 do_action( 'woocommerce_edit_account_form' );  ?>



    <?php } else {?>

        <h2><?php _e( 'Password change', 'woocommerce' ); ?></h2>

        <input type="hidden" class="woocommerce-Input woocommerce-Input--email input-text" name="account_first_name" id="account_email" value="<?php echo esc_attr( $current_user->first_name ? $current_user->first_name : $current_user->display_name ); ?>" />
        <input type="hidden" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $current_user->last_name ); ?>" />
        <input type="hidden" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" />

        <fieldset>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password_current"><?php _e( 'CURRENT PASSWORD (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password_1"><?php _e( 'NEW PASSWORD (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password_2"><?php _e( 'NEW PASSWORD', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" />
            </p>
        </fieldset>
    <?php } ?>

    <div class="clear"></div>

    <p>
		<?php wp_nonce_field( 'save_account_details' ); ?>
		<input type="submit" class="woocommerce-Button button" id="save_account_details" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" />
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form1' ); ?>
<?php do_action( 'woocommerce_after_edit_account_form' ); ?>


