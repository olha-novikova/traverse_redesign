<?php

/*
* Template Name: New About Page
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

        <section class="section section_contact">
            <div class="container container_contact">
                <p class="section__header section__header_contact">Contact us</p>
                <p class="section__description section__description_contact">The Range Influence team is here to provide you with more information, answer any questions you may have and create an effective solution for influencer campaign needs.</p>
                <div class="contacts">
                    <div class="contacts__block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/marker.png" alt="" class="contacts__icon"/>
                        <p class="contacts__text">Seattle, Washington</p>
                    </div>
                    <div class="contacts__block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/mobile.png" alt="" class="contacts__icon"/>
                        <p class="contacts__text">(323)539-7301 help@RangeInfluence.com</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section_insta">
            <ul class="insta__photos">
              <?php $photos = scrape_insta('jrrny'); foreach ($photos as $elem) : ?>
                <li class="insta__photo"><img src="<?php echo $elem->display_src ?>" alt="" class="insta__photo__image"/></li>
                <?php endforeach; ?>
            </ul><a href="http://www.instagram.com/jrrny" class="button button_results button__insta">Follow us on Instagram</a>
        </section>


    </div>
<?php get_footer("newhomepage"); ?>

