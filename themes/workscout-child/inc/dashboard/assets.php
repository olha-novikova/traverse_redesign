<?php



add_action( 'wp_enqueue_scripts', 'dashboard_enqueue_styles' );

function dashboard_enqueue_styles() {

    remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

    wp_dequeue_style( 'woocommerce_frontend_styles' );

    wp_dequeue_style( 'woocommerce-general');

    wp_dequeue_style( 'woocommerce-layout' );



	wp_enqueue_style( 'main-style', get_stylesheet_directory_uri() . '/css/dashboard/main.css', array() );

	wp_enqueue_style( 'vendor-style', get_stylesheet_directory_uri() . '/css/dashboard/vendor.css', array() );

//	wp_enqueue_style( 'general-style', get_stylesheet_directory_uri() . '/css/general.css', array() );

	if (is_page_template(['influencer-dashboard.php', 'recent-opportunities.php', 'influencer-pitches.php', 'template-new-balance.php'])) {

		wp_enqueue_style( 'influencer-dashboard-style', get_stylesheet_directory_uri() . '/css/dashboard/influencer-dashboard.css', array() );

	}



	if (is_singular('resume')) {

		wp_enqueue_style( 'influencer-single-grid', get_stylesheet_directory_uri() . '/css/images-grid.css', array() );

		wp_enqueue_style( 'influencer-single-style', get_stylesheet_directory_uri() . '/css/dashboard/influencer-about.css', array() );

		wp_enqueue_style( 'workscout-font-awesome');



	}



    if ( is_front_page() ){

        wp_dequeue_style('main-style');

        wp_enqueue_style('newhomepage-vendor', get_stylesheet_directory_uri().'/css/home/home-vendor.css');

        wp_enqueue_style('newhomepage-main', get_stylesheet_directory_uri().'/css/home/home-main.css', array('newhomepage-vendor') );

    }



    if (is_page_template('browse-influencers.php') ){

        wp_enqueue_style('brand-browse', get_stylesheet_directory_uri().'/css/brand-browse.css');

    }

    if ( is_page('create-lising') ||  ( ( isset ($_GET["action"]) && trim ($_GET["action"]) == 'edit' ) && (isset ($_GET["job_id"]) && trim ($_GET["action"]) != '') ) ){

        wp_enqueue_style('brand-create', get_stylesheet_directory_uri().'/css/dashboard/brand-create.css');

    }



    if (is_singular('job_listing')){

        wp_enqueue_style('brand-single', get_stylesheet_directory_uri().'/css/dashboard/completed-listing.css');

    }



    if( is_page('job-dashboard') && ( !isset ($_GET["action"]) || isset ($_GET["action"]) && trim ($_GET["action"]) != 'edit' ) ){

        wp_enqueue_style('brand-dashboard', get_stylesheet_directory_uri().'/css/dashboard/brand-dashboard.css');

    }



    if( is_page_template('my-listings.php')){
        wp_enqueue_style('brand-listing', get_stylesheet_directory_uri().'/css/dashboard/brand-listing-page.css');
        wp_enqueue_style('dialog-style', get_stylesheet_directory_uri().'/css/dashboard/dialog-style.css');
    }



    if ( is_checkout() ){

        wp_enqueue_style('brand-listing', get_stylesheet_directory_uri().'/css/checkout-page.css');

    }



    if ( is_account_page() ){

        wp_enqueue_style('brand-listing', get_stylesheet_directory_uri().'/css/dashboard/brand-account.css');

    }



}



function dashboard_child_scripts(){

	wp_enqueue_script( 'dashboard-vendor', get_stylesheet_directory_uri() . '/js/vendor.min.js', array('jquery'), '20150705', true );

	wp_enqueue_script( 'dashboard-main', get_stylesheet_directory_uri() . '/js/main.min.js', array('jquery'), '20150705', true );

	wp_enqueue_script( 'dashboard-ajax', get_stylesheet_directory_uri() . '/js/influencer-ajax.js', array('jquery'), '20150705', true );



	if (is_singular('resume')) {

		wp_enqueue_script( 'single-grid-js', get_stylesheet_directory_uri() . '/js/images-grid.js', array('jquery'), '', true);

	}



    if( is_page_template('my-listings.php')){

        wp_enqueue_script( 'tabs', get_stylesheet_directory_uri() . '/js/tabs.js', array('jquery'), '20150705', true );

    }



    if (is_singular('job_listing')){

        wp_enqueue_script( 'single-listing', get_stylesheet_directory_uri() . '/js/single-listing.js', array('jquery'), '20150705', true );

    }



    if ( is_account_page() ){

        wp_enqueue_script('sinc-social', get_stylesheet_directory_uri().'/js/sinc-social.js', array('jquery'), '', true);

    }

    if ( is_front_page() ){

        wp_dequeue_script('dashboard-main');

        wp_enqueue_script( 'home-carousel', get_stylesheet_directory_uri() . '/js/home/carousel.js', array('jquery'), '20150705', true );

        wp_enqueue_script( 'home-main', get_stylesheet_directory_uri() . '/js/home/main.js', array('jquery'), '20150705', true );

    }

}

add_action( 'wp_enqueue_scripts', 'dashboard_child_scripts' );

