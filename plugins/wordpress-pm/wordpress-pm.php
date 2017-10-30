<?php
/*
	* Plugin Name: WordPress Private Messages / Ajax PM
	* Plugin URI: https://www.blueweb.md
	* Description: Ajax WordPress Private Messages / PM System
	* Version: 1.0.0
	* Author: Dan Lapteacru
	* Author URI: https://www.blueweb.md
	* Requires at least: 3.6
	* Tested up to: 4.5
	*
	* Copyright: © 2017 bluwebteam.
	* License: GNU General Public License v3.0
	* License URI: http://www.gnu.org/licenses/gpl-3.0.html
	*
*/
	if (version_compare(PHP_VERSION, "5.4.0", "<")) 
	{

		function check_php_version_admin_notice() { ?>
			<div class="error">
				<p>Sorry this plugin use some <b>PHP VERSION 5.4</b> functionality. If you want to use this plugin please update your server <b>PHP VERSION 5.4</b> or higher.</p>
			</div>
			<?php
		}
		add_action( 'admin_notices', 'check_php_version_admin_notice' );
		return;
	}

	// define( 'PM_URL', untrailingslashit( get_stylesheet_directory_uri()."/pm" ) );
	define( 'PM_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
	define( 'PM_IMG', PM_URL.'/assets/img/' );
	define( 'PM_CSS', PM_URL.'/assets/css/' );
	define( 'PM_JS', PM_URL.'/assets/js/' );
	define( 'PM_VERSION',  1 );
	
	function pm_scripts() 
	{
		//CSS
        wp_enqueue_style('wp-private-messages', PM_CSS.'wp-pm.css');
        wp_enqueue_style('sweetalert2', 'https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.css');
		
		//JS
		wp_register_script( 'wp-private-messages', PM_JS . 'wp-pm.js', array('jquery'), PM_VERSION, true );
		wp_localize_script( 'wp-private-messages', 'wp_pm_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'userID' => get_current_user_id() ) );
		wp_enqueue_script( 'wp-private-messages' );
        wp_enqueue_script('sweetalert2', 'https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.all.min.js');
        wp_enqueue_script('sweetalert2-min', 'https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.min.js');
        wp_enqueue_script('dropzonejs', PM_JS . 'dropzone.js', array(), PM_VERSION, true );
		
		// wp_register_script( 'momentjs', PM_JS . 'moment.js', array('jquery'), '2.19.1', true );
		// wp_enqueue_script( 'momentjs' );
		
		// wp_register_script( 'livestampjs', PM_JS . 'livestamp.min.js', array('jquery', 'momentjs'), '1.1.2', true );
		// wp_enqueue_script( 'livestampjs' );
		
		
	}

	add_action( 'wp_enqueue_scripts', 'pm_scripts' );
	
	include("helper.php");
	include("ajax.php");
	include("shortcode.php");
	
	new PrivateMessagesAjax;