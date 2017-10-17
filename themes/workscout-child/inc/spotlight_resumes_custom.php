<?php

/**
* Spotlight shortcode
* Usage: [spotlight_jobs]
* Shows selected jobs in carousel
*/

function spotlight_resumes_custom( $atts ) {
    ob_start();

    extract( $atts = shortcode_atts( apply_filters( 'job_manager_output_jobs_defaults', array(
        'per_page'                  => get_option( 'job_manager_per_page' ),
        'orderby'                   => 'featured',
        'order'                     => 'DESC',
        'title'                     => 'Resumes Spotlight',
        'visible'                   => '1,1,1,1',
        'autoplay'                  => "off",
        'delay'                     => 5000,
        // Limit what jobs are shown based on category and type
        'categories'                => '',
        'resume_ids'                   => '',
        'featured'                  => null, // True to show only featured, false to hide featured, leave null to show both.
       
        
    ) ), $atts ) );

    $randID = rand(1, 99); 

    // Array handling
    $categories         = is_array( $categories ) ? $categories : array_filter( array_map( 'trim', explode( ',', $categories ) ) );
  
    if ( ! is_null( $featured ) ) {
      
        $featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, array( '1', 'true', 'yes' ) ) ? true : false;
    }

   $query_args = array(
        'post_type'              => 'resume',
        'post_status'            => 'publish',
        'ignore_sticky_posts'    => 1,
        'offset'                 => 0,
        'posts_per_page'         => intval( $per_page ),
        'orderby'                => $orderby,
        'order'                  => $order,
        'fields'                 => 'all'
    );

   if(!empty($job_ids)) {
        $inc = explode(",", $job_ids);
        $query_args['post__in'] = $inc;
    }

    if ( ! is_null( $featured ) ) {
        $query_args['meta_query'][] = array(
            'key'     => '_featured',
            'value'   => '1',
            'compare' => $featured ? '=' : '!='
        );
    }



    if ( ! empty( $categories ) ) {
        $field    = is_numeric( $categories[0] ) ? 'term_id' : 'slug';
        $operator = 'all' === get_option( 'resume_manager_category_filter_type', 'all' ) && sizeof( $categories ) > 1 ? 'AND' : 'IN';
        $query_args['tax_query'][] = array(
            'taxonomy'         => 'resume_category',
            'field'            => $field,
            'terms'            => array_values( $categories ),
            'include_children' => $operator !== 'AND' ,
            'operator'         => $operator
        );
    }

    if ( 'featured' === $orderby ) {
        $orderby = array(
            'menu_order' => 'ASC',
            'date'       => 'DESC'
        );
    }

   $wp_query = new WP_Query( $query_args );
   if ( $wp_query->have_posts() ):
     
        ?>
 
        <h3 class="margin-bottom-5"><?php echo esc_html($title); ?></h3>
        <!-- Navigation -->
        <div class="showbiz-navigation">
            <div id="showbiz_left_<?php echo esc_attr($randID); ?>" class="sb-navigation-left"><i class="fa fa-angle-left"></i></div>
            <div id="showbiz_right_<?php echo esc_attr($randID); ?>" class="sb-navigation-right"><i class="fa fa-angle-right"></i></div>
        </div>
        <div class="clearfix"></div>
        
        <!-- Showbiz Container -->
        <div id="job-spotlight" data-delay="<?php echo $delay; ?>" data-autoplay="<?php echo $autoplay; ?>" class="job-spotlight-car showbiz-container" data-visible="[<?php echo $visible; ?>]">
            <div class="showbiz" data-left="#showbiz_left_<?php echo esc_attr($randID); ?>" data-right="#showbiz_right_<?php echo esc_attr($randID); ?>" data-play="#showbiz_play_<?php echo esc_attr($randID); ?>" >
                <div class="overflowholder">
                    <ul>
                      <?php  while( $wp_query->have_posts() ) : $wp_query->the_post();
                          $id = get_the_id();
                          $category = get_the_resume_category($id);?>
                        <li>
                            <div class="resume-spotlight">

                            <a href="<?php the_permalink(); ?>">
                                <?php    $portfolio_name = get_post_meta($id, '_portfolio_name', true); ?>
                                <h4><?php the_title(); ?> <span><?php echo $portfolio_name; ?></span></h4>
                                <?php if ( $category ) : ?>
                                    <div class="resume-category">
                                        <?php echo $category ?>
                                    </div>
                                <?php endif; ?>
                                <span><i class="fa fa-map-marker"></i> <?php the_candidate_location(); ?></span>

                                <p><?php  
                                    $excerpt = get_the_excerpt();
                                    echo workscout_string_limit_words($excerpt,20); ?>...
                                </p>
                                <?php if ( ( $skills = wp_get_object_terms( $id , 'resume_skill', array( 'fields' => 'names' ) ) ) && is_array( $skills ) ) : ?>
                                <div class="skills">
                                    <?php echo '<span>' . implode( '</span><span>', $skills ) . '</span>'; ?>
                                </div>
                                <div class="clearfix"></div>
                            <?php endif; ?>
                                <a href="<?php the_permalink(); ?>" class="button"><?php esc_html_e('Contact Candidate','workscout') ?></a>
                            </div>
                        </li>
                        <?php endwhile; ?>
                        
                    </ul>
                    <div class="clearfix"></div>

                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    <?php  
        
    endif; 
    wp_reset_postdata();
    $job_listings_output =  ob_get_clean();

    return $job_listings_output;

}?>