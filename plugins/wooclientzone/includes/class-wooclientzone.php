<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://blendscapes.com
 * @since      1.0.0
 *
 * @package    Wooclientzone
 * @subpackage Wooclientzone/includes
 */

/**
 * Constants used in activity logging.
 *
 * @since      1.0.0
 */
define('WOOCLIENTZONE_LOG_EMERGENCY', 7);
define('WOOCLIENTZONE_LOG_ALERT',     6);
define('WOOCLIENTZONE_LOG_CRITICAL',  5);
define('WOOCLIENTZONE_LOG_ERROR',     4);
define('WOOCLIENTZONE_LOG_WARNING',   3);
define('WOOCLIENTZONE_LOG_NOTICE',    2);
define('WOOCLIENTZONE_LOG_INFO',      1);

define('WOOCLIENTZONE_LOG_NONE',    100);
define('WOOCLIENTZONE_LOG_DEBUG',   100);

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wooclientzone
 * @subpackage Wooclientzone/includes
 * @author     Enrico Sandoli <enrico.sandoli@blendscapes.com>
 */
class Wooclientzone {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wooclientzone_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wooclientzone';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_settings_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - wp-admin/include/file.php. WP core file, needed to use get_home_path() in the front end.
	 * - Wooclientzone_Loader. Orchestrates the hooks of the plugin.
	 * - Wooclientzone_File_Manager. Manages the files of the plugin.
	 * - Wooclientzone_i18n. Defines internationalization functionality.
	 * - Wooclientzone_Settings. Manages the admin configuration and navigation.
	 * - Wooclientzone_Admin. Defines all hooks for the admin area.
	 * - Wooclientzone_Public. Defines all hooks for the public side of the site.
	 * - Wooclientzone_Tools. Defines utility methods used by the plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * This is crucial if we need to use get_home_path() in the front end
		 * SEE: http://wordpress.stackexchange.com/questions/188448/whats-the-difference-between-get-home-path-and-abspath
		 */
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wooclientzone-loader.php';

		/**
		 * The class responsible for managing the files.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wooclientzone-file-manager.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wooclientzone-i18n.php';

		/**
		 * The class responsible for defining all settings in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wooclientzone-settings.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wooclientzone-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wooclientzone-public.php';

		$this->loader = new Wooclientzone_Loader();

		/**
		 * A class with common tools such as the debug method.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wooclientzone-tools.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wooclientzone_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wooclientzone_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_settings_hooks() {

		$plugin_settings = new Wooclientzone_Settings( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_settings, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_settings, 'enqueue_scripts' );

		// GENERAL SETTINGS

		// create settings tab within main woocommerce settings page
		$this->loader->add_filter('woocommerce_settings_tabs_array',            $plugin_settings, 'add_settings_tab', 30);
		$this->loader->add_action('woocommerce_settings_tabs_wooclientzone',    $plugin_settings, 'create_settings');
		$this->loader->add_action('woocommerce_update_options_wooclientzone',   $plugin_settings, 'update_settings');

		// PRODUCT DATA SETTINGS

		// create new product type option checkbox after virtual and downloadable
		$this->loader->add_filter('product_type_options', $plugin_settings, 'add_product_type_option');
		// save the new product type options
		$this->loader->add_action('woocommerce_process_product_meta', $plugin_settings, 'save_product_data_panel');

		// USER CLIENT ZONES SETTINGS

		// check if user-based client zones are enabled
		if (get_option('wooclientzone_use_userzones') == 'yes') {
			// CREATE BUTTON ON reports customer list table (as a new button of the actions)
			// add an 'action' to view the user common client zone from the backend
			$this->loader->add_filter( 'woocommerce_admin_user_actions', $plugin_settings, 'customer_list_add_action_button', 30, 2);

			// ADD column to WP user list table
			$this->loader->add_filter('manage_users_columns', $plugin_settings, 'add_userlist_column_header');
			$this->loader->add_filter('manage_users_custom_column', $plugin_settings, 'add_userlist_column_content', 30, 3);

			// ADD link to Client Zone in user edit page
			$this->loader->add_action('personal_options', $plugin_settings, 'add_link_to_clientzone_to_user_edit_page');
		}

		// ORDER CLIENT ZONES SETTINGS

		// CREATE BUTTON ON orders list table (as a new button of the actions)
		// add an 'action' to view the client zone for the relevan order
		$this->loader->add_filter( 'woocommerce_admin_order_actions', $plugin_settings, 'order_list_add_action_button', 30, 2);

		// ADD link to Client Zone to admin order edit page
		$this->loader->add_action('woocommerce_order_item_add_action_buttons', $plugin_settings, 'add_link_to_clientzone_to_order_edit_page');

		// CREATE A NEW ADMIN PAGE to host the client zones

		$this->loader->add_action('admin_menu', $plugin_settings, 'create_admin_wooclientzone');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wooclientzone_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// these are the filters to add extra links to the meta information on the plugins screen
		$this->loader->add_filter( 'plugin_action_links_' . "wooclientzone/wooclientzone.php", $plugin_admin, 'plugin_action_links' );
		$this->loader->add_filter('plugin_row_meta', $plugin_admin, 'plugin_row_meta', 10, 2);
		
		// this is the action hook that allows the Wooclientzone_Admin object to access the admin page set up by the Wooclientzone_Settings instance
		$this->loader->add_action('wooclientzone_admin_clientzone', $plugin_admin, 'admin_wooclientzone');

		// AJAX hooks
		$this->loader->add_action('wp_ajax_admin_submit_message', $plugin_admin, 'admin_submit_message');
		$this->loader->add_action('wp_ajax_admin_upload_files', $plugin_admin, 'admin_upload_files');
		$this->loader->add_action('wp_ajax_admin_load_communications', $plugin_admin, 'admin_load_communications');
		$this->loader->add_action('wp_ajax_admin_refresh_communications', $plugin_admin, 'admin_refresh_communications');

		$this->loader->add_action('wp_ajax_admin_notify_client', $plugin_admin, 'admin_notify_client');
		$this->loader->add_action('wp_ajax_admin_save_client_permissions', $plugin_admin, 'admin_save_client_permissions');
		$this->loader->add_action('wp_ajax_admin_move_clientzone', $plugin_admin, 'admin_move_clientzone');

		// Dashboard widget for notifications
		$this->loader->add_action('wp_dashboard_setup', $plugin_admin, 'admin_dashboard_widget_notifications');
		$this->loader->add_action('wp_ajax_admin_dashboard_notifications_get_content', $plugin_admin, 'admin_dashboard_notifications_get_content');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wooclientzone_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		/**
		 * Links previous orders to a new customer upon registration. Not really needed since we are requiring registration upfront.
		 * AS seen on https://www.skyverge.com/blog/automatically-link-woocommerce-orders-customer-registration/
		 *
		 * @param int $user_id the ID for the new user
		 */
		//$this->loader->add_action( 'woocommerce_created_customer', $plugin_public, 'sv_link_orders_at_registration' );

