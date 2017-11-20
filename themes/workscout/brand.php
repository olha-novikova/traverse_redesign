<?php
/**
 * Template Name: login with brand
 *
 * @package WordPress
 * @subpackage workscout
 * @since workscout 1.0

 */
//require_once('../../../../wp-load.php');
//require_once('../../../wp-config.php');
//require_once('../../../wp-includes/wp-db.php');

get_header(); 
global $wpdb;
?>

<?php
while ( have_posts() ) : the_post(); ?>

<div class="container " >
    <article <?php post_class("sixteen columns"); ?>>
                <?php the_content(); ?>
    </article>
</div>
<?php endwhile;?> 


 

<?php get_footer(); ?>
