<?php
/**
 * Template Name: Page Template with Revolution Slider
 *
 * A custom page template with Revolution Slider
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 * @package WordPress
 * @subpackage trizzy
 * @since trizzy 1.0
 */

get_header();


$slider = get_post_meta($post->ID, 'pp_page_layer', true);
if($slider) { putRevSlider($slider); }

while ( have_posts() ) : the_post(); 
	
get_template_part( 'template-parts/content', 'page' ); 

 endwhile; // end of the loop.

get_footer(); ?>