<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 06.09.17
 * Time: 14:10
 * To change this template use File | Settings | File Templates.
 */
add_action('init', 'message_post_type_init');

function message_post_type_init(){
    register_post_type('application_message', array(
        'labels'             => array(
            'name'               => 'Messages',
            'singular_name'      => 'Message',
        ),
        'public'             => false,
        'show_in_menu'       => false,
        'rewrite'            => true,
        'hierarchical'       => true
    ) );
}

function sent_notification($subject = '', $from = '', $to = '', $message = '' ){

    $to_mail      = sanitize_email( $to );
    $to_mail = "olha.novikova@gmail.com";
    @wp_mail($to_mail, $subject, $message);

   __return_true();

}

function send_message_to_candidate(){
    parse_str( $_POST['message'], $formData);

    $nonce = $formData['_wpnonce'];
    $response['success'] = false;

    if( !wp_verify_nonce( $nonce, 'my-nonce' ) ){

        $response['error'] = 'Verify Nonce Error';

    }else{

        $application_id     = absint($formData['application_id']);
        $job_id             = absint($formData['job_id']);
        $resume_id          = absint($formData['resume_id']);

        $to                 = absint($formData['msg_to']);
        $from               = absint($formData['msg_from']);

        $text               = htmlentities($formData['appl_message']);
        $title              = get_post_meta( $application_id, '_job_applied_for', true  );

        $email_to           = get_post_meta( $application_id, '_candidate_email', true );

        $current_user_id    = get_current_user_id();

        $user_to_info       = get_userdata( $to);
        $user_to_info       = $user_to_info -> display_name;

        $user_from_info     = get_userdata( $from);
        $user_from_info     = $user_from_info -> display_name;

        $resume_author      = get_post_field('post_author', $resume_id, 'db');

        if ( $from  == $resume_author ) $user_from_info = get_the_title($resume_id);
        if ( $to  == $resume_author ) $user_to_info = get_the_title($resume_id);

        if ( $from == $current_user_id){
            $post_data = array(
                'post_title'    => wp_strip_all_tags( $title ),
                'post_content'  => wp_strip_all_tags( $text ),
                'post_status'   => 'publish',
                'post_author'   => $from,
                'post_type'     => 'application_message',
                // 'post_date'     => date ('Y-m-d H:i:s')
            );

            if ( $post_id = wp_insert_post( $post_data) ){

                update_post_meta( $post_id, '_message_to', $to );
                update_post_meta( $post_id, '_message_from', $from );
                if ( $application_id ) update_post_meta( $post_id, '_target_application', $application_id );
                update_post_meta( $post_id, '_target_resume', $resume_id );
                if ( $job_id ) update_post_meta( $post_id, '_target_job', $job_id );
                update_post_meta( $post_id, '_message_status', 'new' );

                $response['success']    = true;
                $response['message_id'] = $post_id;
                $response['text']       = $text;
                $response['from']       = $user_from_info ;
                $response['to']         = $user_to_info ;
                $response['date']       = date( 'F j, Y H:i', strtotime(get_post_field( 'post_date', $post_id )) );

                $to_email = get_userdata( $to);
                $to_email = $to_email->user_email;

                $sbj = 'New message about '.$title;
                $msg = $user_from_info." send you new message about ".$title."\n";

                sent_notification($sbj, '', $to_email, $msg);

            }else{
                $response['error'] = 'Message publication error';
            }
        }else{
            $response['error'] = 'Message user error';
        }

    }

    print_r(json_encode($response));
    wp_die();
}

add_action('wp_ajax_send_message_to_candidate', 'send_message_to_candidate');
add_action('wp_ajax_nopriv_send_message_to_candidate', 'send_message_to_candidate');

