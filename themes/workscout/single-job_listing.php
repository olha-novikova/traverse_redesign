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
<!-- Titlebar
================================================== -->
<?php 
$header_image = get_post_meta($post->ID, 'pp_job_header_bg', TRUE); 

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
									
				$print_cats = join( " / ", $jobcats ); ?>
			 	<?php echo '<span>'.$print_cats.'</span>'; ?>
			<?php 
			endif; ?>
				<h1><?php the_title(); ?> 
				<?php if ( get_option( 'job_manager_enable_types' ) ) { ?>
					<span class="job-type <?php echo get_the_job_type() ? sanitize_title( get_the_job_type()->slug ) : ''; ?>"><?php the_job_type(); ?></span>
				<?php } ?>
					<?php if(workscout_newly_posted()) { echo '<span class="new_job">'.esc_html__('NEW','workscout').'</span>'; } ?>
				</h1>
			</div>

			<div class="six columns">
			<?php do_action('workscout_bookmark_hook') ?>
				
			</div>

		</div>
	</div>

<!-- Content
================================================== -->
<?php 

$layout = Kirki::get_option( 'workscout', 'pp_job_layout' ); ?>
<div class="container <?php echo esc_attr($layout); ?>">
	<div class="sixteen columns">
		<?php do_action('job_content_start'); ?>
	</div>

<?php if(class_exists( 'WP_Job_Manager_Applications' )) : ?>			
	<?php if ( is_position_filled() ) : ?>
			<div class="sixteen columns"><div class="notification closeable notice "><?php esc_html_e( 'This position has been filled', 'workscout' ); ?></div><div class="margin-bottom-35"></div></div>	
	<?php elseif ( ! candidates_can_apply() && 'preview' !== $post->post_status ) : ?>
			<div class="sixteen columns"><div class="notification closeable notice "><?php esc_html_e( 'Applications have closed', 'workscout' ); ?></div></div>	
	<?php endif; ?>