		// define action to create a link to the user client zone in the client's my-account dashboard area
		$this->loader->add_action('woocommerce_account_dashboard', $plugin_public, 'add_link_to_public_wooclientzone');

		// create link to client zone from order's page
		$this->loader->add_filter('woocommerce_my_account_my_orders_actions', $plugin_public, 'my_orders_table_add_clientzone_link', 30, 2);

		// create link to client zone from order details page
		$this->loader->add_action('woocommerce_order_details_after_order_table', $plugin_public, 'order_details_add_clientzone_link');

		// we use this action hook to move any communications in the user-based client zone to aclient zone related to the new successful order
		// this is only going to be hooked if both userzones and orderzones are enabled (order eligibility id checked within the callback function)
		$userzones_enabled = get_option('wooclientzone_use_userzones') == 'yes';
		$orderzones_enabled = get_option('wooclientzone_use_orderzones') != 'never';
		if (get_option('wooclientzone_automove_to_orderzone') == 'yes' && $userzones_enabled && $orderzones_enabled) {
			$this->loader->add_action('woocommerce_thankyou', $plugin_public, 'automove_to_orderzone');
		}

		// create new endpoint in my accounts page; a new menu entry is also created if user-based client zones are enabled
		if (get_option('wooclientzone_use_userzones') == 'yes') {
			$this->loader->add_filter('woocommerce_account_menu_items', $plugin_public, 'my_account_clientzone_endpoint_menu', 10, 1);
		}
		$this->loader->add_filter('the_title', $plugin_public, 'my_account_clientzone_endpoint_title');
		$this->loader->add_action('init', $plugin_public, 'my_account_clientzone_endpoint_rewrite');
		$this->loader->add_action('template_redirect', $plugin_public, 'my_account_clientzone_endpoint_hooks');
		$this->loader->add_action('wooclientzone_public_clientzone', $plugin_public, 'public_wooclientzone');

		// AJAX hooks for frontend site. We assume users are registered
		$this->loader->add_action('wp_ajax_public_submit_message', $plugin_public, 'public_submit_message');
		$this->loader->add_action('wp_ajax_public_upload_files', $plugin_public, 'public_upload_files');
		$this->loader->add_action('wp_ajax_public_load_communications', $plugin_public, 'public_load_communications');
		$this->loader->add_action('wp_ajax_public_refresh_communications', $plugin_public, 'public_refresh_communications');

		// My Account notifications
		$this->loader->add_action('wp_ajax_my_account_notifications_get_content', $plugin_public, 'my_account_notifications_get_content');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wooclientzone_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
