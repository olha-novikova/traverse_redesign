<?php

/*
* Template Name: Page For non Login User
*/

get_header("newhomepage");

?>
<div class="wrapper">

    <div class="sections">
        <div class="section section_about">
            <div class="container container_firstscreen">
                <?php
                while ( have_posts() ) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile;
                ?>
            </div>
        </div>



        <section class="section section_insta">
            <ul class="insta__photos">
              <?php $photos = scrape_insta('jrrny'); foreach ($photos as $elem) : ?>
                <li class="insta__photo"><img src="<?php echo $elem->display_src ?>" alt="" class="insta__photo__image"/></li>
                <?php endforeach; ?>
            </ul><a href="http://www.instagram.com/jrrny" class="button button_results button__insta">Follow us on Instagram</a>
        </section>


    </div>
<?php get_footer("newhomepage"); ?>

