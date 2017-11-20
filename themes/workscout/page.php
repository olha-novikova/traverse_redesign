
<?php

/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WPVoyager
 */

get_header();

if(get_post_meta($post->ID, 'pp_page_slider_status', true) == 'on'){
	$slider = get_post_meta($post->ID, 'pp_page_layer', true);
	if($slider) { putRevSlider($slider); }
}
while ( have_posts() ) : the_post();

	$job_page = get_option('job_manager_jobs_page_id');
	$resume_page = get_option('resume_manager_resumes_page_id');
	
	if(!empty($job_page) && is_page($job_page)){
		get_template_part( 'jobs' );	
	} elseif (!empty($resume_page) && is_page($resume_page)) {
		get_template_part( 'resumes' );
	}
	else {
		get_template_part( 'template-parts/content', 'page' ); 
	}

endwhile; // End of the loop. 

get_footer(); 
?>
