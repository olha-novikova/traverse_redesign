<?php
/**
 * Template Name: Job Categories Page Template
 *
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 * @package WordPress
 * @subpackage resumes
 * @since resumes 1.0
 */

get_header();


while ( have_posts() ) : the_post(); ?>
<?php $header_image = get_post_meta($post->ID, 'pp_job_header_bg', TRUE); 
if(!empty($header_image)) { ?>
<div id="titlebar" class="photo-bg" style="background: url('<?php echo esc_url($header_image); ?>')">
<?php } else { ?>
<div id="titlebar">
<?php } ?>

	<div class="container">
		<div class="sixteen columns">
			<div class="ten columns">
				<h2><?php the_title();?></h2>
			</div>
			<?php if(get_option('workscout_enable_add_job_button')) { ?>
		        <div class="six columns">
					<a href="<?php echo get_permalink(get_option('job_manager_submit_job_form_page_id')); ?>" class="button"><?php esc_html_e('Post a Job, It\'s Free!','workscout'); ?></a>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<!-- 960 Container -->
<div class="container page-container home-page-container">
    <article  <?php post_class("sixteen columns"); ?>>
        <?php the_content(); ?>
    </article>
</div>

<div  style="margin-bottom:-45px;"></div>
<?php endwhile; // end of the loop.

get_footer(); ?>