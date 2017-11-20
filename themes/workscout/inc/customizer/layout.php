<?php 

Kirki::add_section( 'layout', array(
    'title'          => esc_html__( 'Layout Options', 'workscout'  ),
    'description'    => esc_html__( 'Layout and header options', 'workscout'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 29,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'    => 'pp_body_style',
	    'label'       => esc_html__( 'Layout style', 'workscout' ),
	    'section'     => 'layout',
	    'description' => '',
	    'default'     => 'fullwidth',
	    'priority'    => 10,
	    'choices'     => array(
	        'boxed'		=> esc_html__( 'Boxed', 'workscout' ),
	        'fullwidth' => esc_html__( 'Full-width', 'workscout' ),
	    ),
	) ); ?>