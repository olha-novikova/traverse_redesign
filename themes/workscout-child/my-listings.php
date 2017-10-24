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
get_sidebar();?>
    <main class="main">
        <?php get_template_part('template-parts/page-header')?>
        <div class="content">
        <section class="section section_listing">
            <div class="section_wrap_titles">
                <div class="section__titles">
                    <a class="button button__title" href="#">My Listings</a>
                    <a class="button button__title active" href="#">New Pitches<span class="button__badge"><?php echo get_total_count_applications();?></span></a>
                </div>
            </div>
        </section>
        <?php
        $jobs_with_applications = get_applications();
        ?>
            <section class="section section_campaigns">
                <div class="section__container">
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
                                    <p class="table__text">Influencers</p>
                                </div>
                            </div>
                        </div>
                        <div class="table__body"> <?php
                        foreach ( $jobs_with_applications as $job_data ):
                            $job = $job_data['job'];?>
                            <div class="table__row table__row_body job_<?php echo $job->ID;  ?>"">
                                <div class="table__data">
                                    <p class="table__text"><?php echo esc_html($job->post_title); ?>, for Brand Name</p>
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
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer">
                                            <?php
                                            $count = get_job_application_count( $job->ID );
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
                                            <a href="#" class="button button_white">View Pitches</a>
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
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                    </div>
                                    <div class="section__persons_items">
                                        <a class="person__single" href="#"><?php echo $last_application->post_title;?></a>
                                        <span><?php echo ( ($total_count -1) > 0 ? ('and '.($total_count -1).' more pitched this'):'' ); ?> </span>
                                    </div>
                                </div>
                                <div class="section__persons" id="application-<?php echo esc_attr( $application->ID ); ?>">
                                    <?php foreach ( $applications as $application ): ?>
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
                                            <a class="button button_link" href="#">Reply</a>
                                            <a class="button button_link" href="#">Attached Photos (2)</a>
                                            <a class="button button_link" href="#">Attached Videos (3)</a>
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
                </div>
            </section>
        </div> <!-- content -->
    </main>
<?php
get_footer('new');
?>