<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://blendscapes.com
 * @since             1.0.0
 * @package           Wooclientzone
 *
 * @wordpress-plugin
 * Plugin Name:       WooClientZone
 * Plugin URI:        http://blendscapes.com/wooclientzone
 * Description:       This plugin integrates with WooCommerce to create areas where clients and merchants can interact by exchanging files and rich-text messages.
 * Version:           1.0.2
 * Author:            Enrico Sandoli
 * Author URI:        http://blendscapes.com
 * Text Domain:       wooclientzone
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wooclientzone-activator.php
 */
function activate_wooclientzone() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wooclientzone-activator.php';
	Wooclientzone_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wooclientzone-deactivator.php
 */
function deactivate_wooclientzone() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wooclientzone-deactivator.php';
	Wooclientzone_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wooclientzone' );
register_deactivation_hook( __FILE__, 'deactivate_wooclientzone' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wooclientzone.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wooclientzone() {

	$plugin = new Wooclientzone();
	$plugin->run();
}

/**
 * Performs various checks and displays global notice messages
 *
 * @since    1.0.0
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	// if this is not the case, recommend to enable the woocommerce_enable_myaccount_registration WooCommerce option
	$woocommerce_enable_myaccount_registration = get_option('woocommerce_enable_myaccount_registration');
	$wooclientzone_use_userzones = get_option('wooclientzone_use_userzones');
	if ($wooclientzone_use_userzones === 'yes' && $woocommerce_enable_myaccount_registration === 'no') {

		function wooclientzone_recommend_woocommerce_enable_myaccount_registration() {
			?>
			<div class="notice notice-warning">
				<p><?php echo sprintf(__( 'With plugin WooClientZone, to be able to effectively use Client Zones related to customers (independently from orders), '
					. 'it is recommended to enable the WooCommerce setting <i>Enable registration on the "My account" page</i>, '
					. 'so that customers can register (and use their individual Client Zone) before placing orders.'
					. '<br><br>Please do so from %sWooCommerce Settings > Accounts%s.', 'wooclientzone' ),
					"<a href='".admin_url( 'admin.php?page=wc-settings&tab=account' )."'>",
					"</a>"
					); ?></p>
			</div>
			<?php
		}
		add_action( 'admin_notices', 'wooclientzone_recommend_woocommerce_enable_myaccount_registration' );	
	}
	
	// check if user- and order-based client zones are enabled; if both are disabled, notify the user that there's no point in using this plugin
	$userzones_enabled = get_option('wooclientzone_use_userzones') == 'yes';
	$orderzones_enabled = get_option('wooclientzone_use_orderzones') != 'never';
	if (!($userzones_enabled || $orderzones_enabled)) {
		
		function wooclientzone_recommend_either_user_or_order_zones_are_enabled() {
			?>
			<div class="notice notice-error">
				<p><?php echo sprintf(__( 'WooClientZone is active, but at present both user-linked and order-linked Client Zones are disabled. '
					. 'Please enable at least one of these types of Client Zones from %sWooclientZone Settings > Client Zones%s tab.', 'wooclientzone' ),
					"<a href='".admin_url( 'admin.php?page=wc-settings&tab=wooclientzone' )."'>",
					"</a>"
					); ?></p>
			</div>
			<?php
		}
		add_action('admin_notices', 'wooclientzone_recommend_either_user_or_order_zones_are_enabled');
	}
	

	// ok, run the plugin
	run_wooclientzone();
	
} else {
	function wooclientzone_woocommerce_not_active_error_notice() {
	    ?>
	    <div class="notice notice-error">
	        <p><?php echo sprintf(__( 'WooClientZone requires WooCommerce to run %s Please install and/or activate WooCommerce to use WooClientZone.', 'wooclientzone' ),
				'<span class="dashicons dashicons-arrow-right"></span>'); ?></p>
	    </div>
	    <?php
	}
	add_action( 'admin_notices', 'wooclientzone_woocommerce_not_active_error_notice' );	
}


