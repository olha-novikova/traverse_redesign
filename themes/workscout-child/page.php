<?php
get_header('new');
global $wpdb;
get_sidebar();?>
<main class="main">
    <?php
        while ( have_posts() ) : the_post(); ?>
          <?php the_content(); ?>
        <?php endwhile;
    ?>
</main>
<?php
get_footer('new');
?>