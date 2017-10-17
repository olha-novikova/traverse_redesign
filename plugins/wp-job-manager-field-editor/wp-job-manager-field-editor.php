<?php
/**
 * Plugin Name: WP Job Manager Field Editor
 * Plugin URI:  https://plugins.smyl.es/wp-job-manager-field-editor
 * Description: Full ajax plugin to Disable, Create, or Modify all WP Job Manager Fields, with numerous other advanced features.
 * Version:     1.7.1
 * Author:      Myles McNamara
 * Author URI:  http://plugins.smyl.es
 * Requires at least: 4.1
 * Tested up to: 4.7.4
 * Domain Path: /languages
 * Text Domain: wp-job-manager-field-editor
 * Last Updated: Mon May 01 2017 14:31:24
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'sMyles_Bug_Report' ) ) require_once plugin_dir_path( __FILE__ ) . '/includes/smyles-bug-report/smyles-bug-report.php';

// General Field Functions
require_once plugin_dir_path( __FILE__ ) . '/includes/functions.php';

/**
 * Class WP_Job_Manager_Field_Editor
 *
 * @since 1.0.0
 */
Class WP_Job_Manager_Field_Editor {

	const PLUGIN_SLUG = 'wp-job-manager-field-editor';
	const PROD_ID = 'WP Job Manager Field Editor';
	const VERSION = '1.7.1';

	private static $instance;
	protected      $fields;
	protected      $cpt;
	protected      $admin;
	protected      $integration;
	protected      $options;
	protected      $field_types;
	protected      $plugin_slug;
	protected      $plugin_file;
	protected      $auto_output;

	function __construct() {

		$this->plugin_product_id = self::PROD_ID;
		$this->plugin_version    = self::VERSION;
		$this->plugin_slug       = self::PLUGIN_SLUG;
		$this->plugin_file       = basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );

		// PHP 5.2 Compatibility
		require_once plugin_dir_path( __FILE__ ) . '/includes/compatibility.php';

		add_action( 'plugins_loaded', array( $this, 'load_translations' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 4 );
		add_filter( 'cron_schedules', array( $this, 'add_weekly' ) );
		add_filter( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

		WP_Job_Manager_Field_Editor_Assets::get_instance();
		WP_Job_Manager_Field_Editor_Transients::first_init();

		$this->directory_check();

		register_activation_hook( __FILE__, array( $this, 'plugin_activated' ) );
		register_deactivation_hook( __FILE__, array($this, 'plugin_deactivated') );

		if ( ! defined( 'WPJM_FIELD_EDITOR_VERSION' ) ) define( 'WPJM_FIELD_EDITOR_VERSION', WP_JOB_MANAGER_FIELD_EDITOR::VERSION );
		if ( ! defined( 'WPJM_FIELD_EDITOR_PROD_ID' ) ) define( 'WPJM_FIELD_EDITOR_PROD_ID', WP_JOB_MANAGER_FIELD_EDITOR::PROD_ID );
		if ( ! defined( 'WPJM_FIELD_EDITOR_PLUGIN_DIR' ) ) define( 'WPJM_FIELD_EDITOR_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		if ( ! defined( 'WPJM_FIELD_EDITOR_PLUGIN_URL' ) ) define( 'WPJM_FIELD_EDITOR_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		if( ! class_exists( 'sMyles_Updater_v2' ) ) {
			require_once WPJM_FIELD_EDITOR_PLUGIN_DIR . '/includes/updater/smyles-updater.php';
		}
		new sMyles_Updater_v2( __FILE__, self::PROD_ID, self::VERSION );

		include WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/requires.php';
		include WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/translations.php';
		include WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/fields.php';
		include WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/shortcodes.php';
		include WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/widget.php';
		include WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/auto-output.php';

		if ( is_admin() ) {
			include WPJM_FIELD_EDITOR_PLUGIN_DIR . '/classes/admin.php';
		}

		if ( get_option( 'jmfe_enable_bug_reporter' ) ) sMyles_Bug_Report::get_instance();

		// Initialize required classes
		$this->cpt();
		$this->auto_output();
		$this->field_types();

	}

	/**
	 * Check Installed Plugin Directory
	 *
	 *
	 * @since 1.4.5
	 *
	 */
	function directory_check(){

		if( basename( __DIR__ ) != self::PLUGIN_SLUG ){
			$dir_check = get_option( 'jmfe_incorrect_directory_notice' );
			if( empty( $dir_check ) ){
				add_action( 'admin_init', array( $this, 'directory_dismiss' ) );
				add_action( 'admin_notices', array( $this, 'directory_notice' ) );
			}

		}

	}

	/**
	 * Output Incorrect Directory Notice
	 *
	 *
	 * @since 1.4.5
	 *
	 */
	function directory_notice(){
		echo "<div class=\"error\">";
		echo "<p><strong>" . sprintf( __( 'The activated copy of the %1$s plugin, is installed in the incorrect directory, the directory should match <code>%2$s</code>.', 'wp-job-manager-field-editor' ), self::PROD_ID, self::PLUGIN_SLUG ) . "</strong></p>";
		echo "<p>" . sprintf( __( 'Please rename the directory from: <code>%1$s</code> to <code>%2$s</code>', 'wp-job-manager-field-editor' ), basename( __DIR__ ), self::PLUGIN_SLUG ) . "</p>";
		echo "<p>" . sprintf( __( 'You can find the directory located at: <br/> <code>%1$s</code>', 'wp-job-manager-field-editor' ), plugin_dir_path( __FILE__ ) ) . "</p>";
		echo "<p>" . __( 'Once you have renamed the directory, come back to the Plugins page to reactivate the plugin.  You will not lose any of your settings or configurations.', 'wp-job-manager-field-editor' ) . "</p>";
		echo "<p>" . sprintf( __( 'You can also just <a href="%1$s">hide this notice</a>, but be warned, there may be issues due to using the incorrect plugin directory!', 'wp-job-manager-field-editor' ), '?jmfe_incorrect_directory_notice=0' ) . "</p>";
		echo "</p></div>";
	}

	/**
	 * Dismiss Directory Notice
	 *
	 *
	 * @since 1.4.5
	 *
	 */
	function directory_dismiss(){
		if( isset($_GET['jmfe_incorrect_directory_notice']) && '0' == $_GET['jmfe_incorrect_directory_notice'] ) {
			add_option( 'jmfe_incorrect_directory_notice', 'true' );
		}
	}

	/**
	 * Fired after plugins are loaded
	 *
	 *
	 * @since 1.4.0
	 *
	 */
	function plugins_loaded(){

		if( get_option( 'jmfe_enable_pmi' ) ) {
			include self::plugin_path() . '/includes/pmi.php';
		}
	}

	/**
	 * Set option when plugin gets activated
	 *
	 *
	 * @since 1.1.10
	 *
	 */
	function plugin_activated(){

		add_option( 'wp_job_manager_field_editor_activated', 'true' );
		wp_schedule_event( time() + 60, 'weekly', 'job_manager_verify_no_errors' );
	}

	/**
	 * Ran when plugin is deactivated to clear cache
	 *
	 *
	 * @since 1.3.5
	 *
	 */
	function plugin_deactivated() {

		wp_clear_scheduled_hook( 'job_manager_verify_no_errors' );
	}

	/**
	 * Load Plugin Translations from Languages Directory
	 *
	 * @since 1.1.8
	 *
	 */
	function load_translations() {

		load_plugin_textdomain( 'wp-job-manager-field-editor', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * WP_Job_Manager_Field_Editor_CPT Class Object
	 *
	 * @since 1.1.8
	 *
	 * @return WP_Job_Manager_Field_Editor_CPT
	 */
	public function cpt() {

		if ( ! class_exists( 'WP_Job_Manager_Field_Editor_CPT' ) ) include self::plugin_path() . '/classes/admin/cpt.php';
		if ( ! $this->cpt ) $this->cpt = WP_Job_Manager_Field_Editor_CPT::get_instance();

		return $this->cpt;
	}

	/**
	 * WP_Job_Manager_Field_Editor_Admin Class Object
	 *
	 * @since 1.1.9
	 *
	 * @return WP_Job_Manager_Field_Editor_Admin
	 */
	public function admin() {

		if( ! is_admin() ) return false;

		if ( ! class_exists( 'WP_Job_Manager_Field_Editor_Admin' ) ) include self::plugin_path() . '/classes/admin.php';
		if ( ! $this->admin ) $this->admin = WP_Job_Manager_Field_Editor_Admin::get_instance();

		return $this->admin;
	}

	/**
	 * WP_Job_Manager_Field_Editor_Field_Types Class Object
	 *
	 * @since 1.1.8
	 *
	 * @return WP_Job_Manager_Field_Editor_Field_Types
	 */
	public function field_types() {

		if ( ! class_exists( 'WP_Job_Manager_Field_Editor_Field_Types' ) ) include self::plugin_path() . '/classes/field-types.php';
		if ( ! $this->field_types ) $this->field_types = WP_Job_Manager_Field_Editor_Field_Types::get_instance();

		return $this->field_types;
	}

	/**
	 * WP_Job_Manager_Field_Editor_Auto_Output Class Object
	 *
	 * @since 1.1.8
	 *
	 * @return WP_Job_Manager_Field_Editor_Auto_Output
	 */
	public function auto_output() {

		if ( ! class_exists( 'WP_Job_Manager_Field_Editor_Auto_Output' ) ) include self::plugin_path() . '/classes/auto-output.php';
		if ( ! $this->auto_output ) $this->auto_output = WP_Job_Manager_Field_Editor_Auto_Output::get_instance();

		return $this->auto_output;
	}

	/**
	 * WP_Job_Manager_Field_Editor_Integration Class Object
	 *
	 * @since 1.1.8
	 *
	 * @return WP_Job_Manager_Field_Editor_Integration
	 */
	public function integration() {

		if ( ! class_exists( 'WP_Job_Manager_Field_Editor_Integration' ) ) include self::plugin_path() . '/classes/integration.php';
		if ( ! $this->integration ) $this->integration = WP_Job_Manager_Field_Editor_Integration::get_instance();

		return $this->integration;
	}

	/**
	 * WP_Job_Manager_Field_Editor_List_Table Class Object
	 *
	 * @since 1.1.8
	 *
	 * @param string|array $field_type  Field type to generate list table for
	 * @param string       $post_type   Post type the field is used on
	 * @param string       $table_title Specify a custom title to use in list table
	 *
	 * @return WP_Job_Manager_Field_Editor_List_Table
	 */
	function list_table( $field_type, $post_type, $table_title = NULL ) {

		if ( ! class_exists( 'WP_Job_Manager_Field_Editor_List_Table' ) ) include self::plugin_path() . '/classes/admin/list-table.php';
		return new WP_Job_Manager_Field_Editor_List_Table( $field_type, $post_type, $table_title );
	}

	/**
	 * WP_Job_Manager_Field_Editor_Fields_Options Class Object
	 *
	 * @since 1.1.8
	 *
	 * @return WP_Job_Manager_Field_Editor_Fields_Options
	 */
	function options() {

		if ( ! class_exists( 'WP_Job_Manager_Field_Editor_Fields_Options' ) ) include self::plugin_path() . '/classes/fields/options.php';
		if ( ! $this->options ) $this->options = new WP_Job_Manager_Field_Editor_Fields_Options();

		return $this->options;
	}

	/**
	 * Add a weekly option to cron jobs
	 *
	 *
	 * @since 1.3.5
	 *
	 * @param $schedules
	 *
	 * @return mixed
	 */
	function add_weekly( $schedules ){

		// add a 'weekly' schedule to the existing set
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'wp-job-manager-field-editor' )
		);

		return $schedules;
	}

	/**
	 * Singleton Instance
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Job_Manager_Field_Editor
	 */
	static function get_instance() {

		if ( NULL == self::$instance ) self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Add Plugin Row Links on WordPress Plugin Page
	 *
	 * @since 1.1.8
	 *
	 * @param $plugin_meta
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $status
	 *
	 * @return array
	 */
	public function add_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

		if ( $this->plugin_slug . '/' . $this->plugin_slug . '.php' == $plugin_file ) {
//			$plugin_meta[ ] = sprintf( '<a href="%s" target="_blank">%s</a>', "https://github.com/tripflex/{$this->plugin_slug}", __( 'GitHub' ) );
//			$plugin_meta[ ] = sprintf( '<a href="%s" target="_blank">%s</a>', "http://wordpress.org/plugins/{$this->plugin_slug}", __( 'WordPress' ) );
			$plugin_meta[ ] = sprintf( '<a href="%s" target="_blank">%s</a>', "https://www.transifex.com/projects/p/{$this->plugin_slug}", __( 'Translations', 'wp-job-manager-field-editor' ) );
		}

		return $plugin_meta;
	}

	/**
	 * Check for WP Resume Manager Files
	 *
	 *
	 * @since 1.1.9
	 *
	 * @return bool
	 */
	function wprm_active() {

		$wprm = 'wp-job-manager-resumes/wp-job-manager-resumes.php';

		if ( ! file_exists( plugin_dir_path( __FILE__ ) . '/classes/resume/' ) ) return false;

		if ( ! defined( 'RESUME_MANAGER_PLUGIN_DIR' ) ){

			if ( ! function_exists( 'is_plugin_active' ) ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( $wprm ) ) return true;
			if ( class_exists( 'WP_Job_Manager_Resumes' ) ) return true;

			return false;
		}


		return true;

	}

	/**
	 * Check if Resume Manager (installed and activated) static method
	 *
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	static function resumes_active(){

		$wprm = 'wp-job-manager-resumes/wp-job-manager-resumes.php';

		if ( ! file_exists( plugin_dir_path( __FILE__ ) . '/classes/resume/' ) ) return FALSE;

		if ( ! defined( 'RESUME_MANAGER_PLUGIN_DIR' ) ) {

			if ( ! function_exists( 'is_plugin_active' ) ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( $wprm ) ) return TRUE;
			if ( class_exists( 'WP_Job_Manager_Resumes' ) ) return TRUE;

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * sMyles Debug Logging
	 *
	 * Custom debug logging to error_log (normally debug.log), including a few backtrace
	 * vars, the WP page, query arguments, and optional formatting.
	 *
	 * @since 1.1.8
	 *
	 * @param mixed  $message           Message, Object, or Array to output in log
	 * @param string $title             Optional title to include in log output
	 * @param bool   $spacer            Whether or not to include a spacer
	 * @param bool   $show_page         Should the page name be included in logging
	 * @param bool   $show_query_string Should any query strings be logged
	 */
	static function log( $message, $title = NULL, $spacer = TRUE, $show_page = FALSE, $show_query_string = FALSE ) {

		global $pagenow;
		$backtrace       = debug_backtrace();
		$caller_function = $backtrace[ 2 ][ 'function' ];
		$caller_line     = $backtrace[ 2 ][ 'line' ];
		$caller_file     = $backtrace[ 2 ][ 'file' ];

		if ( ! $pagenow ) {
			$filename = $_SERVER[ 'SCRIPT_FILENAME' ];
		} else {
			$filename = $pagenow;
		}

		if ( WP_DEBUG === TRUE ) {
			$query        = $_SERVER[ 'QUERY_STRING' ];
			$date_ident   = date( 's' );
			$date_title   = $date_ident . ' - ';
			$plugin_title = '[ jmfe ][ ' . $caller_function . ' ]:' . $caller_line . ' - ';

			if ( $title ) {
				$title = '{' . strtolower( $title ) . '}> ';
			}
			if ( $spacer ) {
				error_log( $plugin_title . '   ---   ' . $date_ident . '   ---   ' );
			}
			if ( $filename && $show_page ) {
				error_log( $plugin_title . ':: {file/page}> ' . $filename );
			}
			if ( $caller_file && $show_page ) {
				error_log( $plugin_title . ':: {caller_file}> ' . $caller_file );
			}
			if ( $query && $show_query_string ) {
				error_log( $plugin_title . ':: {args}> ' . $query );
			}

			if ( is_array( $message ) || is_object( $message ) ) {
				error_log( $plugin_title . $title . 'Array/Object:' );
				error_log( print_r( $message, TRUE ) );
			} else {
				error_log( $plugin_title . $title . strtolower( $message ) );
			}
		}
	}

	/**
	 * Get Job Listing Post Type Label
	 *
	 *
	 * @since 1.3.5
	 *
	 * @return string|void
	 */
	public static function get_job_post_label(){

		$job_obj      = get_post_type_object( 'job_listing' );
		$job_singular = is_object( $job_obj ) ? $job_obj->labels->singular_name : __( 'Job', 'wp-job-manager-field-editor' );

		if( ! $job_singular ) $job_singular = __( 'Job', 'wp-job-manager-field-editor' );

		return $job_singular;

	}

	public static function autoload( $class ) {
		// Exit autoload if being called by a class other than ours
		if ( FALSE === strpos( $class, 'WP_Job_Manager_Field_Editor' ) ) return;

		$class_file = str_replace( 'WP_Job_Manager_Field_Editor_', '', $class );
		$file_array = array_map( 'strtolower', explode( '_', $class_file ) );

		$dirs = 0;
		$file = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/classes/';

		while ( $dirs ++ < count( $file_array ) ) {
			$file .= '/' . $file_array[ $dirs - 1 ];
		}

		$file .= '.php';

		if ( ! file_exists( $file ) || $class === 'WP_Job_Manager_Field_Editor' ) {
			return;
		}

		include $file;

	}

	/**
	 * Return Plugin Path
	 *
	 *
	 * @since 1.7.0
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public static function plugin_path( $file = '' ){

		if( defined( 'WPJM_FIELD_EDITOR_PLUGIN_DIR' ) ){
			return WPJM_FIELD_EDITOR_PLUGIN_DIR . $file;
		} else {
			return untrailingslashit( plugin_dir_path( __FILE__ ) ) . $file;
		}

	}
}

spl_autoload_register( array( 'WP_Job_Manager_Field_Editor', 'autoload' ) );

WP_Job_Manager_Field_Editor::get_instance();
