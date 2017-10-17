<?php 

// ----------- HEADER OPTIONS ----------

Kirki::add_section( 'header', array(
    'title'          => esc_html__( 'Header Options', 'workscout'  ),
    'description'    => esc_html__( 'Header related options', 'workscout'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );


	Kirki::add_field( 'workscout', array(
		    'type'        => 'select',
		    'settings'    => 'pp_header_style',
		    'label'       => esc_html__( 'Header style', 'workscout' ),
		    'section'     => 'header',
		    'description' => '',
		    'default'     => 'default',
		    'priority'    => 11,
		    'choices'     => array(
		        'default'		=> esc_html__( 'Default', 'workscout' ),
		        'alternative' 	=> esc_html__( 'Alternative', 'workscout' ),
		        'full-width' 	=> esc_html__( 'Full-width', 'workscout' ),
		    ),
		) );


	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_minicart_in_header',
	    'label'       => esc_html__( 'Mini shop cart in header', 'workscout' ),
	    'section'     => 'header',
	    'description' => esc_html__( 'Enable/disable mini shop cart in header', 'workscout' ),
	    'default'     => false,
	    'priority'    => 10,
	
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_sticky_header',
	    'label'       => esc_html__( 'Sticky header', 'workscout' ),
	    'section'     => 'header',
	    'description' => esc_html__( 'Enable/disable sticky header', 'workscout' ),
	    'default'     => false,
	    'priority'    => 12,
	
	) );

 ?>