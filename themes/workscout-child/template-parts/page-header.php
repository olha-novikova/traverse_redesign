<?php
/**
 * Header on pages
 */

$user = wp_get_current_user();
$user_id = get_current_user_id();
?>
<section class="section_profile">
    <div class="profile__background"><img src="<?php //echo get_main_image( $user_id ); ?>" alt="" class="profile__background__image"/>
        <div class="profile__background__gradient"></div>
        <div class="profile__button profile__button_add"></div>
        <div class="profile__button profile__button_message"></div>
        <div class="profile__button profile__button_settings"></div>
    </div>
    <div class="profile__action">
        <ul class="profile__links">

            <?php
            if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
                $employer_dashboard_page_id = get_option( 'job_manager_job_dashboard_page_id' );
                $canidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
                $submit_job_page = get_option('job_manager_submit_job_form_page_id');
                $influencers = get_option('resume_manager_resumes_page_id');
                $brand_name = get_user_meta( $user_id, 'company_name', true );
                ?>
                <li class="profile__link"><a href="<?php echo  get_permalink( $employer_dashboard_page_id); ?>" class="profile__link__a">Dashboard</a></li>
                <li class="profile__link"><a href="<?php echo get_permalink($submit_job_page) ?>" class="profile__link__a">Create Listing</a></li>
                <li class="profile__link"><a href="<?php echo get_permalink($influencers) ?>" class="profile__link__a">Influencers</a></li>
                <li class="profile__link profile__link_brand"><a href="#" class="profile__brandname"> <?php echo $brand_name; ?> </a></li>
                <li class="profile__link"><a href="<?php echo  home_url('/my-listings'); ?>" class="profile__link__a">My Listings</a></li>
                <li class="profile__link"><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="profile__link__a">Account</a></li>
            <?php endif;

            if ( in_array( 'candidate', (array) $user->roles ) /*|| in_array( 'administrator', (array) $user->roles ) */): ?>
                <li class="profile__link"><a href="<?php echo  get_permalink( $canidate_dashboard_page_id); ?>" class="profile__link__a">Dashboard</a></li>
                <li class="profile__link"><a href="#" class="profile__link__a">Find Opportunities</a></li>
                <li class="profile__link"><a href="<?php echo home_url('/my-pitches'); ?>" class="profile__link__a">My Pitches</a></li>
                <li class="profile__link profile__link_brand"><a href="#" class="profile__brandname">Brand Name / Agency </a></li>
                <li class="profile__link"><a href="<?php echo  home_url('/my-balance'); ?>" class="profile__link__a">Balance/Cash Out</a></li>
                <li class="profile__link"><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="profile__link__a">Account</a></li>

            <?php endif;
            ?>
        </ul>
    </div>
</section>