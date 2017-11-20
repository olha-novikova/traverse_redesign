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
get_template_part('template-parts/page-header');
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
        <section class="section section_browse">
            <div class="section__container">
                <div class="job-success">
                    <p class="section__header"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your listing has been submitted. You will receive pitches from awesome influencers soon!', 'workscout' ), $order ); ?></p>
                </div>
                <p class="section__header section__header_browse">Influencers You Can Invite to Campaign</p>

                <?php

                foreach ( $order->get_items() as $item_id => $item ) {
                    $job_id = wc_get_order_item_meta($item_id, '_job_id', true);

                    $product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
                    $_sku = $sku = $product->get_sku();

                }

                $categories = wp_get_post_terms($job_id, 'job_listing_category', array("fields" => "id=>slug"));
                $types  = wp_get_post_terms($job_id, 'job_listing_type', array("fields" => "id=>slug"));

                $social_media_query = array();
                $social_media_query_use = false;

                if ( in_array('facebook', $types) ){
                    $social_media_query[] = '_facebook_link';
                    $social_media_query_use = true;
                }

                if ( in_array('instagram', $types) ){
                    $social_media_query[] = '_instagram_link';
                    $social_media_query_use = true;
                }

                if ( in_array('youtube', $types) ){
                    $social_media_query[] = '_youtube_link';
                    $social_media_query_use = true;
                }

                if ( in_array('twitter', $types) ){
                    $social_media_query[] =  '_twitter_link';
                    $social_media_query_use = true;
                }



                $meta_query = array('relation' => 'AND');
                $meta_use = false;

                $meta_query[] = array(
                    'key'       => '_audience',
                    'compare'   => '>',
                    'value'     => '0',
                    'type'      => 'NUMERIC'
                );

                if ( $_sku == 'pro_inf' ){
                    $meta_query[] = array(
                        'key'       => '_audience',
                        'compare'   => '<',
                        'value'     => '500000',
                        'type'      => 'NUMERIC'
                    );
                    $meta_use = true;
                }elseif ( $_sku == 'growth_inf' ){
                    $meta_query[] = array(
                        'key'       => '_audience',
                        'compare'   => '<',
                        'value'     => '500000',
                        'type'      => 'NUMERIC'
                    );
                    $meta_use = true;
                }elseif ( $_sku == 'micro_inf' ){
                    $meta_query[] = array(
                        'key'       => '_audience',
                        'compare'   => '<',
                        'value'     => '50000',
                        'type'      => 'NUMERIC'
                    );
                    $meta_use = true;
                }

                $args = array(
                    'post_type'           => 'resume',
                    'post_status'         => array( 'publish'),
                    'ignore_sticky_posts' => 1,
                    'orderby'             => 'ASC',
                    'order'               => 'date',
                    'posts_per_page'      => -1,
                    'fields'              => 'ids'
                );

                if ( isset($_POST['traveler_type']) && !empty($_POST['traveler_type']) )
                    $categories = $_POST['traveler_type'];

                if ( $categories ){
                    $args['tax_query'][] = array(
                        'taxonomy'         => 'resume_category',
                        'field'            => 'slug',
                        'terms'            => array_values( $categories ),
                        'include_children' => false,
                        'operator'         => 'IN'
                    );
                }

                if ( $meta_use ) {
                    $args['meta_query'] = $meta_query;
                }

                $resumes = new WP_Query($args);
                $resumes_ids  = $resumes->posts;

                foreach ($resumes_ids as $key => $resume){
                    $metas = array();

                    foreach ( $social_media_query as $meta_value ){
                       if ( get_post_meta($resume, $meta_value, true) )
                           $metas[] = $meta_value ;
                    }
                    $exists = array_intersect ($social_media_query,$metas );
                    if ( !$exists ) unset( $resumes_ids[$key] );
                }

                $resumes = new WP_Query(array(
                    'post_type'           => 'resume',
                    'post_status'         => array( 'publish'),
                    'ignore_sticky_posts' => 1,
                    'orderby'             => 'ASC',
                    'order'               => 'date',
                    'posts_per_page'      => -1,
                    'post__in'              => $resumes_ids
                ));

                if ( $resumes->have_posts() ) :?>

                <div class="carousel">
                    <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>

                        <?php get_template_part('template-parts/content', 'influencer')?>

                    <?php endwhile; ?>
                </div>
            <?php  wp_reset_postdata(); endif; ?>
            </div>
        </section>
	<?php endif; ?>
<?php else : ?>

	<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'workscout' ), null ); ?></p>

<?php endif; ?>
