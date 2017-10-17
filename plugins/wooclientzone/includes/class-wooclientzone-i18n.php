<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://blendscapes.com
 * @since      1.0.0
 *
 * @package    Wooclientzone
 * @subpackage Wooclientzone/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wooclientzone
 * @subpackage Wooclientzone/includes
 * @author     Enrico Sandoli <enrico.sandoli@blendscapes.com>
 */
class Wooclientzone_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wooclientzone',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
