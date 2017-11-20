<?php
/**
 * Initialize the custom Theme Options.
 */
add_action( 'admin_init', 'workscout_custom_theme_options' );

/**
 * Build the custom settings & update OptionTree.
 *
 * @return    void
 * @since     2.0
 */
function workscout_custom_theme_options() {
   global $wpdb;
   $revsliders = array();

   $faicons = workscout_icons_list();
   $newfaicons = array();
   foreach ($faicons as $key => $value) {
     $newfaicons[] =  array('value'=> $key,'label' => $value);
   }
   


   /**
   * Get a copy of the saved settings array.
   */
    $saved_settings = get_option( ot_settings_id(), array() );


    $table_name = $wpdb->prefix . "revslider_sliders";
    // Get sliders

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
      $sliders = $wpdb->get_results( "SELECT alias, title FROM $table_name" );
    } else {
      $sliders = '';
    }

    if($sliders) {
      foreach($sliders as $key => $item) {
        $revsliders[] = array(
          'label' => $item->title,
          'value' => $item->alias
          );
      }
    } else {
      $revsliders[] = array(
        'label' => esc_html__('No Sliders Found','workscout'),
        'value' => ''
        );
    }
  /**
   * Custom settings array that will eventually be
   * passes to the OptionTree Settings API Class.
   */
  $custom_settings = array(

    'sections'        => array(

      array(
        'title'       => esc_html__('Contact Page','workscout'),
        'id'          => 'contact'
      ),
      array(
        'title'       =>  esc_html__( 'General', 'workscout' ),
        'id'          => 'general_default'
        ), 
      array(
        'title'       => esc_html__( 'Typography', 'workscout' ),
        'id'          => 'typography'
        ),
      array(
        'id'          => 'sidebars',
        'title'       => esc_html__( 'Sidebars', 'workscout' )
      ),    
   
    ),

    'settings'        => array(



        array(
            'label'       => 'Choose "My Bookmarks"',
            'id'          => 'pp_bookmarks_page',
            'type'        => 'page_select',
            'desc'        => 'this page needs to have shortcode [my_bookmarks] in the content',
            'std'         => '',
            'rows'        => '',
            'post_type'   => '',
            'taxonomy'    => '',
            'class'       => '',
            'section'     => 'general_default'
        ),
        
        array(
          'label'       => 'Comments on pages',
          'id'          => 'pp_pagecomments',
          'type'        => 'on_off',
          'desc'        => 'You can disable globaly comments on all pages with this option, or you can do it per page in Page editor',
          'std'         => 'on',
          'rows'        => '',
          'post_type'   => '',
          'taxonomy'    => '',
          'class'       => '',
          'section'     => 'general_default'
        ),

        array(
              'id'          => 'pp_custom_css',
              'label'       => 'Custom CSS',
              'desc'        => 'To prevent problems with theme update, write here any custom css (or use child themes)',
              'std'         => '',
              'type'        => 'textarea-simple',
              'section'     => 'general_default',
              'rows'        => '',
              'post_type'   => '',
              'taxonomy'    => '',
              'class'       => ''
        ),
        array(
            'id'          => 'pp-fonts',
            'label'       => esc_html__( 'Google Fonts', 'workscout' ),
            'desc'        => '',
            'std'         => array( 
                array(
                    'family'    => 'montserrat',
                    'variants'  => array( 'regular','700' ),
                    'subsets'   => array( 'latin' )
                ),
                array(
                    'family'    => 'lato',
                    'variants'  => array( 'regular', '300', '700'),
                    'subsets'   => array( 'latin')
                )
            ),
            'type'        => 'google-fonts',
            'section'     => 'typography',
            'rows'        => '',
            'post_type'   => '',
            'taxonomy'    => '',
            'min_max_step'=> '',
            'class'       => '',
            'condition'   => '',
            'operator'    => 'and'
        ),
        array(
          'label'       => 'Body Font',
          'id'          => 'workscout_body_font',
          'type'        => 'typography',
          'desc'        => '',
          'std'         => '',
          'rows'        => '',
          'post_type'   => '',
          'taxonomy'    => '',
          'class'       => '',
          'section'     => 'typography'
          ),
        array(
          'label'       => 'Menu Font',
          'id'          => 'workscout_menu_font',
          'type'        => 'typography',
          'desc'        => '',
          'std'         => '',
          'rows'        => '',
          'post_type'   => '',
          'taxonomy'    => '',
          'class'       => '',
          'section'     => 'typography'
          ),
        array(
          'label'       => 'Logo Font',
          'id'          => 'workscout_logo_font',
          'type'        => 'typography',
          'desc'        => '',
          'std'         => '',
          'rows'        => '',
          'post_type'   => '',
          'taxonomy'    => '',
          'class'       => '',
          'section'     => 'typography'
          ),     
        array(
          'label'       => 'Headers (h1..h6) Font',
          'id'          => 'workscout_headers_font',
          'type'        => 'typography',
          'desc'        => 'Size and related to it settings will be ignored here.',
          'std'         => '',
          'rows'        => '',
          'post_type'   => '',
          'taxonomy'    => '',
          'class'       => '',
          'section'     => 'typography'
          ),
        
      array(
          'id'          => 'sidebars_text',
          'label'       => 'About sidebars',
          'desc'        => 'All sidebars that you create here will appear both in the Appearance > Widgets, and then you can choose them for specific pages or posts.',
          'std'         => '',
          'type'        => 'textblock',
          'section'     => 'sidebars',
          'rows'        => '',
          'post_type'   => '',
          'taxonomy'    => '',
          'class'       => ''
          ),
      array(
          'label'       => 'Create Sidebars',
          'id'          => 'incr_sidebars',
          'type'        => 'list-item',
          'desc'        => 'Choose a unique title for each sidebar',
          'section'     => 'sidebars',
          'settings'    => array(
            array(
              'label'       => 'ID',
              'id'          => 'id',
              'type'        => 'text',
              'desc'        => 'Write a lowercase single world as ID (it can\'t start with a number!), without any spaces',
              'std'         => 'my_new_sidebar',
              'rows'        => '',
              'post_type'   => '',
              'taxonomy'    => '',
              'class'       => ''
              )
            )
        ),
      
      array(
        'label'       => 'Maps configuration',
        'id'          => 'pp_maps_text',
        'type'        => 'textblock',
        'desc'        => 'Add custom markers to the map',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'contact'
        ),

      array(
        'label'       => 'Zoom level for map',
        'id'          => 'pp_contact_zoom',
        'type'        => 'numeric-slider',
        'desc'        => '0 - whole world, 19 - maximum zoom.',
        'std'         => '13',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'min_max_step'=> '1,19,1',
        'section'     => 'contact'
        ),
      array(
        'label'       => 'Map type',
        'id'          => 'pp_contact_maptype',
        'type'        => 'select',
        'desc'        => '',
        'choices'     => array(
          array(
            'label'       => 'ROADMAP',
            'value'       => 'ROADMAP'
            ),
          array(
            'label'       => 'SATELLITE',
            'value'       => 'SATELLITE'
            ),
          array(
            'label'       => 'HYBRID',
            'value'       => 'HYBRID'
            ),
          array(
            'label'       => 'TERRAIN',
            'value'       => 'TERRAIN'
            )
          ),
        'std'         => 'ROADMAP',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'contact'
        ),
      array(
        'label'       => 'Markers on map',
        'id'          => 'pp_contact_map',
        'type'        => 'list-item',
        'desc'        => 'Manage markers on the contact page map.',
        'settings'    => array(
          array(
            'label'       => 'Address of marker on map',
            'id'          => 'address',
            'type'        => 'text',
            'desc'        => '',
            'std'         => '',
            'rows'        => '',
            'post_type'   => '',
            'taxonomy'    => '',
            'class'       => ''
            ),
          array(
            'label'       => 'HTML content of marker',
            'id'          => 'content',
            'type'        => 'textarea',
            'desc'        => '',
            'std'         => '',
            'rows'        => '',
            'post_type'   => '',
            'taxonomy'    => '',
            'class'       => ''
            )
          ),
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'contact'
        ),

      array(
          'label'       => 'Footer social icons',
          'id'          => 'pp_footericons',
          'type'        => 'list-item',
          'desc'        => 'Manage socials icons in footer.',
          'settings'    => array(
            array(
              'id'          => 'icons_service',
              'label'       => 'Choose service',
              'desc'        => '',
              'std'         => '',
              'type'        => 'select',
              'rows'        => '',
              'post_type'   => '',
              'taxonomy'    => '',
              'class'       => '',
              'choices'     => array(
                array('value'=> 'twitter','label' => 'Twitter','src'=> ''),
                array('value'=> 'wordpress','label' => 'WordPress','src'=> ''),
                array('value'=> 'facebook','label' => 'Facebook','src'=> ''),
                array('value'=> 'linkedin','label' => 'LinkedIN','src'=> ''),
                array('value'=> 'steam','label' => 'Steam','src'=> ''),
                array('value'=> 'tumblr','label' => 'Tumblr','src'=> ''),
                array('value'=> 'github','label' => 'GitHub','src'=> ''),
                array('value'=> 'delicious','label' => 'Delicious','src'=> ''),
                array('value'=> 'instagram','label' => 'Instagram','src'=> ''),
                array('value'=> 'xing','label' => 'Xing','src'=> ''),
                array('value'=> 'amazon','label'=> 'Amazon','src'=> ''),
                array('value'=> 'dropbox','label'=> 'Dropbox','src'=> ''),
                array('value'=> 'paypal','label'=> 'PayPal','src'=> ''),
                array('value'=> 'gplus','label' => 'Google Plus','src'=> ''),
                array('value'=> 'stumbleupon','label' => 'StumbleUpon','src'=> ''),
                array('value'=> 'yahoo','label' => 'Yahoo','src'=> ''),
                array('value'=> 'pinterest','label' => 'Pinterest','src'=> ''),
                array('value'=> 'dribbble','label' => 'Dribbble','src'=> ''),
                array('value'=> 'flickr','label' => 'Flickr','src'=> ''),
                array('value'=> 'reddit','label' => 'Reddit','src'=> ''),
                array('value'=> 'vimeo','label' => 'Vimeo','src'=> ''),
                array('value'=> 'spotify','label' => 'Spotify','src'=> ''),
                array('value'=> 'rss','label' => 'RSS','src'=> ''),
                array('value'=> 'youtube','label' => 'YouTube','src'=> ''),
                array('value'=> 'blogger','label' => 'Blogger','src'=> ''),
                array('value'=> 'evernote','label' => 'Evernote','src'=> ''),
                array('value'=> 'digg','label' => 'Digg','src'=> ''),
                array('value'=> 'fivehundredpx','label' => '500px','src'=> ''),
                array('value'=> 'forrst','label' => 'Forrst','src'=> ''),
                array('value'=> 'appstore','label' => 'AppStore','src'=> ''),
                array('value'=> 'lastfm','label' => 'LastFM','src'=> ''),
                ),
            ),
            array(
              'label'       => 'URL to profile page',
              'id'          => 'icons_url',
              'type'        => 'text',
              'desc'        => '',
              'std'         => '',
              'rows'        => '',
              'post_type'   => '',
              'taxonomy'    => '',
              'class'       => ''
              )
        ),
      'std'         => '',
      'rows'        => '',
      'post_type'   => '',
      'taxonomy'    => '',
      'class'       => '',
      'section'     => 'general_default'
      ),
    )
  );

  /* allow settings to be filtered before saving */
  $custom_settings = apply_filters( ot_settings_id() . '_args', $custom_settings );

  /* settings are not the same update the DB */
  if ( $saved_settings !== $custom_settings ) {
    update_option( ot_settings_id(), $custom_settings );
  }

}