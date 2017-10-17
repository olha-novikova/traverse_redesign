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
?><!-- Titlebar
================================================== -->
<?php 
$map 			= Kirki::get_option( 'workscout', 'pp_enable_jobs_map', 0 ); 
$titlebar 		= get_post_meta( $post->ID, 'pp_page_titlebar', true ); 
$header_image 	= Kirki::get_option( 'workscout', 'pp_jobs_header_upload', '' ); 

if($titlebar == 'off') {
	// no titlebar
} else { 
	if(!empty($header_image)) { ?>
		<div id="titlebar" class="photo-bg single <?php if($map) echo " with-map"; ?>" style="background: url('<?php echo esc_url($header_image); ?>')">
	<?php } else { ?>
		<div id="titlebar" class="single <?php if($map) echo " with-map"; ?>">
	<?php } ?>
		<div class="container">
			<div class="sixteen columns">
				<div class="ten columns">
					<?php 
					$count_jobs = wp_count_posts( 'job_listing', 'readable' );
					?>
					<span class="showing_jobs" style="display: none">
						<?php esc_html_e('Browse Jobs','workscout') ?>
					</span>
					<h2 ><?php 
 					printf(_n(  'We have <em class="count_jobs">%s</em> <em class="job_text">job offer</em> for you', 'We have <em class="count_jobs">%s</em> <em class="job_text">job offers</em> for you' , $count_jobs->publish, 'workscout' ), $count_jobs->publish); 
					//printf( esc_html__( 'We have %s job offers for you', 'workscout' ), '<em class="count_jobs">' . $count_jobs->publish . '</em>' ) ?></h2>
				</div>
				<?php 
				$call_to_action = Kirki::get_option( 'workscout', 'pp_call_to_action_jobs', 'job' );
				switch ($call_to_action) {
				  	case 'job':
				  		get_template_part( 'template-parts/button', 'job' );
				  		break;			  	
				  	case 'resume':
				  		get_template_part( 'template-parts/button', 'resume' );
				  		break;
				  	default:
				  		# code...
				  		break;
			  	}  
			 	?>
			</div>
		</div>
	</div>
<?php 
}
	$layout  = get_post_meta($post->ID, 'pp_sidebar_layout', true);
	if(empty($layout)) { $layout = 'right-sidebar'; }

	wp_dequeue_script('wp-job-manager-ajax-filters' );
	wp_enqueue_script( 'workscout-wp-job-manager-ajax-filters' );

if($map) { 
	$all_map = Kirki::get_option( 'workscout', 'pp_enable_all_jobs_map', 0 ); 
	if($all_map){ 
		echo do_shortcode('[workscout-map type="job_listing" class="jobs_page"]'); 
	} else { ?>
		<div id="search_map"></div>
	<?php 
	}
} ?>

<div class="container  wpjm-container <?php echo esc_attr($layout); ?>">
	<?php  get_sidebar('jobs');?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('eleven columns'); ?>>
		<div class="padding-right">
			<?php 
			if ( ! empty( $_GET['search_keywords'] ) ) {
				$keywords = sanitize_text_field( $_GET['search_keywords'] );
			} else {
				$keywords = '';
			}
			?>
			<form class="list-search"  method="GET" action="<?php echo get_permalink(get_option('job_manager_jobs_page_id')); ?>">
				<div class="search_keywords">
					<button><i class="fa fa-search"></i></button>
					<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'job title, keywords or company name', 'workscout' ); ?>" value="<?php echo esc_attr( $keywords ); ?>" />
					<div class="clearfix"></div>
				</div>
			</form>

 
			<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
			<footer class="entry-footer">
				<?php edit_post_link( esc_html__( 'Edit', 'workscout' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .entry-footer -->
		</div>
	</article>

</div>
<?php
get_footer(); 
?>
