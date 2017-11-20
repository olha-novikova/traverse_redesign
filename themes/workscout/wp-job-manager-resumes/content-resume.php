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
			<h4><?php the_title(); ?> <?php the_candidate_title( '<span>', '</span> ' ); ?></h4>
			<span><i class="fa fa-map-marker"></i> <?php the_candidate_location( false ); ?></span>
			<?php $rate = get_post_meta( $post->ID, '_rate_min', true );
			if(!empty($rate)) { ?>
				<span class="icons"><i class="fa fa-money"></i> <?php echo get_workscout_currency_symbol();  echo get_post_meta( $post->ID, '_rate_min', true ); ?> <?php esc_html_e('/ hour','workscout') ?></span>
			<?php } ?>
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