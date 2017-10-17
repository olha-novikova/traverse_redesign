<?php 

Kirki::add_section( 'homepage', array(
    'title'          => esc_html__( 'Jobs Home Page Options', 'workscout'  ),
    'description'    => esc_html__( 'Options for Page with Job Search', 'workscout'  ),
    'panel'          => 'jobs_panel', // Not typically needed.
    'priority'       => 21,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'image',
	    'settings'     => 'pp_jobs_search_bg',
	    'label'       => esc_html__( 'Background for search banner on homepage', 'workscout' ),
	    'description' => esc_html__( 'Set image for search banner, should be 1920px wide', 'workscout' ),
	    'section'     => 'homepage',
	    'default'     => '',
	    'priority'    => 10,
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_transparent_header',
	    'label'       => esc_html__( 'Transparent header', 'workscout' ),
	    'section'     => 'homepage',
	    'description' => esc_html__( 'Enabling transparent header works only on \'Page with Jobs Search\'', 'workscout' ),
	    'default'     => false,
	    'priority'    => 12,
	
	) );

	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_home_job_counter',
	    'label'       => esc_html__( 'Show job counter', 'workscout' ),
	    'section'     => 'homepage',
	    'description' => esc_html__( 'Disable to hide jobs counter', 'workscout' ),
	    'default'     => true,
	    'priority'    => 10,
	
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'dropdown-pages',
	    'settings'    => 'pp_categories_page',
	    'label'       => esc_html__( 'Choose "Browse Categories Page"', 'workscout' ),
	    'section'     => 'homepage',
	    'description' => esc_html__( 'This page needs to use template named "Job Categories Page Template"', 'workscout' ),
	    'priority'    => 10,
	) );

 ?>