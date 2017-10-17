<?php


/*
    Shortcode prints grid of categories with icon boxes
    Usage: [box_job_categories orderby="count" order="ASC" number]
*/

function workscout_box_job_categories( $atts ) {
    extract(shortcode_atts(array(
        'hide_empty'        => 0,
        'orderby'           => 'count',
        'order'             => 'DESC',
        'number'            => '8',
        'browse_link'       => '',
        'include'           => '',
        'exclude'           => '',
        'child_of'          => 0,

        ), $atts));
    $include         = is_array( $include ) ? $include : array_filter( array_map( 'trim', explode( ',', $include ) ) );
    $exclude         = is_array( $exclude ) ? $exclude : array_filter( array_map( 'trim', explode( ',', $exclude ) ) );

    $output = '<ul id="popular-categories">';


    $categories = get_terms( 'job_listing_category', array(
        'orderby'       => $orderby, // id count name - Default slug term_group - Not fully implemented (avoid using) none
        'order'         => $order, // id count name - Default slug term_group - Not fully implemented (avoid using) none
        'hide_empty'    => $hide_empty,
        'number'        => $number,
        'include'       => $include,
        'exclude'       => $exclude,
        'child_of'      => $child_of,
     ) );
    
    if ( !is_wp_error( $categories ) ) {
    
      foreach ($categories  as $term ) {
        $t_id = $term->term_id;
        $term_meta = get_option( "taxonomy_$t_id" ); 
        if(isset($term_meta['fa_icon'])) {
            if ($term_meta['fa_icon'] == 'fa fa-' || $term_meta['fa_icon'] == 'ln ln-' ) {
                $icon = '';
            } else {
                $icon = $term_meta['fa_icon'];
            }
        } else {
            $icon = '';
        }
        $imageicon = $term_meta['upload_icon'];
        $output .= ' 
        <li>
            <a href="' . get_term_link( $term ) . '">';
            if (!empty($imageicon)) {
                $output .= '<img src="'.esc_attr($imageicon).'"/>';
            } else if(!empty($icon)) { 
                $check_if_new = substr($icon, 0, 3);
                if($check_if_new == 'fa ' ||$check_if_new == 'ln ') {
                    $output .= ' <i class="'.esc_attr($icon).'"></i>'; 
                } else {
                    $output .= ' <i class="fa fa-'.esc_attr($icon).'"></i>'; 
                }
            }
            
            $output .=  $term->name .'</a>
        </li>';
      }
    }  
    if  (is_wp_error( $categories )) {
        $output .= '<li>Please enable  categories for listings in wp-admin > Job Listings -> Settings and add some categories</li>';

    }
    $output .= '</ul><div class="clearfix"></div>
        <div class="margin-top-30"></div>';
        if($browse_link) {
                if(Kirki::get_option( 'workscout','pp_categories_page')){
                    $categories_page = Kirki::get_option( 'workscout','pp_categories_page');
                } elseif (ot_get_option('pp_categories_page')){
                    $categories_page = ot_get_option('pp_categories_page'); 
                }
                if(!empty($categories_page)) :
                    $output .= '<a href="'.esc_url( get_permalink($categories_page) ).'" class="button centered">'.esc_html__('Browse All Categories','workscout').'</a><div class="margin-bottom-50"></div>';
                endif;
        }
    return $output;
}?>