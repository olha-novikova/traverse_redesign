<?php 
// ----------- FOOTER OPTIONS ----------

Kirki::add_section( 'footer', array(
    'title'          => esc_html__( 'Footer Options', 'workscout'  ),
    'description'    => esc_html__( 'Footer related options', 'workscout'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 160,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'textarea',
	    'settings'    => 'pp_copyrights',
	    'label'       => esc_html__( 'Copyrights text', 'workscout' ),
	    'default'     => '&copy; Theme by Purethemes.net. All Rights Reserved.',
	    'section'     => 'footer',
	    'priority'    => 10,
	) );

	Kirki::add_field( 'workscout', array(
    'type'        => 'select',
    'settings'    => 'pp_footer_widgets',
    'label'       => esc_html__( 'Footer widgets layout', 'workscout' ),
    'description' => esc_html__( 'Total width of footer is 16 columns, here you can decide layout based on columns number for each widget area in footer', 'workscout' ),
    'section'     => 'footer',
    'default'     => '5,3,3,5',
    'priority'    => 10,
    'choices'     => array(
        '7,3,6'		=> esc_html__( '7 | 3 | 6', 'workscout' ),
        '7,3,3,3' 	=> esc_html__( '7 | 3 | 3 | 3', 'workscout' ),
        '5,3,3,5' 	=> esc_html__( '5 | 3 | 3 | 5', 'workscout' ),
        '4,4,4,4' 	=> esc_html__( '4 | 4 | 4 | 4', 'workscout' ),
        '8,8' 		=> esc_html__( '8 | 8', 'workscout' ),
        '1/3,2/3' 	=> esc_html__( '1/3 | 2/3', 'workscout' ),
        '2/3,1/3' 	=> esc_html__( '2/3 | 1/3', 'workscout' ),
        '1/3,1/3,1/3' 	=> esc_html__( '1/3 | 1/3 | 1/3', 'workscout' ),
    ),
	) );
 ?>