<?php

/**
 * The file manager of the plugin.
 *
 * @link	   http://blendscapes.com
 * @since	  1.0.0
 *
 * @package	Wooclientzone
 * @subpackage Wooclientzone/includes
 */

/**
 * The file manager of the plugin.
 *
 * This class is responsible for managing upload and download of files,
 * as well as generating list of files as objects to be sent json-encoded
 *
 * @package	Wooclientzone
 * @subpackage Wooclientzone/includes
 * @author	 Enrico Sandoli <enrico.sandoli@blendscapes.com>
 */

class Wooclientzone_File_Manager {

	/**
	 * The ID of this plugin.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$plugin_name	The ID of this plugin.
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
	 * The name of the file containing the client's last access.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$client_lastaccess_file
	 */
	private $client_lastaccess_file;

	/**
	 * The name of the file containing the admin's last access.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$admin_lastaccess_file
	 */
	private $admin_lastaccess_file;

	/**
	 * The name of the file containing the client's permissions.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$status_file
	 */
	private $status_file;

	/**
	 * The format for the dates under the communications bubbles.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  string	$date_format
	 */
	private $date_format;

	/**
	 * A property signalling the file manager is busy.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var	  bool	$is_busy	The tools class instance of this plugin.
	 */
	private $is_busy;

	/**
	 * The tools private property.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var		  object		$tools		An object with utility methods.
	 */
	private $tools;

	/**
	 * The url address of the ajax endpoint.
	 *
	 * @since	1.0.0
	 * @var	  string	$ajaxurl
	 */
	public $ajaxurl;

	/**
	 * The name of the file containing PDF icon.
	 *
	 * @since	1.0.0
	 * @var	  string	$pdf_icon
	 */
	public $pdf_icon;

