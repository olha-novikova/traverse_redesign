<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link	   http://blendscapes.com
 * @since	  1.0.0
 *
 * @package	Wooclientzone
 * @subpackage Wooclientzone/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package	Wooclientzone
 * @subpackage Wooclientzone/admin
 * @author	 Enrico Sandoli <enrico.sandoli@blendscapes.com>
 */
class Wooclientzone_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var	   string	$plugin_name	The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$version	The current version of this plugin.
	 */
	private $version;

	/**
	 * The file manager private property.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  object	$filemanager	The file manager class instance of this plugin.
	 */
	private $filemanager;

	/**
	 * The tools private property.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var		  object		$tools		An object with utility methods.
	 */
	private $tools;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string	$plugin_name	   The name of this plugin.
	 * @param    string	$version	The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->filemanager = new Wooclientzone_File_Manager( $plugin_name, $version );
		$this->tools = new Wooclientzone_Tools( $plugin_name, $version );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wooclientzone_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wooclientzone_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// we enqueue the style sheet and add some inline style immediately after
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wooclientzone-admin.css', array(), $this->version, 'all' );

		// we now add dynamically generated styling (for progress bar color)
		$progress_bar_color = get_option( 'wooclientzone_progress_bar_color_admin' );

		// check colors: if problems use default ones */
		$color_regex = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';

		if ( ! preg_match( $color_regex, $progress_bar_color ) ) {
			$progress_bar_color = "#55cce1";
		}

		$progress_bar_color_css = "
			.dz-upload {
				background-color: ".$progress_bar_color.";
			}
			";

		wp_add_inline_style( $this->plugin_name, $progress_bar_color_css );
		
		// COMMON-FILE STYLES
		
		// enqueue common styles; the handler must be unique, so we added _common
		wp_enqueue_style( $this->plugin_name.'_common', plugin_dir_url( __FILE__ ) . '../includes/css/wooclientzone.css', array(), $this->version, 'all' );

		// we now add dynamically generated styling (for bubble colors)
		$bubbles_color_client_admin    = get_option('wooclientzone_bubbles_color_client_admin');
		$bubbles_color_merchant_admin  = get_option('wooclientzone_bubbles_color_merchant_admin');

		// check colors: if problems use default ones */
		$color_regex = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';
		if (!preg_match($color_regex, $bubbles_color_client_admin)) {
			$bubbles_color_client_admin = "#f0f0f0";
		}
		if (!preg_match($color_regex, $bubbles_color_merchant_admin)) {
			$bubbles_color_merchant_admin = "#dcf7c8";
		}
		$bubbles_color_css = "
			.bubbles-color-client-admin {
				background: ".$bubbles_color_client_admin.";
			}
			.bubbles-color-client-admin:after {
				border-color: transparent ".$bubbles_color_client_admin.";
			}
			.bubbles-color-merchant-admin {
				background: ".$bubbles_color_merchant_admin.";
			}
			.bubbles-color-merchant-admin:after {
				border-color: transparent ".$bubbles_color_merchant_admin.";
			}
			";

		wp_add_inline_style($this->plugin_name.'_common', $bubbles_color_css);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wooclientzone_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wooclientzone_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// we enqueue and localize the js script
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wooclientzone-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'admin_js_options', $this->filemanager->get_admin_js_options() );
		// enqueue the dropzone
		wp_enqueue_script( $this->plugin_name.'_dropzone', plugin_dir_url( __FILE__ ) . '../includes/js/dropzone.js');
	}
	
	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=wooclientzone' ) . '" title="' . esc_attr( __( 'View WooClientZone Settings', 'wooclientzone' ) ) . '">' . __( 'Settings', 'wooclientzone' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public static function plugin_row_meta( $links, $file ) {

		if ( $file == "wooclientzone/wooclientzone.php" ) {
			$row_meta = array(
				'docs'     => '<a href="' . esc_url( 'http://blendscapes.com/wooclientzone_documentation/' ) . '" title="' . esc_attr( __( 'View WooClientZone Documentation', 'wooclientzone' ) ) . '" target="_blank">' . __( 'Docs', 'wooclientzone' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	/**
	 * Ajax callback to send email notifications to clients.
	 *
	 * @since	1.0.0
	 */
	public function admin_notify_client() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		$userid = isset($_POST['userid']) ? $_POST['userid'] : false;
		$orderid = isset($_POST['orderid']) ? $_POST['orderid'] : false;

		// set the base string for logging
		if ($orderid) {
			$current_action_log_msg = sprintf(__('Sending email notification of unseen messages to User ID %s (for Client Zone linked to Order ID %s).', 'wooclientzone'), $userid, $orderid);
		} else {
			$current_action_log_msg = sprintf(__('Sending email notification of unseen messages to User ID %s (for user-linked Client Zone).', 'wooclientzone'), $userid);
		}
		
		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_notify_client', 'security', false );
		if ( ! $security_check_passed ) {
			$response->error = true;
			$response->errorstring = __( 'Security check failed or timeout (notifying client)', 'wooclientzone' );
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode($response);
			die();
		}

		$email_text = isset($_POST['email_text']) ? trim( $_POST['email_text'] ) : ( $orderid ? get_option( 'wooclientzone_mail_to_client_order_clientzone' ) : get_option( 'wooclientzone_mail_to_client_user_clientzone' ) );

		// sanity check
		if ( ! $email_text ) {
			$response->error = true;
			$response->errorstring = __('Email text not found', 'wooclientzone');
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode($response);
			die();
		}

		// prepare email text
		$user_info = get_userdata( $userid );
		$client_name = $user_info->first_name ? $user_info->first_name : $user_info->user_login;
		$site_name = get_bloginfo( 'name' );
		$my_account_link = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

		$email_text = str_replace( '[client_name]', $client_name, $email_text );
		$email_text = str_replace( '[site_name]', $site_name, $email_text );

		// we want to try and match the [my_account_link] and the optional label (.+)
		// if the label is present, then the $matches array would have two elements
		// (the first would be the full matched string, the second would be the actual label)
		$matched = preg_match('/\[my_account_link]\s*\((.+)\)/', $email_text, $matches);
		if ($matched) {
			$email_text = str_replace($matches[0], '<a href="'.$my_account_link.'">'.$matches[1].'</a>', $email_text);
		} else {
			$email_text = str_replace('[my_account_link]', $my_account_link, $email_text);
		}
		
//		$email_text = preg_replace('/\[my_account_link]\((.+)\)/', '<a href="'.$my_account_link.'">${1}</a>', $email_text);
//		$email_text = str_replace( '[my_account_link]', $my_account_link, $email_text );
		if ( $orderid ) {
			$email_text = str_replace( '[order_id]', $orderid, $email_text );
		}
		
		$email_text = wpautop( $email_text );

		// prepare other email parameters
		// we provide filter hook for $user_email, which can be used to extend functionality, to send email to a range of addresses
		$user_email = apply_filters('wooclientzone_client_notification_email_to', $user_info->user_email);
		$subject = isset($_POST['email_subject']) ? trim( $_POST['email_subject'] ) : get_option( 'wooclientzone_mail_to_client_subject' );

		// sanity check
		if ( ! $user_email ) {
			$response->error = true;
			$response->errorstring = __( 'User email not found', 'wooclientzone' );
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode( $response );
			die();
		}

		// send the email in html format and using the admin address and website name as from:
		$headers[] = "Content-type: text/html";
		$headers[] = "From: ".get_bloginfo('name')." <".get_bloginfo( 'admin_email' ).">";
		$email_result = wp_mail( $user_email, $subject, $email_text, $headers );

		// check for error
		if ( ! $email_result ) {
			$response->error = true;
			$response->errorstring = __( 'Error sending email', 'wooclientzone' );
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode( $response );
			die();
		}

		// return successfully
		$response->feedback = __( 'Email sent successfully', 'wooclientzone' );
		$this->tools->log($current_action_log_msg." ".$response->feedback, WOOCLIENTZONE_LOG_INFO);
		echo json_encode( $response );
		die();
	}

	/**
	 * Ajax callback to save clientzone-specific client permissions.
	 *
	 * @since	1.0.0
	 */
	public function admin_save_client_permissions() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		// create args object and ask filemanager to set client permissions
		$args = new stdClass();
		$args->userid = isset($_POST['userid']) ? $_POST['userid'] : false;
		$args->orderid = isset($_POST['orderid']) ? $_POST['orderid'] : false;
		$args->upload_enabled = $_POST['uploadEnabled'];
		$args->message_enabled = $_POST['messageEnabled'];

		// set the base string for logging
		if ($args->orderid) {
			$current_action_log_msg = sprintf(__('Saving new client permissions [upload: %s; message: %s] for User ID %s (for Client Zone linked to Order ID %s).', 'wooclientzone'),
				($args->upload_enabled === 'true' ? 'yes' : 'no'), ($args->message_enabled === 'true' ? 'yes' : 'no'), $args->userid, $args->orderid);
		} else {
			$current_action_log_msg = sprintf(__('Saving new client permissions [upload: %s; message: %s] for User ID %s (for user-linked Client Zone).', 'wooclientzone'),
				($args->upload_enabled === 'true' ? 'yes' : 'no'), ($args->message_enabled === 'true' ? 'yes' : 'no'), $args->userid);
		}
		
		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_save_client_permissions', 'security', false );
		if ( ! $security_check_passed ) {
			$response->error = true;
			$response->errorstring = __( 'Security check failed or timeout (saving client permissions)', 'wooclientzone' );
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode( $response );
			die();
		}

		$feedback = $this->filemanager->set_client_permissions( $args );
		if ( $feedback->error ) {
			$response->error = true;
			$response->errorstring = $feedback->errorstring;
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode( $response );
			die();
		}

		$response->feedback = __( 'New permissions saved successfully', 'wooclientzone' );
		$this->tools->log($current_action_log_msg." ".$response->feedback, WOOCLIENTZONE_LOG_INFO);
		echo json_encode( $response );
		die();
	}

	/**
	 * Ajax callback to move a client zone.
	 *
	 * @since	1.0.0
	 */
	public function admin_move_clientzone() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		// set args for file manager call
		$args = new stdClass();
		$args->userid = isset($_POST['userid']) ? $_POST['userid'] : false;
		$args->orderid = isset($_POST['orderid']) ? $_POST['orderid'] : false;
		$args->new_orderid = isset($_POST['newOrderid']) ? $_POST['newOrderid'] : false;
		$args->move_permissions = isset($_POST['movePermissions']) ? $_POST['movePermissions'] : false;

		// set the base string for logging
		if ($args->orderid) {
			$current_action_log_msg = sprintf(__('Moving the Client Zone of User ID %s (linked to Order ID %s) ', 'wooclientzone'), $args->userid, $args->orderid);
		} else {
			$current_action_log_msg = sprintf(__('Moving the Client Zone of User ID %s ', 'wooclientzone'), $args->userid, $args->orderid);
		}
		if ($args->new_orderid) {
			$current_action_log_msg .= sprintf(__('to Client Zone linked to Order ID %s.', 'wooclientzone'), $args->new_orderid);
		} else {
			$current_action_log_msg .= __('to their personal Client Zone.', 'wooclientzone');
		}
		
		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_move_clientzone', 'security', false );
		if ( ! $security_check_passed ) {
			$response->error = true;
			$response->errorstring = __( 'Security check failed or timeout (moving Client Zone)', 'wooclientzone' );
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode( $response );
			die();
		}

		$filemanager_response = $this->filemanager->move_files_across_clientzones( $args );

		if ( $filemanager_response->error ) {
			$response->error = true;
			$response->errorstring = $filemanager_response->errorstring;
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode($response);
			die();
		}

		$this->tools->log($current_action_log_msg, WOOCLIENTZONE_LOG_INFO);
		echo json_encode( $response );
		die();
	}

	/**
	 * Ajax callback to store a new message from the public site.
	 *
	 * @since	1.0.0
	 */
	public function admin_submit_message() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		// set args for file manager call
		$args = new stdClass();
		$args->is_admin = true;
		$args->userid = isset( $_POST['userid'] ) ? $_POST['userid'] : false;
		$args->orderid = isset( $_POST['orderid'] ) ? $_POST['orderid'] : false;

		// set the base string for logging
		if ($args->orderid) {
			$current_action_log_msg = sprintf(__('Admin is submitting a message to Client Zone of User ID %s linked to Order ID %s.', 'wooclientzone'), $args->userid, $args->orderid);
		} else {
			$current_action_log_msg = sprintf(__('Admin is submitting a message to the personal Client Zone of User ID %s.', 'wooclientzone'), $args->userid);
		}

		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_submit_message_admin', 'security', false );
		if ( ! $security_check_passed ) {
			$response->error = true;
			$response->errorstring = __('Security check failed or timeout (submit message)', 'wooclientzone');
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode($response);
			die();
		}

		// prevent refreshes during this process
		$this->filemanager->do_busy();

		// get the file manager to manage the submission
		$filemanager_response = $this->filemanager->submit_message( $args );

		if ( $filemanager_response->error ) {
			$this->filemanager->undo_busy();
			$response->error = true;
			$response->errorstring = $filemanager_response->errorstring;
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode( $response );
			die();
		}

		// Ok the message has been submitted, so now we can go ahead and get this message, together with any
		// potential new files uploaded or messages submitted by the client since the last access by us (admin)

		// load an object with all the latest communications; we use the same args as the previous call, we just add the following:
		$args->load_latest = true; // we are asking to load only the new files since our (admin) last access to the directory
		$communications = $this->filemanager->load_communications_object( $args );

		// check for errors
		if ( $communications->error ) {
			$this->filemanager->undo_busy();
			$response->error = true;
			$response->errorType = $communications->errorType; // this is needed to display an info message instead of a warning in case no files are found
			$response->errorstring = $communications->errorstring;
			$this->tools->log($current_action_log_msg." ".$response->errorstring, ($communications->errorType === 'info') ? WOOCLIENTZONE_LOG_INFO : WOOCLIENTZONE_LOG_ERROR);
			echo json_encode( $response );
			die();
		}

		// Note we need to pass the client last access (returned by filemanager together with the files array) to be able to set up the 'seen' strings correctly
		$response->new_divs = $this->get_communications_divs( $communications->files, $communications->client_lastaccess );
		$response->client_lastaccess = $communications->client_lastaccess;

		$this->tools->log($current_action_log_msg, WOOCLIENTZONE_LOG_INFO);
		echo json_encode( $response );
		$this->filemanager->undo_busy();
		die();
	}

	/**
	 * Ajax callback to upload files from the public site.
	 *
	 * @since	1.0.0
	 */
	public function admin_upload_files() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		// set args for file manager call
		$args = new stdClass();
		$args->is_admin = true;
		$args->userid = isset( $_POST['userid'] ) ? $_POST['userid'] : false;
		$args->orderid = isset( $_POST['orderid'] ) ? $_POST['orderid'] : false;

		// set the base string for logging
		if ($args->orderid) {
			$current_action_log_msg = sprintf(__('Admin is upoading a file to Client Zone of User ID %s linked to Order ID %s.', 'wooclientzone'), $args->userid, $args->orderid);
		} else {
			$current_action_log_msg = sprintf(__('Admin is uploading a file to the personal Client Zone of User ID %s.', 'wooclientzone'), $args->userid);
		}

		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_upload_files_admin', 'security', false );
		if ( ! $security_check_passed ) {
			$response->error = true;
			$response->errorstring = __( 'Security check failed or timeout (upload files)', 'wooclientzone' );
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode( $response );
			die();
		}

		// prevent refreshes during this process
		$this->filemanager->do_busy();

		// get the file manager to manage the upload
		$filemanager_response = $this->filemanager->upload_file( $args );

		if ( $filemanager_response->error ) {
			$this->filemanager->undo_busy();
			$response->error = true;
			$response->errorstring = $filemanager_response->errorstring;
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode($response);
			die();
		}

		// Ok the file has been uploaded, so now we can go ahead and get this file (together with any
		// potential new files uploaded or messages submitted by the client since the last access by us (admin)

		// load an object with all the latest communications; we use the same args as the previous call, we just add the following:
		$args->load_latest = true; // we are asking to load only the new files since our (admin) last access to the directory
		$communications = $this->filemanager->load_communications_object($args);

		// check for errors
		if ($communications->error) {
			$this->filemanager->undo_busy();
			$response->error = true;
			$response->errorType = $communications->errorType; // this is needed to display an info message instead of a warning in case no files are found
			$response->errorstring = $communications->errorstring;
			$this->tools->log($current_action_log_msg." ".$response->errorstring, ($communications->errorType === 'info') ? WOOCLIENTZONE_LOG_INFO : WOOCLIENTZONE_LOG_ERROR);
			echo json_encode($response);
			die();
		}

		// Note we need to pass the client last access (returned by filemanager together with the files array) to be able to set up the 'seen' strings correctly
		$response->new_divs = $this->get_communications_divs($communications->files, $communications->client_lastaccess);
		$response->client_lastaccess = $communications->client_lastaccess;

		$this->tools->log($current_action_log_msg, WOOCLIENTZONE_LOG_INFO);
		echo json_encode($response);
		$this->filemanager->undo_busy();
		die();
	}

	/**
	 * Ajax callback to load communications (files and messages) to the client zone in the admin site.
	 * This method is called when first loading all communications and also whenever a refresh is needed,
	 * such as after file or message upload or for a periodic refresh; it asks the filemanager instance
	 * for a list of files, returning a list of the div elements that are to be included inside the
	 * #adminCommunicationsPlaceholder div element.
	 *
	 * @since	1.0.0
	 */
	public function admin_load_communications() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		$userid = isset($_POST['userid']) ? $_POST['userid'] : false;
		$orderid = isset($_POST['orderid']) ? $_POST['orderid'] : false;
		$refreshing = isset($_POST['refreshing']) ? $_POST['refreshing'] : false;

		// set the base string for logging
		if ($orderid) {
			$current_action_log_msg = sprintf(__('Admin is viewing the Client Zone of User ID %s linked to Order ID %s.', 'wooclientzone'), $userid, $orderid);
		} else {
			$current_action_log_msg = sprintf(__('Admin is viewing the personal Client Zone of User ID %s.', 'wooclientzone'), $userid);
		}

		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_load_communications_admin', 'security', false );
		if (!$security_check_passed) {
			$response->error = true;
			$response->errorstring = __('Security check failed or timeout (load communications)', 'wooclientzone');
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode($response);
			die();
		}

		// if refreshing check if filemnager is busy
		if ($refreshing && $this->filemanager->is_busy()) {
			$response->error = true; // note that we are terminating gracefully during a refresh (see js script)
			$response->errorstring = false;
			echo json_encode($response);
			die();
		}

		// sanity check
		if (!$userid && !$orderid) {
			$response->error = true;
			$response->errorstring = __('User or Order ID not found', 'wooclientzone');
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode($response);
			die();
		}

		// load an object with all communications
		$args = new stdClass();
		$args->is_admin = true;
		$args->userid = $userid;
		$args->orderid = $orderid;
		$args->load_latest = $refreshing ? true : false; // we are asking whether to load all files or just the latest
		$communications = $this->filemanager->load_communications_object($args);

		// we immediately set the client last access, because this is needed to check the 'seen' visibility in the js script
		// even if no new files are found during a refresh
		$response->client_lastaccess = $communications->client_lastaccess;

		// check for errors
		if ($communications->error) {
			$response->error = true;
			$response->errorType = $communications->errorType; // this is needed to display an info message instead of a warning in case no files are found
			// we terminate gracefully when returning no new comms after a refresh
			$response->errorstring = $refreshing ? false : $communications->errorstring;
			if (!$refreshing) {
				$this->tools->log($current_action_log_msg." ".$response->errorstring, ($communications->errorType === 'info') ? WOOCLIENTZONE_LOG_INFO : WOOCLIENTZONE_LOG_ERROR);
			}
			echo json_encode($response);
			die();
		}

		$response->new_divs = $this->get_communications_divs($communications->files, $communications->client_lastaccess);

		$this->tools->log($current_action_log_msg, WOOCLIENTZONE_LOG_INFO);
		echo json_encode($response);
		die();
	}

	/**
	 * Get the html of the communications to be displayed.
	 * 
	 * This method takes as input a file array (in the format defined by
	 * filemanager->load_communications_object) and a timestamp of the last
	 * access by the client, and returns a string with a list of div elements
	 * that are to be included in the #adminCommunicationsPlaceholder div
	 * element, or appended to the inside end of it (such as after a
	 * file upload, a message submission, or a refresh of the Client Zone).
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array    $files			The array of files.
	 * @param    integer  $client_lastaccess	  A timestamp of the last access by admin.
	 * @return string
	 */
	private function get_communications_divs($files, $client_lastaccess) {

		if (!$files) {
			// return gracefully
			return false;
		}

		// get option for bubbles position
		$client_bubbles_position = get_option('wooclientzone_my_bubbles_position');

		ob_start();

		if (false) :
		?>
		<div>
			<?php
			echo "<h3>DEBUG Info</h3>";
			foreach($files as $file) {
				echo '<pre>', var_dump($file), '</pre>';
			}
			?>
		</div>
		<?php
		endif;
		foreach($files as $file) {

			// validate the file url as the first thing
			if (validate_file($file['url'])) {
				continue; // validate_file returns 0 if ok, 1, 2 or 3 if failed
			}

			// set bubble position and colors based on backend options
			$bubble_position = $file['origin'] == 'admin' ? $client_bubbles_position : ($client_bubbles_position == 'right' ? 'left' : 'right');
			$bubble_color_class = $file['origin'] == 'admin' ? 'bubbles-color-merchant-admin' : 'bubbles-color-client-admin';

			// date text just below the bubble
			$bubble_date = $file['upload_date'];

			// the 'seen' div visibility is managed by the js script using the client last access date; here we only set the div for admin communications
			$seen_div = $file['origin'] == 'admin' ? "<div class='bubble-footer-seen' style='display:none;'>".__('Seen', 'wooclientzone')."</div>" : "";

			// define bubble type (has different padding styling) and content in the various cases
			if ($file['is_message']) {
				$bubble_type = 'text'; // needed to set different padding around images and text messages
				$bubble_content = '<div class="bubble-content">'.$file['message'].'</div>';
			}
			else {
				// img enclosing tags with path info to be used by js in its ajax call to download script
				// TODO see if we can use a script to download the files
				$a_tag_start = "<a href='".$file['url']."' download='".$file['name']."'>";
				$a_tag_end = "</a>";

				// display image as responsive
				if (substr($file['type'], 0, 5) == 'image') {
					// TODO if $file['url'] is false (absolute path or path outside the web root) then load an image icon instead
					$bubble_type = 'image';
					$bubble_content = $a_tag_start."<img src='".$file['url']."' title='".$file['name']."' class='wooclientzone-responsive-img'>".$a_tag_end;
				}
				else if ($file['type'] == 'application/pdf') {
					$bubble_type = 'icon';
					$bubble_content = $a_tag_start."<img src='".$this->filemanager->icon_pdf."' title='".$file['name']."' class='wooclientzone-responsive-img'>".$a_tag_end;
				}
				else {
					$bubble_type = 'icon';
					$bubble_content = $a_tag_start."<img src='".$this->filemanager->icon_file."' title='".$file['name']."' class='wooclientzone-responsive-img'>".$a_tag_end;
				}
			}
x			?>
			<!-- data-timestamp is used by the js script to set any 'seen' string accordingly -->
			<div data-timestamp='<?php echo $file['upload_timestamp'] ; ?>' class='filediv clearfix'>
				<div class='bubble bubble-<?php echo $bubble_type; ?> bubble-<?php echo $bubble_position; ?> <?php echo $bubble_color_class; ?> wooclientzone_shadowed_box'><?php echo $bubble_content; ?></div>
				<div class='bubble-footer bubble-footer-<?php echo $bubble_position; ?>'><div class='bubble-footer-date'><?php echo $bubble_date; ?></div><?php echo $seen_div; ?></div>
			</div>
		<?php
		}

		$communications_divs = ob_get_contents();
		ob_end_clean();
		return $communications_divs;
	}

	/**
	 * Here we define the actual code to display the client zone.
	 * 
	 * This is the callback from the action hook admin_wooclientzone that we fired
	 * from method display_admin_wooclientzone() of the settings class, which
	 * creates the new admin page. Parameters are passed by a $_GET global
	 * variable because it is not possible to directly pass parameters to the
	 * callback function hooked to the admin menu page creation.
	 *
	 * @since	1.0.0
	 */
	public function admin_wooclientzone() {

		$userid = $_GET['userid'];
		$orderid = isset($_GET['orderid']) ? $_GET['orderid'] : false;

		//
		$user_display_name = get_userdata($_GET['userid'])->display_name;
		$user_edit_link = "<a href='".get_edit_user_link($userid)."'>".$user_display_name."</a>";

		// set title
		if ($orderid) {
			// create title for order-linked client zone
			$order_edit_link = "<a href='".get_edit_post_link($orderid)."'>#".$orderid."</a>";
			$title = sprintf(__('Client zone linked to Order %s (Customer %s)','wooclientzone'), $order_edit_link, $user_edit_link);
		}
		else {
			// create title for user-linked client zone
			$title = sprintf(__('Client Zone for customer %s', 'wooclientzone'), $user_edit_link);
		}

		// we create the select line to give options to switch to another client zone, if available
		$notify_client_action = $this->get_notify_client_table($userid, $orderid);

		// we create the select line to give options to switch to another client zone, if available
		$select_other_clientzone = $this->get_select_other_clientzone_table($userid, $orderid);

		// we create the checkboxes to locally modify client permissions on this clientzone
		$client_permissions_action = $this->get_client_permissions_action_table($userid, $orderid);

		// we create the select line to move files from the current client zone to another of the same user
		$select_move_clientzone = $this->get_select_move_clientzone_table($userid, $orderid);

		// This is the template used by dropzone to create each file preview (including name, size, progress bar)
		// It is not displayed as such, but we define it to be referenced by our js script, to be used by dropzone
		$this->echo_dropzone_template();
		?>

		<!--The main wrapper-->
		<div id='adminWooclientzoneWrapper'>
			<!--The actions wrapper-->
			<div id='adminWooclientzoneActionsDiv'>
				<div id='wooclientzoneNotifyClientDiv' class='wooclientzone_actions wooclientzone_shadowed_box'>
					<div class='wooclientzone_actions_header'>
						<div class='wooclientzone_actions_header_text'><?php echo __('Notify Client', 'wooclientzone'); ?></div>
						<div class='wooclientzone_actions_header_icon'></div>
					</div>
					<div class='wooclientzone_actions_content'><?php echo $notify_client_action; ?></div>
				</div>
				<div class='wooclientzone_actions wooclientzone_shadowed_box'>
					<div class='wooclientzone_actions_header'>
						<div class='wooclientzone_actions_header_text'><?php echo __('Client Permissions', 'wooclientzone'); ?></div>
						<div class='wooclientzone_actions_header_icon'></div>
					</div>
					<div class='wooclientzone_actions_content'><?php echo $client_permissions_action; ?></div>
				</div>
				<?php if ($select_other_clientzone) : ?>
				<div class='wooclientzone_actions wooclientzone_shadowed_box'>
					<div class='wooclientzone_actions_header'>
						<div class='wooclientzone_actions_header_text'><?php echo __('Switch View', 'wooclientzone'); ?></div>
						<div class='wooclientzone_actions_header_icon'></div>
					</div>
					<div class='wooclientzone_actions_content'><?php echo $select_other_clientzone; ?></div>
				</div>
				<?php endif; ?>
				<div id='adminWooclientzoneMovezoneDiv'> <!-- needed to handle the action box from the admin js script (to hide it in case of empty zone) -->
				<?php if ($select_move_clientzone) : ?>
				<div class='wooclientzone_actions wooclientzone_shadowed_box'>
					<div class='wooclientzone_actions_header'>
						<div class='wooclientzone_actions_header_text'><?php echo __('Move this Client Zone', 'wooclientzone'); ?></div>
						<div class='wooclientzone_actions_header_icon'></div>
					</div>
					<div class='wooclientzone_actions_content'><?php echo $select_move_clientzone; ?></div>
				</div>
				<?php endif; ?>
				</div>
			</div>
			<!--The communications wrapper-->
			<div id='adminWooclientzoneCommunicationsDiv' class='wooclientzone_shadowed_box'>
				<!--Title area-->
				<div id='adminWooclientzoneTitle'><h1><?php echo $title; ?></h1></div>

				<!--The wrapper for all communications bubbles; the loader div is replaced by the js script with the actual content-->
				<div id='adminCommunicationsPlaceholder'><div class="loader-admin"></div></div>
				<div id='errorMessage'></div>
				<div id='successMessage'></div>

				<!--set message/upload div (which can be toggled on and off view)-->

				<div id='adminAddCommunicationHeader'>
					<div id='adminAddCommunicationHeaderText'><h3><?php _e('New communication', 'wooclientzone'); ?></h3></div>
					<div id='adminAddCommunicationHeaderIcon'></div>
				</div>
				<div id="adminAddCommunicationContent">
					<!--display send message area-->
					<div id='adminMessageFormPlaceholder' class='admin-message-editor-div'>
						<div>
						<?php
						$settings = array(
							'media_buttons' => false,
							'textarea_name' => 'admin_message_textarea',
							'textarea_rows' => 8,
							'editor_class'  => 'admin-editor',
							'quicktags'		=> false, // this removes the Visual/Text tabs
						);
						// id (second argument) to contain only lowercase letters and underscores (no hyphens)
						wp_editor('', 'adminmessagetextarea', $settings);
						?>
						</div>
						<div class='admin-message-submit-button-div'>
							<button id='adminMessageSubmitButton' class='button button-primary admin-message-submit-button'><?php _e('Send message', 'wooclientzone'); ?></button>
						</div>
					</div>
					<!--display upload area-->
					<div id='adminDropzone' class="dropzone dropzone-admin"></div>
					<div id='adminDropzonePreviewsContainer' class='dropzone-previews'></div>
				</div>
			</div>
		</div>
		<?php

	}

	/**
	 * Create the notifications html to send emails to the client.
	 * 
	 * This html string will get included inside the relevant action box.
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @param    int      $userid
	 * @param    int      $orderid
	 * @return   string
	 */
	private function get_notify_client_table($userid, $orderid) {

		$email_subject = get_option('wooclientzone_mail_to_client_subject');
		$label = "<strong>".__('Email body','wooclientzone')."</strong><br>";
		if ($orderid) {
			$email_text = get_option('wooclientzone_mail_to_client_order_clientzone');
			$label .= __('The following is the text of the email that you may send the client. '
				. 'Allowed codes are [client_name], [site_name], [order_id] and [my_account_link]. '
				. 'Note that the latter may be immediately followed by the link name in round brackets.', 'wooclientzone');
		} else {
			$email_text = get_option('wooclientzone_mail_to_client_user_clientzone');
			$label .= __('The following is the text of the email that you may send the client. '
				. 'Allowed codes are [client_name], [site_name] and [my_account_link]. '
				. 'Note that the latter may be immediately followed by the link name in round brackets.', 'wooclientzone');
		}

		$notify_client_table = "
			<table id='wooclientzoneNotifyClientTable' class='form-table'>
				<tbody>
				<tr>
					<td style='padding-top:0px;padding-bottom:0px;'>
					<div id='feedbackMessageNotifyClient' class='action_message'></div>
					</td>
				</tr>
				<tr>
					<td class='forminp forminp-input'>
					<fieldset>
						<legend>
						<label style='margin-bottom:10px !important;font-weight:bold;'>
							".__('Subject', 'wooclientzone')."
						</label>
						</legend>
						<input type='text' id='wooclientzoneNotifyClientSubject' style='width:100%;' value='".$email_subject."'>
					</fieldset>
					</td>
				</tr>
				<tr>
					<td class='forminp forminp-textarea'>
					<fieldset>
						<legend>
						<label style='margin-bottom:10px !important;'>
							".$label."
						</label>
						</legend>
						<textarea id='wooclientzoneNotifyClientTextarea' style='width:100%;height:200px'>".$email_text
						."</textarea>
					</fieldset>
					</td>
				</tr>
				</tbody>
			</table>
			<p class='submit' style='text-align:center'>
				<input id='wooclientzoneNotifyClientButton' name='save' class='button-primary woocommerce-save-button' type='submit' value='".__('Notify Client', 'wooclientzone')."'>
			</p>
			";

		return $notify_client_table;
	}

	/**
	 * Create the select line to give options to switch to another client zone.
	 * 
	 * This html string will get included inside the relevant action box.
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @param    int      $userid
	 * @param    int      $orderid
	 * @return   string
	 */
	private function get_select_other_clientzone_table($userid, $orderid) {

		$display_select = false;
		$select_other_clientzone = "
			<table id='wooclientzoneSelectOtherTable' class='form-table'>
				<tbody>
				<tr>
					<td class='forminp forminp-select'>
					<fieldset>
						<legend>
						<label>
							".__('Display Client Zone', 'wooclientzone')."
							<select id='selectOtherClientzone'>
							<option value='-1' selected>".__('Select Client Zone', 'wooclientzone')."</option>
			";
		if ($orders = $this->tools->get_orders_for_user_id($userid)) { // note this is an assignment, so single = is ok
			foreach($orders as $order) {

				// this is how we access order id (which should not be accessed directly as $order->id)
				$order_id = trim(str_replace('#', '', $order->get_order_number()));

				// skip current order
				if ($order_id == $orderid) {
					continue;
				}
				// skip if order client zone is not available
				if (!$this->tools->clientzone_enabled_for_order($order)) {
					continue;
				}
				//$userid = $order->get_user_id();
				$nonce_action = 'wooclientzone-userid='.$userid.'&orderid='.$order_id;
				$url = esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$userid.'&orderid='.$order_id), $nonce_action, 'wooclientzone'));
				$select_other_clientzone .= "<option value='".$order_id."' data-url='".$url."'>linked to order #".$order_id."</option>";
				$display_select = true;
			}
		}
		if ($orderid && get_option('wooclientzone_use_userzones') == 'yes') {
			// if viewing an order client zone, add option to switch to user client zone
			$nonce_action = 'wooclientzone-userid='.$userid.'&orderid=';
			$url = esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$userid), $nonce_action, 'wooclientzone'));
			$select_other_clientzone .= "<option value='0' data-url='".$url."'>not linked to an order</option>";
			$display_select = true;
		}
		if ($display_select) {
			$select_other_clientzone .= "
							</select> ".__('for this user', 'wooclientzone')."
						</label>
						<p class='description'>&nbsp;</p>
						</legend>
					</fieldset>
					</td>
				</tr>
				</tbody>
			</table>
			";
		} else {
			$select_other_clientzone = "";
		}

		return $select_other_clientzone;
	}

	/**
	 * Create the checkboxes to locally modify client permissions on this clientzone.
	 * 
	 * This html string will get included inside the relevant action box.
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @param    int      $userid
	 * @param    int      $orderid
	 * @return   string
	 */
	private function get_client_permissions_action_table($userid, $orderid) {

		$client_permissions = $this->filemanager->get_client_permissions($userid, $orderid);
		$client_permissions_action = "
			<table id='wooclientzoneClientPermissionsTable' class='form-table'>
				<tbody>
				<tr>
					<td style='padding-top:0px;padding-bottom:0px;'>
					<div id='feedbackMessageClientPermissions' class='action_message'></div>
					</td>
				</tr>
				<tr>
					<td class='forminp forminp-checkbox'>
					<fieldset>
						<legend>
						<label>
							<input id='wooclientzoneThisUploadEnabled' class='wooclientzone_client_permissions_checkbox' type='checkbox' value='1' ".($client_permissions->upload_enabled ? "checked='checked'" : "").">
							</input>
							Allow file upload
						</label>
						</legend>
					</fieldset>
					<fieldset>
						<legend>
						<label>
							<input id='wooclientzoneThisMessageEnabled' class='wooclientzone_client_permissions_checkbox' type='checkbox' value='1' ".($client_permissions->message_enabled ? "checked='checked'" : "").">
							</input>
							Allow submission of messages
						</label>
						<p class='description'>".__('This settings will only apply to this Client Zone and only when accessed by the client.', 'wooclientzone')."
						</p>
						</legend>
					</fieldset>
					</td>
				</tr>
				</tbody>
			</table>
			<p class='submit' style='text-align:center'>
				<input id='wooclientzoneThisClientPermissionsSaveButton' name='save' class='button-primary woocommerce-save-button' type='submit' value='".__('Save changes', 'wooclientzone')."' style='display:none;'>
			</p>
			";

		return $client_permissions_action;
	}

	/**
	 * Create the select line to move files from the current client zone to another of the same user.
	 * 
	 * This html string will get included inside the relevant action box.
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @param    int      $userid
	 * @param    int      $orderid
	 * @return   string
	 */
	private function get_select_move_clientzone_table($userid, $orderid) {

		$display_select = false;
		$select_move_clientzone = "
			<table id='wooclientzoneSelectMoveTable' class='form-table'>
				<tbody>
				<tr>
					<td class='forminp forminp-select'>
					<fieldset>
						<legend>
						<label>
							".__('Move Client Zone to one', 'wooclientzone')."
							<select id='selectMoveClientzone'>
							<option value='-1' selected>".__('Select Client Zone', 'wooclientzone')."</option>
			";
		if ($orders = $this->tools->get_orders_for_user_id($userid)) { // note this is an assignment, so single = is ok
			foreach($orders as $order) {
				
				// this is how we access order id (which should not be accessed directly as $order->id)
				$order_id = trim(str_replace('#', '', $order->get_order_number()));

				// skip current order
				if ($order_id == $orderid) {
					continue;
				}
				// skip if order client zone is not available
				if (!$this->tools->clientzone_enabled_for_order($order)) {
					continue;
				}
				$nonce_action = 'wooclientzone-userid='.$userid.'&orderid='.$order_id;
				$url = esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$userid.'&orderid='.$order_id), $nonce_action, 'wooclientzone'));
				$select_move_clientzone .= "<option value='".$order_id."' data-url='".$url."'>linked to order #".$order_id."</option>";
				$display_select = true;
			}
		}
		if ($orderid && get_option('wooclientzone_use_userzones') == 'yes') {
			// if viewing an order client zone, add option to switch to user client zone
			$nonce_action = 'wooclientzone-userid='.$userid.'&orderid=';
			$url = esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$userid), $nonce_action, 'wooclientzone'));
			$select_move_clientzone .= "<option value='0' data-url='".$url."'>not linked to an order</option>";
			$display_select = true;
		}
		if ($display_select) {
			$select_move_clientzone .= "
							</select> ".__('for this user', 'wooclientzone')."
						</label>
						</legend>
					</fieldset>
					</td>
				</tr>
				<tr>
					<td class='forminp forminp-checkbox' style='padding-top:0px;'>
					<fieldset>
						<legend>
						<label>
							<input id='wooclientzoneMoveClientPermissions' type='checkbox' value='1' checked='checked'>
							</input>
							Also move any specific client permissions you may have set
						</label>
						<p class='description'><div class='action_message action_message_warning'>".__('This action will move all communications from this Client Zone to the new one selected belonging to this same user.', 'wooclientzone')."</div></p>
						<div id='errorMessageMoveClientzone' class='action_message action_message_error'></div>
						</legend>
					</fieldset>
					</td>
				</tr>
				</tbody>
			</table>
			<p class='submit' style='text-align:center'>
				<input id='wooclientzoneMoveButton' name='save' class='button-primary woocommerce-save-button' type='submit' value='".__('Move Client Zone', 'wooclientzone')."' style='display:none;'>
			</p>
			";
		} else {
			$select_move_clientzone = "";
		}

		return $select_move_clientzone;
	}

	/**
	 * Create the html for the template of the dropzone.
	 * 
	 * This is the template used by dropzone to create each file preview
	 * (including name, size, progress bar); it is not displayed as such, but
	 * we define it to be referenced by our js script, to be used by dropzone.
	 * 
	 * @since    1.0.0
	 * @access   private
	 */
	private function echo_dropzone_template() {
		?>
		<div id='adminDropzonePreviewTemplate' style='display:none;'>
			<div class="dz-preview dz-file-preview">
				<div class="dz-details" style='margin-top:25px;margin-bottom:7px'>
					<div class="dz-filename" style='display:inline-block;'><span data-dz-name></span></div>
					<div class="dz-size" data-dz-size style='display:inline-block;margin-left:15px'></div>
					<div data-dz-remove style='display:inline-block;margin-left:15px'>&#x2715;</div>
				</div>
				<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
				<div class="dz-error-message"><span data-dz-errormessage></span></div>
			</div>
		</div>
		<?php
	}

	// DASHBOARD WIDGET FOR NOTIFICATIONS

	/**
	 * This is the action hooked to the admin widget hook.
	 *
	 * @since	1.0.0
	 */
	public function admin_dashboard_widget_notifications() {

		wp_add_dashboard_widget('wooclientzone_dashboard_widget_notifications','Client Zones Status', array($this, 'admin_dashboard_widget_notifications_content'));
	}

	/**
	 * This is the action hooked to the admin widget hook callback to create its content.
	 *
	 * @since	1.0.0
	 */
	public function admin_dashboard_widget_notifications_content() {

		// we just output the placeholder div for the content, which will be loaded by the js script following an ajax call to admin_dashboard_notifications_get_content()
		echo "
			<div id='dashboardWidgetNotificationsPlaceholder'></div>
			<div id='dashboardWidgetNotificationsRefreshIconDiv'><span id='dashboardWidgetNotificationsRefreshIcon' class='dashicons dashicons-update'></span></div>
			";
	}

	/**
	 * Ajax callback to create the content of the admin notifications widget
	 *
	 * @since	1.0.0
	 */
	public function admin_dashboard_notifications_get_content() {

		$response = new stdClass();
		$response->error = false;

		// set the base string for logging
		$current_action_log_msg = __('Loading data for admin-side notification of unseen communications.', 'wooclientzone');
		
		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_admin_widget_notifications', 'security', false );
		if (!$security_check_passed) {
			$response->error = true;
			$response->errorstring = __('Security check failed or timeout (admin widget notifications)', 'wooclientzone');
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode($response);
			die();
		}

		// get the notifications data from file manager
		$notifications = $this->filemanager->get_notifications_data();
		if ($notifications->error) {
			$response->error = true;
			$response->errorType = $notifications->errorType;
			$response->errorstring = $notifications->errorstring;
			$this->tools->log($current_action_log_msg." ".$response->errorstring, ($notifications->errorType === 'info') ? WOOCLIENTZONE_LOG_INFO : WOOCLIENTZONE_LOG_ERROR);
			echo json_encode($response);
			die();
		}

		// create notifications header
		$content = "
			<ul class='clearfix'>
				<li class='admin_widget_notifications_header_top'>&nbsp;</li>
				<li class='admin_widget_notifications_header_top'>".__('Communications viewed status', 'wooclientzone')."</li>
				<li class='admin_widget_notifications_header admin_widget_notifications_header_clientzone'>".__('Client Zone', 'wooclientzone')."</li>
				<li class='admin_widget_notifications_header admin_widget_notifications_header_unseen'><span class='dashicons dashicons-arrow-right'></span>".__('client', 'wooclientzone')."</li>
				<li class='admin_widget_notifications_header admin_widget_notifications_header_unseen'><span class='dashicons dashicons-arrow-right'></span>".__('merchant', 'wooclientzone')."</li>
				";

		// loop through notifications object
		$client_unseen_items = $admin_unseen_items = 0;
		foreach($notifications->notifications_array as $line) {

			if ($line->client_unseen) {
				$client_unseen_icon = 'flag';
				$admin_unseen_icon = 'yes';
				$client_unseen_items++;
			} else if ($line->admin_unseen) {
				$client_unseen_icon = 'yes';
				$admin_unseen_icon = 'flag';
				$admin_unseen_items++;
			} else {
				// this should not happen as this condition should be managed by the file manager (it happens there if no file was found in an existing directory, or no unseen file was found)
				continue;
			}
			$nonce_action = 'wooclientzone-userid='.$line->userid.'&orderid='.$line->orderid;
			if ($line->orderid) {
				$url = esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$line->userid.'&orderid='.$line->orderid), $nonce_action, 'wooclientzone'));
				$clientzone_link = "<a href='".$url."'>User #".$line->userid." <span class='dashicons dashicons-leftright'></span> Order #".$line->orderid."</a>";
			} else {
				$url = esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$line->userid), $nonce_action, 'wooclientzone'));
				$clientzone_link = "<a href='".$url."'>User #".$line->userid."</a>";
			}
			$content .= "
				<li class='admin_widget_notifications admin_widget_notifications_clientzone'>".$clientzone_link."</li>
				<li class='admin_widget_notifications admin_widget_notifications_unseen'><span class='dashicons dashicons-".$client_unseen_icon."'></span></li>
				<li class='admin_widget_notifications admin_widget_notifications_unseen'><span class='dashicons dashicons-".$admin_unseen_icon."'></span></li>
				";
		}

		$content .= "</ul>";

		// log
		$this->tools->log($current_action_log_msg." ".sprintf(__('Unseen items found: %s unseen by client; %s unseen by admin', 'wooclientzone'), $client_unseen_items, $admin_unseen_items), WOOCLIENTZONE_LOG_INFO);

		// return response
		$response->content = $content;
		echo json_encode($response);
		die();
	}
}
