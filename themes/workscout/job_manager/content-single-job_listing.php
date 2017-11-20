<?php global $post; ?>

<?php $header_image = get_post_meta($post->ID, 'pp_job_header_bg', TRUE); 
if(!empty($header_image)) { ?>

<div id="titlebar" class="photo-bg" style="background: url('<?php echo esc_url($header_image); ?>')">

<?php } else { ?>

<div id="titlebar">

<?php } ?>

	<div class="container">
		<div class="ten columns">
	
		<?php
		$terms = get_the_terms( $post->ID, 'job_listing_category' );
								
		if ( $terms && ! is_wp_error( $terms ) ) : 

			$jobcats = array();
		 	
			foreach ( $terms as $term ) {
				$term_link = get_term_link( $term );
				$jobcats[] = '<a href="'.$term_link.'">'.$term->name.'</a>';
			}
								
			$print_cats = join( " / ", $jobcats );
			?>
			<?php echo '<span>'.$print_cats.'</span>'; ?>


		<?php endif; ?>
			
			<h2><?php the_title(); ?> 
			<?php if ( get_option( 'job_manager_enable_types' ) ) { ?>
				<span class="job-type <?php echo get_the_job_type() ? sanitize_title( get_the_job_type()->slug ) : ''; ?>"><?php the_job_type(); ?></span>
			<?php } ?>
				<?php if(workscout_newly_posted()) { echo '<span class="new_job">'.esc_html__('NEW','workscout').'</span>'; } ?>
				
			</h2>
		</div>

		<div class="six columns">
		<?php do_action('workscout_bookmark_hook') ?>
			
		</div>

	</div>
</div>

<!-- Content
================================================== -->
<div class="container">
	
	<!-- Recent Jobs -->
	<?php $logo_position = Kirki::get_option( 'workscout','pp_job_list_logo_position', 'left' );?>


	<!-- Recent Jobs -->
	<div class="eleven columns">
	<div class="padding-right">
		<?php if ( get_the_company_name() ) { ?>
			<!-- Company Info -->
			<div class="company-info <?php echo ($logo_position == 'left') ? 'left-company-logo' : 'right-company-logo' ;?>" itemscope itemtype="http://data-vocabulary.org/Organization">
				<?php if(class_exists('Astoundify_Job_Manager_Companies')) { echo workscout_get_company_link(the_company_name('','',false)); } ?>
					<?php ($logo_position == 'left') ? the_company_logo() : the_company_logo('medium'); ?></a>
				<?php if(class_exists('Astoundify_Job_Manager_Companies')) { echo "</a>"; } ?>
				<div class="content">
					<h4>
						<?php if(class_exists('Astoundify_Job_Manager_Companies')) { echo workscout_get_company_link(the_company_name('','',false)); } ?>
						<?php the_company_name( '<strong itemprop="name">', '</strong>' ); ?> 
						<?php if(class_exists('Astoundify_Job_Manager_Companies')) { echo "</a>"; } ?>
					<?php the_company_tagline( '<span class="company-tagline">- ', '</span>' ); ?></h4>
					<?php if ( $website = get_the_company_website() ) : ?>
						<span><a class="website" href="<?php echo esc_url( $website ); ?>" itemprop="url" target="_blank" rel="nofollow"><i class="fa fa-link"></i> <?php esc_html_e( 'Website', 'workscout' ); ?></a></span>
					<?php endif; ?>
					<?php if ( get_the_company_twitter() ) : ?>
						<span><a href="http://twitter.com/<?php echo get_the_company_twitter(); ?>">
							<i class="fa fa-twitter"></i>
							@<?php echo get_the_company_twitter(); ?>
						</a></span>
					<?php endif; ?>
				</div>
				<div class="clearfix"></div>
			</div>
		<?php } ?>
	<?php if ( get_option( 'job_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
		<div class="job-manager-info"><?php esc_html_e( 'This listing has expired.', 'workscout' ); ?></div>
	<?php endif; ?>
		<?php the_content(); ?>

	</div>
	</div>


	<!-- Widgets -->
	<div class="five columns">

		<!-- Sort by -->
		<div class="widget">
			<h4><?php esc_html_e('Overview','workscout') ?></h4>

			<div class="job-overview">
				
				<ul>
					<?php do_action( 'single_job_listing_meta_start' ); ?>
					<li>
						<i class="fa fa-map-marker"></i>
						<div>
							<strong><?php esc_html_e('Location','workscout') ?>:</strong>
							<span class="location" itemprop="jobLocation"><?php ws_job_location(); ?></span>
						</div>
					</li>
					<li>
						<i class="fa fa-user"></i>
						<div>
							<strong><?php esc_html_e('Job Title','workscout') ?>:</strong>
							<span><?php the_title(); ?></span>
						</div>
					</li>
					<?php $hours = get_post_meta( $post->ID, '_hours', true ); 
					 if ( $hours ) { ?>
					<li>
						<i class="fa fa-clock-o"></i>
						<div>
							<strong><?php esc_html_e('Hours','workscout') ?>:</strong>
							<span><?php echo esc_html( $hours ) ?>h / week</span>
						</div>
					</li>
					<?php } ?>

					<?php $rate_min = get_post_meta( $post->ID, '_rate_min', true ); 
					 if ( $rate_min ) { 
					 	$rate_max = get_post_meta( $post->ID, '_rate_max', true );  ?>
					<li>
						<i class="fa fa-money"></i>
						<div>
							<strong><?php esc_html_e('Rate:','workscout'); ?></strong>
							<span>				
								<?php  echo get_workscout_currency_symbol(); echo esc_html( $rate_min ) ?> <?php if(!empty($rate_max)) { echo '- '.get_workscout_currency_symbol().$rate_max; } ?> <?php esc_html_e('/ hour','workscout') ?>
							</span>
						</div>
					</li>
					<?php } ?>

					<?php $salary_min = get_post_meta( $post->ID, '_salary_min', true ); 
					 if ( $salary_min ) { 
					 	$salary_max = get_post_meta( $post->ID, '_salary_max', true );  ?>
					<li>
						<i class="fa fa-money"></i>
						<div>
							<strong><?php esc_html_e('Salary:','workscout'); ?></strong>
							<span>				
								<?php  echo get_workscout_currency_symbol(); echo esc_html( $salary_min ) ?> <?php if(!empty($salary_max)) { echo '- '.get_workscout_currency_symbol().$salary_max; } ?>
							</span>
						</div>
					</li>
					<?php } ?>

					<?php do_action( 'single_job_listing_meta_end' ); ?>
				</ul>
				
				<?php do_action( 'single_job_listing_meta_after' ); ?>

				<?php if ( candidates_can_apply() ) : ?>
					<?php get_job_manager_template( 'job-application.php' ); ?>
				<?php endif; ?>

				
			</div>

		</div>

	</div>
	<!-- Widgets / End -->


</div>	