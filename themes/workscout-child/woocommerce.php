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

get_header('new');
global $wpdb;
get_sidebar();?>
<main class="main">
    <?php get_template_part('template-parts/page-header')?>
    <div class="content">

        <article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>

            <?php woocommerce_content(); ?>

        </article>

    </div>
</main>
<?php
get_footer('new');
?>
