<?php

/**
 * A class of common tools of the plugin.
 *
 * @link       http://blendscapes.com
 * @since      1.0.0
 *
 * @package    Wooclientzone
 * @subpackage Wooclientzone/includes
 */

/**
 * The Tools class provides utility methods for the plugin.
 *
 * @package    Wooclientzone
 * @subpackage Wooclientzone/includes
 * @author     Enrico Sandoli <enrico.sandoli@blendscapes.com>
 */

class Wooclientzone_Tools {

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
	 * The logger private property. This is a reference to the new WC_Logger class in WC v3.0.0
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var		  object		$logger		The logger object (from WC v3.0.0).
	 */
	private $logger;

	/**
	 * The logger_old private property. This is a reference to the old WC_Logger class prior to WC v3.0.0
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var		  object		$logger		The logger object (before WC v3.0.0).
	 */
	private $logger_old;

	/**
	 * A hash to contain log levels names.
	 * 
	 * This is used to add log level names to the log entry when using the old logger class.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @var		  array		$log_level_name
	 */
	private $log_level_name;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		// wait for WooCommerce plugin to be loaded to initialise the logger
		add_action('plugins_loaded', array($this, 'setup_logger'));
		
		// setup hash for log levels
		$this->log_level_name = array(
			WOOCLIENTZONE_LOG_EMERGENCY => 'EMERGENCY',
			WOOCLIENTZONE_LOG_ALERT => 'ALERT',
			WOOCLIENTZONE_LOG_CRITICAL => 'CRITICAL',
			WOOCLIENTZONE_LOG_ERROR => 'ERROR',
			WOOCLIENTZONE_LOG_WARNING => 'WARNING',
			WOOCLIENTZONE_LOG_NOTICE => 'NOTICE',
			WOOCLIENTZONE_LOG_INFO => 'INFO',
			WOOCLIENTZONE_LOG_DEBUG => 'DEBUG',
		);
	}

	/**
	 * Utility for debugging: prints variables to a file.
	 *
	 * We can call this function with or without the $label, that is
	 * $label could be an array or an object (instead of the expected string)
	 * and this function would then assign it to the $data argument.
	 *
	 * @since    1.0.0
	 * @param    string         $label
	 * @param    object|array   $data
	 * @param    bool           $reset    If true the debug file is overriden
	 */
	public function debug($label, $data = '', $reset = false) {

		$debugfile = plugin_dir_path( dirname( __FILE__ ) ) . 'debug/debugfile.txt';
		if ($reset) {
			file_put_contents($debugfile, "");
		}
		else {
			file_put_contents($debugfile, "\n", FILE_APPEND);
		}
		if (is_array($label) || is_object($label)) {
			// we've called without label
			$data = $label;
			$label = '';
		}
		file_put_contents($debugfile, date("Y-m-d H-i-s", time()).'> '.$label.(($label && $data) ? ": " : ""), FILE_APPEND);
		if ($data && (is_array($data) || is_object($data))) {
			file_put_contents ($debugfile, print_r($data, true), FILE_APPEND);
		} else if ($data) {
			file_put_contents ($debugfile, $data, FILE_APPEND);
		}
	}

	/**
	 * Sets up the logger.
	 *
	 * We use the new wc_get_logger() function if WooCommerce from version 3.0.0
	 * is installed, otherwise we fall back to instantiating the logger using the actual class
	 */
	public function setup_logger() {
		
		// this is only available from version 3.0.0 of WooCommerce
		if(function_exists('wc_get_logger')) {
			$this->logger = wc_get_logger();
		}
		else {
			$this->logger = false;
			$this->logger_old = new WC_Logger();
			
		}
	}

	/**
	 * Our logging method.
	 *
	 * We use the logging functionality of WooCommerce from version 3.0.0
	 * (but we'll fall back to the old add() method if using an earlier version)
	 * 
	 * If using the new class, values for $level are:
	 * 
	 * Emergency:     system is unusable
	 * Alert:         action must be taken immediately
	 * Critical:      critical conditions
	 * Error:         error conditions
	 * Warning:       warning conditions
	 * Notice:        normal but significant condition
	 * Informational: informational messages
	 * Debug:         debug-level messages
	 *
	 * @since    1.0.0
	 * @param    string    $message
	 * @param    string    $level     default level is debug
	 */
	public function log($message, $level = WOOCLIENTZONE_LOG_DEBUG) {
		
		// check if we need to log this $message
		$log_debug = get_option('wooclientzone_logging_debug');
		$min_level = get_option('wooclientzone_logging_level');
		 
		// check if debug
		if ($level == WOOCLIENTZONE_LOG_DEBUG && $log_debug === 'no') {
			return;
		}
		// check for other levels (and we filter through debug messages, which if they get here they should go ahead)
		if ($level < $min_level && $level != WOOCLIENTZONE_LOG_DEBUG) {
			return;
		}
		
		// if the new logger was not instantiated, then we use the old one with the old
		// method add(); we also add the log level infront of the log entry and return
		if (!$this->logger) {
			$this->logger_old->add('wooclientzone', $this->log_level_name[$level]." ".$message);
			return;
		}
		
		// using the new logger function
		$context = array('source' => 'wooclientzone');
		
		switch($level) {
			case WOOCLIENTZONE_LOG_EMERGENCY:
				$this->logger->emergency($message, $context);
				break;
			case WOOCLIENTZONE_LOG_ALERT:
				$this->logger->alert($message, $context);
				break;
			case WOOCLIENTZONE_LOG_CRITICAL:
				$this->logger->critical($message, $context);
				break;
			case WOOCLIENTZONE_LOG_ERROR:
				$this->logger->error($message, $context);
				break;
			case WOOCLIENTZONE_LOG_WARNING:
				$this->logger->warning($message, $context);
				break;
			case WOOCLIENTZONE_LOG_NOTICE:
				$this->logger->notice($message, $context);
				break;
			case WOOCLIENTZONE_LOG_INFO:
				$this->logger->info($message, $context);
				break;
			case WOOCLIENTZONE_LOG_DEBUG:
			default:
				$this->logger->debug($message, $context);
				break;
		}
	}
	
	/**
	 * Checks if a certain order requires a client zone.
	 *
	 * This method checks if order zones are enabled, and if they are
	 * dependent on specific products, if these are present in the order.
	 *
	 * @since    1.0.0
	 * @param    object    $order
	 * @return   bool
	 */
	public function clientzone_enabled_for_order ($order) {

		//$this->debug($order);
		if (empty($order)) {
			return false;
		}

		// Note that in any case we don't do client zones on cancelled orders
		if ($order->has_status('cancelled')) {
			return false;
		}

		// we check global backend parameter and act accordingly
		$use_orderzones = get_option('wooclientzone_use_orderzones');
		switch ($use_orderzones) {
			case 'always':
				return true;
			case 'never':
				return false;
			default:
				// we only enable a client zone if one of the products included in the order have it specified
				$items = $order->get_items();
				foreach ($items as $item) {
					if ($this->clientzone_enabled_for_product($item)) {
						return true;
					}
				}
		}
		return false;
	}

	/**
	 * Checks if a certain product has the client zone enabled.
	 *
	 * @since    1.0.0
	 * @param    object    $product
	 * @return   bool
	 */
	public function clientzone_enabled_for_product($product) {
		// the product ID is the post_id in the wp_postmeta table
		return (get_post_meta($product['product_id'], '_wooclientzone_enabled', true) == 'yes');
	}

	/**
	 * Checks if the current user is the owner of a specified order.
	 *
	 * @since    1.0.0
	 * @param    int    $orderid
	 * @return   bool
	 */
	public function has_current_user_order_id($orderid) {

		$order = wc_get_order($orderid);
		return is_user_logged_in() && $order->get_user_id() === get_current_user_id();
	}

	/**
	 * Checks if an order belongs to a certain user.
	 * 
	 * It assumes the current user and can check for client-zones-orders only.
	 *
	 * @since    1.0.0
	 * @param    int    $orderid
	 * @param    int    $userid
	 * @param    bool   $order_has_clientzone
	 * @return   bool
	 */
	public function match_orderid_userid($orderid, $userid = false, $order_has_clientzone = true) {

		if (!$userid) {
			$userid = get_current_user_id();
		}
		if (empty($orderid)) {
			return false;
		}
		$order = wc_get_order($orderid);
		$match = $order->get_user_id() === $userid ? true : false;
		if ($order_has_clientzone) {
			if (!$this->clientzone_enabled_for_order($order)) {
				return false;
			}
		}
		return $match;
	}

	/**
	 * Gets all orders for a specified customer.
	 * 
	 * @since    1.0.0
	 * @param    int    $userid
	 * @param    bool   $return_ids
	 * @return   array
	 */
	public function get_orders_for_user_id($userid, $return_ids = false) {

		$orders = wc_get_orders(array('customer' => $userid));

		if (empty($orders)) {
			return false;
		}

		if (!$return_ids) {
			return $orders;
		}

		foreach($orders as $order) {
			// this is how we access order id (which should not be accessed directly as $order->id)
			$order_ids[] = trim(str_replace('#', '', $order->get_order_number()));
		}
		return $order_ids;
	}

	/**
	 * Gets get the public client zone url.
	 * 
	 * The argument could be a either a string, in which case we take it as
	 * the order ID, or an object, in which case we take it as the order object,
	 * or it could be boolean false if linking to a user-based client zone.
	 * 
	 * @since    1.0.0
	 * @param    string|object|bool    $order
	 * @return   string
	 */
	public function get_public_clientzone_nonced_url($order = false) {

		if (!$order) {
			$orderid = '';
		}
		else if (is_string($order)) {
			$orderid = $order;
		}
		else {
			// this is how we access order id (which should not be accessed directly as $order->id)
			$orderid = trim(str_replace('#', '', $order->get_order_number()));
		}

		// user-linked zones need not be nonced
		if (!$orderid) {
			$url = add_query_arg(array(
					'clientzone'	=> '',
				), get_permalink(get_page_by_path('my-account')));
			return esc_url($url);
		} else {
			$url = add_query_arg(array(
					'clientzone'	=> '',
					'orderid'	=> $orderid
				), get_permalink(get_page_by_path('my-account')));
			return esc_url(wp_nonce_url($url, 'wooclientzone-orderid='.$orderid, 'wooclientzone'));
		}
	}

	/**
	 * Creates a div element with an error message.
	 * 
	 * This is used in the admin and public client zone pages in place
	 * of the actual client zone code in case of a security error.
	 * 
	 * @since    1.0.0
	 * @param    string    $message
	 * @param    bool      $direct_echo
	 * @return   string
	 */
	public function error_message($message, $direct_echo = true) {
		$message_div = "<div id='errorMessage' style='display:block'>".$message."</div>";
		if (!$direct_echo) {
			return $message_div;
		}
		echo $message_div;
	}

	/**
	 * Creates a div element with a success message.
	 * 
	 * This is the mirror method of error_message, but it is currently unused.
	 * 
	 * @since    1.0.0
	 * @param    string    $message
	 * @param    bool      $direct_echo
	 * @return   string
	 */
	public function success_message($message, $direct_echo = true) {
		$message_div = "<div id='successMessage' style='display:block'>".$message."</div>";
		if (!$direct_echo) {
			return $message_div;
		}
		echo $message_div;
	}
}

