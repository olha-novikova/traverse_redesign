<?php
/**
 * The template for displaying all single jobs.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WorkScout
 */

get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php if ( resume_manager_user_can_view_resume( $post->ID ) ) : 

	$resume_photo_style = Kirki::get_option( 'workscout','pp_resume_rounded_photos','off' );

	if($resume_photo_style){
		$photo_class = "square";
	} else {
		$photo_class = "rounded";
	}
?>
	
	<!-- Titlebar
	================================================== -->
	<div id="titlebar" class="resume">
		<div class="container">
			<div class="ten columns">
				<div class="resume-titlebar photo-<?php echo $photo_class?>">
					<?php the_candidate_photo('workscout-resume', get_template_directory_uri().'/images/candidate.png'); ?>

					<div class="resumes-content">
						<h4><?php the_title(); ?> <span><?php the_candidate_title(); ?></span></h4>
						<span class="icons"><i class="fa fa-map-marker"></i><?php the_candidate_location(); ?></span>
						<?php $rate = get_post_meta( $post->ID, '_rate_min', true );
						if(!empty($rate)) { ?>
							<span class="icons"><i class="fa fa-money"></i> <?php  echo get_workscout_currency_symbol();  echo get_post_meta( $post->ID, '_rate_min', true ); ?> <?php esc_html_e('/ hour','workscout') ?></span>
						<?php } ?>
						<?php foreach( get_resume_links() as $link ) : ?>
							<?php
								$parsed_url = parse_url( $link['url'] );
								$host       = isset( $parsed_url['host'] ) ? current( explode( '.', $parsed_url['host'] ) ) : '';
							?>
							<span class="icons">
								<a rel="nofollow" href="<?php echo esc_url( $link['url'] ); ?>"><i class="fa fa-link"></i> <?php echo esc_html( $link['name'] ); ?></a>
							</span>
						<?php endforeach; ?>
						<?php if ( resume_has_file() ) : ?>
							<?php
							if ( ( $resume_files = get_resume_files() ) && apply_filters( 'resume_manager_user_can_download_resume_file', true, $post->ID ) ) : ?>
								<?php foreach ( $resume_files as $key => $resume_file ) : ?>
									<span class="icons">
										<a rel="nofollow" href="<?php echo esc_url( get_resume_file_download_url( null, $key ) ); ?>"><i class="fa fa-file"></i> <?php echo basename( $resume_file ); ?></a>
									</span>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ( ( $skills = wp_get_object_terms( $post->ID, 'resume_skill', array( 'fields' => 'names' ) ) ) && is_array( $skills ) ) : ?>
							<div class="skills">
								<?php echo '<span>' . implode( '</span><span>', $skills ) . '</span>'; ?>
							</div>
							<div class="clearfix"></div>
						<?php endif; ?>


						

					</div>
				</div>
			</div>

			<div class="six columns">
				<div class="two-buttons">
					<?php get_job_manager_template( 'contact-details.php', array( 'post' => $post ), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

					<?php do_action('workscout_bookmark_hook') ?>

				</div>
			</div>
			
		</div>
	</div>


	<!-- Content
	================================================== -->
	<div class="container ">
	<?php do_action( 'single_resume_start' ); ?>

		<?php 
		$education = get_post_meta( $post->ID, '_candidate_education', true );
		$experience = get_post_meta( $post->ID, '_candidate_experience', true );
		if(empty($education) && empty($experience) ) { ?>
		<div class="sixteen columns resume_description">
				<?php the_candidate_video(); ?>
				<?php echo do_shortcode(apply_filters( 'the_resume_description', get_the_content() )); ?>

		</div>
		<?php } else { ?>
		<!-- Recent Jobs -->
		<div class="eight columns">
			<div class="padding-right resume_description">
				<?php the_candidate_video(); ?>
				<?php echo do_shortcode(apply_filters( 'the_resume_description', get_the_content() )); ?>
				<?php do_action( 'single_resume_meta_start' ); ?>
				<?php do_action( 'single_resume_meta_end' ); ?>
			</div>
		</div>

		<!-- Widgets -->
		<div class="eight columns">
			<?php if ( $items = get_post_meta( $post->ID, '_candidate_education', true ) ) : ?>
				<h3 class="margin-bottom-20"><?php esc_html_e( 'Education', 'workscout' ); ?></h3>
				<dl class="resume-table resume-manager-education">
				<?php
					foreach( $items as $item ) : ?>

						<dt>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<strong><?php printf( esc_html__( '%s at %s', 'workscout' ), '<span class="qualification">' . esc_html( $item['qualification'] ) . '</span>', '<span class="location">' . esc_html( $item['location'] ) . '</span>' ); ?></strong> 
						</dt>
						<dd>
							<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
						</dd>

					<?php endforeach;
				?>
				</dl>
			<?php endif; ?>
			
			<?php if ( $items = get_post_meta( $post->ID, '_candidate_experience', true ) ) : ?>
				<h3 class="margin-bottom-20"><?php esc_html_e( 'Experience', 'workscout' ); ?></h3>
				<dl class="resume-table resume-manager-experience">
				<?php
					foreach( $items as $item ) : ?>

						<dt>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<strong><?php printf( esc_html__( '%s at %s', 'workscout' ), '<span class="job_title">' . esc_html( $item['job_title'] ) . '</span>', '<span class="employer">' . esc_html( $item['employer'] ) . '</span>' ); ?></strong> 
						</dt>
						<dd>
							<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
						</dd>

					<?php endforeach;
				?>
				</dl>
			<?php endif; ?>

		</div>
		<?php } ?>

		<?php do_action( 'single_resume_end' ); ?>

	</div>
	<div class="margin-top-10"></div>
	<div class="clearfix"></div>
	<div class="container">
		<div class="columns sixteen">
		<?php
			if(get_option('workscout_enable_resume_comments')) {
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			}
		?>
		</div>
	</div>
<?php else : ?>

	<?php get_job_manager_template_part( 'access-denied', 'single-resume', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

<?php endif; ?>
<?php endwhile; // End of the loop. ?>
<?php get_footer(); ?>