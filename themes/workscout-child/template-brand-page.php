<?php
/**
 * Template Name: Page Brand
 *
 */
if (is_user_logged_in() ){
    $user = wp_get_current_user();
    if ( !in_array( 'employer', (array) $user->roles ) && !in_array( 'administrator', (array) $user->roles ) ) wp_redirect(home_url());
}else wp_redirect(home_url());
get_header('new');
global $wpdb;
get_sidebar();?>
    <main class="main">
        <?php get_template_part('template-parts/page-header')?>
        <?php
        while ( have_posts() ) : the_post(); ?>
            <?php the_content(); ?>
        <?php endwhile;
        ?>
    </main>
<?php
get_footer('new');
?>