<?php  endif;  ?>

	<!-- Recent Jobs -->
	<?php $logo_position = Kirki::get_option( 'workscout','pp_job_list_logo_position', 'left' );?>

	<div class="eleven columns ">
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
			
			<div class="single_job_listing" itemscope itemtype="http://schema.org/JobPosting">
				<meta itemprop="title" content="<?php echo esc_attr( $post->post_title ); ?>" />

				<?php if ( get_option( 'job_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
					<div class="job-manager-info"><?php esc_html_e( 'This listing has expired.', 'workscout' ); ?></div>
				<?php else : ?>
					<div class="job_description" itemprop="description">
						<?php do_action('workscout_single_job_before_content'); ?>
						<?php the_company_video(); ?>
						<?php echo do_shortcode(apply_filters( 'the_job_description', get_the_content() )); ?>
					</div>
					<?php
						/**
						 * single_job_listing_end hook
						 */
						do_action( 'single_job_listing_end' );
					?>

					<?php 
						$share_options = Kirki::get_option( 'workscout', 'pp_job_share' ); 
						
						if(!empty($share_options)) {
								$id = $post->ID;
							    $title = urlencode($post->post_title);
							    $url =  urlencode( get_permalink($id) );
							    $summary = urlencode(workscout_string_limit_words($post->post_excerpt,20));
							    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'medium' );
							    $imageurl = urlencode($thumb[0]);
							?>
							<ul class="share-post">
								<?php if (in_array("facebook", $share_options)) { ?><li><?php echo '<a target="_blank" class="facebook-share" href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '">Facebook</a>'; ?></li><?php } ?>
								<?php if (in_array("twitter", $share_options)) { ?><li><?php echo '<a target="_blank" class="twitter-share" href="https://twitter.com/share?url=' . $url . '&amp;text=' . esc_attr($summary ). '" title="' . __( 'Twitter', 'workscout' ) . '">Twitter</a>'; ?></li><?php } ?>
								<?php if (in_array("google-plus", $share_options)) { ?><li><?php echo '<a target="_blank" class="google-plus-share" href="https://plus.google.com/share?url=' . $url . '&amp;title="' . esc_attr($title) . '" onclick=\'javascript:window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;\'>Google Plus</a>'; ?></li><?php } ?>
								<?php if (in_array("pinterest", $share_options)) { ?><li><?php echo '<a target="_blank"  class="pinterest-share" href="http://pinterest.com/pin/create/button/?url=' . $url . '&amp;description=' . esc_attr($summary) . '&media=' . esc_attr($imageurl) . '" onclick="window.open(this.href); return false;">Pinterest</a>'; ?></li><?php } ?>
								<?php if (in_array("linkedin", $share_options)) { ?><li><?php echo '<a target="_blank"  class="linkedin-share" href="https://www.linkedin.com/cws/share?url=' . $url . '">LinkedIn</a>'; ?></li><?php } ?>

								<!-- <li><a href="#add-review" class="rate-recipe">Add Review</a></li> -->
							</ul>
						<?php } ?>
					<div class="clearfix"></div>

				<?php endif; ?>

				<?php
				$related = Kirki::get_option( 'workscout', 'pp_enable_related_jobs' ); 
				
				 if($related) { get_template_part('template-parts/jobs-related'); }?>

			</div>

		</div>
	</div>


	<!-- Widgets -->
	<div class="five columns" id="job-details">
		<?php dynamic_sidebar( 'sidebar-job-before' ); ?>
		<!-- Sort by -->
		<div class="widget">
			<h4><?php esc_html_e('Job Overview','workscout') ?></h4>

			<div class="job-overview">
				<?php do_action( 'single_job_listing_meta_before' ); ?>
				<ul>
					<?php do_action( 'single_job_listing_meta_start' ); ?>
					<li>
						<i class="fa fa-calendar"></i>
						<div>
							<strong><?php esc_html_e('Date Posted','workscout'); ?>:</strong>
							<span><?php printf( esc_html__( 'Posted %s ago', 'workscout' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>
						</div>
					</li>
					<?php 
					$expired_date = get_post_meta( $post->ID, '_job_expires', true );
					$hide_expiration = get_post_meta( $post->ID, '_hide_expiration', true );
					
					if(empty($hide_expiration )) {
						if(!empty($expired_date)) { ?>
					<li>
						<i class="fa fa-calendar"></i>
						<div>
							<strong><?php esc_html_e('Expiration date','workscout'); ?>:</strong>
							<span><?php echo date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, '_job_expires', true ) ) ) ?></span>
						</div>
					</li>
					<?php }
					} ?>

					<?php 
					if ( $deadline = get_post_meta( $post->ID, '_application_deadline', true ) ) {
						$expiring_days = apply_filters( 'job_manager_application_deadline_expiring_days', 2 );
						$expiring = ( floor( ( time() - strtotime( $deadline ) ) / ( 60 * 60 * 24 ) ) >= $expiring_days );
						$expired  = ( floor( ( time() - strtotime( $deadline ) ) / ( 60 * 60 * 24 ) ) >= 0 );

						echo '<li class="ws-application-deadline ' . ( $expiring ? 'expiring' : '' ) . ' ' . ( $expired ? 'expired' : '' ) . '"><i class="fa fa-calendar"></i>
						<div>
							<strong>' . ( $expired ? __( 'Closed', 'workscout' ) : __( 'Closes', 'workscout' ) ) . ':</strong><span>' . date_i18n( __( 'M j, Y', 'workscout' ), strtotime( $deadline ) ) . '</span></div></li>';
					} ?>
					<li>
						<i class="fa fa-map-marker"></i>
						<div>
							<strong><?php esc_html_e('Location','workscout'); ?>:</strong>
							<span class="location" itemprop="jobLocation"><?php ws_job_location(); ?></span>
						</div>
					</li>
					<li>
						<i class="fa fa-user"></i>
						<div>
							<strong><?php esc_html_e('Job Title','workscout'); ?>:</strong>
							<span><?php the_title(); ?></span>
						</div>
					</li>
					<?php $hours = get_post_meta( $post->ID, '_hours', true ); 
					 if ( $hours ) { ?>
					<li>
						<i class="fa fa-clock-o"></i>
						<div>
							<strong><?php esc_html_e('Hours','workscout'); ?>:</strong>
							<span><?php echo esc_html( $hours ) ?><?php esc_html_e('h / week','workscout'); ?></span>
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
								<?php echo get_workscout_currency_symbol(); echo esc_html( $rate_min ) ?> 
								<?php if(!empty($rate_max)) { echo '- '.get_workscout_currency_symbol().$rate_max; } ?><?php esc_html_e(' / hour','workscout'); ?>
							</span>
						</div>
					</li>
					<?php } ?>
					
					<?php 
					$salary = get_post_meta( $post->ID, '_salary_min', true ); 
					$salary_max = get_post_meta( $post->ID, '_salary_max', true ); 
					 if ( $salary || $salary_max  ) { 
						
					 	?>
					<li>
						<i class="fa fa-money"></i>
						<div>
							<strong><?php esc_html_e('Salary:','workscout'); ?></strong>
							<span>
							<?php  
							if ( $salary ) { echo get_workscout_currency_symbol();  echo esc_html( $salary ); } ?> 
							<?php if ( $salary_max ) {  ?> - <?php echo get_workscout_currency_symbol();echo esc_html($salary_max); } ?></span>
						</div>
					</li>
					<?php } ?>
					<?php do_action( 'single_job_listing_meta_end' ); ?>
				</ul>
				
				<?php do_action( 'single_job_listing_meta_after' ); ?>
				
				<?php if ( candidates_can_apply() ) : ?>
					<?php 
						$external_apply = get_post_meta( $post->ID, '_apply_link', true ); 
						if(!empty($external_apply)) {
							echo '<a class="button" target="_blank" href="'.esc_url($external_apply).'">'.esc_html__( 'Apply for job', 'workscout' ).'</a>';
						} else {
							get_job_manager_template( 'job-application.php' ); 
						}
					?>
					
				<?php endif; ?>

				
			</div>

		</div>

		<?php 
		$single_map = Kirki::get_option( 'workscout', 'pp_enable_single_jobs_map' ); 
		$lng = $post->geolocation_long;
		if($single_map && !empty($lng)) :
		?>

			<div class="widget">
				<h4><?php esc_html_e('Job Location','workscout') ?></h4>
				
				<div id="job_map" data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">
					
				</div>
			</div>

		<?php 
		endif;
		dynamic_sidebar( 'sidebar-job-after' ); ?>

	</div>
	<!-- Widgets / End -->


</div>
<div class="clearfix"></div>
<div class="margin-top-55"></div>

<?php endwhile; // End of the loop. ?>

<?php get_footer(); ?>
