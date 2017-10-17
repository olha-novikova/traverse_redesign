<?php
/**
 * Template Name: Page with new Design
 *
 */
get_header('new');
global $wpdb;

while ( have_posts() ) : the_post(); ?>
  <?php the_content(); ?>
<?php endwhile;

get_footer('new');
?>