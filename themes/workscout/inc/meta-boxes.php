<?php 

/**
 * Initialize the meta boxes.
 */
add_action( 'admin_init', 'workscout_custom_meta_boxes' );

function workscout_custom_meta_boxes() {
  
  $testimonials = array(
    'id'        => 'pp_metabox_testimonials',
    'title'     => esc_html__('Testimonials info','workscout'),
    'desc'      => esc_html__('Fill field below to use testimonials in slider','workscout'),
    'pages'     => array( 'testimonial' ),
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
        array(
          'id'          => 'pp_author',
          'label'       => esc_html__('Author of testimonial','workscout'),
          'desc'        => esc_html__('If empty, the title of testimony will be used','workscout'),
          'std'         => '',
          'type'        => 'text',
          'class'       => '',
        ),
        array(
          'id'          => 'pp_link',
          'label'       => esc_html__('Link to author\'s website (optional)','workscout'),
          'desc'        => '',
          'std'         => '',
          'type'        => 'text',
          'class'       => '',
        ),
        array(
          'id'          => 'pp_position',
          'label'       => esc_html__('Enter their position in their specific company.','workscout'),
          'desc'        => '',
          'std'         => '',
          'type'        => 'text',
          'class'       => '',
        )
      )
  );
  ot_register_meta_box( $testimonials );


  $job = array(
    'id'        => 'pp_job_settings',
    'title'     => esc_html__('Background image for header','workscout'),
    'desc'      => '',
    'pages'     => array( 'job_listing','page','post' ),
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
      array(
        'id'          => 'pp_job_header_bg',
        'label'       => esc_html__('Job header background ','workscout'),
        'desc'        => esc_html__('Set image for header, should be 1920px wide.','workscout'),
        'std'         => '',
        'type'        => 'upload',
        'class'       => '',
        ),
      )
  );
  ot_register_meta_box( $job );  


  $job = array(
    'id'        => 'pp_job_locations',
    'title'     => esc_html__('Location settings','workscout'),
    'desc'      => '',
    'pages'     => array( 'job_listing','resume' ),
    'context'   => 'normal',
    'priority'  => 'low',
    'fields'    => array(
      array(
        'id'          => 'geolocation_lat',
        'label'       => esc_html__('Latitude','workscout'),
        'std'         => '',
        'type'        => 'text',
        'class'       => '',
        ),      
      array(
        'id'          => 'geolocation_long',
        'label'       => esc_html__('Longitude ','workscout'),
        'std'         => '',
        'type'        => 'text',
        'class'       => '',
        ),
      )
  );
  ot_register_meta_box( $job );



  $revos = array();
  global $wpdb;
  // Table name
  $table_name = $wpdb->prefix . "revslider_sliders";
  // Get sliders
  if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    $sliders = $wpdb->get_results( "SELECT alias, title FROM $table_name" );
  } else {
    $sliders = '';
  }

  // Iterate over the sliders
  if($sliders) {
    foreach($sliders as $key => $item) {
      $revos[] = array(
        'label' => $item->title,
        'value' => $item->alias
        );
    }
  } else {
    $revos[] = array(
      'label' => esc_html__('No Sliders Found','workscout'),
      'value' => ''
      );
  }


  $slider = array(
    'id'        => 'pp_metabox_slider',
    'title'     => esc_html__('Slider settings','workscout'),
    'desc'      => esc_html__('Enable option below to dispay Revolution Slider on this page (the page template "Revolution Page" is now deprecated).','workscout'),
    'pages'     => array( 'page' ),
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
       array(
        'id'          => 'pp_page_slider_status',
        'label'       => __( 'Display Revolution Slider on this page', 'workscout' ),
        'std'         => 'off',
        'type'        => 'on-off',
        'condition'   => '',
 
      ),
      array(
        'id'          => 'pp_page_layer',
        'label'       => esc_html__('Revolution Slider','workscout'),
        'desc'        => '',
        'std'         => '',
        'condition'   => 'pp_page_slider_status:is(on)',
        'type'        => 'select',
        'choices'     => $revos,
        'class'       => '',
        ),
      ),
  );
  ot_register_meta_box( $slider );

  $post_layout = array(
    'id'        => 'pp_metabox_sidebar',
    'title'     => esc_html__('Layout','workscout'),
    'desc'      => esc_html__('You can choose a sidebar from the list below. Sidebars can be created in the Theme Options and configured in the Appearance -> Widgets.','workscout'),
    'pages'     => array( 'post' ),
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    =>   array(
      array(
        'id'          => 'pp_sidebar_layout',
        'label'       => esc_html__('Layout','workscout'),
        'desc'        => '',
        'std'         => 'right-sidebar',
        'type'        => 'radio_image',
        'class'       => '',
        'choices'     => array(
          array(
            'value'   => 'left-sidebar',
            'label'   => esc_html__('Left Sidebar','workscout'),
            'src'     => OT_URL . '/assets/images/layout/left-sidebar.png'
            ),
          array(
            'value'   => 'right-sidebar',
            'label'   => esc_html__('Right Sidebar','workscout'),
            'src'     => OT_URL . '/assets/images/layout/right-sidebar.png'
            )
          ),
        ),
      array(
        'id'          => 'pp_sidebar_set',
        'label'       => esc_html__('Sidebar','workscout'),
        'desc'        => '',
        'std'         => '',
        'type'        => 'sidebar-select',
        'class'       => '',
        )
      )
    );
    ot_register_meta_box( $post_layout );


  $page_layout = array(
    'id'        => 'pp_metabox_sidebar',
    'title'     => esc_html__('Layout','workscout'),
    'desc'      => esc_html__('You can choose a sidebar from the list below. Sidebars can be created in the Theme Options and configured in the Appearance -> Widgets.','workscout'),
    'pages'     => array( 'page' ),
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
      array(
        'id'          => 'pp_page_titlebar',
        'label'       => 'Titlebar',
        'desc'        => 'set to OFF to hide titlebar on this page',
        'std'         => 'on',
        'type'        => 'on_off',
        'class'       => '',
      ),
      array(
        'id'          => 'pp_sidebar_layout',
        'label'       => esc_html__('Layout','workscout'),
        'desc'        => '',
        'std'         => 'full-width',
        'type'        => 'radio_image',
        'class'       => '',
        'choices'     => array(
          array(
            'value'   => 'left-sidebar',
            'label'   => esc_html__('Left Sidebar','workscout'),
            'src'     => OT_URL . '/assets/images/layout/left-sidebar.png'
            ),
          array(
            'value'   => 'right-sidebar',
            'label'   => esc_html__('Right Sidebar','workscout'),
            'src'     => OT_URL . '/assets/images/layout/right-sidebar.png'
            ),
          array(
            'value'   => 'full-width',
            'label'   => esc_html__('Full Width (no sidebar)','workscout'),
            'src'     => OT_URL . '/assets/images/layout/full-width.png'
            )
          ),
        ),
      array(
        'id'          => 'pp_sidebar_set',
        'label'       => 'Sidebar',
        'desc'        => '',
        'std'         => '',
        'type'        => 'sidebar-select',
        'class'       => '',
        )
      )
  );
  ot_register_meta_box( $page_layout );

}

?>