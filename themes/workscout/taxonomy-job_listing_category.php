<?php
/**
 * Job Category
 *
 * @package WorkScout
 * @since WorkScout 1.0
 */

$taxonomy = get_taxonomy( get_queried_object()->taxonomy );

get_header(); ?>

<?php 

$t_id = get_queried_object()->term_id;
$term_meta = get_option( "taxonomy_$t_id" ); 
$map =  Kirki::get_option( 'workscout', 'pp_enable_jobs_map', 0 ); 

$header_image = isset($term_meta['upload_header']) ? $term_meta['upload_header'] : '';
if(!empty($header_image)) { ?>
	<div id="titlebar" class="photo-bg single <?php if($map) echo " with-map"; ?>" style="background: url('<?php echo esc_url($header_image); ?>')">
<?php } else { ?>
	<div id="titlebar" class="single <?php if($map) echo " with-map"; ?>">
<?php } ?>
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
	wp_dequeue_script('wp-job-manager-ajax-filters' );
	wp_enqueue_script( 'workscout-wp-job-manager-ajax-filters' );


if($map) { ?><div id="search_map"></div><?php } ?>


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
			<form class="list-search"  method="GET" action="">
				<div class="search_keywords">
					<button><i class="fa fa-search"></i></button>
					<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'job title, keywords or company name', 'workscout' ); ?>" value="<?php echo esc_attr( $keywords ); ?>" />
					<div class="clearfix"></div>
				</div>
			</form>

			<?php echo do_shortcode('[jobs categories='.get_query_var('job_listing_category').' show_filters="false"]'); ?>
			<footer class="entry-footer">
				<?php edit_post_link( esc_html__( 'Edit', 'workscout' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .entry-footer -->
		</div>
	</article>
	

</div>
<?php get_footer(); ?>

