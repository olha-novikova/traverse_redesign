<?php 
Kirki::add_section( 'shop', array(
    'title'          => esc_html__( 'WooCommerce Options', 'workscout'  ),
    'description'    => esc_html__( 'Shop related options', 'workscout'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 27,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	Kirki::add_field( 'workscout', array(
	    'type'        => 'radio-image',
	    'settings'     => 'pp_shop_layout',
	    'label'       => esc_html__( 'Shop layout', 'workscout' ),
	    'description' => esc_html__( 'Choose the sidebar side for shop', 'workscout' ),
	    'section'     => 'shop',
	    'default'     => 'full-width',
	    'priority'    => 10,
	    'choices'     => array(
	        'left-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/left-sidebar.png',
	        'right-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/right-sidebar.png',
	        'full-width' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/full-width.png',
	    ),
	) );
	

	Kirki::add_field( 'workscout', array(
	    'type'        => 'switch',
	    'settings'    => 'pp_shop_ordering',
	    'label'       => esc_html__( 'Show/hide results count and order select on shop page', 'workscout' ),
	    'section'     => 'shop',
	    'description' => esc_html__( 'With this setting set to On, results count and order select on shop page will be displayed', 'workscout' ),
	    'default'     => true,
	    'priority'    => 10,
	) );

 ?>