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
                        <a class="button button__title app-link" href="#pitches">Pitches<span class="button__badge"><?php echo  get_total_count_applications( array('new') );?></span></a>
                        <a class="button button__title app-link" href="#active">Active Campaigns<span class="button__badge"><?php echo  get_total_count_applications( array('hired') );?></span></a>
                        <a class="button button__title app-link" href="#completed">Completed Campaigns<span class="button__badge"><?php echo  get_total_count_applications( array('completed', 'in_review') );?></span></a>
                    </div>
                </div>
            </section>
            <section class="section section_campaigns app-tabs">
                <section class="section__container app-tab-content opened" id="listings">
                    <?php $jobs = get_job_listings_list('publish'); ?>
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
                                                $count =  ( get_post_meta($job->ID, '_applications_number', TRUE) ? get_post_meta($job->ID, '_applications_number', TRUE) : 1 );
                                                for ( $i=1; $i<$count; $i++){
                                                    echo '<div class="table__influencer"></div>';
                                                }
                                                ?>
                                                <div class="table__influencer">
                                                    <?php
                                                    echo '<a href="'.home_url('/my-listings').'">';?>
                                                    <div class="table__influencer__number">
                                                        <?php echo ( $count > 0 ? $count: "0" ); ?>
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
                    $jobs_with_applications = get_applications( array('new') );
                    $statuses = array('new') ;
                    if ( $jobs_with_applications ) {?>
                    <?php include ( locate_template('template-parts/campaign-pithes.php'));?>
                    <?php } else { ?>
                    <?php include ( locate_template('template-parts/campaign-pithes-none.php'));?>
                    <?php } ?>
                </section>
                <section class="section__container app-tab-content" id="active">
                    <?php
                    $jobs_with_applications = get_applications( array('hired') );
                    $statuses = array('hired') ;
                    if ( $jobs_with_applications ) {?>
                        <?php include ( locate_template('template-parts/campaign-pithes.php'));?>
                    <?php } ?>
                </section>
                <section class="section__container app-tab-content" id="completed">
                    <?php  $jobs_with_applications = get_applications( array('completed', 'in_review') );
                    $statuses = array('completed', 'in_review') ;
                    if ( $jobs_with_applications ) {?>
                        <?php include ( locate_template('template-parts/campaign-pithes.php'));?>
                    <?php } ?>
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