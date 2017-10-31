<?php

function get_pitched_campaigns ($user_id, $status) {

	$pitched = [];

	$resume = get_user_resume ($user_id);

	$applications = get_posts( array(
		'post_type'      => 'job_application',
		'post_status'    => $status,
		'posts_per_page' => -1,
	) );

	foreach ($applications as $application) {
		$resume_id = get_job_application_resume_id($application->ID);

		$job_id = $application->post_parent;
		$app_message = $application->post_content;
		$app_date = $application->post_date;

		if ($resume_id == $resume[0]->ID) {
			$job = get_posts( array(
				'p' => $job_id,
				'post_type'              => 'job_listing',
				'post_status'            => 'publish',
			) );
			$job['message'] = $app_message;
			$job['id'] = $application->ID;
			$job['date'] = $app_date;
			$pitched[] = $job;
		}
	}

	return $pitched;
}

function get_user_resume ($user_id) {

	$resumes = get_posts( array(
		'post_type'           => 'resume',
		'post_status'         => array( 'publish'),
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1,
		'orderby'             => 'date',
		'order'               => 'desc',
		'author'              => $user_id

	) );


	return $resumes;
}


add_action( 'wp_ajax_nopriv_get_jobs', 'get_jobs'  );
add_action( 'wp_ajax_get_jobs',  'get_jobs'  );

function get_jobs(){
	$args = unserialize(stripslashes($_POST['query']));
	$args['paged'] = $_POST['page'] + 1;
	$args['post_type'] = 'job_listing';
	$args['post_status'] = 'publish';



	$q = new WP_Query($args);
	if( $q->have_posts() ):
		while($q->have_posts()): $q->the_post();

			$id = get_the_ID();
			$location =  get_post_meta($id, '_job_location', true);
			$date =  get_post_meta($id, '_publish_date', true);
			$desc =  substr(get_post_meta($id, '_job_description', true), 250);
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
							<?php $avatar = get_job_application_avatar($application->ID); ?>

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
			<?php
		endwhile;
	endif;
	wp_reset_postdata();
	die();
}

