<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 18.10.17
 * Time: 9:29
 * To change this template use File | Settings | File Templates.
 */

function get_count_of_influencers(){
    $query_args = array(
        'post_type'              => 'resume',
        'post_status'            => 'publish',
        'ignore_sticky_posts'    => 1,
        'posts_per_page'         => -1,
        'fields'                 => 'ids'
    );

    $result = new WP_Query( $query_args );

    $users_count = (int) $result->post_count;

    echo $users_count;


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
    $post = get_post( $resume );

    if ( $post->post_type !== 'resume' )
        return;

    global $wpdb;

    $query = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE (post_id = '".$post->ID."' AND ((meta_key = '_newsletter' OR meta_key = '_instagram_link' OR meta_key = '_twitter_link' OR meta_key = '_youtube_link' OR meta_key = '_influencer_website' OR meta_key = '_jrrny_link' )AND meta_value != ''))");

    return count($query);

}


function output_candidate_campaigns_count( $user_id ){

    if ( ! $user_id ) {
        return false;
    }

    return sizeof( get_posts( array(
        'post_type'      => 'job_application',
        'post_status'    =>  array_keys( get_job_application_statuses() ) ,
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array(
                'key' => '_candidate_user_id',
                'value' => absint( $user_id )
            )
        )
    ) ) );

}

function get_main_image( $user_id = null ){
    if ( ! $user_id )
        return false;

    $user = get_user_by('ID', $user_id);

    if ( in_array( 'employer', (array) $user->roles )  ) :
        $logo = get_user_meta( $user_id, 'logo', true);
    endif;

    if ( in_array( 'candidate', (array) $user->roles )  ) :
        $logo = get_user_meta( $user_id, 'photo', true);
    endif;

    if ( in_array( 'administrator', (array) $user->roles )  ) :
        $logo = get_user_meta( $user_id, 'logo', true);
    endif;

    if( $logo ) {
        $dir = wp_get_upload_dir();
        return $dir['baseurl'].'/users/'.$logo;
    }
    return false;

}

add_action( 'wp_ajax_nopriv_resume_manager_get_influencers', 'get_influencers'  );
add_action( 'wp_ajax_resume_manager_get_influencers',  'get_influencers'  );

function get_influencers(){
    global $wpdb;

    ob_start();

    $search_keywords   = sanitize_text_field( stripslashes( $_POST['search_keywords'] ) );

    $order = sanitize_text_field( $_POST['order'] );
    $order_by = sanitize_text_field( $_POST['orderby'] );

    switch ( $order_by ){
        case 'audience':
            $meta_key = "_audience";
            $order_by = 'meta_value_num';
            break;
        case 'companies':
            $meta_key = "_finished_companies";
            $order_by = 'meta_value_num';
            break;
        case 'date':
            $order_by = 'date';
            break;
        default:
            $order_by = 'date';
    }

    $order = ( $order == 'asc' || $order == "ASC" ) ? ( "ASC" ) : ( "DESC" );

    $args = array(
        'search_keywords'   => $search_keywords,
        'orderby'           => $order_by,
        'order'             => $order,
        'offset'            => ( absint( $_POST['page'] ) - 1 ) * absint( $_POST['per_page'] ),
        'posts_per_page'    => absint( $_POST['per_page'] )
    );

    if ( $meta_key ) {
        $args['meta_key'] = $meta_key;
        add_filter( 'resume_manager_get_resumes', 'add_meta_filter', 10, 2 );
    }

    $resumes = get_resumes( apply_filters( 'resume_manager_get_resumes_args', $args ) );

    remove_filter( 'resume_manager_get_resumes', 'add_meta_filter', 10 );

    $result = array();
    $result['found_resumes'] = false;

    if ( $resumes->have_posts() ) : $result['found_resumes'] = true; ?>

        <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>

            <?php get_template_part('template-parts/content', 'influencer')?>

        <?php endwhile; ?>

    <?php else : ?>

        <div class="no-influencers"><?php _e( 'No resumes found matching your selection.', 'wp-job-manager-resumes' ); ?></div>

    <?php endif;

    $result['html'] = ob_get_clean();

    // Generate pagination
    if ( isset( $_POST['show_pagination'] ) && $_POST['show_pagination'] === 'true' ) {
        $result['pagination'] = get_job_listing_pagination( $resumes->max_num_pages, absint( $_POST['page'] ) );
    }

    $result['max_num_pages'] = $resumes->max_num_pages;

    echo '<!--WPJM-->';
    echo json_encode( $result );
    echo '<!--WPJM_END-->';

    die();
}

