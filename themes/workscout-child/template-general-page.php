<?php

/*
* Template Name: Page For Pages Without Role
*/

get_header("newhomepage");

?>
<div class="wrapper">
    <?php
    while ( have_posts() ) : the_post(); ?>
    <div class="sections">
        <div class="section section_general_page">
        </div>
        <section>
            <div class="container container_content">
                <?php the_content(); ?>
            </div>
        </section>
        <?php endwhile;
        ?>
        <section class="section section_insta">
            <ul class="insta__photos">
              <?php $photos = scrape_insta('jrrny'); foreach ($photos as $elem) : ?>
                <li class="insta__photo"><img src="<?php echo $elem->display_src ?>" alt="" class="insta__photo__image"/></li>
                <?php endforeach; ?>
            </ul><a href="http://www.instagram.com/jrrny" class="button button_results button__insta">Follow us on Instagram</a>
        </section>

    </div>
<?php get_footer("newhomepage"); ?>

