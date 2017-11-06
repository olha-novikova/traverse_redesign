<?php
/**
 * Template Name: Page My Listings
 */
if (is_user_logged_in() ){
    $user = wp_get_current_user();
    if ( !in_array( 'employer', (array) $user->roles ) && !in_array( 'administrator', (array) $user->roles ) ) wp_redirect(home_url());
}else wp_redirect(home_url());
get_header('new');
global $wpdb;
get_sidebar();

?>
    <main class="main">
        <?php get_template_part('template-parts/page-header')?>
        <div class="content tabs-content">
            <section class="section section_listing app-content">
                <div class="section_wrap_titles">
                    <div class="section__titles">
                        <a class="button button__title app-link active" href="#listings">My Campaigns</a>
                        <a class="button button__title app-link" href="#pitches">Pitches<span class="button__badge"><?php echo get_total_count_applications();?></span></a>
                    </div>
                </div>
            </section>
            <section class="section section_campaigns app-tabs">

                <section class="section__container app-tab-content opened" id="listings">
                    <?php $jobs = get_job_listings_list(); ?>
                    <?php if ( ! $jobs ) : 
                        $submit_job_page = get_option('job_manager_submit_job_form_page_id'); ?>
                        <p style="padding: 1.45vw;"><?php esc_html_e( 'You’ll need to add a listing before you add influencers!', 'workscout' ); ?> </p>
                        <div class="after-table">
                            <a  href="<?php echo get_permalink($submit_job_page) ?>" class="button button_green large_text">Create Listing</a>
                        </div>
                    <?php else : ?>
                        <div class="table">
                            <div class="table__head">
                                <div class="table__row table__row_header">
                                    <div class="table__header">
                                        <p class="table__text">Campaign</p>
                                    </div>
                                    <div class="table__header">
                                        <p class="table__text">Location</p>
                                    </div>
                                    <div class="table__header">
                                        <p class="table__text">Campaign Start Date</p>
                                    </div>
                                    <div class="table__header">
                                        <p class="table__text">Campaign Description</p>
                                    </div>
                                    <div class="table__header">
                                        <p class="table__text"># of Influencers</p>
                                    </div>
                                </div>
                            </div>
                            <div class="table__body">
                                <?php foreach ( $jobs as $job ) :?>
                                    <div class="table__row table__row_body job_<?php echo $job->ID;  ?>">
                                        <div class="table__data">
                                            <p class="table__text"><?php echo esc_html($job->post_title); ?><br>(<?php the_job_status( $job ); ?>)</p>
                                        </div>
                                        <div class="table__data">
                                            <p class="table__text"><?php
                                                $location = get_post_meta($job->ID, '_job_location', TRUE);
                                                if ( $location )echo wp_kses_post( $location );?>
                                            </p>
                                        </div>
                                        <div class="table__data">
                                            <p class="table__text"> <?php echo date_i18n( 'M d, Y  h:i A', strtotime( $job->post_date ) ); ?></p>
                                        </div>
                                        <div class="table__data">
                                            <p class="table__text">
                                                <?php
                                                $excerpt = wp_trim_words ( strip_shortcodes( $job->post_content), 15  );
                                                echo $excerpt;
                                                ?>
                                            </p>
                                        </div>
                                        <div class="table__data">
                                            <div class="table__influencers">
                                                <?php
                                                $count = get_job_application_count( $job->ID );
                                                for ( $i=1; $i<$count; $i++){
                                                    echo '<div class="table__influencer"></div>';
                                                }
                                                ?>
                                                <div class="table__influencer">
                                                    <?php
                                                    echo '<a href="'.home_url('/my-listings').'">';?>
                                                    <div class="table__influencer__number">
                                                        <?php echo ( $count > 0 ? "+".$count: "0" ); ?>
                                                    </div>
                                                    <?php echo "</a>"; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table__data">
                                            <div class="table__buttons">
                                                <?php if ( $job->post_status == 'publish' ) : ?>
                                                    <a class="button button_green" href="<?php echo get_permalink( $job->ID ); ?>">View Campaign</a>
                                                <?php endif; ?>
                                                <?php if ( $job->post_status == 'publish' ):
                                                    $dash_url = get_permalink(get_option( 'job_manager_job_dashboard_page_id' ));
                                                    $action_url = add_query_arg( array( 'action' => 'edit', 'job_id' => $job->ID ),$dash_url );
                                                    echo '<a class="button button_white job-dashboard-action-edit" href="' . esc_url( $action_url ) . '">Edit Campaign</a>';
                                                endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div><!--table__body-->
                        </div><!--table-->
                    <?php endif; ?>
                </section>
                <section class="section__container app-tab-content" id="pitches">
                    <?php
                    $jobs_with_applications = get_applications();
                    ?>
                    <div class="table">
                        <div class="table__head">
                            <div class="table__row table__row_header">
                                <div class="table__header">
                                    <p class="table__text">Campaign</p>
                                </div>
                                <div class="table__header">
                                    <p class="table__text">Location</p>
                                </div>
                                <div class="table__header">
                                    <p class="table__text">Campaign Date</p>
                                </div>
                                <div class="table__header">
                                    <p class="table__text">Campaign Description</p>
                                </div>
                                <div class="table__header">
                                    <p class="table__text"># of Pitches</p>
                                </div>
                            </div>
                        </div>
                        <div class="table__body"> <?php
                        foreach ( $jobs_with_applications as $job_data ):
                            $job = $job_data['job'];?>
                            <div class="table__row table__row_body job_<?php echo $job->ID;  ?>">
                                <div class="table__data">
                                    <p class="table__text"><?php echo esc_html($job->post_title); ?>, for <?php echo get_post_meta($job->ID,'_company_name', true);?> (<?php the_job_status( $job ); ?>)</p>
                                    <span class="button__badge"><?php if ( $count = get_job_application_count( $job->ID )) echo  $count;?></span>
                                </div>
                                <div class="table__data">
                                    <p class="table__text"><?php
                                        $location = get_post_meta($job->ID, '_job_location', TRUE);
                                        if ( $location )echo wp_kses_post( $location );?></p>
                                </div>
                                <div class="table__data">
                                    <p class="table__text"><?php echo date_i18n( 'M d, Y  h:i A', strtotime( $job->post_date ) ); ?></p>
                                </div>
                                <div class="table__data">
                                    <p class="table__text">
                                        <?php
                                        $excerpt = wp_trim_words ( strip_shortcodes( $job->post_content), 15  );
                                        echo $excerpt;
                                        ?>
                                    </p>
                                </div>
                                <div class="table__data">
                                    <div class="table__influencers">
                                        <?php
                                        $count = get_job_application_count( $job->ID );
                                        for ( $i=1; $i<$count; $i++){
                                            echo '<div class="table__influencer"></div>';
                                        }
                                        ?>
                                        <div class="table__influencer">
                                            <?php
                                            echo '<a href="'.home_url('/my-listings').'">';?>
                                            <div class="table__influencer__number">
                                                <?php echo ( $count > 0 ? "+".$count: "0" ); ?>
                                            </div>
                                            <?php echo "</a>"; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="table__data">
                                    <div class="table__buttons">
                                        <?php if ( $job->post_status == 'publish' ) : ?>
                                            <a class="button button_green" href="<?php echo get_permalink( $job->ID ); ?>">View Campaign</a>
                                        <?php endif; ?>
                                        <?php  if ( get_job_application_count( $job->ID ) ) :?>
                                            <a href="#job-<?php echo $job->ID; ?>" class="button button_white pitches_toggle">View Pitches</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="section__pitches">
                                <?php
                                $applications = $job_data['applications'];
                                if ( $applications ):
                                    $total_count = count ( $applications );

                                    $last_application = get_last_application( $job->ID );
                                    ?>
                                <div class="section__pitches_line">
                                    <div class="table__influencers">
                                        <?php
                                        for ( $i=1; $i<=$total_count; $i++){
                                            echo '<div class="table__influencer"></div>';
                                        }
                                        ?>

                                    </div>
                                    <div class="section__persons_items">
                                        <a class="person__single" href="#"><?php echo $last_application->post_title;?></a>
                                        <span><?php echo ( ($total_count -1) > 0 ? ('and '.($total_count -1).' more pitched this'):'' ); ?> </span>
                                    </div>
                                </div>
                                <div class="section__persons" id="job-<?php echo esc_attr( $job->ID ); ?>">
                                    <?php
                                    global $wp_post_statuses;
                                    foreach ( $applications as $application ): ?>
                                    <div class="section__list_person">
                                        <div class="section__list_header">
                                            <div class="section_left">
                                                <div class="person_info">
                                                    <div class="person_image">
                                                        <div class="person_photo"></div>
                                                    </div>
                                                    <div class="person_and_time">
                                                        <?php if ( ( $resume_id = get_job_application_resume_id( $application->ID ) ) && 'publish' === get_post_status( $resume_id ) && function_exists( 'get_resume_share_link' ) && (
                                                        $share_link = get_resume_share_link( $resume_id ) ) ) {?>
                                                            <a class="person_name" href="<?php echo $share_link;?>"><?php echo  $application->post_title; ?></a>
                                                        <?php }else{ ?>
                                                            <a class="person_name" href="#"><?php echo  $application->post_title; ?></a>
                                                        <?php }; ?>

                                                        <span class="person_time_ago">
                                                            <?php printf( esc_html__( '%s ago', 'workscout' ), human_time_diff( get_post_time( 'U', true, $application->ID ), current_time( 'timestamp' ) ) ); ?>
                                                        </span>

                                                        <span class="person_status status_<?php echo $application->post_status;?>">
                                                            <?php echo $wp_post_statuses[ $application->post_status ]->label; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="section_right">
                                                ...
                                            </div>
                                        </div>
                                        <div class="section__list_content">
                                            <p><?php job_application_content( $application ); ?></p>
                                        </div>
                                        <div class="section__list_footer">
                                            <?php $user_id = get_post_meta( $application->ID , "_candidate_user_id", true); ?>

                                            <?php if($application->post_status=="new"){ ?>
                                                <a href="#hire-dialod-<?php echo $application->ID;?>" class="button button_green open-popup-hire">Hire</a>
                                            <?php }elseif( $application->post_status=="in_review" ){
                                                ?>
                                                <a href="#review-<?php echo esc_attr( $application->ID );?>" title="<?php esc_html_e( 'Review and Approve', 'workscout' ); ?>" class="button button_green open-popup-hire"><?php esc_html_e( 'Review and Approve', 'workscout' ); ?></a>
                                            <?php } ?>

                                            <div class="openchat button button_orange" data-reciever-id="<?php echo $user_id;?>" data-job-id="<?php echo esc_attr( $job->ID )?>" data-job-name="<?php echo esc_html($job->post_title);?>">Message</div>

                                            <?php if( $application->post_status=="new"){ ?>
                                                <div id = "hire-dialod-<?php echo $application->ID;?>" class="small-dialog zoom-anim-dialog mfp-hide apply-popup ">
                                                    <div class="small-dialog-headline">
                                                        <h2>Pitch Status Change</h2>
                                                    </div>
                                                    <div class="small-dialog-content">
                                                        <p>You are about to hire <strong><?php echo  $application->post_title; ?></strong> for  <strong><?php echo  $job->post_title; ?></strong></p>
                                                        <p>Would you like to proceed?</p>

                                                        <form class="inline job-manager-application-edit-form job-manager-form" method="post">
                                                            <input type="hidden" name="application_rating"/>
                                                            <input type="hidden" name="application_status" value="hired" />
                                                            <input type="hidden" name="application_id" value="<?php echo absint( $application->ID ); ?>" />
                                                            <?php wp_nonce_field( 'edit_job_application' ); ?>
                                                            <input class="button button_blue" type="submit" name="wp_job_manager_edit_application" value="<?php esc_html_e( 'Yes, accept', 'workscout' ); ?>" />
                                                        </form>
                                                        <div class="button button_orange mfp-close">Cancel</div>

                                                    </div>
                                                </div>
                                            <?php }elseif( $application->post_status=="in_review" ){
                                                ?>
                                                <div id = "review-<?php echo $application->ID;?>" class="small-dialog zoom-anim-dialog mfp-hide">
                                                    <div class="small-dialog-headline">
                                                        <h2>Review and Approve</h2>
                                                    </div>
                                                    <div class="small-dialog-content">
                                                        <p>Please, review this Pitch</p>
                                                        <p class="rv_msg"><?php echo get_post_meta($application->ID, '_review_msg', true); ?></p></p>

                                                        <form class="inline job-manager-application-edit-form job-manager-form" method="post">
                                                            <input type="hidden" name="application_rating"/>
                                                            <input type="hidden" name="application_status" value="completed" />
                                                            <input type="hidden" name="application_id" value="<?php echo absint( $application->ID ); ?>" />
                                                            <?php wp_nonce_field( 'edit_job_application' ); ?>
                                                            <input class="button" type="submit" name="wp_job_manager_edit_application" value="<?php esc_html_e( 'Approve', 'workscout' ); ?>" />
                                                        </form>
                                                        <div class="button button_orange mfp-close">Cancel</div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php
                            endif;
                        endforeach;?>
                        </div>
                    </div>
                </section>
            </section>
        </div> <!-- content -->
        <script>
            jQuery(document).ready(function ($) {
                jQuery('.open-popup-hire').magnificPopup({
                    type:'inline',
                    midClick: true
                });
            });
        </script>
    </main>
<?php
get_footer('new');
?>