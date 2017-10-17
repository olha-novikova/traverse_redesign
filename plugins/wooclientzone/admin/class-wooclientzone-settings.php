<?php

/**
 * The admin-settings specific functionality of the plugin.
 *
 * @link		   http://blendscapes.com
 * @since		  1.0.0
 *
 * @package		Wooclientzone
 * @subpackage Wooclientzone/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package		Wooclientzone
 * @subpackage Wooclientzone/admin
 * @author		 Enrico Sandoli <enrico.sandoli@blendscapes.com>
 */
class Wooclientzone_Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since		1.0.0
	 * @access   private
	 * @var		  string		$plugin_name		The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since		1.0.0
	 * @access   private
	 * @var		  string		$version		The current version of this plugin.
	 */
	private $version;

	/**
	 * The tools private property.
	 *
	 * @since		1.0.0
	 * @access   private
	 * @var		  object		$tools		An object with utility methods.
	 */
	private $tools;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since		1.0.0
	 * @param		  string		$plugin_name		   The name of this plugin.
	 * @param		  string		$version		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->tools = new Wooclientzone_Tools($plugin_name, $version);
	}

	/**
	 * Register the stylesheets for the settings area.
	 *
	 * @since		1.0.0
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

		// the handler must be unique, so we added _settings
		wp_enqueue_style( $this->plugin_name.'_settings', plugin_dir_url( __FILE__ ) . 'css/wooclientzone-settings.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the settings area.
	 *
	 * @since		1.0.0
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

		// the handler must be unique, so we added _settings
		wp_enqueue_script( $this->plugin_name.'_settings', plugin_dir_url( __FILE__ ) . 'js/wooclientzone-settings.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add tab to WooCommerce Settings page.
	 *
	 * @since		1.0.0
	 */
	public function add_settings_tab($tab_array) {

		$tab_array['wooclientzone'] = __('Client Zone', 'wooclientzone');
		return $tab_array;

	}

	/**
	 * Add the various settings tab to WooCommerce Settings page.
	 *
	 * @since		1.0.0
	 */
	public function create_settings() {

		woocommerce_admin_fields($this->get_settings());
	}

	// All the default values are created in the activator class, when activating the plugin!
	private function get_settings() {

		// note that the id is the value of the option_name field in the options db table
		$settings = array(

			'file_locations'        => array(
				'id'				=> 'wooclientzone_file_locations_title',
				'name'				=> __('File locations', 'wooclientzone'),
				'type'				=> 'title',
				'desc'				=> '',
			),
			'root_folder'			=> array(
				'id'				=> 'wooclientzone_root_folder',
				'name'				=> __('Files root folder', 'wooclientzone'),
				'type'				=> 'text',
				'css'				=> 'min-width:200px;',
				'default'			=> 'wooclientzone_repo',
				'desc'				=> "<br><br><p>".__('This is the root folder for all client and merchant files of which a Client Zone is made of. It is defined starting '
										. 'from the "Site Address (URL)" in Wordpress General Settings, and it can include subfolders, such as in wooclientzone_repo/[subfolder]. '
										. 'Under the root, files are organised inside "User ID [user_id] / Common" folders for user-linked files, and inside '
										. '"User ID [user_id] / Order ID [order_id]" folders for files of client zones embedded in orders.',
										'wooclientzone')."</p>",
//												'desc'		  => __('This is the root folder for all client and merchant files. Files will be stored grouped by user ID and/or Order ID depending on configuration. This parameter is never displayed or sent over the network.', 'wooclientzone'),
				'desc_tip'			=> false,
			),
		// TODO we will at some point manage files outside the web root, and thus allow the administrator to define the file root in absolute terms
//		'root_folder_relative'	=> array(
//			'id'				=> 'wooclientzone_root_is_relative',
//			'name'		  => __('Root folder relative to WP root', 'wooclientzone'),
//			'type'			=> 'checkbox',
//			'default'   => 'yes',
//			'desc'			=> __("If checked, this parameter indicates that the above root folder is to be intended as relative to the Wordpress root folder. "
//							. "If left unchecked, you'll need to specify the root folder in absolute terms (as a full path). Ensure that the root folder falls inside "
//							. "the web root.", 'wooclientzone'),
////			'desc'			=> __("If checked, this parameter indicates that the above root folder is to be intended as relative to the Wordpress root folder. If left unchecked, you'll need to specify the root folder in absolute terms (as a full path). Setting the root folder outside of the web root altogether increases security as the files will not be reachable using a web browser outside the scope of this plugin.", 'wooclientzone'),
//			'desc_tip'  => true,
//		),

//								'description'		   => array(
//												'name'		  => __('Description', 'wooclientzone'),
//												'type'		  => 'textarea',
//												'desc'		  => __('This is a paragraph with some description text', 'wooclientzone_textdoamin'),
//												'id'				=> 'wooclientzone_description',
//												'desc_tip'  => true,
//								),
			'file_locations_end'	=> array(
				'type'				=> 'sectionend',
				'id'				=> 'wooclientzone_file_locations_end',
			),

			'orderzones_options'	=> array(
				'id'				=> 'wooclientzone_orderzones_options_title',
				'name'				=> __('User- and Order-linked Client Zones', 'wooclientzone'),
				'type'				=> 'title',
				'desc'				=> __('Defines if and how to create Client Zones not associated to orders (available to any registered user) and/or linked to orders. '
										. 'Note that cancelled orders will not have a Client Zone associated.', 'wooclientzone'),
			),
			'use_userzones'			=> array(
				'title'				=> __('Create user-linked zones', 'wooclientzone'),
				'id'				=> 'wooclientzone_use_userzones',
				'desc'				=> __('Available to any registered user', 'wooclientzone'),
				'type'				=> 'checkbox',
				'checkboxgroup'		=> 'start',
				'default'			=> 'yes',
				'desc_tip'			=> __("If checked, each registered user will have an individual Client Zone (not linked to any specific order).", 'wooclientzone'),
			),
			'automove_to_orderzone'	=> array(
				'id'				=> 'wooclientzone_automove_to_orderzone',
				'desc'				=> __('Automatically move a user-linked Client Zone to the next eligible order', 'wooclientzone'),
				'type'				=> 'checkbox',
				'checkboxgroup'		=> 'start',
				'default'			=> 'no',
				'desc_tip'			=> __("If checked, when clients make a purchase, their user-linked Client Zone becomes the Client Zone associated to that order, provided "
										. "both user-linked  and order-linked Client Zones are enabled, and, if so defined in the parameter below, an eligible product is included in the order.", 'wooclientzone'),
			),
			'use_orderzones'		=> array(
				'id'				=> 'wooclientzone_use_orderzones',
				'name'				=> __('Create order-linked zones', 'wooclientzone'),
				'type'				=> 'select',
				'default'			=> 'products',
				'options'			=> array (
					'never'			=> 'never create order-linked zones',
					'products'		=> 'only for orders containing specific products',
					'always'		=> 'for all orders',
				),
				'desc'				=> __("Client zones linked to orders can be defined for all orders or just for "
										. "those including spcific products; these are products with the option 'Use Client Zone' selected.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'orderzones_options_end'=> array(
				'type'				=> 'sectionend',
				'id'				=> 'wooclientzone_orderzones_options_end',
			),

			'default_access'		=> array(
				'id'				=> 'wooclientzone_default_access_title',
				'name'				=> __('Default access', 'wooclientzone'),
				'type'				=> 'title',
				'desc'				=> __('Different default permissions can be set on user-linked as opposed to order-linked Client Zones. '
					. 'In all cases these parameters only apply to permissions for the client: the merchant always has the ability to both upload and submit messages to any Client Zone.', 'wooclientzone'),
			),
			'client_message_userzones'	=> array(
				'title'				=> __('User-linked Permissions', 'wooclientzone'),
				'id'				=> 'wooclientzone_client_message_userzones',
				'desc'				=> __('Allow submission of messages', 'wooclientzone'),
				'type'				=> 'checkbox',
				'checkboxgroup'		=> 'start',
				'default'			=> 'yes',
				'desc_tip'			=> __("If checked, clients are allowed to send text messages from their Client Zones. This setting can be changed within individual Client Zones.", 'wooclientzone'),
			),
			'client_upload_userzones'	=> array(
				'id'				=> 'wooclientzone_client_upload_userzones',
				'desc'				=> __('Allow file upload', 'wooclientzone'),
				'type'				=> 'checkbox',
				'checkboxgroup'		=> 'end',
				'default'			=> 'yes',
				'desc_tip'			=> __("If checked, clients are allowed to upload files to their Client Zones. This setting can be changed within individual Client Zones.", 'wooclientzone'),
			),
			'client_message_orderzones'	=> array(
				'title'				=> __('Order-linked Permissions', 'wooclientzone'),
				'id'				=> 'wooclientzone_client_message_orderzones',
				'desc'				=> __('Allow submission of messages', 'wooclientzone'),
				'type'				=> 'checkbox',
				'checkboxgroup'		=> 'start',
				'default'			=> 'yes',
				'desc_tip'			=> __("If checked, clients are allowed to send text messages from their order-linked Client Zone. This setting can be changed within individual Client Zones.", 'wooclientzone'),
			),
			'client_upload_orderzones'	=> array(
				'id'				=> 'wooclientzone_client_upload_orderzones',
				'desc'				=> __('Allow file upload', 'wooclientzone'),
				'type'				=> 'checkbox',
				'checkboxgroup'		=> 'end',
				'default'			=> 'yes',
				'desc_tip'			=> __("If checked, clients are allowed to upload files to their order-linked Client Zones. This setting can be changed within individual Client Zones.", 'wooclientzone'),
			),
			'default_access_end'	=> array(
				'type'				=> 'sectionend',
				'id'				=> 'wooclientzone_default_access_end',
			),

			'display_options'		=> array(
				'id'				=> 'wooclientzone_display_options_title',
				'name'				=> __('Display options', 'wooclientzone'),
				'type'				=> 'title',
				'desc'				=> __('Defines options such as the positioning of the bubbles containing messages and documents, as well as various colors.', 'wooclientzone'),
			),
			'date_format'			=> array(
				'id'				=> 'wooclientzone_date_format',
				'name'				=> __('Date format', 'wooclientzone'),
				'type'				=> 'select',
				'default'			=> 'j M Y, H:i',
				'options'			=> array (
					'j M Y, H:i'	=> 'D MMM YYYY, hh:mm',
					'M j, Y, H:i'	=> 'MMM D, YYYY, hh:mm',
					'd/m/Y, H:i'	=> 'DD/MM/YYYY, hh:mm',
					'm/d/Y, H:i'	=> 'MM/DD/YYYY, hh:mm',
				),
				'desc'				=> __("This is the date format used under the bubbles of files and messages in a Client Zone.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'my_bubbles_position'	=> array(
				'id'				=> 'wooclientzone_my_bubbles_position',
				'name'				=> __('Position of "my" bubbles', 'wooclientzone'),
				'type'				=> 'select',
				'default'			=> 'right',
				'options'			=> array (
					'left'			=> __('Left', 'wooclientzone'),
					'right'			=> __('Right', 'wooclientzone'),
				),
				'desc'				=> __("This parameter defines the position of the bubbles of the active user. In the admin site this is the merchant, and in the public site it's the client.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'bubbles_color_client_public' => array(
				'id'				=> 'wooclientzone_bubbles_color_client_public',
				'name'				=> __('Client bubbles (Public)', 'wooclientzone'),
				'type'				=> 'color',
				'default'			=> '#dcf7c8',
				'css'				=> 'width:6em;',
				'desc'				=> __("This is the color of the client's bubbles in the public (frontend) site.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'bubbles_color_merchant_public'		 => array(
				'id'				=> 'wooclientzone_bubbles_color_merchant_public',
				'name'				=> __('Merchant bubbles (Public)', 'wooclientzone'),
				'type'				=> 'color',
				'default'			=> '#e0e0e0',
				'css'				=> 'width:6em;',
				'desc'				=> __("This is the color of the merchant's bubbles in the public (frontend) site.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'bubbles_color_merchant_admin' => array(
				'id'				=> 'wooclientzone_bubbles_color_merchant_admin',
				'name'				=> __('Merchant bubbles (Admin)', 'wooclientzone'),
				'type'				=> 'color',
				'default'			=> '#dcf7c8',
				'css'				=> 'width:6em;',
				'desc'				=> __("This is the color of the merchant's bubbles in the administrative (backend) site.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'bubbles_color_client_admin' => array(
				'id'				=> 'wooclientzone_bubbles_color_client_admin',
				'name'				=> __('Client bubbles (Admin)', 'wooclientzone'),
				'type'				=> 'color',
				'default'			=> '#f0f0f0',
				'css'				=> 'width:6em;',
				'desc'				=> __("This is the color of the client's bubbles in the administrative (backend) site.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'display_options_end'	=> array(
				'type'				=> 'sectionend',
				'id'				=> 'wooclientzone_display_options_end',
			),

			'uploads_options'		=> array(
				'id'				=> 'wooclientzone_uploads_options_title',
				'name'				=> __('File uploads', 'wooclientzone'),
				'type'				=> 'title',
				//'desc'			=> 'Defines options related to file uploads',
			),
			'accepted_files_public'	=> array(
				'id'				=> 'wooclientzone_accepted_files_public',
				'name'				=> __('Accepted files (Public)', 'wooclientzone'),
				'type'				=> 'text',
				'css'				=> 'min-width:300px;',
				'default'			=> 'image/*,application/pdf',
				'desc'				=> __('This is a comma separated list of mime types or file extensions. For example: image/*,application/pdf,.psd', 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'max_filesize_public'	=> array(
				'id'				=> 'wooclientzone_max_filesize_public',
				'name'				=> __('Max size in MB (Public)', 'wooclientzone'),
				'type'				=> 'text',
				'css'				=> 'width:50px;',
				'default'			=> '8',
				'desc'				=> __('This is the maximum file size accepted by the browser. The actual maximum file size will also depend on the server settings,'
					. ' specifically the PHP configuration parameters upload_max_size, post_max_size, max_execution_time, which you may manage either in the '
					. 'php.ini file, or by using a htaccess in the WP root directory.', 'wooclientzone'),
				'desc_tip'  => true,
			),
			'accepted_files_admin'	=> array(
				'id'				=> 'wooclientzone_accepted_files_admin',
				'name'				=> __('Accepted files (Admin)', 'wooclientzone'),
				'type'				=> 'text',
				'css'				=> 'min-width:300px;',
				'default'			=> 'image/*,application/pdf,.doc,.docx',
				'desc'				=> __('This is a comma separated list of mime types or file extensions. For example: image/*,application/pdf,.psd', 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'max_filesize_admin'	=> array(
				'id'				=> 'wooclientzone_max_filesize_admin',
				'name'				=> __('Max size in MB (Admin)', 'wooclientzone'),
				'type'				=> 'text',
				'css'				=> 'width:50px;',
				'default'			=> '12',
				'desc'				=> __('This is the maximum file size accepted by the browser. The actual maximum file size will also depend on the server settings,'
					. ' specifically the PHP configuration parameters upload_max_size, post_max_size, max_execution_time, which you may manage either in the '
					. 'php.ini file, or by using a htaccess in the WP root directory.', 'wooclientzone'),
				'desc_tip'  => true,
			),
			'progress_bar_color_public' => array(
				'id'				=> 'wooclientzone_progress_bar_color_public',
				'name'				=> __('Progress bar (Public)', 'wooclientzone'),
			'type'					=> 'color',
				'default'			=> '#55cce1',
				'css'				=> 'width:6em;',
				'desc'				=> __("This is the color of the progress bar when uploading files in the public (frontend) site.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'progress_bar_color_admin' => array(
				'id'				=> 'wooclientzone_progress_bar_color_admin',
				'name'				=> __('Progress bar (Admin)', 'wooclientzone'),
				'type'				=> 'color',
				'default'			=> '#55cce1',
				'css'				=> 'width:6em;',
				'desc'				=> __("This is the color of the progress bar when uploading files in the administrative (backend) site.", 'wooclientzone'),
				'desc_tip'  => true,
			),
			'uploads_options_end'	=> array(
				'type'				=> 'sectionend',
				'id'				=> 'wooclientzone_uploads_options_end',
			),

			'notifications_options' => array(
				'id'				=> 'wooclientzone_notifications_options_title',
				'name'				=> __('Client notifications', 'wooclientzone'),
				'type'				=> 'title',
				'desc'				=> __('These emails are not sent automatically, but can be sent from the individual Client Zones pages.', 'wooclientzone'),
			),
			'mail_to_client_subject' => array(
				'id'				=> 'wooclientzone_mail_to_client_subject',
				'name'				=> __('Email subject', 'wooclientzone'),
				'type'				=> 'text',
				'css'				=> 'min-width:300px;',
				'default'			=> 'New communications for you',
				'desc'				=> "<br><br><p>".__('This is the subject of the email notification to the clients.', 'wooclientzone')."</p>",
				'desc_tip'			=> false,
			),
			'mail_to_client_user_clientzone' => array(
				'id'				=> 'wooclientzone_mail_to_client_user_clientzone',
				'name'				=> __('User Client Zone', 'wooclientzone'),
				'type'				=> 'textarea',
				'css'				=> 'min-width:300px;height:150px;white-space: pre-wrap;',
				'default'			=>
__('Dear [client_name],

		This is to inform you that you have new communications from [site_name].
		To view them, please go to [my_account_link](My Account page).

		Kind regards,
		The team at [site_name]', 'wooclientzone'),
				'desc'				=> __('This is the email that can be sent to notify clients of new communications related to their Client Zone not linked to a specific order. '
					. 'Allowed placeholders are [client_name], [site_name] and [my_account_link]. Note that the latter may be immediately followed by the link name in round brackets.', 'wooclientzone_textdoamin'),
				'desc_tip'			=> true,
			),
			'mail_to_client_order_clientzone' => array(
				'id'				=> 'wooclientzone_mail_to_client_order_clientzone',
				'name'				=> __('Order-linked Client Zone', 'wooclientzone'),
				'type'				=> 'textarea',
				'css'				=> 'min-width:300px;height:150px;white-space: pre-wrap;',
				'default'			=>
__('Dear [client_name],

		This is to inform you that you have new communications from [site_name] related to your order ID [order_id].
		To view them, please go to [my_account_link].

		Kind regards,
		The team at [site_name]', 'wooclientzone'),
				'desc'				=> __('This is the email that can be sent to notify clients of new communications related to their Client Zone linked to a specific order. '
					. 'Allowed placeholders are [client_name], [site_name], [order_id] and [my_account_link]. Note that the latter may be immediately followed by the link name in round brackets.', 'wooclientzone_textdoamin'),
				'desc_tip'			=> true,
			),
			'notifications_options_end' => array(
				'type'				=> 'sectionend',
				'id'				=> 'wooclientzone_notifications_options_end',
			),

			'logging_options'		=> array(
				'id'				=> 'wooclientzone_logging_options_title',
				'name'				=> __('Logging options', 'wooclientzone'),
				'type'				=> 'title',
				'desc'				=> __('You can set the minimum level of severity of the messages to be logged.', 'wooclientzone'),
			),
			'logging_level'			=> array(
				'id'				=> 'wooclientzone_logging_level',
				'name'				=> __('Minimum severity', 'wooclientzone'),
				'type'				=> 'select',
				'default'			=> WOOCLIENTZONE_LOG_WARNING,
				'options'			=> array (
					WOOCLIENTZONE_LOG_EMERGENCY => __('Emergency level', 'wooclientzone'),
					WOOCLIENTZONE_LOG_ALERT     => __('Alert level', 'wooclientzone'),
					WOOCLIENTZONE_LOG_CRITICAL  => __('Critical level', 'wooclientzone'),
					WOOCLIENTZONE_LOG_ERROR     => __('Error level', 'wooclientzone'),
					WOOCLIENTZONE_LOG_WARNING   => __('Warning level', 'wooclientzone'),
					WOOCLIENTZONE_LOG_NOTICE    => __('Notice level', 'wooclientzone'),
					WOOCLIENTZONE_LOG_INFO      => __('Information level', 'wooclientzone'),
					WOOCLIENTZONE_LOG_NONE      => __('Do not log any activity', 'wooclientzone'),
				),
				'desc'				=> __("You can access the logs from WooCommerce > System Status > Logs, then selecting the 'wooclientzone-' log file from the drop-down menu.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'logging_debug'			=> array(
				'id'				=> 'wooclientzone_logging_debug',
				'desc'				=> __('Log debugging messages', 'wooclientzone'),
				'type'				=> 'checkbox',
				'default'			=> 'no',
			),
			'logging_options_end'	=> array(
				'type'				=> 'sectionend',
				'id'				=> 'wooclientzone_logging_options_end',
			),
			
			'advanced_options'		=> array(
				'id'				=> 'wooclientzone_advanced_options_title',
				'name'				=> __('Advanced options', 'wooclientzone'),
				'type'				=> 'title',
			),
			'refresh_rate'			=> array(
				'id'				=> 'wooclientzone_refresh_rate',
				'name'				=> __('Check for new messages', 'wooclientzone'),
				'type'				=> 'select',
				'default'			=> '5000',
				'options'			=> array (
					'5000'			=> __('Every 5 seconds', 'wooclientzone'),
					'10000'			=> __('Every 10 seconds', 'wooclientzone'),
					'15000'			=> __('Every 15 seconds', 'wooclientzone'),
					'30000'			=> __('Every 30 seconds', 'wooclientzone'),
					'60000'			=> __('Every minute', 'wooclientzone'),
				),
				'desc'				=> __("When viewing a Client Zone, the system will automatically check for new messages or files from the other party with a frequency defined by this parameter.", 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'myaccount_menu_item_text' => array(
				'id'				=> 'wooclientzone_myaccount_menu_item_text',
				'name'				=> __('My Account menu text', 'wooclientzone'),
				'type'				=> 'text',
				'css'				=> 'min-width:150px;',
				'desc'				=> __('Text to display on the WooClientZone menu icon on the My Account public page', 'wooclientzone'),
				'default'			=> __('Communications', 'wooclientzone'),
				'desc_tip'			=> true,
			),
			'myaccount_menu_item_display_icon' => array(
				'id'				=> 'wooclientzone_myaccount_menu_item_display_icon',
				'title'				=> __('My Account menu icon', 'wooclientzone'),
				'desc'				=> __('Display custom WooClientZone icon', 'wooclientzone'),
				'type'				=> 'checkbox',
				'default'			=> 'yes',
				'desc_tip'			=> __('By default, Wooclientzone adds a custom icon next to its menu entry on the My Account frontend page. Hiding this icon works better '
					. 'with those themes that do not display icons in that menu. On the other hand, a theme like Storefront displays such icons, so keeping '
					. 'this checkbox selected will display the custom WooClientZone icon instead of a generic one.', 'wooclientzone'),
			),
			'reset_on_activation'	=> array(
				'title'				=> __('Reset on activation', 'wooclientzone'),
				'id'				=> 'wooclientzone_reset_on_activation',
				'desc'				=> __('Re-activating the plugin will reset all settings to their default value', 'wooclientzone'),
				'type'				=> 'checkbox',
				'default'			=> 'no',
				'desc_tip'			=> __("If checked, all settings will be removed from the database when deactivating the plugin (actual Client Zones will not be affected,"
					. " although they may have to be 'repointed' if the 'Files root folder' was changed from its default value)", 'wooclientzone'),
			),
			'advanced_options_end'	=> array(
				'type'				=> 'sectionend',
				'id'				=> 'wooclientzone_advanced_options_end',
			),

		);
		return apply_filters('wooclientzone_settings', $settings);
	}

	public function update_settings() {

		woocommerce_update_options($this->get_settings());
	}

	// PRODUCT DATA NEW TYPE FOR CLIENT ZONE

	/**
	 * Add the various settings tab to WooCommerce Product page.
	 *
	 * @since		1.0.0
	 */
	public function add_product_type_option($product_type_options) {

		// new product type option checkbox after virtual and downloadable
		$product_type_options['wooclientzone_enabled'] = array(
			'id'					=> '_wooclientzone_enabled',
			'wrapper_class'			=> 'hide_if_external',
			'label'					=> __('Use Client Zone', 'wooclientzone'),
			'description'			=> __('Any order containing this product will have a Client Zone enabled, where files and messages can be exchanged between the customer '
				. 'and the merchant. This default setting may be changed from the Woocommerce settings page (Client Zone tab)', 'wooclientzone'),
			'default'				=> 'no',
		);
		return $product_type_options;
	}

	// save new product type option
	public function save_product_data_panel($post_id) {

		// save Use Client Zone setting in product type options (side of virtual and downloadable)
		$use_wooclientzone = isset($_POST['_wooclientzone_enabled']) ? 'yes' : 'no';
		update_post_meta($post_id, '_wooclientzone_enabled', $use_wooclientzone);
	}

	// WC REPORTS -> CUSTOMERS LIST NEW ACTION BUTTON

	/**
	 * Displays the client zone button as an extra action in the customer list table within the wc reports -> customers -> customer list area.
	 * The url parameter links to the admin wooclientzone, which is an admin page created by hooking to admin_menu
	 *
	 * @since		1.0.0
	 */
	public function customer_list_add_action_button($actions, $user) {

		$nonce_action = 'wooclientzone-userid='.$user->ID.'&orderid=';
		$actions['wooclientzone'] = array(
			'url'			=> esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$user->ID), $nonce_action, 'wooclientzone')),
			'name'			=> sprintf(__( 'Client%sZone', 'wooclientzone' ), "<br>"),
			'action'		=> "clientzone-button" // this is the button class, and is defined in wooclientzone-settings.css
		);
		return $actions;
	}

	// WC ORDERS LIST NEW ACTION BUTTON

	/**
	 * Displays the client zone button as an extra action in the order list table.
	 * The url parameter links to the admin wooclientzone, which is an admin page created by hooking to admin_menu
	 *
	 * @since		1.0.0
	 */
	public function order_list_add_action_button($actions, $order) {

		if ($this->tools->clientzone_enabled_for_order($order)) {
			$userid = $order->get_user_id();

			// this is how we access order id (which should not be accessed directly as $order->id)
			$orderid = trim(str_replace('#', '', $order->get_order_number()));

			$nonce_action = 'wooclientzone-userid='.$userid.'&orderid='.$orderid;
			$actions['wooclientzone'] = array(
				'url'		=> esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$userid.'&orderid='.$orderid), $nonce_action, 'wooclientzone')),
				'name'		=> sprintf(__( 'Client%sZone', 'wooclientzone' ), "<br>"),
				'action'	=> "clientzone-button" // this is the button class, and is defined in wooclientzone-settings.css
			);
		}
		return $actions;
	}

	// CLIENT ZONE ADMIN PAGE

	/**
	 * This creates the client zone page; it's the method hooked to admin_menu; no admin menu item is created.
	 *
	 * @since		1.0.0
	 */
	public function create_admin_wooclientzone() {

		add_menu_page( 'Client Zone', 'Client Zone', 'edit_posts', 'wooclientzone', array($this, 'display_admin_wooclientzone'), '', 24 );
		remove_menu_page('wooclientzone'); // we don't display the wooclientzone as a menu item. It will be referenced directly from other parts of the admin site
	}

	/**
	 * This method is fired by the add_menu_page() which creates the backend page. Once the page is created is also available statically as the url parameter in
	 * the customer_list_add_action_button(), which creates a client zone button in the customers list table (as an extra action button in the last column). So
	 * we can get to this method from anywhere in the admin site, such as from the orders table. When linking from areas of the admin site we also pass the user
	 * ID parameter (or the order ID) as a get parameter, and we retrieve this from the global $_GET.
	 *
	 * This method just fires an action admin_clientzone, which is meant to be used by class Wooclientzone_Admin to provide the actual display functionality.
	 *
	 * @since		1.0.0
	 */
	public function display_admin_wooclientzone() {

		// we now check the nonce and fire the action that is hooked by Wooclientzone_Admin
		// to display the admin client zones (we add two more actions before and after for extensions)
		$orderid = isset($_GET['orderid']) ? $_GET['orderid'] : '';
		if (is_user_logged_in() && wp_verify_nonce($_GET['wooclientzone'], 'wooclientzone-userid='.$_GET['userid'].'&orderid='.$orderid)) {

			do_action('wooclientzone_admin_clientzone_before');
			do_action('wooclientzone_admin_clientzone');
			do_action('wooclientzone_admin_clientzone_after');
		}
		else {
			$this->tools->error_message(__('Cannot view Communications Area. Reload if secure link expired (code 3)', 'wooclientzone'));
		}
	}

	// WP USER TABLE CUSTOM COLUMN

	/**
	 * Displays a client zone link as an extra column in the users list table of the Wordpress settings
	 * This is the filter for for the column header
	 *
	 * @since		1.0.0
	 */
	public function add_userlist_column_header($column) {

		$column['wooclientzone'] = "CZone";//<div class='dashicons dashicons-format-chat' style='font-size:20px;'></div>";
		return $column;
	}

	/**
	 * Displays a client zone link as an extra column in the users list table of the Wordpress settings
	 * This is the filter for for the column content
	 *
	 * @since		1.0.0
	 */
	public function add_userlist_column_content($val, $column_name, $userid) {

		$nonce_action = 'wooclientzone-userid='.$userid.'&orderid=';
		$url = esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$userid), $nonce_action, 'wooclientzone'));
		if ($column_name == 'wooclientzone') {
			$title = __( 'Client Zone', 'wooclientzone' );
			return "<a href=".$url." title='".$title."'><div class='dashicons dashicons-format-chat' style='font-size:20px;'></div></a>";
		}
		return $val;
	}

	public function add_link_to_clientzone_to_user_edit_page($user) {

		//$this->tools->debug('USER', $user);
		$nonce_action = 'wooclientzone-userid='.$user->ID.'&orderid=';
		$url = esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$user->ID), $nonce_action, 'wooclientzone'));
		$title = __( 'Client Zone', 'wooclientzone' );
		?>
		<div id='userProfileWooclientzoneLinkDiv'>
			<h2>
				<div id='userProfileWooclientzoneLinkLabel'><?php echo $title; ?></div>
				<a id ='userProfileWooclientzoneLinkTag' href='<?php echo $url; ?>'>
					<div id='userProfileWooclientzoneIcon' class='dashicons dashicons-format-chat'></div>
				</a>
			</h2>
		</div>
		<?php
	}

	public function add_link_to_clientzone_to_order_edit_page($order) {

		// check if the order is associated with a client zone
		if (!$this->tools->clientzone_enabled_for_order($order)) {
			return;
		}

		$userid = $order->get_user_id();

		// this is how we access order id (which should not be accessed directly as $order->id)
		$orderid = trim(str_replace('#', '', $order->get_order_number()));

		$nonce_action = 'wooclientzone-userid='.$userid.'&orderid='.$orderid;
		$url = esc_url(wp_nonce_url(admin_url('?page=wooclientzone&userid='.$userid.'&orderid='.$orderid), $nonce_action, 'wooclientzone'));
		?>
		<div id ='orderEditWooclientzoneLinkDiv'>
			<a id ='orderEditWooclientzoneLinkTag' href='<?php echo $url; ?>'>
				<div id='orderEditWooclientzoneLinkLabel'><strong><?php echo __('View Client Zone associated to this order', 'wooclientzone'); ?></strong></div>
				<div id ='orderEditWooclientzoneIcon' class='dashicons dashicons-format-chat'></div>
			</a>
		</div>
		<?php
	}
}
