<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = array(
	'job_manager_facebook_app_id',
	'job_manager_apply_with_facebook_cover_letter',
	'job_manager_allow_facebook_applications_field'
);

foreach ( $options as $option ) {
	delete_option( $option );
}