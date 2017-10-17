<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 28.08.17
 * Time: 16:19
 * To change this template use File | Settings | File Templates.
 */
$current_user = get_current_user_id();
wp_enqueue_script( 'message-by-job' );
if ( user_has_messages_without_application($current_user)){
    ?>
    <h2>Jobs you haven't applied:</h2>
    <?php

    $probably_applications = get_jobs_with_messages_without_application($current_user);

    foreach ( $probably_applications as $job_id => $job ){
        foreach ( $job as $resume_id => $resume ){
        ?>
        <div class="application job-application" id="application-<?php echo esc_attr( $job_id ); ?>">
            <div class="app-content">

                <div class="info">
                    <?php

                    $size = 90;

                    if ( $resume_id && 'publish' === get_post_status( $resume_id  ) && function_exists( 'get_the_candidate_photo' ) ) {
                        $photo = get_the_candidate_photo( $resume_id );

                        if ( $photo ) {
                            $photo = job_manager_get_resized_image( $photo, $size );
                            $image = '<img class="candidate_photo" src="' . $photo . '" alt="Photo" />';
                        }
                        else
                            $image = '<img class="candidate_photo" src="' . apply_filters( 'resume_manager_default_candidate_photo', RESUME_MANAGER_PLUGIN_URL . '/assets/images/candidate.png' ) . '" alt="Logo" />';
                    } else {
                        $author = get_post_field('post_author',$resume_id, 'db');
                        $email = get_the_author_meta('email', $author);
                        $image =  $email ? get_avatar( $email, $size ) : '';
                    }
                    echo $image;
                    ?>

                    <?php if ( $resume['job_status'] == 'publish' ) { ?>
                        <a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>"><?php echo esc_html( get_post_field('post_title',$job_id,'display' ) ); ?></a>
                    <?php } else {
                        echo esc_html(  get_post_field('post_title',$job_id,'display' ) );
                    } ?>
                </div>
                <div class="buttons">

                    <?php
                    if (job_has_messages( $job_id )){?>
                        <a href="#message-<?php echo esc_attr( $job_id );?>" title="<?php esc_html_e( 'Conversation', 'workscout' ); ?>" class="button gray app-link job-application-toggle-notes"><i class="fa fa-sticky-note"></i> <?php esc_html_e( 'Conversation', 'workscout' ); ?></a>
                    <?php }

                    if ( $resume['applied_for']== 0 && candidates_can_apply( $job_id ) &&  $apply = get_the_job_application_method( $job_id )) {

                        wp_enqueue_script( 'wp-job-manager-job-application' );
                        ?>

                        <?php do_action( 'job_application_start', $apply ); ?>


                        <a href="#apply-dialog-<?php echo $job_id."-".$resume_id;?>" class="small-dialog popup-with-zoom-anim button"><?php esc_html_e( 'Apply for job', 'workscout' ); ?></a>

                        <div id="apply-dialog-<?php echo $job_id."-".$resume_id;?>" class="small-dialog zoom-anim-dialog mfp-hide apply-popup">
                            <div class="small-dialog-headline">
                                <h2><?php esc_html_e('Pitch Listing','workscout') ?></h2>
                            </div>
                            <div class="small-dialog-content">

                                <form class="job-manager-application-form-directly job-manager-form" method="post" enctype="multipart/form-data">
                                    <?php do_action( 'job_application_form_fields_start' ); ?>

                                    <p>
                                        <label>Message:</label>
                                        <textarea name="application_message" cols="20" rows="4" required=""></textarea>
                                    </p>

                                    <?php do_action( 'job_application_form_fields_end' ); ?>

                                    <p class="send-app-btn" >
                                        <input type="submit" name="wp_job_manager_send_application_directly" value="<?php esc_attr_e( 'Apply job', 'workscout' ); ?>" />
                                        <input type="hidden" name="job_id" value="<?php echo absint( $job_id ); ?>" />
                                        <input type="hidden" name="resume_id" value="<?php echo absint( $resume_id ); ?>" />
                                        <input type="hidden" name="wp_job_manager_send_application_directly" value="<?php esc_attr_e( 'Apply job', 'workscout' ); ?>" />
                                    </p>
                                </form>
                            </div>
                        </div>


                        <?php do_action( 'job_application_end', $apply ); ?>

                    <?php
                    }
                    ?>
                </div>
                <div class="clearfix"></div>

            </div>

            <div class="app-tabs">

                <a href="#" class="close-tab button gray"><i class="fa fa-close"></i></a>

                <div class="app-tab-content"  id="message-<?php echo esc_attr( $job_id);?>">
                    <?php
                    if (job_has_messages( $job_id))
                        messages_by_job_and_resume( $job_id,  $resume_id);
                    ?>
                </div>
            </div>

            <!-- Footer -->
            <div class="app-footer">
                <ul class="meta">
                    <li><i class="fa fa-calendar"></i> <?php echo esc_html( get_the_date( get_option( 'date_format' ),$job_id ) ); ?></li>
                </ul>
                <div class="clearfix"></div>

            </div>
        </div>

    <?php
        }
    }

}

?>


<h2>You applied for:</h2>