if ( ! function_exists( 'send_message_to_candidate_by_job' ) ) {

    function send_message_to_candidate_by_job( ) {
        parse_str( $_POST['message'], $formData);

        $nonce = $formData['_wpnonce'];
        $response['success'] = false;

        if( !wp_verify_nonce( $nonce, 'my-nonce' ) ){

            $response['error'] = 'Verify Nonce Error';

        }else{

            $job_id             = absint($formData['job_id']);
            $resume_id          = absint($formData['resume_id']);
            $resume_name        = get_the_title( $resume_id );

            $title              = get_the_title( $job_id );
            $text               = htmlentities($formData['appl_message']);

            $current_user_id    = get_current_user_id();

            $to       = get_post_field( 'post_author', $resume_id );;
            $from     = $current_user_id;

            $user_to_info       = get_userdata( $to);
            $user_from_info     = get_userdata( $from);

            if ( $current_user_id ){
                $post_data = array(
                    'post_title'    => wp_strip_all_tags( $title ),
                    'post_content'  => wp_strip_all_tags( $text ),
                    'post_status'   => 'publish',
                    'post_author'   => $current_user_id,
                    'post_type'     => 'application_message',
                    // 'post_date'     => date ('Y-m-d H:i:s')
                );

                if ( $post_id = wp_insert_post( $post_data) ){

                    update_post_meta( $post_id, '_message_to', $to );
                    update_post_meta( $post_id, '_message_from', $from );
                    update_post_meta( $post_id, '_target_resume', $resume_id );
                    update_post_meta( $post_id, '_target_job', $job_id );
                    update_post_meta( $post_id, '_message_status', 'new' );

                    if (user_has_applied_for_job( $to, $job_id ) ){
                        $application_id = get_application_id_user_has_applied_for_job( $to, $job_id  );
                        update_post_meta( $post_id, '_target_application', $application_id );
                    }

                    $response['success']    = true;
                    $response['message_id'] = $post_id;
                    $response['to']         = $resume_name;

                    $to_email = $user_to_info->user_email;

                    $sbj = 'New message about '.$title;
                    $msg = $user_from_info." send you new message about ".$title."\n";

                    sent_notification($sbj, '', $to_email, $msg);

                }else{
                    $response['error'] = 'Message publication error';
                }
            }else{
                $response['error'] = 'Message user error';
            }

        }

        print_r(json_encode($response));
        wp_die();

    }
}

add_action('wp_ajax_send_message_to_candidate_by_job', 'send_message_to_candidate_by_job');
add_action('wp_ajax_nopriv_send_message_to_candidate_by_job', 'send_message_to_candidate_by_job');

if ( ! function_exists( 'messages_by_application' ) ) {

    function messages_by_application( $application_id ) {
        $current_user_id = get_current_user_id();

        $args = array(
            'post_type'        => 'application_message',
            'post_status'      => 'any',
            'posts_per_page'   => -1,
            'meta_key'         => '_target_application',
            'meta_value'       => $application_id,
            'order'            =>'ASC'
        );

        $resume_id      = get_job_application_resume_id( $application_id);

        $job_id         = wp_get_post_parent_id( $application_id );
        $candidate_id   = get_post_meta( $application_id, '_candidate_user_id', true );
        $employer_id    = get_post_field( 'post_author', $job_id );
        $employer_info   = get_userdata($employer_id);

        $from = $current_user_id;

        if( $current_user_id == $employer_id ) {
            $from   = $employer_id;
            $title  = get_the_title($resume_id);
        }
        elseif ( $current_user_id == $candidate_id )  {
            $from   = $candidate_id;
            $title  = $employer_info-> display_name;;
        }

        if( $from == $employer_id ) $to = $candidate_id; else $to = $employer_id;

        $messages = new WP_Query( $args );
        ?>
        <div class="msg_part">
            <?php

            if ( $messages->have_posts() ) {
                ?>
                <h2>Conversation</h2>
                <div class="msg_set">
                    <?php
                    while ( $messages->have_posts() ) {
                        $messages->the_post();
                        $status     = get_post_meta( $messages->post->ID, '_message_status', true );
                        $msg_to     = get_post_meta( $messages->post->ID, '_message_to', true );
                        $msg_from   = get_post_meta( $messages->post->ID, '_message_from', true );
                        $resume_id  = get_post_meta( $messages->post->ID, '_target_resume', true );
                        $from_user_msg_info = get_userdata($msg_from);

                        if (get_post_field( 'post_author', $resume_id ) == $msg_from){
                            $msg_title = get_the_title($resume_id);
                        }else{
                            $msg_title = $from_user_msg_info-> display_name;
                        }

                        ?>
                        <div class="msg msg-<?php echo $messages->post->ID; ?>">
                    <span class="msg_meta">
                        <i class="fa fa-commenting-o"></i><?php echo $msg_title; ?> <span class="msg_data"> <?php echo date( 'F j, Y H:i', strtotime(get_post_field( 'post_date', $messages->post->ID )) );?> </span>
                    </span>
                            <div class="msg_text">
                                <?php the_content();?>
                            </div>


                        </div>
                    <?php
                    }   //foreach
                    wp_reset_postdata();
                    ?>
                </div>

            <?php
            }  else{?>
                <div class="msg_set" style="display: none;"></div>
            <?php }
            ?>

            <form class="job-manager-application-message-form job-manager-form" method="post">

                <fieldset class="fieldset-message">
                    <label for="application-status-<?php echo esc_attr( $application_id ); ?>">Message to <?php echo $title; ?>:</label>
                    <div class="field">
                        <textarea name="appl_message" class = "job-manager-application-message-text"></textarea>
                    </div>
                </fieldset>

                <div class="clearfix"></div>
                <p>
                    <input class="button margin-top-15 wp_job_manager_message_to_application" type="submit" disabled="disabled"  name="wp_job_manager_message_to_application" value="<?php esc_attr_e( 'Send', 'workscout' ); ?>" />
                    <input type="hidden" name="application_id" value="<?php echo absint($application_id );?>" />
                    <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id);?>" />
                    <input type="hidden" name="resume_id" value="<?php echo esc_attr($resume_id);?>" />
                    <input type="hidden" name="msg_to" value="<?php echo $to;?>" />
                    <input type="hidden" name="msg_from" value="<?php echo $from; ?>" />

                    <?php wp_nonce_field('my-nonce'); ?>
                </p>
            </form>
        </div>
    <?php
    }   //messages_by_application
}   //function_exists( 'messages_by_application' )

