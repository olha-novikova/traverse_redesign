<?php

/**
 * Fired during plugin deactivation
 *
 * @link	   http://blendscapes.com
 * @since	  1.0.0
 *
 * @package	Wooclientzone
 * @subpackage Wooclientzone/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since	  1.0.0
 * @package	Wooclientzone
 * @subpackage Wooclientzone/includes
 * @author	 Enrico Sandoli <enrico.sandoli@blendscapes.com>
 */
class Wooclientzone_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since	1.0.0
	 */
	public static function deactivate() {

		$reset_on_activation = get_option('wooclientzone_reset_on_activation');

		if ($reset_on_activation == 'yes') {
		
			// delete all options
			delete_option('wooclientzone_root_folder', 'wooclientzone_repo');
			//delete_option('wooclientzone_root_is_relative', 'yes');

			delete_option('wooclientzone_use_userzones');
			delete_option('wooclientzone_automove_to_orderzone');
			delete_option('wooclientzone_use_orderzones');

			delete_option('wooclientzone_client_message_userzones');
			delete_option('wooclientzone_client_message_orderzones');
			delete_option('wooclientzone_client_upload_userzones');
			delete_option('wooclientzone_client_upload_orderzones');

			delete_option('wooclientzone_date_format');

			delete_option('wooclientzone_my_bubbles_position');
			delete_option('wooclientzone_bubbles_color_client_public');
			delete_option('wooclientzone_bubbles_color_merchant_public');
			delete_option('wooclientzone_bubbles_color_merchant_admin');
			delete_option('wooclientzone_bubbles_color_client_admin');

			delete_option('wooclientzone_accepted_files_public');
			delete_option('wooclientzone_max_filesize_public');
			delete_option('wooclientzone_accepted_files_admin');
			delete_option('wooclientzone_max_filesize_admin');

			delete_option('wooclientzone_progress_bar_color_public');
			delete_option('wooclientzone_progress_bar_color_admin');

			delete_option('wooclientzone_mail_to_client_subject');
			delete_option('wooclientzone_mail_to_client_user_clientzone');
			delete_option('wooclientzone_mail_to_client_order_clientzone');

			delete_option('wooclientzone_logging_level');
			delete_option('wooclientzone_logging_debug');

			delete_option('wooclientzone_refresh_rate');
			delete_option('wooclientzone_myaccount_menu_item_text');
			delete_option('wooclientzone_myaccount_menu_item_display_icon');
			delete_option('wooclientzone_reset_on_activation');
		}
	}

}
