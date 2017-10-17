<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://blendscapes.com
 * @since      1.0.0
 *
 * @package    Wooclientzone
 * @subpackage Wooclientzone/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wooclientzone
 * @subpackage Wooclientzone/public
 * @author     Enrico Sandoli <enrico.sandoli@blendscapes.com>
 */

class Wooclientzone_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->filemanager = new Wooclientzone_File_Manager($plugin_name, $version);
		$this->tools = new Wooclientzone_Tools($plugin_name, $version);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
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
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wooclientzone-public.css', array('dashicons'), $this->version, 'all' );


		// we now add dynamically generated styling

		// we now add dynamically generated styling (for progress bar color)
		$progress_bar_color = get_option('wooclientzone_progress_bar_color_public');

		// check colors: if problems use default ones */
		$color_regex = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';

		if (!preg_match($color_regex, $progress_bar_color)) {
			$progress_bar_color = "#55cce1";
		}

		// create css and add it to the style file
		$new_css = "
			.dz-upload {
				background-color: ".$progress_bar_color.";
			}
			";

		// add the my-account menu item icon if required
		$display_myaccount_menu_icon = get_option('wooclientzone_myaccount_menu_item_display_icon');
		if ($display_myaccount_menu_icon == 'yes') {
//			div.woocommerce nav.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link.woocommerce-MyAccount-navigation-link--clientzone a:before {
			$new_css .= "
			.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--clientzone a:before {
				font-size: 1.1em;
				font-family: dashicons !important;
				content: '\\f125' !important;
			}
			";
		}
		wp_add_inline_style($this->plugin_name, $new_css);

		// COMMON-FILE STYLES

		// enqueue common styles; the handler must be unique, so we added _common
		wp_enqueue_style( $this->plugin_name.'_common', plugin_dir_url( __FILE__ ) . '../includes/css/wooclientzone.css', array(), $this->version, 'all' );

		// we now add dynamically generated styling (for bubble colors)
		$bubbles_color_client_public   = get_option('wooclientzone_bubbles_color_client_public');
		$bubbles_color_merchant_public = get_option('wooclientzone_bubbles_color_merchant_public');

		// check colors: if problems use default ones */
		$color_regex = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';
		if (!preg_match($color_regex, $bubbles_color_client_public)) {
			$bubbles_color_client_public = "#dcf7c8";
		}
		if (!preg_match($color_regex, $bubbles_color_merchant_public)) {
			$bubbles_color_merchant_public = "#e0e0e0";
		}
		$bubbles_color_css = "
			.bubbles-color-client-public {
				background: ".$bubbles_color_client_public.";
			}
			.bubbles-color-client-public:after {
				border-color: transparent ".$bubbles_color_client_public.";
			}
			.bubbles-color-merchant-public {
				background: ".$bubbles_color_merchant_public.";
			}
			.bubbles-color-merchant-public:after {
				border-color: transparent ".$bubbles_color_merchant_public.";
			}
			";

		wp_add_inline_style($this->plugin_name.'_common', $bubbles_color_css);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wooclientzone-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'public_js_options', $this->filemanager->get_public_js_options() );

		// enqueue the dropzone
		wp_enqueue_script( $this->plugin_name.'_dropzone', plugin_dir_url( __FILE__ ) . '../includes/js/dropzone.js');
	}

	/**
	 * Links previous orders to a new customer upon registration. Not needed since we are requiring registration upfront.
	 * AS seen on https://www.skyverge.com/blog/automatically-link-woocommerce-orders-customer-registration/
	 *
	 * @param int $user_id the ID for the new user
	 */
