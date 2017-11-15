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
        $header_image = get_post_meta( $post->ID, '_header_image', TRUE );
        $spot =  ( get_post_meta($post->ID, '_applications_number', TRUE) ? get_post_meta($post->ID, '_applications_number', TRUE) : 1 );
        $website = ( get_post_meta( $post->ID, '_website', TRUE )) ? ( get_post_meta( $post->ID, '_website', TRUE )) : "#";
        ?>

        <section class="section_profile section_profile-completed">
            <div class="profile__background">
                <!--<div class="profile__background__gradient"></div>-->
                <div class="profile__image_circle" style="background-image: url(' <?php echo get_company_logo_url($post->ID); ?>'); background-size:cover; ">
                </div>
            </div>
            <div class="profile__action">
                <ul class="profile__links">
                    <li class="profile__link profile__link_brand"><a href="<?php echo $website;?>" class="profile__brandname"><?php the_company_name(); ?></a></li>
                </ul>
            </div>
        </section>
        <div class="content">
            <section class="section section_listing-view">
                <div class="section__container">
                    <div class="section__top">
                        <p class="section__header"><?php the_title(); ?></p>

                        <?php do_action( 'single_job_listing_meta_after' ); ?>

                        <?php if ( candidates_have_active_resume(get_current_user_id()) && candidates_can_apply() &&  !user_has_applied_for_job( get_current_user_id(), $post->ID )) : ?>
                            <?php
                            get_job_manager_template( 'job-application.php' );
                            ?>
                        <?php elseif( !candidates_have_active_resume( get_current_user_id()) ):?>
                            <div class="job-manager-applications-applied-notice"> You account is not active yet. Please add more information about yourself</div>
                        <?php elseif( !candidates_can_apply() ):?>
                            <div class="job-manager-applications-applied-notice"> This job has expired or has filled already</div>
                        <?php elseif( !user_has_applied_for_job(get_current_user_id(), $post->ID) ):?>
                            <div class="job-manager-applications-applied-notice"> You have successfully pitched on this listing.</div>
                        <?php endif; ?>
                    </div>
                    <div class="section__body">
                        <?php if ($header_image ){?>
                        <img src="<?php echo $header_image;?>" alt="" class="listing-view__image"/>
                        <?php }?>
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
                                        <?php
                                        for ( $i=1; $i <= $spot; $i++){
                                            echo '<div class="table__influencer"></div>';
                                        }
                                        ?>
                                    </div>
                                    <p class="spots__text">Spots available: <span><?php echo $spot;  ?></span></p>
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
                        $assets_available_files   = get_post_meta($post->ID,'_asset_upload', true);

                        if ( $assets_available_files ){?>
                        <div class="listing__view__assets">
                            <p class="listing-view__assets__header">Example Assets</p>
                                <?php
                                if ( $assets_available_files ){ ?>
                                    <div class="assets">
                                        <?php
                                        foreach ($assets_available_files as $assets_available_file){
                                            $path_parts = pathinfo($assets_available_file);
                                            $extension = $path_parts['extension'];
                                            ?>
                                            <a href="<?php echo $assets_available_file; ?>" download>
                                                <?php
                                                if( in_array($extension,array("jpeg","jpg","png","gif")) ){?>
                                                    <img src="<?php echo $assets_available_file; ?>" alt="Download File" class="asset"/>
                                                <?php } elseif( in_array($extension,array("pdf")) ){?>
                                                   <img src="<?php echo get_stylesheet_directory_uri();?>/img/pdf-download-icon.png" alt="Download File" class="asset"/>
                                                <?php }elseif( in_array( $extension,array("doc","docx") ) ){?>
                                                   <img src="<?php echo get_stylesheet_directory_uri();?>/img/word-download-icon.png" alt="Download File" class="asset"/>
                                                <?php } else{?>
                                                    <img src="<?php echo get_stylesheet_directory_uri();?>/img/file-downloads-icon.png" alt="Download File" class="asset"/>
                                                <?php } ?>
                                            </a> <?php
                                        } ?>
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
