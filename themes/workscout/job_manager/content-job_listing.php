<?php global $post; 
$layout = Kirki::get_option( 'workscout','pp_job_list_logo_position', 'left' );?>


<li <?php job_listing_class($layout); ?> data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">
	<a href="<?php the_job_permalink(); ?>">
		<?php 
		($layout == 'left') ? the_company_logo() : the_company_logo('medium'); ?>
		<div class="job-list-content">
			<h4><?php the_title(); ?> 
			<?php if ( get_option( 'job_manager_enable_types' ) ) { ?>
				<span class="job-type <?php echo get_the_job_type() ? sanitize_title( get_the_job_type()->slug ) : ''; ?>"><?php the_job_type(); ?></span>
			<?php } ?>
			<?php if(workscout_newly_posted()) { echo '<span class="new_job">'.esc_html__('NEW','workscout').'</span>'; } ?> 
			</h4>

			<div class="job-icons">
				<?php do_action( 'workscout_job_listing_meta_start' ); ?>
				<span class="ws-meta-company-name"><i class="fa fa-briefcase"></i> <?php the_company_name();?></span>
				<span class="ws-meta-job-location"><i class="fa fa-map-marker"></i> <?php ws_job_location( false ); ?></span>

				<?php 
				$rate_min = get_post_meta( $post->ID, '_rate_min', true ); 
				if ( $rate_min) { 
					$rate_max = get_post_meta( $post->ID, '_rate_max', true );  ?>
					<span class="ws-meta-rate">
						<i class="fa fa-money"></i> <?php echo get_workscout_currency_symbol(); echo esc_html( $rate_min ); if(!empty($rate_max)) { echo '- '.get_workscout_currency_symbol().$rate_max; } ?> <?php esc_html_e('/ hour','workscout'); ?>
					</span>
				<?php } ?>

				<?php 
				$salary_min = get_post_meta( $post->ID, '_salary_min', true ); 
				if ( $salary_min ) {
					$salary_max = get_post_meta( $post->ID, '_salary_max', true );  ?>
					<span class="ws-meta-salary">
						<i class="fa fa-money"></i>
						<?php echo get_workscout_currency_symbol(); echo esc_html( $salary_min ) ?> <?php if(!empty($salary_max)) { echo '- '.get_workscout_currency_symbol().$salary_max; } ?>
					</span>
				<?php } ?>
				<?php do_action( 'workscout_job_listing_meta_end' ); ?>
			</div>
			<div class="listing-desc"><?php the_excerpt(); ?>
				
			</div>
			
				
		</div>
	</a>
<div class="clearfix"></div>
</li>