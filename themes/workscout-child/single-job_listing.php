<?php
/**
 * The template for displaying all single jobs.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WorkScout
 */

get_header('new');
global $wpdb;
get_sidebar();?>
<main class="main">
    <?php
    while ( have_posts() ) : the_post();
        $header_image = get_post_meta($post->ID, '_header_image', TRUE);
        $product_id = get_post_meta($post->ID, '_wcpl_jmfe_product_id',true);
        $package = get_post( $product_id);
        ?>

        <section class="section_profile section_profile-completed">
            <div class="profile__background">
                <div class="profile__background__gradient"></div>
                <div class="profile__image_circle" style="background-image: url(' <?php echo get_company_logo_url($post->ID); ?>'); background-size:cover; ">
                </div>
            </div>
            <div class="profile__action">
                <ul class="profile__links">
                    <li class="profile__link profile__link_brand"><a href="#" class="profile__brandname"><?php the_company_name(); ?></a></li>
                </ul>
            </div>
        </section>
        <div class="content">
            <section class="section section_listing-view">
                <div class="section__container">
                    <div class="section__top">
                        <p class="section__header"><?php the_title(); ?></p>

                        <?php do_action( 'single_job_listing_meta_after' ); ?>

                        <?php if ( candidates_can_apply() &&  !user_has_applied_for_job( get_current_user_id(), $post->ID )) : ?>
                            <?php
                            get_job_manager_template( 'job-application.php' );
                            ?>
                        <?php endif; ?>
<!--                        -->
                    </div>
                    <div class="section__body">
                        <img src="<?php echo $header_image;?>" alt="" class="listing-view__image"/>
                        <div class="listing-view__text">
                            <div class="listing-view__description">
                                <p class="listing__view__header">
                                    <span class="company-name"><?php the_company_name(); ?></span> created the <span class="company-campaign"> Campaign</p>
                                <p class="listing-view__date">Posted:
                                    <span class="company-date"><?php printf(get_post_time( 'F d' ))?></span> at
                                    <span class="company-time"><?php printf(get_post_time( 'g:i a' ))?></span>
                                </p>
                                <p class="listing-view__description__text">
                                    <?php
                                    echo esc_html__(get_the_content());
                                    ?>
                                </p>
                                <?php do_action( 'single_job_listing_meta_after' ); ?>

                                <?php if ( candidates_can_apply() &&  !user_has_applied_for_job( get_current_user_id(), $post->ID )) : ?>
                                    <?php
                                    get_job_manager_template( 'job-application.php' );
                                    ?>
                                <?php endif; ?>
                            </div>
                            <div class="listing-view__overview">
                                <p class="listing-view__overview__header">Campaign Overview</p>
                                <p class="listing-view__overview__item listing-view__overview__item_budget">
                                    <?php   $salary = get_post_meta( $post->ID, '_targeted_budget', true );?>
                                    Budget: $<span><?php echo esc_html( $salary );?></span>
                                </p>
                                <p class="listing-view__overview__item listing-view__overview__item_geo"><?php ws_job_location(); ?></p>
                                <p class="listing-view__overview__item listing-view__overview__item_date"><?php echo date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, '_job_expires', true ) ) ) ?></p>
                                <div class="spots">
                                    <div class="table__influencers">
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                    </div>
                                    <p class="spots__text">Spots available: <span><?php echo  get_job_application_count( $post->ID )  ?></span></p>
                                </div>
                                <?php $target_socials      = get_post_meta($post->ID,'_target_social', true);
                                if ( $target_socials ){?>
                                    <p class="listing-view__overview__item-text">Target Social Channels:</p>
                                    <div class="listing-view__socials">
                                        <?php
                                        foreach($target_socials as $target_social)  {
                                            if ( $target_social == 'instagram')
                                                echo '<img src="'.get_stylesheet_directory_uri().'/img/listing-in.png" alt="" class="listing-view__social listing-view__social_in"/>';
                                            if ( $target_social == 'facebook')
                                                echo '<img src="'.get_stylesheet_directory_uri().'/img/listing-fb.png" alt="" class="listing-view__social listing-view__social_fb"/>';
                                        }?>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                        <?php
                        $assets_available_link  = get_post_meta($post->ID,'_asset_links', true);
                        $assets_available_files   = get_post_meta($post->ID,'_asset_upload', true);

                        if ( $assets_available_files || $assets_available_link ){?>
                        <div class="listing__view__assets">
                            <p class="listing-view__assets__header">Example Assets</p>
                                <?php

                                if ( $assets_available_link ) echo "<span><a href=\"".$assets_available_link."\" target=\"_blank\">Example URL</a> </span>";

                                if ( $assets_available_files ){ ?>
                                    <div class="assets">
                                        <?php
                                        foreach ($assets_available_files as $assets_available_file){?>
                                            <img src="#" alt="" class="asset"/>
                                        <?php } ?>
                                    </div>
                                <?php }
                                ?>
                        </div>
                        <?php  }?>
                    </div>
                </div>
            </section>
        </div>
    <?php

    endwhile;
    ?>
</main>
<?php
get_footer('new');
?>
