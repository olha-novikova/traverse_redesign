<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Dinah!' );
}

class PUR_Assets {
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
		}
	}

	public function assets() {
	}

	public function admin_assets() {
		wp_register_style( 'pur_options', PUR_URL . 'assets/css/options.css', array(), PUR_VERSION );
		
		wp_enqueue_style( 'pur_options' );
	}

	public function frontend_assets() {
	}
}

return new PUR_Assets();