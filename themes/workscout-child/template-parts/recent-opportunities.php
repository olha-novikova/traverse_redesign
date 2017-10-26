<section class="section section_opportuinities">
	<div class="section__container section__container_top">
		<p class="section__header">Most Recent Opportunities</p>
	</div>
	<div class="section__container section__container_bottom">
		<div class="table">
			<div class="table__head">
				<div class="table__row table__row_header">
					<div class="table__header">
						<p class="table__text">Campaign</p>
					</div>
					<div class="table__header">
						<p class="table__text">Location</p>
					</div>
					<div class="table__header">
						<p class="table__text">Campaign Date</p>
					</div>
					<div class="table__header">
						<p class="table__text">Campaign Description</p>
					</div>
					<div class="table__header">
						<p class="table__text">Influencers</p>
					</div>
				</div>
			</div>
			<div class="table__body" id="jobs-table">
				<?php

				$query_args = array(
					'post_type'              => 'job_listing',
					'post_status'            => 'publish',
					'ignore_sticky_posts'    => 1,
					'posts_per_page'         => 2,
				);

				$jobs = new WP_Query( $query_args );



				if ( $jobs->have_posts() ) : while ( $jobs->have_posts() ) : $jobs->the_post(); ?>

					<?php
					$id = get_the_ID();
					$location =  get_post_meta($id, '_job_location', true);
					$date =  get_post_meta($id, '_publish_date', true);
					$desc =  get_post_meta($id, '_job_description', true);
					$count = get_job_application_count( $id );


					$applications = get_posts( array(
						'post_type'      => 'job_application',
						'post_status'    => array_merge( array_keys( get_job_application_statuses() ), array( 'publish' ) ),
						'posts_per_page' => -1,
						'post_parent'    => $id
					) );

					?>

					<div class="table__row table__row_body">
						<div class="table__data">
							<?php the_title() ?>
						</div>
						<div class="table__data">
							<p class="table__text"><?php esc_html_e($location) ?></p>
						</div>
						<div class="table__data">
							<p class="table__text"><?php esc_html_e(the_date('F  j, Y \a\t g:i a')) ?></p>
						</div>
						<div class="table__data">
							<p class="table__text"><?php esc_html_e($desc) ?></p>
						</div>
						<div class="table__data">
							<div class="table__influencers">
								<?php foreach($applications as $application) : ?>
									<?php $avatar = get_job_application_avatar($application->ID);  ?>
									<div class="table__influencer">
										<?= $avatar ?>
									</div>
								<?php endforeach; ?>
								<div class="table__influencer">
									<div class="table__influencer__number"><?php echo $count ?></div>
								</div>
							</div>
						</div>
						<div class="table__data">
							<div class="table__buttons">
								<a href="<?php the_permalink() ?>" class="button button_green">View Campaign</a>
							</div>
						</div>
					</div>
				<?php endwhile; endif; ?>
			</div>
		</div>
		<?php if (  $jobs->max_num_pages > 1 ) : ?>
		<div class="after-table">

			<script>
                var true_posts = '<?php echo serialize($jobs->query_vars); ?>';
                var current_page = <?php echo (get_query_var('paged')) ? get_query_var('paged') : 1; ?>;
                var max_pages = '<?php echo $jobs->max_num_pages; ?>';
			</script>

			<div class="button button_green" id="loadmore">View More Opportunities</div>
		</div>
	</div>
	<?php endif; ?>
</section>