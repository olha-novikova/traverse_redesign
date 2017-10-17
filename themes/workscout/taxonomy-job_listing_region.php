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

				<?php echo do_shortcode('[jobs selected_region="'. get_query_var('job_listing_region') .'"  show_filters="false" per_page="10"]'); ?>
			
			
		</div>
	</article>
	<?php  get_sidebar('jobs');?>

</div>
<?php get_footer(); ?>