function add_meta_filter( $query_args, $args ){

    if ( array_key_exists('meta_key', $args) && !empty($args['meta_key']) ){
        $query_args['meta_key'] = $args['meta_key'];
    }

    return $query_args;

}

function get_total_count_applications( ) {

    $args     = apply_filters( 'job_manager_get_dashboard_jobs_args', array(
        'post_type'           => 'job_listing',
        'post_status'         => array( 'publish', 'expired', 'pending' ),
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => -1,
        'author'              => get_current_user_id(),
        'fields'              =>'ids'
    ) );

    $jobs_query = new WP_Query();

    $jobs = $jobs_query->query( $args );

    foreach ( $jobs as $key => $job){
        if ( !get_job_application_count($job) ){
            unset ($jobs[$key]);
        }
    }

    $args = apply_filters( 'job_manager_job_applications_args', array(
        'post_type'           => 'job_application',
        'post_status'         => array_diff( array_merge( array_keys( get_job_application_statuses() ), array( 'publish' ) ), array( 'archived' ) ),
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => -1,
        'offset'              => '',
        'post_parent__in'     => $jobs,
    ) );

    $jobs_query = new WP_Query($args);

    return $jobs_query->post_count;
}

function get_applications( ) {

    $args     = apply_filters( 'job_manager_get_dashboard_jobs_args', array(
        'post_type'           => 'job_listing',
        'post_status'         => array( 'publish', 'expired', 'pending' ),
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => -1,
        'orderby'             => 'date',
        'order'               => 'desc',
        'author'              => get_current_user_id(),
        'fields'              =>'ids'
    ) );

    $jobs_query = new WP_Query();

    $jobs = $jobs_query->query( $args );

    $applications_with_job = array();

    foreach ( $jobs as $key => $job){
        if ( !get_job_application_count($job) ){
            unset ($jobs[$key]);
        }else{
            $args = apply_filters( 'job_manager_job_applications_args', array(
                'post_type'           => 'job_application',
                'post_status'         => array_diff( array_merge( array_keys( get_job_application_statuses() ), array( 'publish' ) ), array( 'archived' ) ),
                'ignore_sticky_posts' => 1,
                'posts_per_page'      => -1,
                'offset'              => '',
                'post_parent'     => $job,
                'order'               => 'DESC',
                'orderby'             => 'date'
            ) );
            $applications_query = new WP_Query;
            $applications = $applications_query->query( $args );
            $applications_with_job[$job]['job'] = get_post( $job );
            $applications_with_job[$job]['applications'] = $applications;
        }
    }

    return $applications_with_job;
}

function get_last_application( $job_id = null  ) {
    if ( !$job_id ) return;

        $job = get_post( $job_id );

    if ( !$job ) return;

    if ( ! job_manager_user_can_edit_job( $job_id)  )  return;

    $args = apply_filters( 'job_manager_job_applications_args', array(
        'post_type'           => 'job_application',
        'post_status'         => array_diff( array_merge( array_keys( get_job_application_statuses() ), array( 'publish' ) ), array( 'archived' ) ),
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => 1,
        'offset'              => '',
        'post_parent'         => $job_id,
        'order'               => 'DESC',
        'orderby'             => 'date'
    ) );

    $applications_query = new WP_Query;

    $applications = $applications_query->query( $args );

    $application = array_shift($applications);

    return $application;
}
