<?php
/**
 * Related jobs
 */

global $post;

$tags = get_the_terms( $post->ID, 'job_listing_category' );

if ( ! $tags || is_wp_error( $tags ) || ! is_array( $tags ) ) {
	return;
}

$tags = wp_list_pluck( $tags, 'term_id' );

$related_args = array(
	'post_type' => 'job_listing',
	'orderby'   => 'rand',
	'posts_per_page' => 6,
	'post_status' => 'publish',
	'post__not_in' => array( $post->ID ),
	'tax_query' => array(
		array(
			'taxonomy' => 'job_listing_category',
			'field'    => 'id',
			'terms'    => $tags
		)
	)
);

$wp_query = new WP_Query($related_args );

if ( ! $wp_query->have_posts() ) {
	return;
}
$randID = rand(1, 99); 
 
?>
<div id="related-job-container">

    <h3 class="margin-bottom-5 margin-top-55"><?php esc_html_e('Related Jobs','workscout'); ?></h3>

    <?php  if($wp_query->post_count > 1) : ?>
        <!-- Navigation -->
        <div class="showbiz-navigation">
            <div id="showbiz_left_<?php echo esc_attr($randID); ?>" class="sb-navigation-left"><i class="fa fa-angle-left"></i></div>
            <div id="showbiz_right_<?php echo esc_attr($randID); ?>" class="sb-navigation-right"><i class="fa fa-angle-right"></i></div>
        </div>
        <div class="clearfix"></div>
    <?php endif; ?>

    <!-- Showbiz Container -->
    <div id="related-job-spotlight" class="related-job-spotlight-car showbiz-container" data-visible="[2,2,1,1]">
        <div class="showbiz" data-left="#showbiz_left_<?php echo esc_attr($randID); ?>" data-right="#showbiz_right_<?php echo esc_attr($randID); ?>" data-play="#showbiz_play_<?php echo esc_attr($randID); ?>" >
            <div class="overflowholder">
                <ul>
                  <?php  while( $wp_query->have_posts() ) : 

                  $wp_query->the_post(); 

                    $id = get_the_id(); ?>
                    <li>
                        <div class="job-spotlight">

                            <a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?> 
                            <?php if ( get_option( 'job_manager_enable_types' ) ) { ?><span class="job-type <?php echo get_the_job_type() ? sanitize_title( get_the_job_type()->slug ) : ''; ?>"><?php the_job_type(); ?></span>
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
                            <a href="<?php the_permalink(); ?>" class="button"><?php esc_html_e('Pitch Listing','workscout') ?></a>
                        </div>
                    </li>
                    <?php endwhile; ?>
                    
                </ul>
                <div class="clearfix"></div>

            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php wp_reset_query(); ?>
