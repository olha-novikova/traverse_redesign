<?php
global $post;

$type     = get_the_job_type();
$location = get_the_job_location();
$company  = get_the_company_name();

echo "\n";

// Job type
if ( $type ) {
    echo esc_html( $type->name ) . ' - ';
}

// Job title
echo esc_html( $post->post_title ) . "\n";

// Location and company
if ( $location ) {
    printf( __( 'Location: %s', 'wp-job-manager-alerts' ) . "\n", esc_html( strip_tags( $location ) ) );
}
if ( $company ) {
    printf( __( 'Company: %s', 'wp-job-manager-alerts' ) . "\n", esc_html( strip_tags( $company ) ) );
}

// Permalink
printf( __( 'View Details: %s', 'wp-job-manager-alerts' ) . "\n", get_the_job_permalink() );
