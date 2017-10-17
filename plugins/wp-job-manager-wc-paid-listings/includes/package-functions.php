<?php
/**
 * Get a package
 * @param  object $package
 * @return WC_Paid_Listings_Package
 */
function wc_paid_listings_get_package( $package ) {
	return new WC_Paid_Listings_Package( $package );
}

/**
 * Approve a listing
 * @param  int $listing_id
 * @param  int $user_id
 * @param  int $user_package_id
 */
function wc_paid_listings_approve_listing_with_package( $listing_id, $user_id, $user_package_id ) {
	if ( wc_paid_listings_package_is_valid( $user_id, $user_package_id ) ) {
		$listing = array(
			'ID'            => $listing_id,
			'post_date'     => current_time( 'mysql' ),
			'post_date_gmt' => current_time( 'mysql', 1 )
		);

		switch ( get_post_type( $listing_id ) ) {
			case 'job_listing' :
				delete_post_meta( $listing_id, '_job_expires' );
				$listing['post_status'] = get_option( 'job_manager_submission_requires_approval' ) ? 'pending' : 'publish';
			break;
			case 'resume' :
				$listing['post_status'] = get_option( 'resume_manager_submission_requires_approval' ) ? 'pending' : 'publish';
			break;
		}

		// Do update
		wp_update_post( $listing );
		update_post_meta( $listing_id, '_user_package_id', $user_package_id );
		wc_paid_listings_increase_package_count( $user_id, $user_package_id );
	}
}

/**
 * Approve a job listing
 * @param  int $job_id
 * @param  int $user_id
 * @param  int $user_package_id
 */
function wc_paid_listings_approve_job_listing_with_package( $job_id, $user_id, $user_package_id ) {
	wc_paid_listings_approve_listing_with_package( $job_id, $user_id, $user_package_id );
}

/**
 * Approve a resume
 * @param  int $job_id
 * @param  int $user_id
 * @param  int $user_package_id
 */
function wc_paid_listings_approve_resume_with_package( $resume_id, $user_id, $user_package_id ) {
	wc_paid_listings_approve_listing_with_package( $resume_id, $user_id, $user_package_id );
}

/**
 * See if a package is valid for use
 * @return bool
 */
function wc_paid_listings_package_is_valid( $user_id, $package_id ) {
	global $wpdb;

	$package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE user_id = %d AND id = %d;", $user_id, $package_id ) );

	if ( ! $package ) {
		return false;
	}

	if ( $package->package_count >= $package->package_limit && $package->package_limit != 0 ) {
		return false;
	}

	return true;
}

/**
 * Increase job count for package
 * @param  int $user_id
 * @param  int $package_id
 * @return int affected rows
 */
function wc_paid_listings_increase_package_count( $user_id, $package_id ) {
	global $wpdb;

	$packages = wc_paid_listings_get_user_packages( $user_id );

	if ( isset( $packages[ $package_id ] ) ) {
		$new_count = $packages[ $package_id ]->package_count + 1;
	} else {
		$new_count = 1;
	}

	return $wpdb->update(
		"{$wpdb->prefix}wcpl_user_packages",
		array(
			'package_count' => $new_count,
		),
		array(
			'user_id' => $user_id,
			'id'      => $package_id
		),
		array( '%d' ),
		array( '%d', '%d' )
	);
}

/**
 * Decrease job count for package
 * @param  int $user_id
 * @param  int $package_id
 * @return int affected rows
 */
function wc_paid_listings_decrease_package_count( $user_id, $package_id ) {
	global $wpdb;

	$packages = wc_paid_listings_get_user_packages( $user_id );

	if ( isset( $packages[ $package_id ] ) ) {
		$new_count = $packages[ $package_id ]->package_count - 1;
	} else {
		$new_count = 0;
	}

	return $wpdb->update(
		"{$wpdb->prefix}wcpl_user_packages",
		array(
			'package_count' => max( 0, $new_count ),
		),
		array(
			'user_id' => $user_id,
			'id'      => $package_id
		),
		array( '%d' ),
		array( '%d', '%d' )
	);
}
