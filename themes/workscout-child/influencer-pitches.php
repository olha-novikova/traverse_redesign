<?php
/**
 * Template Name: Page My Pitches
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
		<?php include( locate_template( 'template-parts/my-pitches') ); ?>
	</div>
  </main>
<?php
get_footer('new');
?>