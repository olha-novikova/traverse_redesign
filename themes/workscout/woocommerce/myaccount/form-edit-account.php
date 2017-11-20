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



 if(current_user_can( 'influencer' ,2 )){

do_action( 'woocommerce_before_edit_account_form' ); ?>
	  <form class="woocommerce-EditAccountForm edit-account" action="" enctype="multipart/form-data" method="post">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	
	
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_email"><?php _e( 'NAME', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="account_first_name" id="account_email" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_email"><?php _e( 'EMAIL ADDRESS', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>
	
	<!---p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_email"><?php _e( 'PHONE NUMBER', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p--->
	
	
	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details' ); ?>
		<input type="submit" class="woocommerce-Button button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" />
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>
<?php }
else{
	echo "No";
	do_action( 'woocommerce_before_edit_account_form1' ); ?>
	
	<form class="woocommerce-EditAccountForm1 edit-account" action="" enctype="multipart/form-data" method="post">

	<?php do_action( 'woocommerce_edit_account_form1_start' ); ?>

	
	
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_email"><?php _e( 'COMPANY NAME', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="account_first_name" id="account_email" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_email"><?php _e( 'EMAIL ADDRESS', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>
	
	
	
	<?php do_action( 'woocommerce_edit_account_form1' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details1' ); ?>
		<input type="submit" class="woocommerce-Button button" name="save_account_details1" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" />
		<input type="hidden" name="action" value="save_account_details1" />
	</p>
	<?php do_action( 'woocommerce_edit_account_form1_end' );?>
	</form>
	<?php
	}
	
?>

<?php do_action( 'woocommerce_after_edit_account_form1' ); ?>
<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
