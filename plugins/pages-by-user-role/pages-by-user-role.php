<?php
/*
Plugin Name: Pages by User Role for WordPress
Plugin URI: http://plugins.righthere.com/pages-by-user-role/
Description: Restrict access to different types of content like Pages, Posts, Custom Post Types or Categories depending on which Role the user has. It removes the Pages, Posts or Custom Post Types from search results and blog roll. You can hide Pages and Categories from the menu when the user is not logged in. You can also set a specific redirect URL for users that don't have the required User Role. 
Version: 1.3.6.78458
Author: Alberto Lau & Maxim Levikov (RightHere LLC)
Author URI: http://plugins.righthere.com
*/

define( 'PUR_VERSION', '1.3.6' );
define( 'PUR_PATH', plugin_dir_path( __FILE__ ) );
define( 'PUR_URL', plugin_dir_url( __FILE__ ) );
define( 'PUR_SLUG', plugin_basename( __FILE__ ) );
define( 'PUR_ADMIN_ROLE', 'administrator' );

require_once( PUR_PATH . 'includes/class-pur-plugin.php' );

if ( ! defined( 'SHORTINIT' ) || true !== SHORTINIT ) {
	register_activation_hook( __FILE__, 'pur_install' );
	
	function pur_install() {
		require_once( PUR_PATH . 'includes/install.php' );
		
		if ( function_exists( 'handle_pur_install' ) ) {
			handle_pur_install();
		}
	}
	
	register_deactivation_hook( __FILE__, 'pur_uninstall' );

	function pur_uninstall() {
		require_once( PUR_PATH . 'includes/install.php' );
		
		if ( function_exists( 'handle_pur_uninstall' ) ) {
			handle_pur_uninstall();
		}
	}
}
