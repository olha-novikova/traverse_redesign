<?php get_template_part( 'template-parts/messages_without_applications'); ?>
<?php foreach ( $applications as $application ):
    global $wp_post_statuses;

    $application_id = $application->ID;
    $job_id         = wp_get_post_parent_id( $application_id );
    $job            = get_post( $job_id );
    $job_title      = get_post_meta( $application_id, '_job_applied_for', true ); ?>
    <div class="application job-application" id="application-<?php echo esc_attr( $application->ID ); ?>">
        <div class="app-content">

            <!-- Name / Avatar -->
            <div class="info">
                <?php echo get_job_application_avatar( $application->ID, 90 ) ?>

                <?php if ( $job && $job->post_status == 'publish' ) { ?>
                    <a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>"><?php echo esc_html( $job_title ); ?></a>
                <?php } else {
                    echo esc_html( $job_title );
                } ?>
            </div>

            <div class="buttons">
                <?php
                if (application_has_messages( $application->ID )){?>
                    <a href="#message-<?php echo esc_attr($application->ID );?>" title="<?php esc_html_e( 'Conversation', 'workscout' ); ?>" class="button gray app-link job-application-toggle-notes"><i class="fa fa-sticky-note"></i> <?php esc_html_e( 'Conversation', 'workscout' ); ?></a>
                <?php }?>
                <a href="#details-<?php echo esc_attr($application->ID );?>" title="<?php _e( 'Pitch Message', 'wp-job-manager-applications' ); ?>" class="button gray app-link job-application-toggle-content"><i class="fa fa-plus-circle"></i> <?php _e( 'Pitch Message', 'wp-job-manager-applications' ); ?></a>
                <?php if($application->post_status=="hired"){ ?>
                    <a href="#review-<?php echo esc_attr($application->ID );?>" title="<?php esc_html_e( 'Send for Review', 'workscout' ); ?>" class="button gray app-link job-application-toggle-content"><i class="fa fa-plus-circle"></i><?php esc_html_e( 'Send for Review', 'workscout' ); ?></a>
                <?php }elseif($application->post_status=="in_review"){?>
                    <a href="#review-<?php echo esc_attr($application->ID );?>" title="<?php esc_html_e( 'Review Message', 'workscout' ); ?>" class="button gray app-link job-application-toggle-content"><i class="fa fa-plus-circle"></i><?php esc_html_e( 'Review Message', 'workscout' ); ?></a>
                <?php }?>
            </div>
            <div class="clearfix"></div>

        </div>

        <div class="app-tabs">

            <a href="#" class="close-tab button gray"><i class="fa fa-close"></i></a>

            <div class="app-tab-content"  id="details-<?php echo esc_attr($application->ID );?>">
                <?php echo wpautop( wp_kses_post($application->post_content ) ); ?>
            </div>

            <div class="app-tab-content"  id="message-<?php echo esc_attr($application->ID );?>">
                <?php
                if (application_has_messages( $application->ID ))
                    messages_by_application( $application->ID );
                ?>
            </div>
            <div class="app-tab-content"  id="review-<?php echo esc_attr($application->ID );?>">
                <?php if($application->post_status=="hired"){?>
                    <form class="inline job-manager-application-review-form job-manager-form" method="post">

                        <fieldset class="fieldset-status"><label for="application-status-<?php esc_attr_e( $application->ID ); ?>"><?php _e( 'Review Message', 'wp-job-manager-applications' ); ?>:</label>
                            <div class="field">
                                <textarea class="application-review-msg" name="application-review-msg"></textarea>
                            </div>
                        </fieldset>
                        <input type="hidden" name="application_status" value="in_review" />
                        <input type="hidden" name="application_id" value="<?php echo absint( $application->ID ); ?>" />
                        <input type="hidden" name="wp_job_manager_review_application"  value="1" />
                        <?php wp_nonce_field( 'edit_job_application' ); ?>
                        <fieldset class="fieldset-status">
                            <input class="button wp_job_manager_review_application" type="button" value="<?php esc_html_e( 'On Review', 'workscout' ); ?>" />
                        </fieldset>
                    </form>
                <?php }elseif($application->post_status=="in_review"){?>
                    <?php echo wpautop( get_post_meta($application->ID, '_review_msg', true) ); ?>
                <?php }?>
            </div>
        </div>

        <!-- Footer -->
        <div class="app-footer">
            <?php $rating = get_job_application_rating( $application->ID ); ?>
            <div class="rating <?php echo workscout_get_rating_class($rating); ?>">
                <div class="star-rating"></div>
                <div class="star-bg"></div>
            </div>
            <?php global $wp_post_statuses; ?>
            <ul class="meta">
                <li><i class="fa fa-file-text-o"></i><?php echo esc_html( $wp_post_statuses[ get_post_status( $application_id ) ]->label ); ?></li>
                <li><i class="fa fa-calendar"></i> <?php echo esc_html( get_the_date( get_option( 'date_format' ), $application_id ) ); ?></li>
            </ul>
            <div class="clearfix"></div>

        </div>

    </div>
    <?php endforeach; ?>

<?php get_job_manager_template( 'pagination.php', array( 'max_num_pages' => $max_num_pages ) ); ?>

<?php wp_reset_postdata(); ?>