<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation">
	<ul>
	<?php

    $user = wp_get_current_user();

    if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
        $candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
        printf( __( '<li class="woocommerce-MyAccount-navigation-link"><a href="%s"> My Portfolios </a></li>', 'workscout' ),
            get_permalink($candidate_dashboard_page_id)
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

    if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
        $employer_dashboard_page_id = get_option( 'job_manager_job_dashboard_page_id' );
        printf( __( '<li class="woocommerce-MyAccount-navigation-link"><a href="%s"> My Listings </a></li>', 'workscout' ),
            get_permalink($employer_dashboard_page_id)
        );
    endif;
    /*alerts*/
    $alerts_page_id = get_option( 'job_manager_alerts_page_id' );
    if ( (in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles )) && !empty($$alerts_page_id) ) :
        printf( __( '<li class="woocommerce-MyAccount-navigation-link"><a href="%s"> Job Alerts </a></li>', 'workscout' ),
            get_permalink($alerts_page_id)
        );
    endif;
    $bookmarks_page_id = ot_get_option('pp_bookmarks_page');

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

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
