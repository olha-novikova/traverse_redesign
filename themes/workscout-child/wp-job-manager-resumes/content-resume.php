<?php
$category = get_the_resume_category(); 
$resume_photo_style = Kirki::get_option( 'workscout','pp_resume_rounded_photos','off' );

if($resume_photo_style){
	$photo_class = "square";
} else {
	$photo_class = "rounded";
}

?>

<li <?php resume_class($photo_class); ?>  data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">

    <a class="photo-<?php echo $photo_class?>" href="<?php the_permalink(); ?>">
		<?php the_candidate_photo(); ?>
		<div class="resumes-content">
            <?php    $portfolio_name = get_post_meta($post->ID, '_portfolio_name', true); ?>
			<h4><?php the_title(); ?> <span><?php echo $portfolio_name; ?></span></h4>
            <?php if ( $category ) : ?>
                <div class="resume-category">
                    <?php echo $category ?>
                </div>
            <?php endif; ?>
			<span><i class="fa fa-map-marker"></i> <?php the_candidate_location( false ); ?></span>
			<?php $rate = get_post_meta( $post->ID, '_rate_min', true );
			if(!empty($rate)) { ?>
				<span class="icons"><i class="fa fa-money"></i> <?php echo get_workscout_currency_symbol();  echo get_post_meta( $post->ID, '_rate_min', true ); ?> <?php esc_html_e('/ hour','workscout') ?></span>
			<?php } ?>
            <?php
            $newsletter = get_post_meta( $post->ID, '_newsletter', true );
            $newsletter_total = get_post_meta( $post->ID, '_newsletter_total', true );

            if ( $newsletter == 'yes' && $newsletter_total>0 ) echo '<span class="icons"><i class="fa fa-link"></i>'.$newsletter_total .' newsletters </span>';

            if ( $insta_link = get_post_meta( $post->ID, '_instagram_link', true ) ){
                $instagram_followers_count = get_instagram_followers_count($insta_link);

                if ( $instagram_followers_count >0 )
                echo '<span class="icons"><i class="fa fa-instagram"></i>'. $instagram_followers_count.' followers</span>';
            }

            if ( $twitter = get_post_meta( $post->ID, '"_twitter_link', true ) ){
                $twitter_followers_count = get_twitter_followers_count($insta_link);

                if ( $twitter_followers_count >0 )
                    echo '<span class="icons"><i class="fa fa-twitter"></i>'.$twitter_followers_count.' followers</span>';
            }

            if ( $youtube = get_post_meta( $post->ID, '_youtube_link', true ) ) {
                $youtube_subscriber_count = get_youtube_subscriber_count($youtube);

                if ( $youtube_subscriber_count >0 )
                 echo '<span class="icons"><i class="fa fa-youtube"></i>'.$youtube_subscriber_count.' subscribers</span>';
            }

            $website = get_post_meta( $post->ID, '_influencer_website', true );
            $monthly_visitors = get_post_meta( $post->ID, '_estimated_monthly_visitors', true );

            if ( $website && $monthly_visitors >0 ) echo '<span class="icons"><i class="fa fa-users"></i>'.$monthly_visitors.' monthly visitors on '. $website. '</span>';

            $jrrny_link = get_post_meta( $post->ID, '_jrrny_link', true );
            $jrrny_followers = get_user_followers_count($jrrny_link);

            if ( $jrrny_followers > 0)  echo '<span class="icons"><i class="fa fa-jrrny"></i>'.$jrrny_followers.' followers on '.$jrrny_link.'</span>';
          //  if ($fb = get_user_meta( $user_id, '_facebook_link', true )) echo get_instagram_followers_count($insta_link);
            ?>
			<p><?php the_excerpt(); ?></p>

			<?php if ( ( $skills = wp_get_object_terms( $post->ID, 'resume_skill', array( 'fields' => 'names' ) ) ) && is_array( $skills ) ) : ?>
				<div class="skills">
					<?php echo '<span>' . implode( '</span><span>', $skills ) . '</span>'; ?>
				</div>
				<div class="clearfix"></div>
			<?php endif; ?>
		</div>
	
	</a>

	<div class="clearfix"></div>
</li>