<?php
/**
 * Header on pages
 */

$user = wp_get_current_user();
$user_id = get_current_user_id();
?>
<section class="section_profile">
    <div class="profile__background">
        <div class="profile__button profile__button_add" style="background-image: url('<?php echo get_main_image( $user_id ); ?>'); background-size: cover">
        </div>
        <a href="<?php echo  home_url('/messages'); ?>"class="profile__button profile__button_message"></a>
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="profile__button profile__button_settings"></a>
    </div>
    <div class="profile__action">
        <ul class="profile__links">

            <?php
            if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
                $employer_dashboard_page_id = get_option( 'job_manager_job_dashboard_page_id' );
                $submit_job_page = get_option('job_manager_submit_job_form_page_id');
                $influencers = get_option('resume_manager_resumes_page_id');
                $brand_name = get_user_meta( $user_id, 'company_name', true );
                $brand_name = get_user_meta( $user_id, 'company_name', true );
                ?>
                <li class="profile__link"><a href="<?php echo  get_permalink( $employer_dashboard_page_id); ?>" class="profile__link__a">Dashboard</a></li>
                <li class="profile__link"><a href="<?php echo get_permalink($submit_job_page) ?>" class="profile__link__a">Create Listing</a></li>
                <li class="profile__link"><a href="<?php echo get_permalink($influencers) ?>" class="profile__link__a">Influencers</a></li>
                <li class="profile__link profile__link_brand"><span class="profile__brandname"> <?php echo $brand_name; ?> </span></li>
                <li class="profile__link"><a href="<?php echo  home_url('/my-listings'); ?>" class="profile__link__a">My Campaigns</a></li>
                <li class="profile__link"><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="profile__link__a">Account</a></li>
            <?php endif;

            if ( in_array( 'candidate', (array) $user->roles ) ):
                $canidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
                $candidate_name = get_user_meta( $user_id, 'first_name', true )." ". get_user_meta( $user_id, 'last_name', true );
                ?>
                <li class="profile__link"><a href="<?php echo  get_permalink( $canidate_dashboard_page_id); ?>" class="profile__link__a">Dashboard</a></li>
                <li class="profile__link"><a href="<?php echo  home_url('my-opportunities') ?>" class="profile__link__a">Find Opportunities</a></li>
                <li class="profile__link"><a href="<?php echo home_url('/my-pitches'); ?>" class="profile__link__a">My Pitches</a></li>
                <li class="profile__link profile__link_brand"><span class="profile__brandname"><?php echo $candidate_name;?></span></li>
                <li class="profile__link"><a href="<?php echo  home_url('/my-balance'); ?>" class="profile__link__a">Balance/Cash Out</a></li>
                <li class="profile__link"><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="profile__link__a">Account</a></li>

            <?php endif;
            ?>
        </ul>
    </div>
</section>