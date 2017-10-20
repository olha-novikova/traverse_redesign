<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 17.10.17
 * Time: 15:44
 * To change this template use File | Settings | File Templates.
 */
add_shortcode( 'redesigned_resumes', 'redesigned_output_resumes' );

function redesigned_output_resumes( $atts ) {
    global $resume_manager;

    ob_start();

    if ( ! resume_manager_user_can_browse_resumes() ) {
        get_job_manager_template_part( 'access-denied', 'browse-resumes', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' );
        return ob_get_clean();
    }

    extract( $atts = shortcode_atts( apply_filters( 'resume_manager_output_resumes_defaults', array(
        'per_page'                  => get_option( 'resume_manager_per_page' ),
        'order'                     => 'DESC',
        'orderby'                   => 'date',
        'show_pagination'           => false,
        'show_more'                 => true,
    ) ), $atts ) );


    $keywords   = '';
    $location   = '';

    // String and bool handling

    $show_more                 = string_to_bool( $show_more );
    $show_pagination           = string_to_bool( $show_pagination );


    if ( ! empty( $_GET['search_keywords'] ) ) {
        $keywords = sanitize_text_field( $_GET['search_keywords'] );
    }

    $resumes = get_resumes( apply_filters( 'resume_manager_output_resumes_args', array(
        'orderby'           => $orderby,
        'order'             => $order,
        'posts_per_page'    => $per_page
    ) ) );

    if ( $resumes->have_posts() ) : ?>
        <div class="influencers__list">
            <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>
                <?php get_template_part('template-parts/content', 'influencer')?>
            <?php endwhile; ?>
        </div>
        <?php

        if ( $resumes->found_posts > $per_page && $show_more ) : ?>

            <?php wp_enqueue_script( 'brand-ajax-filters',  get_stylesheet_directory_uri() . '/js/brand-ajax-filters.js',array('jquery'), '1', true ); ?>

            <?php if ( $show_pagination ) : ?>
                <?php echo get_job_listing_pagination( $resumes->max_num_pages ); ?>
            <?php else : ?>
                <a class="load_more_resumes" href="#"><strong><?php _e( 'Load more resumes', 'wp-job-manager-resumes' ); ?></strong></a>
            <?php endif; ?>

        <?php endif; ?>
    <?php else :?>
        <div class="no-influencers"><?php _e( 'No resumes found matching your selection.', 'wp-job-manager-resumes' ); ?></div>
    <?php endif;

    wp_reset_postdata();

    $data_attributes_string = '';
    $data_attributes        = array(
        'location'        => $location,
        'keywords'        => $keywords,
        'show_pagination' => $show_pagination ? 'true' : 'false',
        'per_page'        => $per_page,
        'orderby'         => $orderby,
        'order'           => $order
    );

    foreach ( $data_attributes as $key => $value ) {
        $data_attributes_string .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
    }

    return '<div class="influencer_output" ' . $data_attributes_string . '>' . ob_get_clean() . '</div>';
}



