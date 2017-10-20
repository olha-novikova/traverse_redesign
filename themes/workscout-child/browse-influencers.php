<?php
/**
 * Template Name: Page Browse Influencers
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
        <div class="content">
            <section class="section section_panel">
                <div class="section__container">
                    <div class="panel__influencers">
                        <p class="panel__influencers__text">Browse Influencers (<span class="panel__influencers__number"><?php get_count_of_influencers(); ?><span>)</p>
                    </div>
                    <div class="panel__sort dropdown show">
                        <p class="panel__sort__label">Order By:</p><a href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="panel__sort__input dropdown-toggle">Audience Size</a>
                        <div aria-labelledby="dropdownMenuLink" class="dropdown-menu">
                            <a href="#" class="dropdown-item" data-sort="asc" data-sort_by="date">Newest to oldest</a>
                            <a href="#" class="dropdown-item" data-sort="desc" data-sort_by="audience">Audience size</a>
                            <a href="#" class="dropdown-item" data-sort="desc" data-sort_by="companies">Total Campaigns Completed</a>
                            <a href="#" class="dropdown-item" data-sort="desc" data-sort_by="date">Oldest to newest</a>
                        </div>
                    </div>
                    <div class="panel__search">
                        <input type="search" placeholder="Search Influencers" class="panel__search__input"/>
                        <button type="submit" class="button button_search"></button>
                    </div>
                </div>
            </section>
            <section class="section section_browse-influencers">
                <?php
                while ( have_posts() ) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile;
                ?>
            </section>
        </div>
    </main>
<?php
get_footer('new');
?>