if ( ! function_exists( 'application_has_messages' ) ) {

    function application_has_messages( $application_id ) {

        $args = array(
            'post_type'        => 'application_message',
            'post_status'      => 'any',
            'posts_per_page'   => 1,

            'meta_key'         => '_target_application',
            'meta_value'       => $application_id
        );

        $messages = new WP_Query( $args );

        if ( !$messages->have_posts() )   return false;

        return true;

    }
}

if ( ! function_exists( 'job_has_messages' ) ) {

    function job_has_messages( $job_id ) {

        $args = array(
            'post_type'        => 'application_message',
            'post_status'      => 'any',
            'posts_per_page'   => 1,
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key' => '_target_job',
                    'value' => absint( $job_id )
                ),
                array(
                    'key' => '_target_application',
                    'value' => '',
                    'compare' => 'NOT EXISTS'
                )
            )
        );

        $messages = new WP_Query( $args );

        if ( !$messages->have_posts() )   return false;

        return true;

    }
}

if ( ! function_exists( 'user_has_messages_without_application' ) ) {

    function user_has_messages_without_application( $user_id ) {

        $args = array(
            'post_type'        => 'application_message',
            'post_status'      => 'any',
            'posts_per_page'   => 1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_message_to',
                    'value' => $user_id
                ),
                array(
                    'key' => '_target_application',
                    'compare' => 'NOT EXISTS',
                    'value' => ''
                ),
                array(
                    'key' => '_target_job',
                    'compare' => 'EXISTS',
                    'value' => ''
                )
            )
        );

        $messages = new WP_Query( $args );

        if ( !$messages->have_posts() )   return false;

        return true;

    }
}


