<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       olha.novikova@gmail.com
 * @since      1.0.0
 *
 * @package    Jrrny_Registration
 * @subpackage Jrrny_Registration/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Jrrny_Registration
 * @subpackage Jrrny_Registration/includes
 * @author     Olha Novikova <olha.novikova@gmail.com>
 */
class Jrrny_Registration_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'jrrny-registration',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
