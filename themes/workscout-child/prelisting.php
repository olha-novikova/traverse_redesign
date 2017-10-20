<?php
/**
 * Template Name: PreListing
 *
 */

get_header('new');
get_sidebar();

$userid = get_current_user_id(); ?>
<main class="main">
    <?php get_template_part('template-parts/page-header')?>
    <div class="content">
        <section class="social_products_section">
            <h1 class="centered">Choose Your Campaign Type:</h1>

            <?php

            while ( have_posts() ) : the_post();

                the_content();

            endwhile;
            ?>
        </section>

    </div>
</main>

<?php

get_footer('new');