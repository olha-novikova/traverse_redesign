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
        'orderby'                   => 'featured',
        'show_filters'              => true,
        'show_categories'           => get_option( 'resume_manager_enable_categories' ),
        'categories'                => '',
        'featured'                  => null, // True to show only featured, false to hide featured, leave null to show both.
        'show_category_multiselect' => get_option( 'resume_manager_enable_default_category_multiselect', false ),
        'selected_category'         => '',
        'show_pagination'           => false,
        'show_more'                 => true,
    ) ), $atts ) );

    $categories = array_filter( array_map( 'trim', explode( ',', $categories ) ) );
    $keywords   = '';
    $location   = '';

    // String and bool handling
    $show_filters              = string_to_bool( $show_filters );
    $show_categories           = string_to_bool( $show_categories );
    $show_category_multiselect = string_to_bool( $show_category_multiselect );
    $show_more                 = string_to_bool( $show_more );
    $show_pagination           = string_to_bool( $show_pagination );



    if ( ! is_null( $featured ) ) {
        $featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, array( '1', 'true', 'yes' ) ) ? true : false;
    }

    if ( ! empty( $_GET['search_keywords'] ) ) {
        $keywords = sanitize_text_field( $_GET['search_keywords'] );
    }

    if ( ! empty( $_GET['search_location'] ) ) {
        $location = sanitize_text_field( $_GET['search_location'] );
    }

    if ( ! empty( $_GET['search_category'] ) ) {
        $selected_category = sanitize_text_field( $_GET['search_category'] );
    }


    $resumes = get_resumes( apply_filters( 'resume_manager_output_resumes_args', array(
        'search_categories' => $categories,
        'orderby'           => $orderby,
        'order'             => $order,
        'posts_per_page'    => $per_page,
        'featured'          => $featured
    ) ) );

    if ( $resumes->have_posts() ) : ?>
        <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>

            <div class="carousel__influencer">
                <div class="carousel__influencer__top"></div>
                <div class="carousel__influencer__person"><?php output_candidate_photo(); ?>
                    <p class="carousel__influencer__name"><?php the_title(); ?></p>
                </div>
                <div class="carousel__influencer__info">
                    <div class="carousel__influencer__info-block">
                        <p class="carousel__influencer__number">48</p>
                        <p class="carousel__influencer__description">Campaigns</p>
                    </div>
                    <div class="carousel__influencer__info-block">
                        <p class="carousel__influencer__number">56300</p>
                        <p class="carousel__influencer__description">Audience</p>
                    </div>
                    <div class="carousel__influencer__info-block">
                        <p class="carousel__influencer__number">4</p>
                        <p class="carousel__influencer__description">Channels</p>
                    </div>
                </div>
                <div class="carousel__influencer__buttons">
                    <div class="carousel__influencer__button carousel__influencer__button_star"></div>
                    <div class="carousel__influencer__button carousel__influencer__button_message"></div>
                </div>
            </div>

        <?php endwhile; ?>
    <?php else :
        do_action( 'resume_manager_output_resumes_no_results' );
    endif;

    wp_reset_postdata();


    $data_attributes_string = '';
    $data_attributes        = array(
        'location'        => $location,
        'keywords'        => $keywords,
        'show_filters'    => $show_filters ? 'true' : 'false',
        'show_pagination' => $show_pagination ? 'true' : 'false',
        'per_page'        => $per_page,
        'orderby'         => $orderby,
        'order'           => $order,
        'categories'      => implode( ',', $categories )
    );
    if ( ! is_null( $featured ) ) {
        $data_attributes[ 'featured' ] = $featured ? 'true' : 'false';
    }
    foreach ( $data_attributes as $key => $value ) {
        $data_attributes_string .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
    }

    return '<div class="influencers__list" ' . $data_attributes_string . '>' . ob_get_clean() . '</div>';
}

function string_to_bool( $value ) {
    return ( is_bool( $value ) && $value ) || in_array( $value, array( '1', 'true', 'yes' ) ) ? true : false;
}

function output_candidate_photo( $size = 'thumbnail', $default = null, $post = null ) {
    $logo = get_the_candidate_photo( $post );

    if ( $logo ) {

        if ( $size !== 'full' ) {
            $logo = job_manager_get_resized_image( $logo, $size );
        }

        echo '<img class="carousel__influencer__image" src="' . $logo . '" alt="Photo" />';

    } elseif ( $default )
        echo '<img class="carousel__influencer__image" src="' . $default . '" alt="Photo" />';
    else
        echo '<img class="carousel__influencer__image" src="' . apply_filters( 'resume_manager_default_candidate_photo', RESUME_MANAGER_PLUGIN_URL . '/assets/images/candidate.png' ) . '" alt="Logo" />';
}

function output_candidate_channels_count(  $resume ) {
    $website = get_user_meta( $user_id, 'website', true );
    $monthlyvisit = get_user_meta( $user_id, 'monthlyvisit', true );
    $insta = get_user_meta( $user_id, 'insta', true );
    $fb = get_user_meta( $user_id, 'fb', true );
    $twitter = get_user_meta( $user_id, 'twitter', true );

    $youtube = get_user_meta( $user_id, 'youtube', true );
    $website = get_user_meta( $user_id, 'website', true );
    $monthlyvisit = get_user_meta( $user_id, 'monthlyvisit', true );
    $insta = get_user_meta( $user_id, 'insta', true );
    $fb = get_user_meta( $user_id, 'fb', true );
    $twitter = get_user_meta( $user_id, 'twitter', true );

    $youtube = get_user_meta( $user_id, 'youtube', true );

}