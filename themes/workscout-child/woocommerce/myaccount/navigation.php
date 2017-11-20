<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$user = wp_get_current_user();
do_action( 'woocommerce_before_account_navigation' );
?>
<p class="settings__menu__list__header">Profile Settings</p>
<ul class="settings__menu__list">
<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
    <?php
    if( $endpoint !='orders' && $endpoint !='downloads' && $endpoint !='edit-address' && $endpoint != 'dashboard' ){?>
        <?php

        if ( $endpoint == 'edit-account') { ?>
            <li class="settings__menu__list-item">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">Edit Profile</a>
            </li>
            <li class="settings__menu__list-item">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ).'?password=change'; ?>"><?php echo esc_html( ucwords('Change Password') ); ?></a>
            </li>
        <?php } ?>

    <?php } ?>
<?php endforeach;?>
</ul>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
