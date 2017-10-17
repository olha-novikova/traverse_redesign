<?php 
Kirki::add_section( 'maps', array(
    'title'          => esc_html__( 'Maps Options', 'workscout'  ),
    'description'    => esc_html__( 'Maps related options', 'workscout'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 24,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );


	Kirki::add_field( 'workscout', array(
	    'type'        => 'text',
	    'settings'     => 'pp_maps_browser_api',
	    'label'       => esc_html__( 'Google Maps API Key', 'workscout' ),
	    'description' => __( 'Check <a href="http://purethemes.helpscoutdocs.com/article/73-google-maps-api-key-for-workscout">How to generate the API key</a> ', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => '',
	    'priority'    => 10,
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_enable_jobs_map',
	    'label'       => esc_html__( 'Enable map on Jobs page', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => 0,
	    'priority'    => 10,
	) );


	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_enable_all_jobs_map',
	    'label'       => esc_html__( 'Use all jobs map', 'workscout' ),
	    'description' => __( 'If enabled map will show ALL your jobs instead of the jobs currently filteres/searched for. ', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => 0,
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_enable_jobs_map',
				'operator' => '==',
				'value'    => 1,
			),
		)
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_enable_resumes_map',
	    'label'       => esc_html__( 'Enable map on Resumes page', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => 0,
	    'priority'    => 10,
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_enable_all_resumes_map',
	    'label'       => esc_html__( 'Use all resumes map', 'workscout' ),
	    'description' => __( 'If enabled map will show ALL your resumes instead of the resumes currently filteres/searched for. ', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => 0,
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_enable_resumes_map',
				'operator' => '==',
				'value'    => 1,
			),
		)
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_maps_geocode',
	    'label'       => esc_html__( 'Enabel address autosuggestion and radius search', 'workscout' ),
	    'description' => __( 'This might require using Premium Plan in Google Maps API as the daily quota for geocoding is 2,500 free requests per day. ', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => 0,
	    'priority'    => 10,
	) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'text',
	    'settings'     => 'pp_maps_default_radius',
	    'label'       => esc_html__( 'Default radius value used for search by location', 'workscout' ),
	    'description' => __( 'Leave this field empty to not use geocoding on default search, this will save your API requests number but jobs will be searched only by comparing text of location.', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => '',
	    'priority'    => 10,
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_miles_default_map',
	    'label'       => esc_html__( 'Set miles as default (instead of km)', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => 0,
	    'priority'    => 10,
	) );

	Kirki::add_field( 'workscout', array(
		'type'        => 'dimension',
		'settings'    => 'pp_map_height',
		'label'       => __( 'Map height', 'my_textdomain' ),
		'section'     => 'maps',
		'default'     => '400px',
		'priority'    => 10,
	) );


	Kirki::add_field( 'workscout', array(
	    'type'        => 'color',
	    'settings'     => 'pp_maps_marker_color',
	    'label'       => esc_html__( 'Marker color', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => '#808080',
	    'priority'    => 10,
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_maps_clusters',
	    'label'       => esc_html__( 'Group nearby markes in clusters', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => 1,
	    'priority'    => 10,
	) );		
	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_maps_autofit',
	    'label'       => esc_html__( 'Autofit all job markers on map', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => 1,
	    'priority'    => 10,
	) );
	Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'     => 'pp_maps_default_zoom',
	    'label'       => esc_html__( 'Default zoom level', 'workscout' ),
	    'section'     => 'maps',
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
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_maps_autofit',
				'operator' => '!=',
				'value'    => 1,
			),
		)
	) );
	Kirki::add_field( 'workscout', array(
	    'type'        => 'text',
	    'settings'    => 'pp_map_center',
	    'label'       => esc_html__( 'Custom Center point', 'workscout' ),
	    'description'     => esc_html__( 'Write latitude and longitude separated by come, for example -34.397,150.644', 'workscout' ),
	    'section'     => 'maps',
	    'priority'    => 10,
	    
	) );
	
	Kirki::add_field( 'workscout', array(
	    'type'        => 'select',
	    'settings'     => 'pp_maps_type',
	    'label'       => esc_html__( 'Map type', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => 'ROADMAP',
	    'choices'     => array(
			'ROADMAP' 	=> 'ROADMAP',
			'HYBRID' 	=> 'HYBRID',
			'SATELLITE' => 'SATELLITE',
			'TERRAIN' 	=> 'TERRAIN',
	    ),
	    'priority'    => 10,
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'     => 'pp_maps_scroll_zoom',
	    'label'       => esc_html__( 'Set zoom with scrollwheel', 'workscout' ),
	     'description' => __( 'Disabled by default as it might create problems witch scrolling page', 'workscout' ),
	    'section'     => 'maps',
	    'default'     => '0',
	    'priority'    => 10,
	) );
 ?>