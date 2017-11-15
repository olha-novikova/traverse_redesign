<?php
if ( ! is_user_logged_in() ) {
    get_header("newhomepage");?>

    <div class="wrapper">
        <div class="sections">
            <div class="section section_general_page"> </div>
            <?php
            while ( have_posts() ) : the_post(); ?>
            <section class="section_main_content">
                <div class="container container_content">

                    <?php the_content(); ?>
                </div>
            </section>
            <?php endwhile;?>
            <section class="section section_insta">
                <ul class="insta__photos">
                    <?php $photos = scrape_insta('jrrny'); foreach ($photos as $elem) : ?>
                        <li class="insta__photo"><img src="<?php echo $elem->display_src ?>" alt="" class="insta__photo__image"/></li>
                    <?php endforeach; ?>
                </ul><a href="http://www.instagram.com/jrrny" class="button button_results button__insta">Follow us on Instagram</a>
            </section>
        </div>
    </div>
    <?php get_footer("newhomepage");
}else{
    get_header('new');
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
}
?>