//		public function sv_link_orders_at_registration( $user_id ) {
//				wc_update_new_customer_past_orders( $user_id );
//		}

	/**
	 * This is an action hook to woocommerce_thankyou, userd to move the current userzone to one related to the new successful order.
	 *
	 * @since	1.0.0
	 * @param   string    $orderid
	 */
	public function automove_to_orderzone($orderid) {

		$userid = get_current_user_id();

		// to proceed we need an order ID, the user to be logged in, and the user-linked client zone to exist
		if (!($orderid && is_user_logged_in() && $this->filemanager->clientzone_exists($userid))) {
			return;
		}

		// this will also check if order-based client zones are enabled and if the order zone is valid (e.g. has eligible products)
		if (!$this->tools->match_orderid_userid($orderid)) {
			return;
		}

		// set args for filemanager call
		$args = new stdClass();
		$args->userid = $userid;
		$args->orderid = false;
		$args->new_orderid = $orderid;
		$args->move_permissions = true;

		// set the base string for logging
		$current_action_log_msg = sprintf(__('Automatically moving the Client Zone of User ID %s to Order ID %s.', 'wooclientzone'), $args->userid, $args->new_orderid);

		$response = $this->filemanager->move_files_across_clientzones($args);
		if ($response->error) {
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
		} else {
			$this->tools->log($current_action_log_msg, WOOCLIENTZONE_LOG_INFO);
		}

		return;
	}

	/**
	 * Ajax callback to display the notifications in the My Account main page.
	 *
	 * @since	1.0.0
	 */
	public function my_account_notifications_get_content() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		// set the base string for logging
		$current_action_log_msg = sprintf(__('Getting notifications of unseen messages for User ID %s.', 'wooclientzone'), get_current_user_id());

		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_my_account_notifications', 'security', false );
		if (!$security_check_passed) {
			$response->error = true;
			$response->errorstring = __('Security check failed or timeout (My Account notifications)', 'wooclientzone');
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode($response);
			die();
		}

		// get the notifications data for the current user from file manager
		$notifications = $this->filemanager->get_notifications_data(true);
		if ($notifications->error) {
			if ($notifications->errorType == 'info') {
				$this->tools->log($current_action_log_msg." ".$notifications->errorstring, WOOCLIENTZONE_LOG_INFO);
				// in the my account area we do not report anything if no unseen communications are found
				$response->content ='';
			} else {
				$response->error = true;
				$response->errorType = $notifications->errorType;
				$response->errorstring = $notifications->errorstring;
				$this->tools->log($current_action_log_msg." ".$notifications->errorstring, WOOCLIENTZONE_LOG_WARNING);
			}
			echo json_encode($response);
			die();
		}

		// create notifications header
		$content = "
			<p>".__('You currently have unseen communications in', 'wooclientzone')."</p>
			<ul>
			";

		// loop through notifications object
		$unseen_communications = false;
		foreach($notifications->notifications_array as $line) {

			// only display clientzones with communications unseen by the client
			if (!$line->client_unseen) {
				continue;
			}
			$unseen_communications = true;
			if ($line->orderid) {
				$url = $this->tools->get_public_clientzone_nonced_url($line->orderid);
				$clientzone_link = "<p><a href='".$url."'>Communications area for Order #".$line->orderid."</a></p>";
			} else {
				$url = $this->tools->get_public_clientzone_nonced_url();
				$clientzone_link = "<p><a href='".$url."'>My Communications area</a></p>";
			}
			$content .= "
				<li>".$clientzone_link."</li>
				";
		}
		$content .= "</ul>";

		$response->content = $unseen_communications ? $content : '';
		echo json_encode($response);
		die();
	}

	/**
	 * Ajax callback to store a new message from the public site.
	 *
	 * @since	1.0.0
	 */
	public function public_submit_message() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		// set args for file manager call
		$args = new stdClass();
		$args->is_admin = false;
		$args->userid = get_current_user_id();
		$args->orderid = isset($_POST['orderid']) ? $_POST['orderid'] : false;

		// set the base string for logging
		if ($args->orderid) {
			$current_action_log_msg = sprintf(__('User ID %s is submitting a message to Client Zone linked to Order ID %s.', 'wooclientzone'), $userid, $orderid);
		} else {
			$current_action_log_msg = sprintf(__('User ID %s is submitting a message to their personal Client Zone.', 'wooclientzone'), $userid);
		}

		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_submit_message_public', 'security', false );
		if (!$security_check_passed) {
			$response->error = true;
			$response->errorstring = __('Security check failed or timeout (submit message)', 'wooclientzone');
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode($response);
			die();
		}

		// prevent refreshes during this process
		$this->filemanager->do_busy();

		// get the file manager to manage the submission
		$filemanager_response = $this->filemanager->submit_message($args);

		if ($filemanager_response->error) {
			$this->filemanager->undo_busy();
			$response->error = true;
			$response->errorstring = $filemanager_response->errorstring;
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode($response);
			die();
		}

		// Ok the message has been submitted, so now we can go ahead and get this message (together with any
		// potential new files uploaded or messages submitted by admin since the last access by us (client)

		// load an object with all the latest communications; we use the same args as the previous call, we just add the following:
		$args->load_latest = true; // we are asking to load only the new files since our (client) last access to the directory
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

		// Note we need to pass the admin last access (returned by filemanager together with the files array) to be able to set up the 'seen' strings correctly
		$response->new_divs = $this->get_communications_divs($communications->files, $communications->admin_lastaccess);
		$response->admin_lastaccess = $communications->admin_lastaccess;

		$this->tools->log($current_action_log_msg, WOOCLIENTZONE_LOG_INFO);
		echo json_encode($response);
		$this->filemanager->undo_busy();
		die();
	}

	/**
	 * Ajax callback to upload files from the public site.
	 *
	 * @since	1.0.0
	 */
	public function public_upload_files() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		// set args for filemanager call
		$args = new stdClass();
		$args->is_admin = false;
		$args->userid = get_current_user_id();
		$args->orderid = isset($_POST['orderid']) ? $_POST['orderid'] : false;

		// set the base string for logging
		if ($args->orderid) {
			$current_action_log_msg = sprintf(__('User ID %s is uploading a file to Client Zone linked to Order ID %s.', 'wooclientzone'), $args->userid, $args->orderid);
		} else {
			$current_action_log_msg = sprintf(__('User ID %s is uploading a file to their personal Client Zone.', 'wooclientzone'), $args->userid);
		}

		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_upload_files_public', 'security', false );
		if (!$security_check_passed) {
			$response->error = true;
			$response->errorstring = __('Security check failed or timeout (upload files)', 'wooclientzone');
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_WARNING);
			echo json_encode($response);
			die();
		}

		// prevent refreshes during this process
		$this->filemanager->do_busy();

		// get the file manager to manage the upload
		$filemanager_response = $this->filemanager->upload_file($args);

		if ($filemanager_response->error) {
			$this->filemanager->undo_busy();
			$response->error = true;
			$response->errorstring = $filemanager_response->errorstring;
			$this->tools->log($current_action_log_msg." ".$response->errorstring, WOOCLIENTZONE_LOG_ERROR);
			echo json_encode($response);
			die();
		}

		// Ok the file has been uploaded, so now we can go ahead and get this file (together with any
		// potential new files uploaded or messages submitted by admin since the last access by us (client)

		// load an object with all the latest communications; we use the same args as the previous call, we just add the following:
		$args->load_latest = true; // we are asking to load only the new files since our (client) last access to the directory
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

		// Note we need to pass the admin last access (returned by filemanager together with the files array) to be able to set up the 'seen' strings correctly
		$response->new_divs = $this->get_communications_divs($communications->files, $communications->admin_lastaccess);
		$response->admin_lastaccess = $communications->admin_lastaccess;

		$this->tools->log($current_action_log_msg, WOOCLIENTZONE_LOG_INFO);
		echo json_encode($response);
		$this->filemanager->undo_busy();
		die();
	}

	/**
	 * Ajax callback to load communications (files and messages) to the client zone in the public site.
	 * This method is called when first loading all communications and also whenever a refresh is needed,
	 * such as after file or message upload or for a periodic refresh; it asks the filemanager instance
	 * for a list of files, returning a list of the div elements that are to be included inside the
	 * #publicCommunicationsPlaceholder div element.
	 *
	 * @since	1.0.0
	 */
	public function public_load_communications() {

		// create response object
		$response = new stdClass();
		$response->error = false;

		$userid = get_current_user_id();
		$orderid = isset($_POST['orderid']) ? $_POST['orderid'] : false;
		$refreshing = isset($_POST['refreshing']) ? $_POST['refreshing'] : false;

		// set the base string for logging
		if ($orderid) {
			$current_action_log_msg = sprintf(__('User ID %s is viewing their Client Zone linked to Order ID %s.', 'wooclientzone'), $userid, $orderid);
		} else {
			$current_action_log_msg = sprintf(__('User ID %s is viewing their personal Client Zone.', 'wooclientzone'), $userid);
		}

		// check nonce
		$security_check_passed = check_ajax_referer( 'wooclientzone_load_communications_public', 'security', false );
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
		$args->is_admin = false;
		$args->userid = $userid;
		$args->orderid = $orderid;
		$args->load_latest = $refreshing ? true : false; // we are asking whether to load all files or just the latest
		$communications = $this->filemanager->load_communications_object($args);

		// we immediately set the admin last access, because this is needed to check the 'seen' visibility in the js script
		// even if no new files are found during a refresh
		$response->admin_lastaccess = $communications->admin_lastaccess;

		// check for errors (this includes no files found)
		if ($communications->error) {
			$response->error = true;
			$response->errorType = $communications->errorType; // this is needed to display an info message instead of a warning in case no files are found
			// sets errorstring
			if ($refreshing) {
				// we terminate gracefully when returning no new communications after a refresh
				$response->errorstring = false;
			} else if ($response->errorType == 'info') {
				// if no files are found (and we are not refreshing) we want to filter the errorstring to allow for a custom info string
				$response->errorstring = apply_filters('wooclientzone_new_clientzone_message', $communications->errorstring);
				$this->tools->log($current_action_log_msg." ".$communications->errorstring, WOOCLIENTZONE_LOG_INFO);
			} else {
				$response->errorstring = $communications->errorstring;
				$this->tools->log($current_action_log_msg." ".$communications->errorstring, WOOCLIENTZONE_LOG_WARNING);
			}
			echo json_encode($response);
			die();
		}

		$response->new_divs = $this->get_communications_divs($communications->files, $communications->admin_lastaccess);

		$this->tools->log($current_action_log_msg, WOOCLIENTZONE_LOG_INFO);
		echo json_encode($response);
		die();
	}

	/**
	 * Get the html of the communications to be displayed.
	 *
	 * This method takes as input a file array (in the format defined by
	 * filemanager->load_communications_object) and a timestamp of the last
	 * access by admin, and returns a string with a list of div elements
	 * that are to be included in the #publicCommunicationsPlaceholder div
	 * element, or appended to the inside end of it (such as after a
	 * file upload, a message submission, or a refresh of the Client Zone).
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array    $files			The array of files.
	 * @param    integer  $admin_lastaccess	  A timestamp of the last access by the client.
	 * @return string
	 */
	private function get_communications_divs($files, $admin_lastaccess) {

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
		$bubble_position = $file['origin'] == 'public' ? $client_bubbles_position : ($client_bubbles_position == 'right' ? 'left' : 'right');
		$bubble_color_class = $file['origin'] == 'public' ? 'bubbles-color-client-public' : 'bubbles-color-merchant-public';

		// date text just below the bubble
		$bubble_date = $file['upload_date'];

		// the 'seen' div visibility is managed by the js script using the admin last access date; here we only set the div for client communications
		$seen_div = $file['origin'] == 'public' ? "<div class='bubble-footer-seen' style='display:none;'>".__('Seen', 'wooclientzone')."</div>" : "";

		// define bubble type (has different padding styling) and content in the various cases
		if ($file['is_message']) {
			$bubble_type = 'text'; // needed to set different padding around images and text messages
			$bubble_content = '<div class="bubble-content">'.$file['message'].'</div>';
		}
		else {
			// img enclosing tags with path info to be used by js in its ajax call to download script
			// TODO see if we can use a script to download the files
//			$data_tag = "data-fullpath='".$file['fullpath']."'";
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
		?>
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
	 * This is the callback from the action hook public_wooclientzone that we fired
	 * from method my_account_clientzone_endpoint_content() of this same class, which
	 * creates the new public page. Parameters are passed by a $_GET global
	 * variable because it is not possible to directly pass parameters to the
	 * callback function hooked to the admin menu page creation.
	 *
	 * @since	1.0.0
	 */
	public function public_wooclientzone() {

		$userid = get_current_user_id();
		
		$orderid = isset($_GET['orderid']) ? $_GET['orderid'] : false;
	
		// here we delegate file manager to define client permissions (which will appropriately use local or global settings) 
		$client_permissions = $this->filemanager->get_client_permissions($userid, $orderid);
		$message_enabled  = $client_permissions->message_enabled;
		$upload_enabled = $client_permissions->upload_enabled;

		$new_message_upload = __('New message or upload', 'wooclientzone');
		$new_message	= __('New message', 'wooclientzone');
		$new_upload		= __('New upload', 'wooclientzone');

		if ($orderid) {
			$order = wc_get_order($orderid);
			$order_link = " <a href='".$order->get_view_order_url()."'>#".$orderid."</a>";
			$title = apply_filters('wooclientzone_clientzone_subtitle_orderlinked', __('Communications related to Order', 'wooclientzone').$order_link, $orderid);
		} else {
			$title = apply_filters('wooclientzone_clientzone_subtitle_userlinked', __('General communications', 'wooclientzone'));
		}

		// we create the select line to give options to switch to another client zone, if available
		$select_other_clientzone = $this->get_select_other_clientzone_div($orderid);

		// This is the template used by dropzone to create each file preview (including name, size, progress bar)
		// It is not displayed as such, but we define it to be referenced by our js script, to be used by dropzone
		$this->echo_dropzone_template();
		
	
	$userid = get_current_user_id();
$user_meta=get_userdata($userid);
$user_roles=$user_meta->roles;
	
	
		?>

		<!--The main wrapper-->
		<div id='publicWooclientzoneWrapper' <?php  echo $user_meta->roles[0]; ?>>
		<!-- The title -->
			<div id='publicWooclientzoneTitle'><h3><?php echo $title; ?></h3></div>
			<!--The actions wrapper-->
			<div id='publicWooclientzoneActionsDiv'>
				<?php if ($select_other_clientzone) : ?>
				<div>
					<div class='wooclientzone_actions_header'>
						<div class='wooclientzone_actions_header_text'><?php _e('Switch View', 'wooclientzone'); ?></div>
						<div class='wooclientzone_actions_header_icon'></div>
					</div>
					<div class='wooclientzone_actions_content wooclientzone_shadowed_box'><?php echo $select_other_clientzone; ?></div>
				</div>
				<?php endif; ?>
			</div>
			<!--The loader div is replaced by the js script with the actual content-->
			<div id='publicCommunicationsPlaceholder'><div class="loader-public"></div></div>
				<div id='errorMessage'></div>
				<div id='successMessage'></div>
				<?php

			// set message/upload div (which can be toggled on and off view)
			if ($message_enabled || $upload_enabled) :

				$open_text = ($message_enabled && $upload_enabled) ? $new_message_upload : ($upload_enabled ? $new_upload : $new_message);
				?>
				<div id='publicAddCommunicationHeader'>
					<div id='publicAddCommunicationHeaderText'><h4><?php _e($open_text, 'wooclientzone'); ?></h4></div>
					<div id='publicAddCommunicationHeaderIcon'></div>
				</div>
				<div id="publicAddCommunicationContent">
					<?php
					// display send message area
					if ($message_enabled) :
					?>
					<div id='publicMessageFormPlaceholder' class='public-message-editor-div'>
						<div>
						<?php
						$settings = array(
							'media_buttons' => false,
							'textarea_name' => 'public_message_textarea',
							'textarea_rows' => 8,
							'editor_class'  => 'public-editor',
							'quicktags'		=> false, // this removes the Visual/Text tabs
						);
						// id (second argument) to contain only lowercase letters and underscores (no hyphens)
						wp_editor('', 'publicmessagetextarea', $settings);
						?>
						</div>
						<div class='public-message-submit-button-div'>
							<button id='publicMessageSubmitButton' class='public-message-submit-button'><?php _e('Send message', 'wooclientzone'); ?></button>
						</div>
					</div>
					<?php
					endif;

					// display upload area
					if ($upload_enabled) :
					?>
					<div id='publicDropzone' class="dropzone dropzone-public"></div>
					<div id='publicDropzonePreviewsContainer' class='dropzone-previews'></div>
					<?php
					endif;
					?>
				</div>
			<?php
			endif;
			?>
		</div>
		<?php
	}

	/**
	 * Create the select line to give options to switch to another client zone.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    int      $orderid
	 * @return   string
	 */
	private function get_select_other_clientzone_div($orderid) {

$userid = get_current_user_id();
$user_meta=get_userdata($userid);
$user_roles=$user_meta->roles[0]; 
if($user_roles=="candidate"){
$display_select = false;
$args = apply_filters( 'job_manager_job_applications_past_args', array(
			'post_type'           => 'job_application',
			'post_status'         => array_keys( get_job_application_statuses() ),//array_keys( get_job_application_statuses() )array('In Progress','Completed')
			'posts_per_page'      => 25,
			'offset'              => ( max( 1, get_query_var('paged') ) - 1 ) * 25,
			'ignore_sticky_posts' => 1,
			'meta_key'            => '_candidate_user_id',
			'meta_value'          => get_current_user_id(),
		) );

		$applications = new WP_Query( $args );
		$order_array=array('#1092','#1012');
		/*foreach ( $applications as $application ) {
		global $wp_post_statuses;

		$application_id = $application->ID;
		$job_id         = wp_get_post_parent_id( $application_id );
		$job            = get_post( $job_id );
		$job_title      = get_post_meta( $application_id, 'job_listing_order_id', true ); 
		array_push($order_array,$job_title);
		
	 } */
	 
	 
	 $select_other_clientzone = __('Switch to view your communications', 'wooclientzone')."&nbsp;&nbsp;"
			."<select id='selectOtherClientzone'>"
			."<option value='-1' selected>".__('Select Communications', 'wooclientzone')."</option>";
		if ($order_array) { // note this is an assignment, so single = is ok
			foreach($order_array as $order) {

				// this is how we access order id (which should not be accessed directly as $order->id)
				$order_id = trim(str_replace('#', '', $order));

				// skip current order
				if ($order_id == $orderid) {
					continue;
				}
				// skip if order client zone is not available
				/*if (!$this->tools->clientzone_enabled_for_order($order)) {
					continue;
				}
				$url = $this->tools->get_public_clientzone_nonced_url($order);*/
				$select_other_clientzone .= "<option value='".$order_id."' data-url='".$url."'>related to order #".$order_id."</option>";
				$display_select = true;
			}
		}
		if ($orderid && get_option('wooclientzone_use_userzones') == 'yes') {
			// if viewing an order client zone, add option to switch to user client zone
			$url = $this->tools->get_public_clientzone_nonced_url();
			$select_other_clientzone .= "<option value='0' data-url='".$url."'>unrelated to any order</option>";
			$display_select = true;
		}
		if ($display_select) {
			$select_other_clientzone .= "</select>";
		} else {
			$select_other_clientzone = " nothing is here";
		}
		return $select_other_clientzone;
	 
}else
{
	
	
		$display_select = false;
		$select_other_clientzone = __('Switch to view your communications', 'wooclientzone')."&nbsp;&nbsp;"
			."<select id='selectOtherClientzone'>"
			."<option value='-1' selected>".__('Select Communications', 'wooclientzone')."</option>";
		if ($orders = $this->tools->get_orders_for_user_id(get_current_user_id())) { // note this is an assignment, so single = is ok
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
				$url = $this->tools->get_public_clientzone_nonced_url($order);
				$select_other_clientzone .= "<option value='".$order_id."' data-url='".$url."'>related to order #".$order_id."</option>";
				$display_select = true;
			}
		}
		if ($orderid && get_option('wooclientzone_use_userzones') == 'yes') {
			// if viewing an order client zone, add option to switch to user client zone
			$url = $this->tools->get_public_clientzone_nonced_url();
			$select_other_clientzone .= "<option value='0' data-url='".$url."'>unrelated to any order</option>";
			$display_select = true;
		}
		if ($display_select) {
			$select_other_clientzone .= "</select>";
		} else {
			$select_other_clientzone = "";
		}
		return $select_other_clientzone;
}
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
		<div id='publicDropzonePreviewTemplate' style='display:none;'>
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

	/*******************************************************************************
	 *
	 * CONFIGURATION METHODS (for creating the public site links, buttons, and
	 * endpoints to make the client zone accessible from orders and my account page)
	 *
	 *******************************************************************************/

	/**
	 * Write in the My Account dashboard information about the client zones.
	 *
	 * This also adds a notification list of unseen client zones, with relevant links.
	 *
	 * @since    1.0.0
	 */
	public function add_link_to_public_wooclientzone() {

		// check if user- and order-based client zones are enabled
		$userzones_enabled = get_option('wooclientzone_use_userzones') == 'yes';
		$orderzones_enabled = get_option('wooclientzone_use_orderzones') != 'never';

		if ($userzones_enabled && $orderzones_enabled) {
			// add a link to the customer public client zone
			$url = $this->tools->get_public_clientzone_nonced_url();
			$clientzone_link = "<a href='".$url."'>".__('communications area', 'wooclientzone')."</a>";
			echo "<p>".sprintf(__('From here you may also access your %s, unrelated to any specific order, '
				. 'while from the orders page you may access communications areas related to them', 'wooclientzone'), $clientzone_link).".</p>";
		}
		else if ($userzones_enabled) {
			// add a link to the customer public client zone
			$url = $this->tools->get_public_clientzone_nonced_url();
			$clientzone_link = "<a href='".$url."'>".__('communications area', 'wooclientzone')."</a>";
			echo "<p>".sprintf(__('From here you may also access your %s', 'wooclientzone'), $clientzone_link).".</p>";
		}
		else if ($orderzones_enabled) {
			echo "<p>".__('From your orders page, you may also access related communication areas.', 'wooclientzone')."</p>";
		}

		// add the placeholder for the unseen-communications list
		if ($userzones_enabled || $orderzones_enabled) {
			do_action('wooclientzone_my_account_notifications_before');
			echo "<p id='myAccountNotificationsPlaceholder'></p>";
			do_action('wooclientzone_my_account_notifications_after');
		}
	}

	/**
	 * This filter creates a client zone button in my orders page.
	 *
	 * @since    1.0.0
	 * @param    array     $actions
	 * @param    object    $order
	 * @return   array
	 */
	public function my_orders_table_add_clientzone_link($actions, $order) {

		if ($this->tools->clientzone_enabled_for_order($order)) {

		// get a link to the order public client zone
		$url = $this->tools->get_public_clientzone_nonced_url($order);

		if (isset($actions['view'])) {
			$view = $actions['view'];
			unset($actions['view']);

			$actions['clientzone'] = array(
			'url'   => $url,
			'name'  => __( '', 'wooclientzone' ), // the icon is assigned via css content (class .clientzone, defined in the key of the $actions array)
			);
			$actions['view'] = $view;
		} else {
			$actions['clientzone'] = array(
			'url'   => $url,
			'name'  => __( '', 'wooclientzone' ), // the icon is assigned via css content (class .clientzone, defined in the key of the $actions array)
			);
		}
		}
		return $actions;
	}

	/**
	 * This action creates a client zone link to an orders details page.
	 *
	 * @since    1.0.0
	 * @param    object    $order
	 * @return   array
	 */
	public function order_details_add_clientzone_link($order) {

		if ($this->tools->clientzone_enabled_for_order($order)) {

			// get a link to the order public client zone
			$url = $this->tools->get_public_clientzone_nonced_url($order);

			?>
			<table class="shop_table _order_details">
				<thhead>
					<th style="text-align:center">
					<?php
						$clientzone_link = "<a href='".$url."'>".__('communications area', 'wooclientzone')."</a>";
						echo apply_filters('wooclientzone_order_edit_clientzone_link_public', sprintf(__('Access your %s for this order', 'wooclientzone'), $clientzone_link), $url);
					?>
					</th>
				</thead>
			</table>
			<?php
		}
	}

	/**
	 * This filter creates a clientzone item in the my account menu items.
	 *
	 * See https://iconicwp.com/add-custom-page-account-area-woocommerce/
	 * and http://wpbeaches.com/change-rename-woocommerce-endpoints-accounts-page/
	 * Note that the link is nonced though the js script, on clicking the link.
	 *
	 * @since    1.0.0
	 * @param    array    $items
	 * @return   array
	 */
	public function my_account_clientzone_endpoint_menu($items) {

		// get the menu item name
		$menu_item_name = get_option('wooclientzone_myaccount_menu_item_text', __('Communications', 'wooclientzone'));

		// we put it just before the logout
		if (isset ($items['customer-logout'])) {

			$logout = $items['customer-logout'];
			unset( $items['customer-logout'] );
			// note that the $items key is appended to the class woocommerce-MyAccount-navigation-link--
			// of the li element of the menu, and we can in this way style the content (its icon)
			$items['clientzone'] = $menu_item_name;
			$items['customer-logout'] = $logout;
		} else {
			$items['clientzone'] = $menu_item_name;
		}
		return $items;
	}

	/**
	 * This action will create a rewrite rule for the new endpoint.
	 *
	 * @since    1.0.0
	 */
	public function my_account_clientzone_endpoint_rewrite() {

		add_rewrite_endpoint( 'clientzone', EP_PAGES );
	}

	/**
	 * This action hooks our new endpoint for the title.
	 *
	 * @since    1.0.0
	 * @param    string    $title
	 * @return   string
	 */
	public function my_account_clientzone_endpoint_title($title) {

		if( $this->is_endpoint('clientzone') && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
		if (!empty($_GET['orderid'])) {
			$title = apply_filters('wooclientzone_clientzone_title_orderlinked', __( 'Order-based communications', 'wooclientzone' ), $_GET['orderid']);
		} else {
			$title = apply_filters('wooclientzone_clientzone_title_userlinked', __( 'My communications', 'wooclientzone' ));
		}
		remove_filter( 'the_title', array( $this, 'my_account_clientzone_endpoint_title' ) );
		}
		return $title;
	}

	/**
	 * This action hooks out new endpoint for content.
	 *
	 * @since    1.0.0
	 */
	public function my_account_clientzone_endpoint_hooks() {

	   if( $this->is_endpoint('clientzone') ) {
		remove_action( 'woocommerce_account_content', 'woocommerce_account_content' );
		add_action( 'woocommerce_account_content', array($this, 'my_account_clientzone_endpoint_content'), 10 );
	   }
	}

	/**
	 * This action creates our new endpoint content
	 *
	 * @since    1.0.0
	 */
	public function my_account_clientzone_endpoint_content() {

		$orderid = isset($_GET['orderid']) ? $_GET['orderid'] : '';

		if ($orderid && is_user_logged_in() && wp_verify_nonce($_GET['wooclientzone'], 'wooclientzone-orderid='.$orderid)) {

			do_action('wooclientzone_public_clientzone_before');
			do_action('wooclientzone_public_clientzone');
			do_action('wooclientzone_public_clientzone_after');
		}
		else if (!$orderid && is_user_logged_in()) {			// user-linked zones need not be nonced
			do_action('wooclientzone_public_clientzone_before');
			do_action('wooclientzone_public_clientzone');
			do_action('wooclientzone_public_clientzone_after');
		}
		else {
			$this->tools->error_message(__('Cannot view Communications Area. Reload if secure link expired (code 1)', 'wooclientzone'));
		}
	}

	/**
	 * A helper function to check for the endpoint
	 *
	 * @since    1.0.0
	 */
	private function is_endpoint( $endpoint = false ) {

		global $wp_query;

		if( !$wp_query ) {
			return false;
		}
		return isset( $wp_query->query[ $endpoint ] );
	}
}
