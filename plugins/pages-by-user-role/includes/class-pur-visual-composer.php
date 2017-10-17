<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Dinah!' );
}

class PUR_Visual_Composer {
	public function __construct() {
		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
	}

	public function vc_before_init() {
		$vc_category = 'Pages by User Roles';

		vc_map( array(
			'name'        => __( 'Access Control', 'pur' ),
			'base'        => 'pur',
			'category'    => $vc_category,
			'description' => __( 'Set Access Control for Content', 'pur' ),
			'params' => array(
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => __( 'Capabilities', 'pur' ),
					'param_name'  => 'capability',
					'value'       => '',
					'description' => __( 'Render RSVP Box from a specific Event', 'rhcrsvp' )
				)
			)
		) );
	}
}

// return new PUR_Visual_Composer();