	/**
	 * The name of the file containing a generic file icon.
	 *
	 * @since	1.0.0
	 * @var	  string	$pdf_icon
	 */
	public $file_icon;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	  string	$plugin_name	   The name of this plugin.
	 * @param	  string	$version	The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->is_busy = false;
		$this->ajaxurl = admin_url('admin-ajax.php');
		$this->icon_pdf  = plugin_dir_url( dirname( __FILE__ ) ) . 'media/pdf-icon.png';
		$this->icon_file = plugin_dir_url( dirname( __FILE__ ) ) . 'media/file-icon.png';
		$this->client_lastaccess_file = '.client_lastaccess';
		$this->admin_lastaccess_file  = '.admin_lastaccess';
		$this->status_file = '.wooclientzone_status';
		$this->date_format = get_option('wooclientzone_date_format');
		$this->tools = new Wooclientzone_Tools($plugin_name, $version);
	}

	/**
	 * Set the file manager as busy
	 *
	 * @since	1.0.0
	 */
	public function do_busy() {
		$this->is_busy = true;
	}

	/**
	 * Set the file manager as not busy
	 *
	 * @since	1.0.0
	 */
	public function undo_busy() {
		$this->is_busy = false;
	}

	/**
	 * Check if the file manager is busy
	 *
	 * @since	1.0.0
	 */
	public function is_busy() {
		return $this->is_busy;
	}

	/**
	 * Create an array to be passed to the public js script, with those options
	 * that need to be set from the server
	 *
	 * @since	1.0.0
	 */
	public function get_public_js_options() {

		$orderid = isset($_GET['orderid']) ? $_GET['orderid'] : false;

		$accepted_files = get_option('wooclientzone_accepted_files_public');
		$max_filesize = get_option('wooclientzone_max_filesize_public');
		$refresh_rate = get_option('wooclientzone_refresh_rate');

		$clientzone_url = $this->tools->get_public_clientzone_nonced_url();

		$nonce_load_communications = wp_create_nonce('wooclientzone_load_communications_public');
		$nonce_submit_message = wp_create_nonce('wooclientzone_submit_message_public');
		$nonce_upload_files = wp_create_nonce('wooclientzone_upload_files_public');
		$nonce_my_account_notifications = wp_create_nonce('wooclientzone_my_account_notifications');

		$options = array(
			'orderid'							=> $orderid,
			'ajaxurl'							=> $this->ajaxurl,
			'dictDefaultMessage'				=> __('Click or drop your files here', 'wooclientzone'),
			'accepted_files'					=> $accepted_files,
			'max_filesize'						=> $max_filesize,
			'refresh_rate'						=> $refresh_rate,
			'clientzone_url'					=> $clientzone_url,
			'nonce_load_communications'			=> $nonce_load_communications,
			'nonce_submit_message'				=> $nonce_submit_message,
			'nonce_upload_files'				=> $nonce_upload_files,
			'nonce_my_account_notifications'	=> $nonce_my_account_notifications,
		);
		return $options;
	}

	/**
	 * Create an array to be passed to the admin js script, with those options
	 * that need to be set from the server
	 *
	 * @since	1.0.0
	 */
	public function get_admin_js_options() {

		$userid = isset($_GET['userid']) ? $_GET['userid'] : false;
		$orderid = isset($_GET['orderid']) ? $_GET['orderid'] : false;

		$accepted_files = get_option('wooclientzone_accepted_files_admin');
		$max_filesize = get_option('wooclientzone_max_filesize_admin');
		$refresh_rate = get_option('wooclientzone_refresh_rate');

		$nonce_load_communications = wp_create_nonce('wooclientzone_load_communications_admin');
		$nonce_submit_message = wp_create_nonce('wooclientzone_submit_message_admin');
		$nonce_upload_files = wp_create_nonce('wooclientzone_upload_files_admin');

		$nonce_notify_client = wp_create_nonce('wooclientzone_notify_client');
		$nonce_save_client_permissions = wp_create_nonce('wooclientzone_save_client_permissions');
		$nonce_move_clientzone = wp_create_nonce('wooclientzone_move_clientzone');

		$nonce_admin_widget_notifications = wp_create_nonce('wooclientzone_admin_widget_notifications');

		$options = array(
			'userid'							=> $userid,
			'orderid'							=> $orderid,
			'ajaxurl'							=> $this->ajaxurl,
			'dictDefaultMessage'				=> __('Click or drop your files here', 'wooclientzone'),
			'accepted_files'					=> $accepted_files,
			'max_filesize'						=> $max_filesize,
			'refresh_rate'						=> $refresh_rate,
			'nonce_load_communications'			=> $nonce_load_communications,
			'nonce_submit_message'				=> $nonce_submit_message,
			'nonce_upload_files'				=> $nonce_upload_files,
			'nonce_notify_client'				=> $nonce_notify_client,
			'nonce_save_client_permissions'		=> $nonce_save_client_permissions,
			'nonce_move_clientzone'				=> $nonce_move_clientzone,
			'nonce_admin_widget_notifications'  => $nonce_admin_widget_notifications,
			'save_changes_string'				=> __('Save changes', 'wooclientzone'),
			'moving_clientzone_string'			=> __('Moving Client Zone ...', 'wooclientzone'),
			'move_clientzone_string'			=> __('Move Client Zone', 'wooclientzone'),
			'saving_string'						=> __('Saving ...', 'wooclientzone'),
			'notify_client_string'				=> __('Notify Client', 'wooclientzone'),
			'sending_email_string'				=> __('Sending email ...', 'wooclientzone'),
		);
		return $options;
	}

	/**
	 * This function returns the root folder as defined in the plugin's back end parameters.
	 *
	 * This methods returns a standard object containing a path and a url for the root folder.
	 * If outside the web root the url is set as false (TODO DISABLED for now)
	 *
	 * @since	1.0.0
	 * @access   private
	 * @return	object
	 */
	private function get_root_folder() {

		$filerepo = get_option('wooclientzone_root_folder');

		// TODO for the time being we assume that the root folder is relative to the web root
		// $is_relative = get_option('wooclientzone_root_is_relative');
		$is_relative = 'yes';

		// create response object
		$response = new stdClass();
		$response->error = false;

		// reject if filerepo contains a double dot, or if the backend parameters are not defined
		if (!$filerepo || !$is_relative || strpos($filerepo, '..')) {
			$this->tools->log(__('The root folder was not found in the back end settings or was found to contain illegal characters', 'wooclientzone'), WOOCLIENTZONE_LOG_ALERT);
			$response->error = true;
			return $response;
		}

		// remove any spaces and other characters, as well as any trailing slash from filerepo
		$filerepo = trim($filerepo);
		$filerepo = rtrim($filerepo, DIRECTORY_SEPARATOR);

		// now set the response values
		if ($is_relative == 'yes') {
			$response->path = get_home_path().$filerepo.DIRECTORY_SEPARATOR;
			$response->url  = get_home_url().DIRECTORY_SEPARATOR.$filerepo.DIRECTORY_SEPARATOR;
		} else {
			// note that currently this is never executed
			// for now set url to false if the path is not relative
			// TODO check whether the path, even if expressed in absolute terms, is still within the web root
			$response->path = $filerepo.DIRECTORY_SEPARATOR;
			$response->url  = false;
		}
		return $response;
	}

	/**
	 * This is where the name of the current folder,  where files should be read and written, is defined.
	 * This depends on the user ID and the order ID. If no user ID is provided, then the current user ID is used.
	 * If there is no order ID we assume we are in the user's common area, otherwise we are inside one user's order folder
	 *
	 * This methods returns a standard object containing a path and a url for the current folder.
	 * If outside the web root the url is set as false (TODO DISABLED for now)
	 *
	 * @since	1.0.0
	 * @access   private
	 * @param	string	$userid		 optional: the user ID for which we are getting the folder
	 * @param	string	$orderid		optional: the order ID for which we are getting the folder
	 * @return	object
	 */
	private function get_current_folder($userid = false, $orderid = false) {

		// create response object
		$response = new stdClass();
		$response->error = false;

		// get the root folder
		$root_folder_object = $this->get_root_folder();

		if ($root_folder_object->error) {
			$response->error = true;
			return $response;
		}

		// now set the name of the current folder by adding a last part which depends on user id / order id

		// if no user ID provided (as when accessing it from admin to view data for a
		// specific user), assume current user (as when accessing it from the front end)
		if (!$userid) {
			$userid = get_current_user_id();
		}
		// all files are within the user ID
		$folder_last_part = 'User ID '.$userid.DIRECTORY_SEPARATOR;
		// specialise order-linked areas or common area
		if ($orderid) {
			$folder_last_part .= 'Order ID '.$orderid;
		}
		else {
			$folder_last_part .= 'Common';
		}

		$response->path = $root_folder_object->path.$folder_last_part.DIRECTORY_SEPARATOR;
		$response->url = $root_folder_object->url ? $root_folder_object->url.$folder_last_part.DIRECTORY_SEPARATOR : false;

		return $response;
	}

	/**
	 * Sets the current folder; we create it only if it doesn't exist.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @param	  string	$current_folder		 the full path as defined by get_current_folder
	 * @return	bool
	 */
	private function create_current_folder($current_folder) {

		if (!$current_folder) {
			return false;
		}

		if (!is_dir($current_folder) && !($rc = @mkdir ($current_folder, 0777, true))) {
			return false;
		}
		return true; // success
	}

	/**
	 * For a given user, submit a new message by putting it in the content of a new file.
	 *
	 * @since	1.0.0
	 * @param	object $args
	 * @return	object
	 */
	public function submit_message($args) {

		$response = new stdClass();
		$response->error = false;

		// this is of course not needed, but it helps document the object elements
		$is_admin = $args->is_admin;
		$userid	  = $args->userid;
		$orderid  = $args->orderid;

		if (!$userid) {
			$response->error = true;
			$response->errorstring = __('No user ID found', 'wooclientzone');
			return $response;
		}
		// sanitize message data with the same sanitization level of a post
		$message = wp_kses_post(stripslashes($_POST['data']));
		if (!$message) {
			$response->error = true;
			$response->errorstring = __('No message found', 'wooclientzone');
			return $response;
		}
		$current_folder = $this->get_current_folder($userid, $orderid);
		if ($current_folder->error) {
			$response->error = true;
			$response->errorstring = __('Error getting current folder name', 'wooclientzone');
			return $response;
		}
		if (!$this->create_current_folder($current_folder->path)) {
			$response->error = true;
			$response->errorstring = __('Error creating the current folder', 'wooclientzone');
			return $response;
		}

		// set filename and create a new file with data as content
		$filename = $this->set_filename_prefix($is_admin, 'message')."msg.txt";
		if (!file_put_contents($current_folder->path.$filename, $message)) {
			$response->error = true;
			$response->errorstring = __('Error saving new message', 'wooclientzone');
			return $response;
		}

		return $response;
	}

	/**
	 * For a given user, upload all files.
	 *
	 * @since	1.0.0
	 * @param	object $args
	 * @return	object
	 */
	public function upload_file($args) {

		// create response object
		$response = new stdClass();
		$response->error = false;

		// this is of course not needed, but it helps document the object elements
		$is_admin = $args->is_admin;
		$userid   = $args->userid;
		$orderid  = $args->orderid;

		if (!$userid) {
			$response->error = true;
			$response->errorstring = __('No user ID found', 'wooclientzone');
			return $response;
		}
		$current_folder = $this->get_current_folder($userid, $orderid);
		if ($current_folder->error) {
			$response->error = true;
			$response->errorstring = __('Error getting current folder name', 'wooclientzone');
			return $response;
		}
		if (!$this->create_current_folder($current_folder->path)) {
			$response->error = true;
			$response->errorstring = __('Error creating the current folder', 'wooclientzone');
			return $response;
		}

		// check if there was an error uploading the tmp file
		if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
			$response->error = true;
			$response->errorstring = __('Server error uploading file', 'wooclientzone');
			return $response;
		}

		// we check mime type/extension and file size
		if (!$this->allowed_file_type($_FILES, $is_admin)) {
			$response->error = true;
			$response->errorstring = __('File type not allowed', 'wooclientzone');
			return $response;
		}
		if (!$this->allowed_file_size($_FILES, $is_admin)) {
			$response->error = true;
			$response->errorstring = __('File size not allowed', 'wooclientzone');
			return $response;
		}

		// set filename prefix and upload the file (note we leave three dashes for future use)
		$filename = $this->set_filename_prefix($is_admin, 'file').$_FILES['file']['name'];
		if (!move_uploaded_file($_FILES['file']['tmp_name'], $current_folder->path.$filename)) {
			$response->error = true;
			$response->errorstring = __('Error uploading file', 'wooclientzone');
			return $response;
		}

		return $response;
	}

	/**
	 * For a given user, load all communications into a stdClass object.
	 *
	 * @since	1.0.0
	 * @param	object $args
	 * @return	object
	 */
	public function load_communications_object($args) {

		// create response object
		$communications = new stdClass();
		$communications->error = false;

		// this is of course not needed, but it helps document the object elements
		$is_admin    = $args->is_admin;
		$load_latest = $args->load_latest;
		$userid      = $args->userid;
		$orderid     = $args->orderid;

		if (!$userid) {
			$communications->error = true;
			$communications->errorstring = __('No user ID found', 'wooclientzone');
			return $communications;
		}
		$current_folder = $this->get_current_folder($userid, $orderid);
		if ($current_folder->error) {
			$communications->error = true;
			$communications->errorstring = __('Error getting current folder name', 'wooclientzone');
			return $communications;
		}

		// we now load the last access times from both parties, and as a minimum (even if no (new) files are found) we return them.
		if (is_file($current_folder->path.$this->client_lastaccess_file)) {
			$client_lastaccess = file_get_contents($current_folder->path.$this->client_lastaccess_file);
		} else {
			$client_lastaccess = 0;
		}
		if (is_file($current_folder->path.$this->admin_lastaccess_file)) {
			$admin_lastaccess = file_get_contents($current_folder->path.$this->admin_lastaccess_file);
		} else {
			$admin_lastaccess = 0;
		}
		$communications->client_lastaccess = $client_lastaccess;
		$communications->admin_lastaccess = $admin_lastaccess;

		// we are here, so $current_folder has the absolute path of the current directory;
		// we can now use our get_file_list method (derived from the WP function list_files) to create an array of files

		// now populate the $coomunications object
		//$communications->files = array();

		// if loading only the latest files, define the right argument for get_file_list()
		if ($load_latest) {
			$time_from = $is_admin ? $admin_lastaccess : $client_lastaccess;
		} else {
			$time_from = false;
		}

		// note that get_file_list() method takes the entire object $current_folder
		$response = $this->get_file_list($current_folder, $time_from);
		if ($response->error) {
			$communications->error = true;
			$communications->errorstring = $response->errorstring;
			return $communications;
		}
		if (!$response->files) {
			$communications->error = true;
			$communications->errorType = 'info';
			$communications->errorstring = __('This communications area is currently empty', 'wooclientzone');
			return $communications;
		}

		// load files 2-dim array onto communications object
		$communications->files = $response->files;

		// Finally, as the client will be viewing the communications from the public side, we can write a timestamp to a hidden file, which, when read
		// on the admin side, will provide info about the viewed state of the various communications, with the display of a 'seen' indication below
		// the messages and/or files; and viceversa for when viewing happens from the admin side.

		// we cannot use the WP core function is_admin() because this function returns true for all ajax calls, both client-side and admin-side,
		// so we use this variable which is passed as an argument by the calling party.
		if ($is_admin) {
			$ret = file_put_contents($current_folder->path.$this->admin_lastaccess_file, $response->access_timestamp);
		} else {
			$ret = file_put_contents($current_folder->path.$this->client_lastaccess_file, $response->access_timestamp);
		}

		return $communications;

	}

	/**
	 * Reads the folder files and returns an object containing those files and
	 * the timestamp of access.
	 *
	 * This function is based on WP core function
	 * list_files(), but it builds the file object based on the prefix structure
	 * used by Wooclientzone. If $time_from is not null, we collect just the
	 * files uploaded after the specified timestamp in $time_from.
	 * TODO We could extend this function with a $time_to parameter which would
	 * aide in backward pagination.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @param	  object	$folder		 the full path as defined by get_current_folder
	 * @param	  int	 $time_from	  the minimum timestamp for the upload datetime to retrieve
	 * @return	object
	 */
	private function get_file_list($folder, $time_from) {

		$response = new stdClass();
		$response->error = false;

		if (empty($folder->path)) {
			$response->error = true;
			$response->errorstring = __('An empty folder was passed', 'wooclientzone');
			return $response;
		}

		// check for dir existence; if it doesn't exist return like no files found
		if (!is_dir($folder->path)) {
			$response->files = false;
			return $response;
		}

		$dir = @opendir( $folder->path );
		if (!$dir) {
			$response->error = true;
			$response->errorstring = __('Error opening the client area', 'wooclientzone');
			return $response;
		}

		// we build an array of files to be added as a property of the response object
		$files = array();
		while (($file = readdir($dir)) !== false) {

			// skip refs to current and upper dirs ( '.' and '..')
			if (in_array($file, array('.', '..'))) {
				continue;
			}

			// we don't display folders or hidden files (files beginning with a dot)
			if ( is_dir( $folder->path . $file ) || substr($file, 0, 1) == '.') {
				continue;
			}

			// we check that the file has the right wooclientzone prefix
			if (!$this->validate_file_prefix($file)) {
				continue;
			}

			// now we can get the timestamp based on the file prefix
			$upload_timestamp = $this->get_upload_timestamp($file);

			// if requested, skip files already seen
			if ($time_from && $upload_timestamp <= $time_from) {
				continue;
			}

			// we build the file array in a separate private function
			$files[] = $this->get_file_array($file, $folder, $upload_timestamp);
		}
		@closedir( $dir );

		// sort files by name (equivalent to upload time)
		if (count($files) > 1) {
			usort($files, function($a, $b) {
				return strcmp($a['prefixed_name'], $b['prefixed_name']);
			});
		}

		// and finally return response
		$response->access_timestamp = time();
		$response->files = $files;
		return $response;
	}

	/**
	 * Checks if a Client Zone exists.
	 *
	 * @since    1.0.0
	 * @param    int    $userid
	 * @param    int    $orderid (optional)
	 * @return   bool
	 */
	public function clientzone_exists($userid, $orderid = false) {

		$clientzone_folder = $this->get_current_folder($userid, $orderid);
		return is_dir($clientzone_folder->path);
	}

	/**
	 * Moves files from one client zone to another.
	 *
	 * @since    1.0.0
	 * @param    object    $args
	 * @return   object
	 */
	public function move_files_across_clientzones($args) {

		$response = new stdClass();
		$response->error = false;

		// first we get the target folder
		$target_folder = $this->get_current_folder($args->userid, $args->new_orderid);
		if ($target_folder->error) {
			$response->error = true;
			$response->errorstring = __('Error getting target folder name', 'wooclientzone');
			return $response;
		}

		// secondly we get the current folder
		$current_folder = $this->get_current_folder($args->userid, $args->orderid);
		if ($current_folder->error) {
			$response->error = true;
			$response->errorstring = __('Error getting current folder name', 'wooclientzone');
			return $response;
		}

		// we create the target if it doesn't exist
		if (!$this->create_current_folder($target_folder->path)) {
			$response->error = true;
			$response->errorstring = __('Error creating the target folder', 'wooclientzone');
			return $response;
		}

		// ok now let's move the files
		$files_found = $files_moved = 0;
		// this check if for extra precaution only. We should never get here because: when coming from admin to move a zone we would already be
		// in a zone to begin with (even if empty); when automoving, if the zone folder to move from does not exist we stop earlier in the code
		if (is_dir($current_folder->path)) {

			$files = scandir($current_folder->path);
			foreach ($files as $file) {
				if (in_array($file, array(".",".."))) {
					continue;
				}
				// if client permissions file was not to be moved, just delete it silently
				if ($file == $this->status_file && $args->move_permissions == 'false') {
					unlink($current_folder->path.$file);
					continue;
				}
				$files_found++;
				if (copy($current_folder->path.$file, $target_folder->path.$file)) {
					$files_moved++;
					unlink($current_folder->path.$file);
				}
			}
		}

		// return a warning if no files were found or not all files were moved
		if (!$files_found) {
			$response->error = true;
			$response->errorstring = __('Nothing to move!', 'wooclientzone');
			return $response;
		}
		if ($outstanding_files = $files_found - $files_moved) { // yes, we are making an assignment, which returns the value which is tested in the if statement
			$response->error = true;
			$response->errorstring = sprintf(__('%d files could not be moved', 'wooclientzone'), $outstanding_files);
		}
		return $response;
	}

	/**
	 * Get client permissions for a specific client zone.
	 *
	 * @since    1.0.0
	 * @param    int      $userid
	 * @param    int      $orderid
	 * @return   object
	 */
	public function get_client_permissions($userid, $orderid) {

		$response = new stdClass();
		$response->error = false;

		// we get the current folder
		$current_folder = $this->get_current_folder($userid, $orderid);
		if ($current_folder->error) {
			$response->error = true;
			$response->errorstring = __('Error getting current folder name', 'wooclientzone');
			return $response;
		}

		// load status file from client zone folder
		if (is_file($current_folder->path.$this->status_file)) {
			$client_access_data = file_get_contents($current_folder->path.$this->status_file);
			$client_access_data = unserialize($client_access_data);
			$client_message = $client_access_data->client_message;
			$client_upload = $client_access_data->client_upload;
		}
		else {
			// status file not found, so we check the default backend settings
			// TODO here we can discriminate between user- or order-linked client zones,
			// if this discrimination is introduced in the backend settings
			$client_message = $orderid ? get_option('wooclientzone_client_message_orderzones') : get_option('wooclientzone_client_message_userzones');
			$client_upload = $orderid ? get_option('wooclientzone_client_upload_orderzones') : get_option('wooclientzone_client_upload_userzones');
		}

		$response->message_enabled  = $client_message == 'yes' ? true : false;
		$response->upload_enabled = $client_upload == 'yes' ? true : false;
		return $response;
	}

	/**
	 * Set client permissions for a specific client zone.
	 *
	 * @since    1.0.0
	 * @param    object   $args
	 * @return   object
	 */
	public function set_client_permissions($args) {

		$response = new stdClass();
		$response->error = false;

		// load status file from client zone folder
		$current_folder = $this->get_current_folder($args->userid, $args->orderid);
		if ($current_folder->error) {
			$response->error = true;
			$response->errorstring = __('Error getting current folder name', 'wooclientzone');
			return $response;
		}

		// we create the directory if it doesn't exist: this fixes the problem in v1.0.0 with unchanging local permissions on new zones
		if (!$this->create_current_folder($current_folder->path)) {
			$response->error = true;
			$response->errorstring = __('Error creating current folder', 'wooclientzone');
			return $response;
		}
		
		$client_access_data = new stdClass();
		$client_access_data->client_upload = $args->upload_enabled === 'true' ? 'yes' : 'no';
		$client_access_data->client_message = $args->message_enabled === 'true' ? 'yes' : 'no';
		file_put_contents($current_folder->path.$this->status_file, serialize($client_access_data));

		return $response;
	}

	/**
	 * Return a readable file size.
	 *
	 * Adapted from php.net filesize function page http://php.net/manual/en/function.filesize.php
	 *
	 * @since    1.0.0
	 * @param    int     $bytes
	 * @return   string
	 */
	public function human_filesize($bytes) {

		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);

		if ($factor < 1) {
			return sprintf("%.0f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor];
		} elseif ($factor < 2) {
			return sprintf("%.0f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor] . 'B';
		} else {
			return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor] . 'B';
		}
	}

	/**
	 * Read a file in chunks.
	 *
	 * This function is reported in readfile() php.net page to bypass readfile() documented problems with large files
	 * TODO this will become private once the calling function is brought into the filemanager (will be used if we implement download by script)
	 *
	 * @since    1.0.0
	 * @param    string  $filename
	 * @param    bool     $retbytes
	 * @return   int (or boolean false)
	 */
	public function readfile_chunked($filename, $retbytes = true) {

		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
		$buffer = '';
		$counter = 0;

		$handle = fopen($filename, 'rb');
		if ($handle === false)
		{
			return false;
		}

		while (!feof($handle))
		{
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			@ob_flush();
			@flush();

			if ($retbytes)
			{
				$counter += strlen($buffer);
			}
		}

		$status = fclose($handle);

		if ($retbytes && $status)
		{
			return $counter; // return num. bytes delivered like readfile() does.
		}

		return $status;
	}

	// ADMIN DASHBOARD WIDGET METHODS

	/**
	 * Read the latest file in all folders and return unseen data for notifying the admin.
	 *
	 * This method reads all folders and compares the last file in each folder
	 * with the last-access timestamp of the party that did not upload it;
	 * this detects whether the file has been seen. It returns an object that
	 * contains the array of objects notifications_array, which is used by both
	 * admin and public classes to build a notification to the user.
	 *
	 * @since    1.0.0
	 * @param    bool    $limit_to_current_user
	 * @return   object
	 */
	public function get_notifications_data($limit_to_current_user = false) {

		$response = new stdClass();
		$response->error = false;

		if ($limit_to_current_user) {
			$current_userid = get_current_user_id();
		}

		// check if user- and order-based client zones are enabled
		$userzones_enabled = get_option('wooclientzone_use_userzones') == 'yes';
		$orderzones_enabled = get_option('wooclientzone_use_orderzones') != 'never';

		// get the root folder
		$root_folder_object = $this->get_root_folder();

		if ($root_folder_object->error) {
			$response->error = true;
			$response->errorstring = __('Cannot get root folder', 'wooclientzone');
			return $response;
		}

		// we just need the path here
		$root_folder = $root_folder_object->path;

		// if the root folder has not been created (it is created with the first communication)
		// then report back as if there were no notifications to report
		if (!is_dir($root_folder)) {
			$response->error = true;
			$response->errorType = 'info';
			$response->errorstring = __('No unseen communications to report', 'wooclientzone');
			return $response;
		}

		// scan first level (users top folders)
		$notifications_array = array();
		$topfolders = array();
		$userids = array();
		$scan = scandir($root_folder);
		if (!$scan) {
			$response->error = true;
			$response->errorstring = __('Cannot scan top level folder', 'wooclientzone');
			return $response;
		}
		foreach($scan as $item) {
			if (is_dir($root_folder.$item) && substr($item, 0, 8) === "User ID ") {
				$parts = explode(' ', $item);
				if ($limit_to_current_user && $current_userid != $parts[count($parts) - 1]) {
					continue;   // skip other users if only data for the current user is requested
								// this also skips folder such as User ID n.backup
				}
				$userids[] = $parts[count($parts) - 1];
				$topfolders[] = $root_folder.$item.DIRECTORY_SEPARATOR;
			}
		}

		// scan second level to start building the notifications_array with userid, orderid and folder elements
		$j = 0;
		for ($i = 0; $i < count($userids); $i++) {

			// check first Common folder (only if userzones are enabled)
			if ($userzones_enabled && is_dir($topfolders[$i]."Common")) {
				$notifications_array[$j]->userid = $userids[$i];
				$notifications_array[$j]->orderid = '';
				$notifications_array[$j]->folder = $topfolders[$i]."Common".DIRECTORY_SEPARATOR;
				$j++;
			}

			// skip reading order dirs if orderzones are not enabled
			if (!$orderzones_enabled) {
				continue;
			}

			// now scan user dir for order dirs
			$scan = scandir($topfolders[$i]);
			if (!$scan) {
				$response->error = true;
				$response->errorstring = sprintf(__('Cannot scan user #%s toplevel folder', 'wooclientzone'), $userids[$i]);
				return $response;
			}

			foreach($scan as $item) {
				if (is_dir($topfolders[$i].$item) && substr($item, 0, 9) === "Order ID ") {
					$parts = explode(' ', $item);
					$notifications_array[$j]->userid = $userids[$i];
					$notifications_array[$j]->orderid = $parts[count($parts) - 1];
					$notifications_array[$j]->folder = $topfolders[$i].$item.DIRECTORY_SEPARATOR;
					$j++;
				}
			}
		}

		// now we can check each individual folder (note that we are iterating on a reference, hence we are changing the array)
		$notification_present = false;
		foreach($notifications_array as &$item) {

			// we only need to compare the last uploaded communications file with the other party last access time
			$scan = scandir($item->folder, SCANDIR_SORT_DESCENDING);
			if (!$scan) {
				$response->error = true;
				if ($item->orderid) {
					$response->errorstring = sprintf(__('Cannot scan user #%s folder for order #%s', 'wooclientzone'), $item->userid, $item->orderid);
				} else {
					$response->errorstring = sprintf(__('Cannot scan user #%s folder', 'wooclientzone'), $item->userid);
				}
				return $response;
			}
			foreach($scan as $file) {
				if ($this->validate_file_prefix($file)) {
					// this is the last uploaded communications file (we read the folder in descending order)
					$item->file = $file;
					break;
				}
			}
			// if no communications file was found then the directory is empty and we don't flag anything
			// (the calling function will not generate any flag)
			if (!$item->file) {
				continue;
			}

			// if we got here a communication file was found (the folder is not empty of communications files), and we make first a quick check on the last access files:
			// if one of those files does not exist (one must be there as there is at least one communications file) then the other party has not seen any files
			$client_lastaccess = file_get_contents($item->folder.$this->client_lastaccess_file);
			$admin_lastaccess = file_get_contents($item->folder.$this->admin_lastaccess_file);
			//
			if (!$client_lastaccess) {
				$item->client_unseen = true;
				$notification_present = true;
				continue;
			}
			if (!$admin_lastaccess) {
				$item->admin_unseen = true;
				$notification_present = true;
				continue;
			}

			// if we got here a (last) file was found and we check its upload time against the last access time of the party that did not upload it
			// note that we pass false to the get_upload_timestamp method to skip the sanity check, as we have already done it (see above) on this file
			$upload_timestamp = $this->get_upload_timestamp($item->file);
			if ($this->get_file_origin($item->file) == 'public' && $upload_timestamp > $admin_lastaccess) {
				// it's a client file older than the admin last access time
				$item->admin_unseen = true;
				$notification_present = true;
			} else if ($this->get_file_origin($item->file) == 'admin' && $upload_timestamp > $client_lastaccess) {
				// it's a client file older than the admin last access time
				$item->client_unseen = true;
				$notification_present = true;
			}
		}

		// check if notifications are present
		if (!$notification_present) {
			$response->error = true;
			$response->errorType = 'info';
			$response->errorstring = __('No unseen communications to report', 'wooclientzone');
			return $response;
		}

		// return response with the notifications array
		$response->notifications_array = $notifications_array;

		// TEST data
//		$response->notifications_array = array();
//		$response->notifications_array[] = (object)array( 'client_unseen' => true, 'userid' => 1, 'orderid' => 264 );
//		$response->notifications_array[] = (object)array( 'admin_unseen' => true, 'userid' => 1, 'orderid' => 261 );
//		$response->notifications_array[] = (object)array( 'admin_unseen' => true, 'userid' => 1, 'orderid' => '' );

		return $response;
	}

	/**
	 * Check file type (mime type or extension).
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array    $files
	 * @param    bool     $is_admin
	 * @return   bool
	 */
	private function allowed_file_type($files, $is_admin) {

		$filetype_option = $is_admin ? get_option('wooclientzone_accepted_files_admin') : get_option('wooclientzone_accepted_files_public');

		// this file data
		$mimetype = mime_content_type($files['file']['tmp_name']);
		$extension = pathinfo($files['file']['name'], PATHINFO_EXTENSION);

		// decode file type option string
		$filetype_option_parts = explode(',', $filetype_option);
		foreach($filetype_option_parts as $allowed_filetype) {
			$allowed_filetype = trim($allowed_filetype);
			if (strstr($allowed_filetype, "/")) {
				if ($allowed_filetype[strlen($allowed_filetype) - 1] === "*") {
					if (substr($allowed_filetype, 0, strlen($allowed_filetype) - 1) === substr($mimetype, 0, strlen($allowed_filetype) - 1)) {
						//$this->tools->debug('MATCHED WILDCARD MIMETYPE', substr($allowed_filetype, 0, strlen($allowed_filetype) - 1));
						return true;
					}
				} else if ($allowed_filetype === $mimetype) {
					//$this->tools->debug('MATCHED FULL MIMETYPE', $allowed_filetype);
					return true;
				}
			} else {
				if ($allowed_filetype === ".".$extension) {
					//$this->tools->debug('MATCHED EXTENSION', $allowed_filetype);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Check file size.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array    $files
	 * @param    bool     $is_admin
	 * @return   bool
	 */
	private function allowed_file_size($files, $is_admin) {

		$max_filesize = $is_admin ? get_option('wooclientzone_max_filesize_admin') : get_option('wooclientzone_max_filesize_public');
		$max_filesize = trim(str_replace(",", ".", $max_filesize));
		if (filesize($files['file']['tmp_name']) > $max_filesize * 1000000) {
			return false;
		}
		return true;
	}

	// METHODS TO MANAGE FILE PREFIXES AND FILE NAMING

	/**
	 * Validates the file based on its prefix.
	 *
	 * We perform a check on the prefix, which should be numbers; note that
	 * we do a loose check on these characters being number, for simplicity
	 * we don't check if they are actual valid date/time numbers.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string   $file
	 * @return   bool
	 */
	private function validate_file_prefix($file) {

//		if (preg_match('/^\d{14}/', $file)) {

		// futureproof: to add more flags, we need to modify this regex with
		// (e.g.) [XY]? after [MF] (the '?' to make it backward compatible)

		if (preg_match('/^\d{14}-[AP][MF]-\d{3}_/', $file)) {
			return true;
		}
		return false;
	}

	/**
	 * Get the upload timestamp of a file based on its prefix.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string   $file
	 * @return   int
	 */
	private function get_upload_timestamp($file) {

		$year   = substr($file, 0, 4);
		$month  = substr($file, 4, 2);
		$day    = substr($file, 6, 2);
		$hour   = substr($file, 8, 2);
		$minute = substr($file, 10, 2);
		$second = substr($file, 12, 2);

		return mktime($hour, $minute, $second, $month, $day, $year);
	}

	/**
	 * Populate an array with file data, mainly based on its prefix.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string   $file
	 * @param    object   $folder
	 * @param    int      $upload_timestamp
	 * @return   array
	 */
	private function get_file_array($file, $folder, $upload_timestamp) {

		$files = array();

		// we calculate the file name start position as it may change in the
		// future if we want to add extra flags before the '_' sign,
		// which we use at the character marking the end of the prefix
		$filename_start_pos = strpos($file, '_') + 1;

		$files['prefixed_name'] = $file;
		$files['name'] = substr($file, $filename_start_pos);
		$files['url'] = $folder->url ? $folder->url.$file : false;
		$files['origin'] = $this->get_file_origin($file);
		$files['is_message'] = (substr($file, 16, 1) == 'M' ? true : false);

		// futureproof: here we would read a future flag; for backward compatibility
		// we would need to check for its existence based on the value of $filename_start_pos

		$files['message'] = ($files['is_message'] ? file_get_contents($folder->path.$file) : '');
		$files['type'] = mime_content_type($folder->path.$file);
		$files['upload_timestamp'] = $upload_timestamp;
		$files['upload_date'] = date($this->date_format, $upload_timestamp);

		return $files;
	}

	/**
	 * Set a file prefix.
	 *
	 * We create a prefix based on the current timestamp, whether it is a
	 * file uploaded by the merchant or the client, and whether the file
	 * is meant to be an attachment file or a messaging file.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    bool     $is_admin
	 * @param    string   $type
	 * @return   string
	 */
	private function set_filename_prefix($is_admin, $type) {

		// we are using prefix format (regex): \d{14}-[AP][MF]-\d{3}_
		$prefix = date('YmdHis', time());
		$prefix .= "-";
		$prefix .= $is_admin ? 'A' : 'P';
		$prefix .= $type == 'message' ? 'M' : 'F';

		// futureproof: to add more flags, we need to modify the regex in
		// validate_file_prefix() as described therein, and add the flag below
		// (e.g.) $prefix .= (condition) ? 'X' : 'Y';
		// we would also need to modify get_file_array() to read the new flag,
		// and for backward compatibility we would need to check the value of
		// $filename_start_pos before attempting to read the new flag

		// we add a random element to make it more difficult to link directly to this resource
		$prefix .= "-".rand(100, 999)."_";

		return $prefix;
	}

	/**
	 * Gets the originating party of a file, based on its prefix.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string   $file
	 * @return   string
	 */
	private function get_file_origin($file) {

		if (substr($file, 15, 1) === 'A') {
			return 'admin';
		}
		return 'public';
	}


}
