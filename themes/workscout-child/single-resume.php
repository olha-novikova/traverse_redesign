
<?php

$photo_samples = array_shift(get_post_meta( $post->ID, '_photo_sample'));

get_header('new');

get_sidebar();
while ( have_posts() ) : the_post();
?>
	<main class="main">
    <?php get_template_part('template-parts/single-influencer-header')?>
		<div class="content">
			<section class="section section_overview">
				<div class="section__container">
					<div class="section__top">
						<p class="section__header">Influencer Overview</p>
						<div class="influencer__tags">
							<?php $tags = explode(', ', get_the_resume_category($post)); ?>
							<?php foreach($tags as $tag) : ?>
							<p class="influencer__tag influencer__tag_blue"><?php esc_html_e($tag) ?></p>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="section__body">
						<div class="section__left">
							<div class="section__block section__block_bio">
								<p class="section__block__header">Bio</p>
								<?php $bio = get_post_meta( $post->ID, '_short_influencer_bio', true ); ?>
								<p class="section__block__text"><?php esc_html_e($bio) ?></p>
							</div>
							<div class="section__block section__block_cities">
								<p class="section__block__header">Cities I Know Well</p>
								<?php $locations = get_post_meta( $post->ID, '_resume_locations', true ); ?>
								<p class="section__block__text"><?php esc_html_e($locations) ?></p>
							</div>
							<div class="section__block section__block_video">
								<p class="section__block__header">Video Samples</p>
								<div class="video">
									<div class="video__thumbnail">
										<div class="wrapper_youtube">
											<div data-embed="gLb7JhO4ikg" class="youtube">
												<div class="play-button video__play-button"></div>
											</div>
										</div>
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="6px" class="button__svg button__svg_video">
											<path fill-rule="evenodd" d="M20.890,5.984 C19.308,5.984 18.025,4.703 18.025,3.121 C18.025,1.539 19.308,0.257 20.890,0.257 C22.472,0.257 23.755,1.539 23.755,3.121 C23.755,4.703 22.472,5.984 20.890,5.984 ZM12.295,5.984 C10.713,5.984 9.430,4.703 9.430,3.121 C9.430,1.539 10.713,0.257 12.295,0.257 C13.877,0.257 15.160,1.539 15.160,3.121 C15.160,4.703 13.877,5.984 12.295,5.984 ZM3.700,5.984 C2.118,5.984 0.835,4.703 0.835,3.121 C0.835,1.539 2.118,0.257 3.700,0.257 C5.283,0.257 6.565,1.539 6.565,3.121 C6.565,4.703 5.283,5.984 3.700,5.984 Z"></path>
										</svg>
									</div>
								</div>
							</div>
						</div>
						<div class="section__right">
							<div class="section__block section__block_audiences">
								<p class="section__block__header">Covered Audiences</p>
                <?php

                $newsletter = get_post_meta( $post->ID, '_newsletter', true );
                $newsletter_total = get_post_meta( $post->ID, '_newsletter_total', true );

                if ( $newsletter == 'yes' && $newsletter_total > 0 ) : echo '
                <p class="section__block__text section__block__text_media">
                <i class="fa fa-link"></i>'.$newsletter_total . ' newsletters
	                </p>';
                endif;

                if ( $insta_link = get_post_meta( $post->ID, '_instagram_link', true ) ) {
	                $instagram_followers_count = get_instagram_followers_count( $insta_link );

	                if ( $instagram_followers_count > 0 ) {
		                echo '
                    <p class="section__block__text section__block__text_media">
                    <i class="fa fa-instagram"></i>' . $instagram_followers_count . ' followers
                     </p>';
	                }
                }

                if ( $twitter = get_post_meta( $post->ID, '_twitter_link', true ) ) {
	                $twitter_followers_count = get_twitter_followers_count( $twitter );

	                if ( $twitter_followers_count > 0 ) {
		                echo '
                    <p class="section__block__text section__block__text_media">
                    <i class="fa fa-twitter-square"></i>' . $twitter_followers_count . ' subscribers
                     </p>';
	                }
                }

                if ( $youtube = get_post_meta( $post->ID, '_youtube_link', true ) ) {
	                $youtube_subscriber_count = get_youtube_subscriber_count($youtube);

	                if ( $youtube_subscriber_count > 0 ) {
		                echo '
                    <p class="section__block__text section__block__text_media">
                    <i class="fa fa-youtube"></i>'.$youtube_subscriber_count.' subscribers
                     </p>';
                }
                }

                if ( $facebook_subscriber_count = get_post_meta( $post->ID, 'fb_subscribers_count', true ) ) {

                    if ( $facebook_subscriber_count > 0 ) {
                        echo '
                    <p class="section__block__text section__block__text_media">
                    <i class="fa facebook-square"></i>'.$facebook_subscriber_count.' subscribers
                     </p>';
                    }
                }

                $website = get_post_meta( $post->ID, '_influencer_website', true );
                $monthly_visitors = get_post_meta( $post->ID, '_estimated_monthly_visitors', true );

                if ( $website && $monthly_visitors > 0 ) {
		                echo '
                    <p class="section__block__text section__block__text_media">
                    <i class="fa fa-users"></i>'.$monthly_visitors.' monthly visitors on '. $website. '
                     </p>';
                }

                $jrrny_link = get_post_meta( $post->ID, '_jrrny_link', true );
                $jrrny_followers = get_user_followers_count($jrrny_link);

                if ( $jrrny_followers > 0)  echo '<p class="section__block__text section__block__text_media"><i class="fa fa-jrrny"></i>'.$jrrny_followers.' followers on '.$jrrny_link.'</p>';

                ?>
							</div>
							<div class="section__block section__block_photo">
								<p class="section__block__header">Photo Samples</p>
								<div id="photos" class="photos">

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</main>
<?php
	endwhile; // End of the loop.
get_footer('new');
?>

<script>
    $(document).ready(function () {
        var photos =<?php echo json_encode($photo_samples );?>;
        jQuery('#photos').imagesGrid({
            images: photos
        });
    })


</script>

