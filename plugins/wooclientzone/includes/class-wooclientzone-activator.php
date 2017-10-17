<?php

/**
 * Fired during plugin activation
 *
 * @link	   http://blendscapes.com
 * @since	  1.0.0
 *
 * @package	Wooclientzone
 * @subpackage Wooclientzone/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since	  1.0.0
 * @package	Wooclientzone
 * @subpackage Wooclientzone/includes
 * @author	 Enrico Sandoli <enrico.sandoli@blendscapes.com>
 */
class Wooclientzone_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since	1.0.0
	 */
	public static function activate() {

		// flush the rewrite rules (to make the my account new endpoint work)
		flush_rewrite_rules();

		// insert new backend parameters

		add_option('wooclientzone_root_folder', 'wooclientzone_repo');
		//add_option('wooclientzone_root_is_relative', 'yes');

		add_option('wooclientzone_use_userzones', 'yes');
		add_option('wooclientzone_automove_to_orderzone', 'no');
		add_option('wooclientzone_use_orderzones', 'products');

		add_option('wooclientzone_client_message_userzones', 'yes');
		add_option('wooclientzone_client_message_orderzones', 'yes');
		add_option('wooclientzone_client_upload_userzones', 'yes');
		add_option('wooclientzone_client_upload_orderzones', 'yes');

		add_option('wooclientzone_date_format', 'j M Y, H:i');

		add_option('wooclientzone_my_bubbles_position', 'right');
		add_option('wooclientzone_bubbles_color_client_public', '#dcf7c8');
		add_option('wooclientzone_bubbles_color_merchant_public', '#e0e0e0');
		add_option('wooclientzone_bubbles_color_merchant_admin', '#dcf7c8');
		add_option('wooclientzone_bubbles_color_client_admin', '#f0f0f0');

		add_option('wooclientzone_accepted_files_public', 'image/*,application/pdf');
		add_option('wooclientzone_max_filesize_public', '8');
		add_option('wooclientzone_accepted_files_admin', 'image/*,application/pdf,.doc,.docx');
		add_option('wooclientzone_max_filesize_admin', '12');

		add_option('wooclientzone_progress_bar_color_public', '#55cce1');
		add_option('wooclientzone_progress_bar_color_admin', '#55cce1');

		add_option('wooclientzone_mail_to_client_subject', 'New communications for you');
		add_option('wooclientzone_mail_to_client_user_clientzone', __('Dear [client_name],

	This is to inform you that you have new communications from [site_name].
	To view them, please go to [my_account_link](My Account page).

	Kind regards,
	The team at [site_name]', 'wooclientzone'));
		add_option('wooclientzone_mail_to_client_order_clientzone', __('Dear [client_name],

	This is to inform you that you have new communications from [site_name] related to your order ID [order_id].
	To view them, please go to [my_account_link](My Account page).

	Kind regards,
	The team at [site_name]', 'wooclientzone'));

		add_option('wooclientzone_logging_level', WOOCLIENTZONE_LOG_WARNING);
		add_option('wooclientzone_logging_debug', 'no');
		
		add_option('wooclientzone_refresh_rate', '5000');
		add_option('wooclientzone_myaccount_menu_item_text', __('Communications', 'wooclientzone'));
		add_option('wooclientzone_myaccount_menu_item_display_icon', 'yes');
		add_option('wooclientzone_reset_on_activation', 'no');
		
		// this option would be set if the free plugin was ever installed (and settings saved) before upgrading to premium version
		delete_option('wooclientzone_upgrade_to_premium_version');
	}

}
