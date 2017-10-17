<?php 


Kirki::add_panel( 'jobs_panel', array(
    'priority'    => 21,
    'title'       => __( 'Jobs Options', 'sphene' ),
    'description' => __( 'Job related options', 'sphene' ),
) );

require get_template_directory() . '/inc/customizer/jobs_home.php';

Kirki::add_section( 'jobs', array(
    'title'          => esc_html__( 'Jobs General Options', 'workscout'  ),
    'description'    => esc_html__( 'Job related options', 'workscout'  ),
    'panel'          => 'jobs_panel', // Not typically needed.
    'priority'       => 22,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'upload',
	    'settings'     => 'pp_jobs_header_upload',
	    'label'       => esc_html__( 'Jobs header image', 'workscout' ),
	    'description' => esc_html__( 'Used on Job archive page. Set image for header, should be 1920px wide', 'workscout' ),
	    'section'     => 'jobs',
	    'default'     => '',
	    'priority'    => 10,
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'    => 'pp_call_to_action_jobs',
	    'label'       => esc_html__( 'Call to action button in header', 'workscout' ),
	    'section'     => 'jobs',
	    'description' => '',
	    'default'     => 'jobs',
	    'priority'    => 10,
	    'choices'     => array(
	        'job'		=> __( 'Post a Job! It\'s Free!', 'workscout' ),
	        'resume'	=> __( 'Post a Resume! It\'s Free!', 'workscout' ),
	        'nothing' 	=> esc_html__( 'Show nothing', 'workscout' ),
	    ),
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'radio-image',
	    'settings'     => 'pp_job_layout',
	    'label'       => esc_html__( 'Single Job layout', 'workscout' ),
	    'description' => esc_html__( 'Choose the sidebar side for single job', 'workscout' ),
	    'section'     => 'jobs',
	    'default'     => 'right-sidebar',
	    'priority'    => 10,
	    'choices'     => array(
	        'left-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/left-sidebar.png',
	        'right-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/right-sidebar.png',
	    ),
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'multicheck',
	    'settings'     => 'pp_job_share',
	    'label'       => esc_html__( 'Share buttons on single job', 'workscout' ),
	    'description' => esc_html__( 'Set which share buttons you want to display on single job post', 'workscout' ),
	    'section'     => 'jobs',
	    'default'     => array(),
	    'priority'    => 10,
	    'choices'     => array(
	        'facebook' 	=> esc_html__( 'Facebook', 'workscout' ),
	        'twitter' 		=> esc_html__( 'Twitter', 'workscout' ),
	        'google-plus' 		=> esc_html__( 'Google Plus', 'workscout' ),
	        'pinterest' 		=> esc_html__( 'Pinterest', 'workscout' ),
	        'linkedin' 		=> esc_html__( 'LinkedIn', 'workscout' ),
	    ),
	) );

	Kirki::add_field( 'workscout', array(
		'type'        => 'select',
		'settings'    => 'pp_job_list_logo_position',
		'label'       => __( 'Logo position on jobs list', 'workscout' ),
		'section'     => 'jobs',
		'description' => esc_html__( 'If you don\'t like cropped out logos, move them to the right!', 'workscout' ),
		'priority'    => 10,
		'default'	  => 'left',
		'choices'     => array(
			'left' 	=> esc_html__( 'Left', 'workscout' ),
	        'right' => esc_html__( 'Right', 'workscout' ),
	    ),
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_enable_related_jobs',
	    'label'       => esc_html__( 'Enable related Jobs on Single Job', 'workscout' ),
	    'section'     => 'jobs',
	    'default'     => 0,
	    'priority'    => 10,
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_enable_single_jobs_map',
	    'label'       => esc_html__( 'Enable map on Single Job', 'workscout' ),
	    'section'     => 'jobs',
	    'default'     => 0,
	    'priority'    => 12,
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'     => 'pp_maps_single_zoom',
	    'label'       => esc_html__( 'Default Map zoom level', 'workscout' ),
	    'section'     => 'jobs',
	    'default'     => '10',
	    'choices'     => array(
			'1' 	=> '1',
			'2' 	=> '2',
			'3' 	=> '3',
			'4' 	=> '4',
			'5' 	=> '5',
			'6' 	=> '6',
			'7' 	=> '7',
			'8' 	=> '8',
			'9' 	=> '9',
			'10' 	=> '10',
			'11' 	=> '11',
			'12' 	=> '12',
			'13' 	=> '13',
			'14' 	=> '14',
			'15' 	=> '15',
			'16' 	=> '16',
			'17' 	=> '17',
			'18' 	=> '18',
	    ),
	    'priority'    => 13,
	   
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'    => 'pp_jobs_order',
	    'label'       => esc_html__( 'Jobs Archive order', 'workscout' ),
	    'section'     => 'jobs',
	    'description' => '',
	    'default'     => 'DESC',
	    'priority'    => 10,
	    'choices'     => array(
	    	'ASC' => 'ascending order from lowest to highest values (1, 2, 3; a, b, c).',
			'DESC' => 'descending order from highest to lowest values (3, 2, 1; c, b, a).',
	    ),
	) );
	Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'    => 'pp_jobs_orderby',
	    'label'       => esc_html__( 'Jobs Archive orderby', 'workscout' ),
	    'section'     => 'jobs',
	    'description' => '',
	    'default'     => 'title',
	    'priority'    => 10,
	    'choices'     => array(
	    	'none'  => 'No order.',
			'ID'  => 'Order by post id. ',
			'author'  => 'Order by author.',
			'title'  => 'Order by title.',
			'name'  => 'Order by post name (post slug).',
			'date'  => 'Order by date.',
			'modified'  => 'Order by last modified date.',
			'rand'  => 'Random order.',
	    ),
	) );

	Kirki::add_field( 'workscout', array(
		'type'        => 'number',
		'settings'    => 'pp_jobs_per_page',
		'label'       => esc_attr__( 'Jobs Archive number of listings', 'workscout' ),
		'section'     => 'jobs',
		'default'     => 10,
		'choices'     => array(
			'min'  => 1,
			'max'  => 50,
			'step' => 1,
		),
	) );

 ?>