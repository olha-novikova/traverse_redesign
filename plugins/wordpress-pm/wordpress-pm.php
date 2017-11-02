<?php
/*
	* Plugin Name: WordPress Private Messages / Ajax PM
	* Plugin URI: https://www.blueweb.md
	* Description: Ajax WordPress Private Messages / PM System
	* Version: 1.0.0
	* Author: Dan Lapteacru
	* Author URI: https://www.blueweb.md
	* Copyright: © 2017 bluwebteam.
	* License: GNU General Public License v3.0
	* License URI: http://www.gnu.org/licenses/gpl-3.0.html
	*
*/
	
	define( 'PM_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( 'PM_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
	define( 'PM_IMG', PM_URL.'/assets/img/' );
	define( 'PM_CSS', PM_URL.'/assets/css/' );
	define( 'PM_JS', PM_URL.'/assets/js/' );
	define( 'PM_VERSION',  1 );
	
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
	
	register_activation_hook( __file__, "wordpress_pm_install" );
	
	function wordpress_pm_install()
	{
		global $wpdb;
		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}
		$schema = "CREATE TABLE {$wpdb->prefix}pm_conversation (
			id bigint(200) NOT NULL auto_increment,
			sender bigint(200) NOT NULL,
			reciever  bigint(200) NOT NULL,
			job bigint(200) NOT NULL,
			job_name varchar(255) NOT NULL,
			delete_status boolean DEFAULT 0 NOT NULL,
			seen tinytext NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}pm_messages (
			id bigint(200) NOT NULL auto_increment,
			conv_id bigint(200) NOT NULL,
			attachment_id bigint(200) NULL,
			sender_id bigint(200) NOT NULL,
			reciever_id bigint(200) NOT NULL,
			message longtext NULL,
			status tinytext NULL,
			seen tinytext NULL,
			delete_status boolean DEFAULT 0 NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}pm_deleted_conversation (
			id bigint(200) NOT NULL auto_increment,
			user bigint(200) NOT NULL,
			conv_id bigint(200) NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}pm_blocked_conversation (
			id bigint(200) NOT NULL auto_increment,
			blocked_by bigint(200) NOT NULL,
			blocked_user bigint(200) NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}pm_attachments (
			id bigint(200) NOT NULL auto_increment,
			conv_id bigint(200) NULL,
			type tinytext NULL,
			size bigint(200) NULL,
			url longtext NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			PRIMARY KEY  (id)
		) $collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta($schema);
	}
	
	function pm_scripts() 
	{
		global $post;
		
		//general CSS
        wp_enqueue_style('sweetalert2', 'https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.css');
		
		//general JS
        wp_enqueue_script('sweetalert2', 'https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.all.min.js');
        wp_enqueue_script('sweetalert2-min', 'https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.min.js');
        
		if (isset($post) && (strpos($post->post_content, '[wp_pm_chatbox]') !== false))
		{
			//Chat CSS
			wp_enqueue_style('wp-private-messages', PM_CSS.'wp-pm.css');
			
			//Chat JS
			wp_enqueue_script( 'wp-private-messages', PM_JS . 'wp-pm.js', array('jquery'), PM_VERSION, true );
			wp_enqueue_script('dropzonejs', PM_JS . 'dropzone.js', array(), PM_VERSION, true );
			wp_register_script( 'momentjs', PM_JS . 'moment.js', array('jquery'), '2.19.1', true );
			wp_enqueue_script( 'momentjs' );
			wp_register_script( 'livestampjs', PM_JS . 'livestamp.min.js', array('jquery', 'momentjs'), '1.1.2', true );
			wp_enqueue_script( 'livestampjs' );
		} else {
			wp_register_script( 'wp-private-messages', PM_JS . 'wp-pm-nochat.js', array('jquery'), PM_VERSION, true );
		}
		
		wp_localize_script( 'wp-private-messages', 'wp_pm_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'userID' => get_current_user_id() ) );
		wp_enqueue_script( 'wp-private-messages' );
	}

	add_action( 'wp_enqueue_scripts', 'pm_scripts' );
	
	include("helper.php");
	include("ajax.php");
	include("shortcode.php");
	
	new PrivateMessagesAjax;