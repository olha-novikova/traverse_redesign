<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Dinah!' );
}
 
class PUR_Plugin {
	public function __construct() {
		add_action( 'after_setup_theme' , array( $this, 'after_setup_theme' ) );
	}
	
	public function after_setup_theme() {
		load_plugin_textdomain( 'pur', null, PUR_PATH . 'languages' );

		require_once PUR_PATH . 'includes/class-pur-options.php';
		require_once PUR_PATH . 'includes/class-pur-assets.php';
		require_once PUR_PATH . 'includes/class-pur-frontend.php';
		require_once PUR_PATH . 'includes/class-pur-shortcodes.php';
		require_once PUR_PATH . 'includes/class-wp-pur.php';
		
		if ( is_admin() ) {
			do_action( 'rh-php-commons' );
			
			require_once PUR_PATH . 'includes/class-pur-taxonomies.php';
			require_once PUR_PATH . 'includes/class-pur-visual-composer.php';
		}
	}
}  

return new PUR_Plugin();