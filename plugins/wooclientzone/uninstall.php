<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://blendscapes.com
 * @since      1.0.0
 *
 * @package    Wooclientzone
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

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

