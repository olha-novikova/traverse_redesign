<?php
/**
 * Job Category
 *
 * @package WorkScout
 * @since WorkScout 1.0
 */

$taxonomy = get_taxonomy( get_queried_object()->taxonomy );

get_header(); ?>
	<div id="titlebar" class="single">
		<div class="container">

			<div class="sixteen columns">
				<h1>
				<?php if( $taxonomy ) : echo esc_attr( $taxonomy->labels->singular_name ); echo ":"; endif; ?>
				<em><?php single_term_title(); ?>	</em>
				</h1>
			
	        	<?php if(function_exists('bcn_display')) { ?>
		        <nav id="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
					<ul>
			        	<?php bcn_display_list(); ?>
			        </ul>
				</nav>
			<?php } ?>
			</div>
		</div>
	</div>

<?php 
	$layout = Kirki::get_option( 'workscout', 'pp_blog_layout' );
	if(empty($layout)) { $layout = 'right-sidebar'; }
?>

<div class="container <?php echo esc_attr($layout); ?>">

	<article id="post-<?php the_ID(); ?>" <?php post_class('eleven columns'); ?>>
		<div class="padding-right">
			<div class="job-list">
				<?php echo do_shortcode('[jobs_by_tag tag="'. get_query_var('term') .'" per_page="10"]'); ?>
			</div>
			
		</div>
	</article>
	<?php  get_sidebar();?>

</div>
<?php get_footer(); ?>

