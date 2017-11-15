<?php
$user = wp_get_current_user();
$submit_job_page = get_permalink(get_option('job_manager_submit_job_form_page_id'));
$employer_dashboard_page_id = get_option( 'job_manager_job_dashboard_page_id' );
$canidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
?>
<sidebar class="sidebar">
  <div class="logo">
    <a href="<?php echo  home_url(); ?>" <p class="logo__text">r</p></a>
  </div>
    <ul class="icon-list">
        <?php if ( in_array( 'candidate', (array) $user->roles )) : ?>
            <li class="icon-list__element"><a href="<?php echo  get_permalink( $canidate_dashboard_page_id); ?>" title="Dashboard"><i class="icon icon_menu"></i></a></li>
        <?php endif; ?>

        <?php if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles )) : ?>
            <li class="icon-list__element"><a href="<?php echo  get_permalink( $employer_dashboard_page_id); ?>" title="Dashboard"><i class="icon icon_menu"></i></a></li>
        <?php endif; ?>

        <?php if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) : ?>
            <li class="icon-list__element"><a href="<?php esc_html_e($submit_job_page) ?>" title="Create Listing"><i class="icon icon_newsfeed"></i></a></li>
        <?php endif; ?>

        <?php if ( in_array( 'candidate', (array) $user->roles )) : ?>
            <li class="icon-list__element"><a href="<?php echo home_url('/my-pitches'); ?>" title="My Pitches"><i class="icon icon_calendar"></i></a></li>
        <?php endif; ?>

        <?php if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) : ?>
            <li class="icon-list__element"><a href="<?php echo  home_url('/my-listings'); ?>" title="My Listings"><i class="icon icon_calendar"></i></a></li>
        <?php endif; ?>

        <li class="icon-list__element"><a href="<?php echo  home_url('/messages'); ?>" title="Messages"><i class="icon icon_friends"></i></a></li>

        <li class="icon-list__element"><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" title="Account"><i class="icon icon_widgets"></i></a></li>
    </ul>
</sidebar>