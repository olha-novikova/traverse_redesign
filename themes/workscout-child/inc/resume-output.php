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

    if ( ! empty( $_GET['search_location'] ) ) {
        $location = sanitize_text_field( $_GET['search_location'] );
    }

    $resumes = get_resumes( apply_filters( 'resume_manager_output_resumes_args', array(
        'orderby'           => $orderby,
        'order'             => $order,
        'posts_per_page'    => $per_page
    ) ) );

    if ( $resumes->have_posts() ) : ?>
        <div class="influencers__list">
            <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>
                <div class="carousel__influencer">
                    <div class="carousel__influencer__top"></div>
                    <div class="carousel__influencer__person"><?php output_candidate_photo(); ?>
                        <p class="carousel__influencer__name"><a href="<?php echo get_permalink()?>"><?php the_title(); ?></a></p>
                    </div>
                    <div class="carousel__influencer__info">
                        <div class="carousel__influencer__info-block">
                            <p class="carousel__influencer__number"><?php echo output_candidate_campaigns_count( $resumes->post->post_author); ?></p>
                            <p class="carousel__influencer__description"><?php echo  _n( 'Campaign', 'Campaigns', output_candidate_campaigns_count($resumes->post->post_author) ); ?></p>
                        </div>
                        <div class="carousel__influencer__info-block">
                            <p class="carousel__influencer__number">56300</p>
                            <p class="carousel__influencer__description">Audience</p>
                        </div>
                        <div class="carousel__influencer__info-block">
                            <p class="carousel__influencer__number"><?php echo output_candidate_channels_count(get_the_ID());?></p>
                            <p class="carousel__influencer__description"><?php echo  _n( 'Channel', 'Channels', output_candidate_channels_count(get_the_ID()) ); ?></p>
                        </div>
                    </div>
                    <div class="carousel__influencer__buttons">
                        <div class="carousel__influencer__button carousel__influencer__button_star"></div>
                        <div class="carousel__influencer__button carousel__influencer__button_message"></div>
                    </div>
                </div>
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
    <?php else :
        do_action( 'resume_manager_output_resumes_no_results' );
    endif;

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



