<?php 


/**
* Spotlight shortcode
* Usage: [spotlight_jobs]
* Shows selected jobs in carousel
*/


function workscout_spotlight_jobs( $atts ) {
    ob_start();

    extract( $atts = shortcode_atts( apply_filters( 'job_manager_output_jobs_defaults', array(
        'per_page'                  => get_option( 'job_manager_per_page' ),
        'orderby'                   => 'featured',
        'order'                     => 'DESC',
        'title'                     => 'Job Spotlight',
        'visible'                   => '1,1,1,1',
        'autoplay'                  => "off",
        'delay'                     => 5000,
        // Limit what jobs are shown based on category and type
        'categories'                => '',
        'job_types'                 => '',
        'job_ids'                   => '',
        'featured'                  => null, // True to show only featured, false to hide featured, leave null to show both.
        'filled'                    => null, // True to show only filled, false to hide filled, leave null to show both/use the settings.

        
    ) ), $atts ) );

    $randID = rand(1, 99); 

    if ( ! is_null( $filled ) ) {
        $filled = ( is_bool( $filled ) && $filled ) || in_array( $filled, array( '1', 'true', 'yes' ) ) ? true : false;
    }

    // Array handling
    $categories         = is_array( $categories ) ? $categories : array_filter( array_map( 'trim', explode( ',', $categories ) ) );
    $job_types          = is_array( $job_types ) ? $job_types : array_filter( array_map( 'trim', explode( ',', $job_types ) ) );
    if ( ! is_null( $featured ) ) {
      
        $featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, array( '1', 'true', 'yes' ) ) ? true : false;
    }

   $query_args = array(
        'post_type'              => 'job_listing',
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

    if ( ! is_null( $filled) || 1 === absint( get_option( 'job_manager_hide_filled_positions' ) ) ) {
        $query_args['meta_query'][] = array(
            'key'     => '_filled',
            'value'   => '1',
            'compare' => $filled ? '=' : '!='
        );
    }

    if ( ! empty( $job_types ) ) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'job_listing_type',
            'field'    => 'slug',
            'terms'    => $job_types
        );
    }

    if ( ! empty( $categories ) ) {
        $field    = is_numeric( $categories[0] ) ? 'term_id' : 'slug';
        
        $operator = 'all' === get_option( 'job_manager_category_filter_type', 'all' ) && sizeof( $categories ) > 1 ? 'AND' : 'IN';
        $query_args['tax_query'][] = array(
            'taxonomy'         => 'job_listing_category',
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
                        $id = get_the_id(); ?>
                        <li>
                            <div class="job-spotlight">
                                <a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?> 
                                <?php if ( get_option( 'job_manager_enable_types' ) ) { ?>
                                    <span class="job-type <?php echo get_the_job_type() ? sanitize_title( get_the_job_type()->slug ) : ''; ?>"><?php the_job_type(); ?></span>
                                <?php } ?>
                                </h4></a>
                                <span class="ws-meta-company-name"><i class="fa fa-briefcase"></i> <?php the_company_name(  ); ?></span>
                                <span class="ws-meta-job-location"><i class="fa fa-map-marker"></i> <?php ws_job_location(); ?></span>
                                
                                <?php 
                                $rate_min = get_post_meta( $id, '_rate_min', true ); 
                                if ( $rate_min) { 
                                    $rate_max = get_post_meta( $id, '_rate_max', true );  ?>
                                    <span class="ws-meta-rate">
                                        <i class="fa fa-money"></i> <?php  echo get_workscout_currency_symbol();  echo esc_html( $rate_min ); if(!empty($rate_max)) { echo '- '.get_workscout_currency_symbol().$rate_max; } ?> <?php _e('/ hour','workscout'); ?>
                                    </span>
                                <?php } ?>

                                <?php 
                                $salary_min = get_post_meta( $id, '_salary_min', true ); 
                                if ( $salary_min ) {
                                    $salary_max = get_post_meta( $id, '_salary_max', true );  ?>
                                    <span class="ws-meta-salary">
                                        <i class="fa fa-money"></i>
                                        <?php echo get_workscout_currency_symbol(); echo esc_html( $salary_min ) ?> 
                                        <?php if(!empty($salary_max)) { echo '- '.get_workscout_currency_symbol().$salary_max; } ?>
                                    </span>
                                <?php } ?>
                                
                                <p><?php  
                                    $excerpt = get_the_excerpt();
                                    echo workscout_string_limit_words($excerpt,20); ?>...
                                </p>
                                <a href="<?php the_permalink(); ?>" class="button"><?php esc_html_e('Apply For This Job','workscout') ?></a>
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

}

?>