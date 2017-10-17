<?php 



Kirki::add_section( 'resume_homepage', array(
    'title'          => esc_html__( 'Resume Home Page Options', 'workscout'  ),
    'description'    => esc_html__( 'Options for Page with Resume Search', 'workscout'  ),
    'panel'          => 'resumes_panel', // Not typically needed.
    'priority'       => 21,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'image',
	    'settings'     => 'pp_resumes_search_bg',
	    'label'       => esc_html__( 'Background for search banner on Resumes homepage', 'workscout' ),
	    'description' => esc_html__( 'Set image for search banner, should be 1920px wide', 'workscout' ),
	    'section'     => 'resume_homepage',
	    'default'     => '',
	    'priority'    => 10,
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_resume_home_transparent_header',
	    'label'       => esc_html__( 'Transparent header', 'workscout' ),
	    'section'     => 'resume_homepage',
	    'description' => esc_html__( 'Enabling transparent header works only on \'Page with Resumes Search\'', 'workscout' ),
	    'default'     => false,
	    'priority'    => 12,
	
	) );


	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_home_resume_counter',
	    'label'       => esc_html__( 'Show resumes counter', 'workscout' ),
	    'section'     => 'resume_homepage',
	    'description' => esc_html__( 'Disable to hide resumes counter', 'workscout' ),
	    'default'     => true,
	    'priority'    => 10,
	
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'dropdown-pages',
	    'settings'    => 'pp_resume_categories_page',
	    'label'       => esc_html__( 'Choose "Browse Resume Categories Page"', 'workscout' ),
	    'section'     => 'resume_homepage',
	    'description' => esc_html__( 'This page needs to use template named "Job/resumes Categories Page Template"', 'workscout' ),
	    'priority'    => 10,
	) );

 ?>