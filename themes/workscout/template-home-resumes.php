<?php
/**
 * Template Name: Page with Resumes Search
 *
 * @package WordPress
 * @subpackage workscout
 * @since workscout 1.0
 */

get_header(); ?>
<?php $fancy_header = Kirki::get_option( 'workscout','pp_resume_home_transparent_header'); ?>

<div id="banner" <?php echo workscout_get_resume_search_header();?>  class="workscout-search-banner <?php if( $fancy_header) { ?> with-transparent-header parallax background <?php } ?>" >
    <div class="container">
        <div class="sixteen columns">
            
            <div class="search-container sc-resumes">

                <!-- Form -->
                <h2><?php esc_html_e('Find Candidate','workscout') ?></h2>
                <form method="GET" action="<?php echo get_permalink(get_option('resume_manager_resumes_page_id')); ?>">
            
                    <input type="text" id="search_keywords" name="search_keywords"  class="ico-01" placeholder="<?php esc_attr_e( 'Search freelancer services (e.g. logo design)', 'workscout' ); ?>" value=""/>
                   
               
                    <?php if ( get_option( 'resume_manager_regions_filter' ) || is_tax( 'resume_region' ) ) {  ?>
                        <?php
                        $dropdown = wp_dropdown_categories( apply_filters( 'job_manager_regions_dropdown_args', array(
                            'show_option_all' => __( 'All Regions', 'wp-job-manager-locations' ),
                            'hierarchical' => true,
                            'orderby' => 'name',
                            'taxonomy' => 'resume_region',
                            'name' => 'search_region',
                            'id' => 'search_location',
                            'class' => 'search_region job-manager-category-dropdown chosen-select-deselect ' . ( is_rtl() ? 'chosen-rtl' : '' ),
                            'hide_empty' => 0,
                            'selected' => isset( $_GET[ 'search_region' ] ) ? $_GET[ 'search_region' ] : '',
                            'echo'=>false,
                        ) ) );
                        $fixed_dropdown = str_replace("&nbsp;", "", $dropdown); echo $fixed_dropdown;
                    } else { ?>
                    <input type="text" id="search_location" name="search_location" class="ico-02" placeholder="<?php esc_attr_e('city, province or region','workscout'); ?>" value=""/> 
                    <?php } ?>

                    <button><i class="fa fa-search"></i></button>

                </form>

                <div class="browse-jobs">
                    <?php 
                    if(Kirki::get_option( 'workscout','pp_resume_categories_page')){
                        $categories_page = Kirki::get_option( 'workscout','pp_resume_categories_page');
                    } 

                    if(!empty($categoriespage)) { 
                        printf( __( ' Or browse resumes by <a href="%s">category</a>', 'workscout' ), get_permalink($categories_page) );
                    } ?>
                </div>
                <?php 
                if(Kirki::get_option( 'workscout','pp_home_resume_counter')) : ?>
                <!-- Announce -->
                <div class="announce">
                    <?php $count_jobs = wp_count_posts( 'resume', 'readable' ); 
                    printf( esc_html__( 'We have %s resumes in our database', 'workscout' ), '<strong>' . $count_jobs->publish . '</strong>' ) ?>
                </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

<?php
while ( have_posts() ) : the_post(); ?>
<!-- 960 Container -->
<div class="container page-container home-page-container">
    <article <?php post_class("sixteen columns"); ?>>
                <?php the_content(); ?>
    </article>
</div>
<?php endwhile; // end of the loop.

get_footer(); ?>