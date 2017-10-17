<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = array(
	'job_manager_xing_api_key',
	'job_manager_xing_api_secret_key',
	'job_manager_apply_with_xing_cover_letter',
	'job_manager_allow_xing_applications_field'
);

foreach ( $options as $option ) {
	delete_option( $option );
}