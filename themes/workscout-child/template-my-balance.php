<?php
if (!session_id())
    session_start();
/**
 * Template Name: Account Balance
 *
 */
get_header();
$user = wp_get_current_user();
$currency = get_woocommerce_currency_symbol();
if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
    $applications_list = get_candidate_account_balance_info($user->ID);
    $available_cash = get_candidate_cash_out_sum($user->ID);

endif;
if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
    $listings_list = get_paid_listings_employer_packages($user->ID);
    $operations_list = get_employer_account_balance_info($user->ID);
          // print_r($operations_list);
endif;

?>
<?php

while ( have_posts() ) : the_post();
$titlebar = get_post_meta( $post->ID, 'pp_page_titlebar', true );


if($titlebar == 'off') {
    // no titlebar
} else {

        $header_image = get_post_meta($post->ID, 'pp_job_header_bg', TRUE);
        if(!empty($header_image)) { ?>
            <div id="titlebar" class="photo-bg single" style="background: url('<?php echo esc_url($header_image); ?>')">
        <?php } else { ?>
            <div id="titlebar" class="single">
        <?php } ?>
        <div class="container">

            <div class="sixteen columns">
                <h1><?php the_title(); ?></h1>
                <?php if(function_exists('bcn_display')) { ?>
                    <nav id="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
                        <ul>
                            <?php bcn_display_list(); ?>
                        </ul>
                    </nav>
                <?php } ?>
            </div>
        </div>
        </div>
    <?php

}
$layout  = get_post_meta( $post->ID, 'pp_sidebar_layout', true ); if ( empty( $layout ) ) { $layout = 'full-width'; }
$class = ($layout !="full-width") ? "eleven columns" : "sixteen columns"; ?>

    <div class="container <?php echo esc_attr($layout); ?>">
        <article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>

            <?php

            do_action( 'woocommerce_before_account_navigation' );
            ?>
            <div class="woocommerce-account">
                <div class="woocommerce">
                    <nav class="woocommerce-MyAccount-navigation">
                        <ul>
                            <?php
                            function the_slug($id) {
                                $post_data = get_post($id, ARRAY_A);
                                $slug = $post_data['post_name'];
                                return $slug;
                            }
                            global $wp;
                            $page = $wp->query_vars[ 'pagename' ];

                            if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
                                $candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );

                                $page = the_slug($candidate_dashboard_page_id);
                                $class = ( $wp->query_vars[ 'pagename' ]== $page )?'is-active':'';

                                printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> My Portfolios </a></li>', 'workscout' ),
                                    $class,
                                    get_permalink($candidate_dashboard_page_id)

                                );
                            endif;

                            if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
                                $employer_dashboard_page_id = get_option( 'job_manager_job_dashboard_page_id' );

                                $page = the_slug($employer_dashboard_page_id);
                                $class = ( $wp->query_vars[ 'pagename' ]== $page )?'is-active':'';
                                printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> My Listings </a></li>', 'workscout' ),
                                    $class,
                                    get_permalink($employer_dashboard_page_id)
                                );
                            endif;

                            if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :

                                $pagename = 'my-pitches';
                                $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';


                                printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> My Pitches </a></li>', 'workscout' ),
                                    $class,
                                    home_url('/my-pitches')
                                );
                            endif;


                            ?>
                            <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                                <?php if($label !='Orders' && $label !='Downloads' && $label !='Addresses' && $label != 'Dashboard'){?>
                                    <?php
                                    if ( $label =='Logout' ){

                                        if ( in_array( 'candidate', (array) $user->roles ) ) :
                                            $pagename = 'my-balance';
                                            $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';

                                            printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> Account Balance</a></li>', 'workscout' ),
                                                $class,
                                                home_url('/my-balance')
                                            );
                                        endif;
                                        if ( in_array( 'employer', (array) $user->roles )  ) :
                                            $pagename = 'my-balance';
                                            $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';

                                            printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> Payment History</a></li>', 'workscout' ),
                                                $class,
                                                home_url('/my-balance')
                                            );
                                        endif;
                                        if (  in_array( 'administrator', (array) $user->roles ) ) :
                                            $pagename = 'my-balance';
                                            $class = ( $wp->query_vars[ 'pagename' ]== $pagename )?'is-active':'';

                                            printf( __( '<li class="woocommerce-MyAccount-navigation-link %s"><a href="%s"> Balance/Payment</a></li>', 'workscout' ),
                                                $class,
                                                home_url('/my-balance')
                                            );
                                        endif;
                                    }
                                    ?>
                                    <li class="woocommerce-MyAccount-navigation-link">
                                        <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( ucwords($label) ); ?></a>
                                    </li>
                                    <?php
                                    if ( $endpoint == 'edit-account') { ?>
                                        <li class="woocommerce-MyAccount-navigation-link">
                                            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ).'?password=change'; ?>"><?php echo esc_html( ucwords('Change Password') ); ?></a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            <?php endforeach;?>
                        </ul>
                    </nav>


                    <?php do_action( 'woocommerce_after_account_navigation' );?>
                    <div class="woocommerce-MyAccount-content">
                    <?php
                    if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :?>

                        <h2 class="my-acc-h2">Account Balance</h2>
                        <div id="account-balance">
                            <table class="resume-manager-resumes manage-table resumes responsive-table">
                                <thead>
                                    <tr>
                                        <th>Listing Name</th>
                                        <th>Listing Status</th>
                                        <th>Pitch Status</th>
                                        <th>Salary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $sum = 0;
                                    foreach ($applications_list as $application){ ?>
                                        <tr>
                                            <td><?php echo $application['job_title']?></td>
                                            <td><?php echo $application['job_status']?></td>
                                            <td><?php echo $application['application_status']?></td>
                                            <td><?php echo $currency.$application['job_price']?></td>
                                            <?php $sum += $application['job_price'];?>
                                        </tr>
                                    <?php }?>

                                <tr style="background: #f6f6f6; padding: 25px 0;"><th colspan="3">Sum</th><th><?php echo $currency.$sum;?></th></tr>
                                </tbody>
                            </table>
                        </div>
                        <h2 class="my-acc-h2">Cash Out Balance</h2>
                        <p>The cash out balance is all available funds for deposits on project or completed work</p>
                        <?php
                        if ( isset ($_SESSION['error'])){
                            echo "<span class='woocommerce-error'>".$_SESSION['error']."</span>";
                            unset ($_SESSION['error']);
                        }elseif  (isset ($_SESSION['success'])){
                            echo "<span class=''>"."You request was sent successfully"."</span>";
                            unset ($_SESSION['success']);
                        }

                        ?>
                        <div id="cash-out-balance">
                            <table class="resume-manager-resumes manage-table resumes responsive-table">
                                <thead>
                                <tr>
                                    <th colspan="3">Available cash out</th>
                                    <th><?php echo $currency.$available_cash;?></th>
                                    <th colspan="2"><a class="small-dialog popup-with-zoom-anim button" href="#cash-out-dialog">Cash Out</a>
                                </tr>
                                </thead>

                            </table>

                            <div id="cash-out-dialog" class="small-dialog zoom-anim-dialog mfp-hide">
                                <div class="small-dialog-headline">
                                    <h2><?php esc_html_e('Cash Out','workscout'); ?></h2>
                                </div>
                                <div class="small-dialog-content">
                                    <form id="payment_request" method="post">
                                        <div class="form-body">
                                            <div class="form-row">
                                                <label for="amount"><h4>How much would you like to cash out? <?php echo $currency.$available_cash?> is available</h4></label>

                                                <input type="text" name="amount" value="">
                                            </div>
                                            <div class="form-row">
                                                <label for = "payout_destination"><h4>Please provide PayPal email for payment</h4></label>
                                                <input type="email" name="payout_destination" value="<?php echo $user->user_email;?>">
                                            </div>
                                            <div class="form-row">
                                                <input type="hidden" name="action" value="send_payment_request"/>
                                                <input type="hidden" name="redirect" value="<?php echo get_permalink(); ?>"/>
                                                <input type="hidden" name="r_nonce" value="<?php echo wp_create_nonce('r-nonce'); ?>"/>
                                                <button type="submit"><?php _e('Send'); ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif;
                    if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :?>
                        <h2 class="my-acc-h2">Listing Payment History</h2>
                        <p>These are all of your purchased listings.</p>
                        <div id="account-balance">
                            <table class="resume-manager-resumes manage-table resumes responsive-table">
                                <thead>
                                <tr>

                                    <th>Package Name</th>
                                    <th>Package Price</th>
                                    <th>Listing Name</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $sum = 0;
                                foreach ($listings_list as $listing){
                                    $listing_object = $listing['listing'][0];
                                    ?>
                                    <tr>
                                        <td><?php echo $listing['name']?></td>
                                        <td><?php echo $listing['currency'].$listing['price'];?></td>
                                        <td><?php if ($listing_object ) echo '<a href="'.get_permalink($listing_object->ID).'" >'.get_the_title($listing_object->ID).'</a>'; else echo "-";?></td>
                                        <?php $sum += $listing['price'];?>
                                    </tr>
                                <?php }?>

                                <tr style="background: #f6f6f6; padding: 25px 0;"><th colspan="2">Sum</th><th><?php echo $listing['currency'].$sum;?></th></tr>
                                </tbody>
                            </table>
                            <br>
                        </div>
                        <h2 class="my-acc-h2">Influencer Payment History</h2>
                        <p>This is a list of all of the payments you have made to influencers.</p>
                        <div id="account-balance">
                            <table class="resume-manager-resumes manage-table resumes responsive-table">
                                <thead>
                                <tr>
                                    <th>Listing Name</th>
                                    <th>Status</th>
                                    <th>Paid</th>
                                    <th>Influencer</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $sum = 0;
                                foreach ($operations_list as $operation){ ?>
                                    <tr>
                                        <td><?php echo $operation['job_title']?></td>
                                        <td><?php echo $operation['application_status']?></td>
                                        <td><?php echo $operation['currency'].$operation['job_price']?></td>
                                        <td><?php echo '<a href="'.get_permalink($operation['influencer_id']).'">'.get_the_title($operation['influencer_id']).'</a>'?></td>
                                        <?php $sum += $operation['job_price'];?>
                                    </tr>
                                <?php }?>

                                <tr style="background: #f6f6f6; padding: 25px 0;"><th colspan="2">Sum</th><th><?php echo $operation['currency'].$sum;?></th></tr>
                                </tbody>
                            </table>
                            <br>
                        </div>
                    <?php endif;
                    ?>

                    </div>
                </div>
            </div>
        </article>
    </div>
</div>

<?php
endwhile; // End of the loop.
 get_footer();