if ( ! function_exists( 'messages_by_job_and_resume' ) ) {

    function messages_by_job_and_resume( $job_id, $resume_id ) {

        $args = array(
            'post_type'        => 'application_message',
            'post_status'      => 'any',
            'posts_per_page'   => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_target_job',
                    'value' => $job_id
                ),
                array(
                    'key' => '_target_resume',
                    'value' => $resume_id
                )
            ),
            'order'            =>'ASC'
        );

        $messages = new WP_Query( $args );

        if ( $messages->have_posts() ) {?>
            <div class="msg_part">
                <h2>Conversation</h2>
                <div class="msg_set">
                    <?php
                    $current_user_id = get_current_user_id();
                    while ( $messages->have_posts() ) {

                        $messages->the_post();

                        $resume_id  = get_post_meta( $messages->post->ID, '_target_resume', true );
                        $msg_from   = get_post_meta( $messages->post->ID, '_message_from', true );
                        $status     = get_post_meta( $messages->post->ID, '_message_status', true );
                        $msg_to     = get_post_meta( $messages->post->ID, '_message_to', true );


                        $from_user_msg_info = get_userdata($msg_from);
                        $to_user_msg_info = get_userdata($msg_to);

                        if ( get_post_field( 'post_author', $resume_id ) == $msg_from){
                            $msg_title = get_the_title($resume_id);
                        }else{
                            $msg_title = $from_user_msg_info-> display_name;
                        }

                        ?>
                        <div class="msg msg-<?php echo $messages->post->ID; ?>">
                                <span class="msg_meta">
                                    <i class="fa fa-commenting-o"></i><?php echo $msg_title; ?> <span class="msg_data"> <?php echo date( 'F j, Y H:i', strtotime(get_post_field( 'post_date', $messages->post->ID )) );?> </span>
                                </span>
                            <div class="msg_text">
                                <?php the_content();?>
                            </div>
                        </div>
                    <?php
                    } ?>
                </div>
                <?php
                $to = $msg_to;
                if ( $current_user_id == $to ) $to = $msg_from;

                $to_name = get_userdata( $to );
                ?>
                <form class="job-manager-application-message-form job-manager-form" method="post">

                    <fieldset class="fieldset-message">
                        <label for="application-status-<?php echo esc_attr( $job_id ); ?>">Message to <?php echo $to_name-> display_name; ?>:</label>
                        <div class="field">
                            <textarea name="appl_message" class = "job-manager-application-message-text"></textarea>
                        </div>
                    </fieldset>

                    <div class="clearfix"></div>
                    <p>
                        <input class="button margin-top-15 wp_job_manager_message_to_application" type="submit" disabled="disabled"  name="wp_job_manager_message_to_application" value="<?php esc_attr_e( 'Send', 'workscout' ); ?>" />

                        <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id);?>" />
                        <input type="hidden" name="resume_id" value="<?php echo esc_attr($resume_id);?>" />
                        <input type="hidden" name="msg_to" value="<?php echo $to;?>" />
                        <input type="hidden" name="msg_from" value="<?php echo $current_user_id; ?>" />

                        <?php wp_nonce_field('my-nonce'); ?>
                    </p>
                </form>
            </div>
            <?php
            wp_reset_postdata();
        }
    }   //messages_by_job
}   //function_exists( 'messages_by_application' )

if ( ! function_exists( 'application_has_messages' ) ) {

    function application_has_messages( $application_id ) {

        $args = array(
            'post_type'        => 'application_message',
            'post_status'      => 'any',
            'posts_per_page'   => 1,

            'meta_key'         => '_target_application',
            'meta_value'       => $application_id
        );

        $messages = new WP_Query( $args );

        if ( !$messages->have_posts() )   return false;

        return true;

    }
}

if ( ! function_exists( 'get_jobs_with_messages_without_application' ) ) {

    function get_jobs_with_messages_without_application( $user_id ) {

        $args = array(
            'post_type'        => 'application_message',
            'post_status'      => 'any',
            'posts_per_page'   => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_message_to',
                    'value' => $user_id
                ),
                array(
                    'key' => '_target_job',
                    'compare' => 'EXISTS',
                ),
                array(
                    'key' => '_target_application',
                    'compare' => 'NOT EXISTS',
                    'value' => ''
                )
            )
        );


        $messages = new WP_Query( $args );

        if ( !$messages->have_posts() )   return false;
        $jobs = array();

        while ( $messages -> have_posts()  ):
            $messages ->the_post();
            $job = array();

            $job_id             = get_post_meta($messages->post->ID,'_target_job', true);
            $resume_id          = get_post_meta($messages->post->ID,'_target_resume', true);
            $job['job_status']  = get_post_field('post_status',$messages->post->ID,'db');
            $job['title']       = get_the_title($job['job_id']);
            $resume_author      = get_post_field('post_author',$job['resume_id'],'db');
            $job['applied_for'] = user_has_applied_for_job( $resume_author, $job['job_id'] );

            if ( !array_key_exists($job_id,$jobs ) ){
                $jobs[$job_id][$resume_id][]= $job;
            }else{
                $current_job = $jobs[$job_id];
                if ( !array_key_exists($resume_id,$current_job ) ){

                    $jobs[$job_id][$resume_id][]= $job;

                }
            }

        endwhile;

        wp_reset_postdata();

        return $jobs;

    }
}