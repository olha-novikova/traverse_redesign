<?php
/**
 * Template Name: Page Influencer Dashboard
 *
 */


if (!session_id())
	session_start();

$user = wp_get_current_user();
$currency = get_woocommerce_currency_symbol();
if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
	$applications_list = get_candidate_account_balance_info($user->ID);
	$available_cash = get_candidate_cash_out_sum($user->ID);

endif;

get_header('new');
global $wpdb;
get_sidebar();?>
	<main class="main">
		<?php get_template_part('template-parts/page-header')?>
	<div class="content">
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
					<div class="table__body">
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

	            $location =  get_post_meta(get_the_ID(), '_job_location', true);
	            $date =  get_post_meta(get_the_ID(), '_publish_date', true);
	            $desc =  get_post_meta(get_the_ID(), '_job_description', true);
	            $count = get_job_application_count( get_the_ID() );

	echo ( $count = get_job_application_count( the_ID()) ) ? '<a class="button" href="' . add_query_arg( array( 'action' => 'show_applications', 'job_id' => the_ID() ), get_permalink( the_ID() ) ) . '">'.__('Show','workscout').' (' . $count . ')</a>' : '&ndash;';
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
									<div class="table__influencer"></div>
									<div class="table__influencer"></div>
									<div class="table__influencer"></div>
									<div class="table__influencer"></div>
									<div class="table__influencer"></div>
									<div class="table__influencer">
										<div class="table__influencer__number">+31</div>
									</div>
								</div>
							</div>
							<div class="table__data">
								<div class="table__buttons">
									<a class="button button_green">View Campaign</a>
								</div>
							</div>
						</div>
<?php endwhile; endif; ?>
					</div>
				</div>
				<div class="after-table">
					<div class="button button_green">View More Opportunities</div>
				</div>
			</div>
		</section>
		<div class="section__container section__container_pitches">
			<div class="table table_pitches">
				<div class="table__head">
					<div class="table__row table__row_header">
						<div class="table__header">
							<p class="table__text">pitched campaigns</p>
						</div>
					</div>
				</div>
				<div class="table__body">
					<div class="table__row table__row_body">
						<div class="table__data"><i class="icon icon_calendar"></i>
							<p class="table__data__date">28</p>
							<p class="table__data__month">May</p>
						</div>
						<div class="table__data">
							<p class="table__text">Campaign 1 for <span>Brand A</span></p>
						</div>
						<div class="table__data">
							<p class="table__text">Seattle, Wa</p>
						</div>
						<div class="table__data">
							<p class="table__text">Hello, I am John Doe, I have arund 50 followers on Instagram, and 100k on facebook. I think my audience would be a good fit for your campaign because</p>
						</div>
						<div class="table__data">
							<div class="table__buttons">
								<div class="button button_green">View Campaign Details</div>
								<div class="button button_green">View Full Pitch</div>
							</div>
						</div>
					</div>
				</div>
				<div class="table__head">
					<div class="table__row table__row_header">
						<div class="table__header">
							<p class="table__text">completed campaigns</p>
						</div>
					</div>
				</div>
				<div class="table__body">
					<div class="table__row table__row_body table__row_empty">
						<div class="empty"><i class="icon icon_calendar"></i>
							<p class="empty-text">There are no completed campaigns to show</p>
						</div>
					</div>
				</div>
				<div class="table__head">
					<div class="table__row table__row_header">
						<div class="table__header">
							<p class="table__text">Current  Campaign</p>
						</div>
					</div>
				</div>
				<div class="table__body">
					<div class="table__row table__row_body">
						<div class="table__data"><i class="icon icon_calendar"></i>
							<p class="table__data__date">28</p>
							<p class="table__data__month">May</p>
						</div>
						<div class="table__data">
							<p class="table__text">Campaign 1 for <span>Brand A</span></p>
						</div>
						<div class="table__data">
							<p class="table__text">Seattle, Wa</p>
						</div>
						<div class="table__data">
							<p class="table__text">Hello, I am John Doe, I have arund 50 followers on Instagram, and 100k on facebook. I think my audience would be a good fit for your campaign because</p>
						</div>
						<div class="table__data">
							<div class="table__buttons">
								<div class="button button_green">View Campaign Details</div>
								<div class="button button_green">View Full Pitch</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<section class="section section_cash">
		<?php $sum = 0;
		foreach ($applications_list as $application) {
      $sum += $application['job_price'];
    }
		?>

			<div class="section__container">
				<div class="money">
					<div class="money__balance">
						<p class="money__balance__header">Account Balance</p>
						<p class="money__balance__amount"><?php echo $operation['currency'].$sum;?></p>
					</div>
					<div class="money__available">
						<p class="money__available__header">Available to Cash Out</p>
						<p class="money__available__amount"><?php echo $currency.$available_cash;?></p>
					</div>
				</div>
				<div class="cash-out">
					<p class="cash-out__header">Cash Out</p>
					<form class="form form_cash">
						<div class="inputs">
							<div class="input__block">
								<input id="first" type="text" class="form__input"/>
								<label for="first" class="form__input__label">How much would you like to cash out?</label>
							</div>
							<div class="input__block">
								<input id="second" type="text" class="form__input"/>
								<label for="second" class="form__input__label">Where should we send it?</label>
							</div>
						</div>
						<div class="buttons"><a href="#" class="button button_grey">View Payment History</a>
							<button type="submit" class="button button_orange">Cash Me Out</button>
						</div>
					</form>
				</div>
			</div>
		</section>
	</div>
<?php
get_footer('new');
?>