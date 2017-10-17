<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           Jrrny_Registration
 *
 * @wordpress-plugin
 * Plugin Name:       Jrrny Registration
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Olha Novikova
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jrrny-registration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jrrny-registration-activator.php
 */
function activate_jrrny_registration() {

    if ( !class_exists( 'woocommerce' ) ){

        wp_die( _e('<strong>JRRNY Registration</strong></a> requires the WooCommerce plugin to be activated. Please <a href="https://woocommerce.com/‎">WooCommerceI</a> first.', 'jrrny_registration').' <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');

    }
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jrrny-registration-deactivator.php
 */
function deactivate_jrrny_registration() {
}


register_activation_hook( __FILE__, 'activate_jrrny_registration' );
register_deactivation_hook( __FILE__, 'deactivate_jrrny_registration' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jrrny-registration.php';
require plugin_dir_path( __FILE__ ) . 'public/public_functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_jrrny_registration() {

	$plugin = new Jrrny_Registration();
	$plugin->run();

}

run_jrrny_registration();
