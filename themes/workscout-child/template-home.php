<?php
/**
 * Template Name: Page with Jobs Search
 *
 * @package WordPress
 * @subpackage workscout
 * @since workscout 1.0
 *  style="background-image: url(http://traverseinfluence.com/wp-content/uploads/2017/04/banner1.jpg)"
 */

get_header(); ?>
<?php $fancy_header = Kirki::get_option( 'workscout','pp_transparent_header');  ?>

<div id="banner22" <?php echo workscout_get_search_header();?>  class="workscout-search-banner <?php if( $fancy_header ) { ?> with-transparent-header parallax background <?php } ?>" >
    <div class="container">
        <div class="sixteen ">

            <div class="search-container sc-jobs">
                <div class="introtext"><h2>WHERE BRANDS AND TRAVEL <br>INFLUENCERS CONNECT </h2>

                    <div class="jrrny">JRRNY brings you Traverse - <br>the intersection of great brands <br> and compelling travel influencers.<br>
                        <?php if(!is_user_logged_in()) { ?>
                            <a href="#signup-dialog" class="small-dialog popup-with-zoom-anim button centered"><i class="fa fa-user"></i> SIGN UP</a>
                        <?php } ?>
                    </div>
                </div>
                <!-- Form -->
                <!--h2><?php esc_html_e('Find Job','workscout') ?></h2-->
                <!--form method="GET" action="<?php echo get_permalink(get_option('job_manager_jobs_page_id')); ?>">

                    <input type="text" id="search_keywords" name="search_keywords"  class="ico-01" placeholder="<?php esc_attr_e('project type, locations, brands, etc.','workscout'); ?>" value=""/>

                    <?php if ( get_option( 'job_manager_regions_filter' ) || is_tax( 'job_listing_region' ) ) {  ?>
                        <?php
                        $dropdown = wp_dropdown_categories( apply_filters( 'job_manager_regions_dropdown_args', array(
                            'show_option_all' => __( 'All Regions', 'wp-job-manager-locations' ),
                            'hierarchical' => true,
                            'orderby' => 'name',
                            'taxonomy' => 'job_listing_region',
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

                </form-->
                <!-- Browse Jobs -->
                <!---<div class="browse-jobs">
                    <?php
                    if(Kirki::get_option( 'workscout','pp_categories_page')){
                        $categoriespage = Kirki::get_option( 'workscout','pp_categories_page');
                    } elseif (ot_get_option('pp_categories_page')){
                        $categoriespage = ot_get_option('pp_categories_page');
                    }

                    if(!empty($categoriespage)) {
                        printf( __( ' Or browse opportunities by <a href="%s">category</a>', 'workscout' ), get_permalink($categoriespage) );
                    } ?>
                </div>---->

                <?php
                if(Kirki::get_option( 'workscout','pp_home_job_counter')) { ?>
                    <!-- Announce -->
                    <div class="announce">
                        <?php $count_jobs = wp_count_posts( 'job_listing', 'readable' );
                        printf( esc_html__( 'We have %s listings for you!', 'workscout' ), '<strong>' . $count_jobs->publish . '</strong>' ) ?>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>

<div class="search-container1 sc-jobs how-it-work" style="background-image: url(http://jrrny.com/wp-content/uploads/2017/08/fineas-anton-108019-e1503911499206.jpg);">

    <div class="container">
        <div class="how-it-work-wrap" >
            <div class="cxs"><h2>HOW DOES IT WORK?</h2></div>
            <!--jrrnyb-->
            <div class="how-it-work-content jrrny">
                <h3>Brands:</h3>
                <p>
                    <strong>Free to join! </strong><br>
                    Choose from thousands of influencers from small to large.<br>
                    Assign influencers based on your choice.<br>
                    Payment managed through our integrated escrow services.<br>
                </p>
            </div>
            <div class="how-it-work-content jrrny">
                <h3>Influencers:</h3>
                <p>
                    <strong>Free to join!</strong> <br>
                    Choose the projects you want from dozens of brands and make money from your influence.<br>
                </p>
            </div>
            <div class="clear"></div>
            <?php if(!is_user_logged_in()) { ?>
                <a class="small-dialog popup-with-zoom-anim button centered" href="#signup-dialog">Sign up</a>
            <?php } ?>

         </div>
                    <!-- Form -->
                    <!--h2><?php esc_html_e('Find Job','workscout') ?></h2-->
                    <!--form method="GET" action="<?php echo get_permalink(get_option('job_manager_jobs_page_id')); ?>">

                        <input type="text" id="search_keywords" name="search_keywords"  class="ico-01" placeholder="<?php esc_attr_e('job title, keywords or company name','workscout'); ?>" value=""/>

                        <?php if ( get_option( 'job_manager_regions_filter' ) || is_tax( 'job_listing_region' ) ) {  ?>
                            <?php
                            $dropdown = wp_dropdown_categories( apply_filters( 'job_manager_regions_dropdown_args', array(
                                'show_option_all' => __( 'All Regions', 'wp-job-manager-locations' ),
                                'hierarchical' => true,
                                'orderby' => 'name',
                                'taxonomy' => 'job_listing_region',
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

                    </form-->
                    <!-- Browse Jobs -->
                    <!---<div class="browse-jobs">
                        <?php
                        if(Kirki::get_option( 'workscout','pp_categories_page')){
                            $categoriespage = Kirki::get_option( 'workscout','pp_categories_page');
                        } elseif (ot_get_option('pp_categories_page')){
                            $categoriespage = ot_get_option('pp_categories_page');
                        }

                        if(!empty($categoriespage)) {
                            printf( __( ' Or browse job offers by <a href="%s">category</a>', 'workscout' ), get_permalink($categoriespage) );
                        } ?>
                    </div>---->

                    <?php
                   if(Kirki::get_option( 'workscout','pp_home_job_counter')) { ?>
                    <!-- Announce -->
                    <div class="announce">
                        <?php $count_jobs = wp_count_posts( 'job_listing', 'readable' );
                        printf( esc_html__( 'We have %s job offers for you!', 'workscout' ), '<strong>' . $count_jobs->publish . '</strong>' ) ?>
                    </div>
                    <?php } ?>
        </div>
    </div>
</div>
<div class="greatBg">
    <div class="container">
        <p>Great Exposure for Brands &amp; Influencers</p>
    </div>
</div>
<div class="lastimg" style="background-image: url(http://traverseinfluence.com/wp-content/uploads/2017/04/banner3.jpg); " >
	<div class="container">
		<div class="wjarrny">
			<h2>WHAT IS JRRNY?</h2>
			<p>JRRNY is a social travel platform and media company founded in 2015, and based in Seattle, WA. With over 10,000 content producers - from the adventure-seeking backpacker, to the non-traveling local expert - over 30k active monthly members, and over 1MM travel experiences read in 2016, JRRNY has become a foundational place to share and discover experiences in the travel and outdoor industries.</p>
			<a class="button" href="http://jrrny.com/"target="_blank">VIEW MORE</a>
		</div>
	</div>
</div>
<div class="ng_gallery_section">
 <?php echo do_shortcode('[nggallery id=4]'); ?>
 <div class="follow_instagram">
     <a href="https://www.instagram.com/jrrny/"><i class="fa fa-instagram" aria-hidden="true"></i><span>Follow us on Instagram</span></a>
 </div>
 </div>

<?php get_footer(); ?>
