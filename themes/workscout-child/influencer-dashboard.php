<?php
/**
 * Template Name: Page Influencer Dashboard
 *
 */


if (!session_id()) session_start();

$user = wp_get_current_user();

if ( !in_array( 'administrator', (array) $user->roles ) && !in_array( 'candidate', (array) $user->roles )) wp_redirect( home_url() );



get_header('new');
get_sidebar();?>
	<main class="main">
		<?php get_template_part('template-parts/page-header')?>
	<div class="content">
	  <?php get_template_part('template-parts/recent-opportunities') ?>
	  <?php include( locate_template( 'template-parts/my-pitches.php') ); ?>
    <?php include( locate_template('template-parts/template-balance.php') ); ?>
	</div>
  </main>
<?php
get_footer('new');
?>