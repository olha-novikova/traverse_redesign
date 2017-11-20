<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WorkScout
 */

get_header();
 ?>

<div id="titlebar" class="single">
	<div class="container">
		<div class="sixteen columns">
			<h1>
			<?php 
				if(is_shop()){
				 the_archive_title(); 
				} else {
					the_title(); 
				}?>
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
	
$layout = Kirki::get_option( 'workscout', 'pp_shop_layout' ); 
$class = ($layout !="full-width") ? "eleven columns" : "sixteen columns" ;

?>

<div class="container <?php echo esc_attr($layout); ?>">
	
	<article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>

		<?php woocommerce_content(); ?>

	</article>

	<?php if($layout !="full-width") { get_sidebar('shop'); } ?>

</div>



<?php get_footer(); ?>
