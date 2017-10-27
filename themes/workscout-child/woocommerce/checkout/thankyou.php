<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $order ) : ?>

	<?php if ( $order->has_status( 'failed' ) ) : ?>
		<div class="notification closeable error">
			<p><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'workscout' ); ?></p>

			<p><?php
				if ( is_user_logged_in() )
					esc_html_e( 'Please attempt your purchase again or go to your account page.', 'workscout' );
				else
					esc_html_e( 'Please attempt your purchase again.', 'workscout' );
			?></p>
			<a class="close"></a>
		</div>
			<p>
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'workscout' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My Account', 'workscout' ); ?></a>
				<?php endif; ?>
			</p>


	<?php else : ?>
		<div class="notification closeable success">
			<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your listing has been submitted, you will receive pitches from awesome influencers soon!', 'workscout' ), $order ); ?></p>
		</div>
        <div class="button_summary">
            <?php
            $employer_dashboard_page_id = get_option( 'job_manager_job_dashboard_page_id' );
            $submit_job_page = get_option('job_manager_submit_job_form_page_id');
            $resume_page = get_option('resume_manager_resumes_page_id');

            if (!empty($employer_dashboard_page_id)) {?>
                <a class="button button_grey" href="<?php echo get_permalink($employer_dashboard_page_id); ?>" >My Listings</a>
            <?php }

            if (!empty($resume_page)) {?>
                <a class = "button button_orange" href="<?php echo get_permalink($resume_page); ?>" >Browse Influencers</a>
            <?php
            }

            if (!empty($submit_job_page)) {  ?>
                <a href="<?php echo get_permalink($submit_job_page) ?>" class="button"><?php esc_html_e('Create Another Listing','workscout'); ?></a>
            <?php

            }
            ?>

        </div>

		<div class="clear"></div>

	<?php endif; ?>

<?php else : ?>

	<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'workscout' ), null ); ?></p>

<?php endif; ?>
