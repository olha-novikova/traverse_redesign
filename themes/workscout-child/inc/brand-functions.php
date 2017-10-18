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