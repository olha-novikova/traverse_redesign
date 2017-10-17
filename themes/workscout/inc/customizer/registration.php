<?php 
// ----------- REGISTRATION OPTIONS ----------

Kirki::add_section( 'registration', array(
    'title'          => esc_html__( 'Login\Registration Options', 'workscout'  ),
    'description'    => esc_html__( 'User Registration options', 'workscout'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 25,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_login_form_status',
	    'label'       => esc_html__( 'Login/Sign Up buttons in header', 'workscout' ),
	    'section'     => 'registration',
	    'description' => esc_html__( 'Enable/disable Login/Sing Up buttons in header', 'workscout' ),
	   	'default'     => true,
	    'priority'    => 10,
	) );		
	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_user_page_status',
	    'label'       => esc_html__( 'User Page button in header', 'workscout' ),
	    'section'     => 'registration',
	    'description' => esc_html__( 'Enable/disable link to User Page in header', 'workscout' ),
	   	'default'     => true,
	    'priority'    => 10,
	) );	
	Kirki::add_field( 'workscout', array(
		'type'        => 'select',
		'settings'    => 'pp_login_form_system',
		'label'       => __( 'Choose the engine for front-end registartion', 'my_textdomain' ),
		'section'     => 'registration',
		'description' => esc_html__( 'Use WooCommerce, Ultimate Member, or you custom plugin', 'workscout' ),
		'priority'    => 10,
		'default'	  => 'workscout',
		'choices'     => array(
			'woocommerce' 	=> esc_html__( 'WooCommerce', 'workscout' ),
	        'um' 			=> esc_html__( 'Ultimate Member', 'workscout' ),
	        'custom'		=> esc_html__( 'Custom', 'workscout' ),
	        'workscout'		=> esc_html__( 'WorkScout', 'workscout' ),
	    ),
	) );
	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_woo_redirect_user_page_candidate',
	    'label'       => esc_html__( 'User Page to Candidate Dashboard redirect', 'workscout' ),
	    'section'     => 'registration',
	    'description' => esc_html__( 'If set to "On" the User Page button will link to Candidate Dashboard instead of My Account page (that applies only to candidates)', 'workscout' ),
	    'default'     => false,
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_login_form_system',
				'operator' => 'contains',
				'value'    => array('woocommerce','workscout'),
			),
		)
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_woo_redirect_user_page_employer',
	    'label'       => esc_html__( 'User Page to Employer Dashboard redirect', 'workscout' ),
	    'section'     => 'registration',
	    'description' => esc_html__( 'If set to "On" the User Page button will link to Employer Dashboard instead of My Account page  (that applies only to employers)', 'workscout' ),
	    'default'     => false,
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_login_form_system',
				'operator' => 'contains',
				'value'    => array('woocommerce','workscout'),
			),
		)
	) );
	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_login_form_type',
	    'label'       => esc_html__( 'Login/Sign Up form in popup', 'workscout' ),
	    'section'     => 'registration',
	    'description' => esc_html__( 'If set to "Off" login/sign up box will show up on separate page', 'workscout' ),
	    'default'     => true,
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_login_form_system',
				'operator' => 'contains',
				'value'    => array('woocommerce','custom','workscout'),
			),
		)
	) );
	Kirki::add_field( 'workscout', array(
	    'type'        => 'dropdown-pages',
	    'settings'    => 'pp_login_workscout_page',
	    'label'       => esc_html__( 'User/login form page', 'workscout' ),
	    'section'     => 'registration',
	    'description' => esc_html__( 'Choose page that uses "Page Template Login"', 'workscout' ),
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_login_form_system',
				'operator' => '==',
				'value'    => 'workscout',
			),
		)
	) );	
		
	
	Kirki::add_field( 'workscout', array(
	    'type'        => 'dropdown-pages',
	    'settings'    => 'pp_login_custom_login',
	    'label'       => esc_html__( 'Login form page', 'workscout' ),
	    'section'     => 'registration',
	    'description' => esc_html__( 'Choose page on which you have login form', 'workscout' ),
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_login_form_system',
				'operator' => '==',
				'value'    => 'custom',
			),
		)
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'dropdown-pages',
	    'settings'    => 'pp_login_custom_register',
	    'label'       => esc_html__( 'Register form page', 'workscout' ),
	    'section'     => 'registration',
	    'description' => esc_html__( 'Choose page on which you have registration form', 'workscout' ),
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_login_form_system',
				'operator' => '==',
				'value'    => 'custom',
			),
		)
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'dropdown-pages',
	    'settings'    => 'pp_login_custom_userpage',
	    'label'       => esc_html__( 'User page', 'workscout' ),
	    'section'     => 'registration',
	    'description' => esc_html__( 'This page will be linked under "User Page" button in header', 'workscout' ),
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_login_form_system',
				'operator' => '==',
				'value'    => 'custom',
			),
		)
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'text',
	    'settings'    => 'pp_login_box_shortcode',
	    'label'       => esc_html__( 'Shortcode for login form in popup', 'workscout' ),
	    'section'     => 'registration',
	    'description' => '',
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_login_form_system',
				'operator' => '==',
				'value'    => 'custom',
			),
			array(
				'setting'  => 'pp_login_form_type',
				'operator' => '==',
				'value'    => 1,
			),
		)
	) );	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'text',
	    'settings'    => 'pp_registration_box_shortcode',
	    'label'       => esc_html__( 'Shortcode for registartion form in popup', 'workscout' ),
	    'section'     => 'registration',
	    'description' => '',
	   	'default'     => true,
	    'priority'    => 10,
	    'active_callback'  => array(
			array(
				'setting'  => 'pp_login_form_system',
				'operator' => '==',
				'value'    => 'custom',
			),
			array(
				'setting'  => 'pp_login_form_type',
				'operator' => '==',
				'value'    => 1,
			),
		)
	) );	


	 ?>