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

  else: wp_redirect(home_url());
endif;

get_header('new');
global $wpdb;
get_sidebar();?>
	<main class="main">
		<?php get_template_part('template-parts/page-header')?>
	<div class="content">
	  <?php get_template_part('template-parts/recent-opportunities') ?>
	  <?php get_template_part('template-parts/my-pitches') ?>
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
					<form action="send_payment_request" class="form form_cash">
						<div class="inputs">
							<div class="input__block">
								<input id="first" type="text" class="form__input"/>
								<label for="first" class="form__input__label">How much would you like to cash out?</label>
							</div>
							<div class="input__block">
								<input id="second" type="text" class="form__input" type="email"/>
								<label for="second" class="form__input__label">Where should we send it?</label>
							</div>
						</div>
						<div class="buttons">
              <a href="/my-balance" class="button button_grey">View Payment History</a>
							<button type="submit" class="button button_orange">Cash Me Out</button>
						</div>
					</form>
				</div>
			</div>
		</section>
	</div>
  </main>
<?php
get_footer('new');
?>