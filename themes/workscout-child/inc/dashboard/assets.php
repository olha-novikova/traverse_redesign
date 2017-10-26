<?php

add_action( 'wp_enqueue_scripts', 'dashboard_enqueue_styles' );
function dashboard_enqueue_styles() {


	wp_enqueue_style( 'main-style', get_stylesheet_directory_uri() . '/css/dashboard/main.css', array() );
	wp_enqueue_style( 'vendor-style', get_stylesheet_directory_uri() . '/css/dashboard/vendor.css', array() );

	if (is_page_template(['influencer-dashboard.php', 'recent-opportunities.php', 'influencer-pitches.php'])) {
		wp_enqueue_style( 'influencer-dashboard-style', get_stylesheet_directory_uri() . '/css/dashboard/influencer-dashboard.css', array() );
	}

	if (is_singular('resume')) {
		wp_enqueue_style( 'influencer-single-grid', get_stylesheet_directory_uri() . '/css/images-grid.css', array() );
		wp_enqueue_style( 'influencer-single-style', get_stylesheet_directory_uri() . '/css/dashboard/influencer-about.css', array() );
		wp_enqueue_style( 'workscout-font-awesome');

	}

}

function dashboard_child_scripts(){
	wp_enqueue_script( 'dashboard-vendor', get_stylesheet_directory_uri() . '/js/vendor.min.js', array('jquery'), '20150705', true );
	wp_enqueue_script( 'dashboard-main', get_stylesheet_directory_uri() . '/js/main.min.js', array('jquery'), '20150705', true );
	wp_enqueue_script( 'dashboard-ajax', get_stylesheet_directory_uri() . '/js/influencer-ajax.js', array('jquery'), '20150705', true );

	if (is_singular('resume')) {
		wp_enqueue_script( 'single-grid-js', get_stylesheet_directory_uri() . '/js/images-grid.js', array('jquery'), '', true);
	}

}

add_action( 'wp_enqueue_scripts', 'dashboard_child_scripts' );