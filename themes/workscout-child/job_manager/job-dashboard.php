<div class="content">
    <section class="section section_campaign">
        <div class="section__container">
            <div class="image__block"><img src="<?php echo get_stylesheet_directory_uri();?>/img/laptop.png" alt="" class="image"/></div>
            <div class="section__text">
                <p class="section__header section__header_main">Estimate Your Campaign</p>
                <form id="estimator_module">
                    <div class="section__text__main">
                        <p class="section__header">What is Your Budget?</p>
                        <div class="inputs">
                            <div class="input__block">
                                <input id="first" name="target_budget" type="text" class="form__input"/>
                                <label for="first" class="form__input__label">i.e. $500 - $50,000+</label>
                            </div>
                        </div>
                        <div class="last-block">
                            <div class="last-block__left">
                                <p class="section__header">What Channels? (Check All That Apply)</p>
                                <div class="checkbox__block">
                                    <div class="checkbox">
                                        <input type="checkbox" name="fb_channel" id="fb">
                                        <label for="fb" class="checkbox__label">Facebook</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="ig_channel" id="ig">
                                        <label for="ig" class="checkbox__label">Instagram</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="yt_channel" id="yt">
                                        <label for="yt" class="checkbox__label">YouTube</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="tw_channel" id="tw">
                                        <label for="tw" class="checkbox__label">Twitter</label>
                                    </div>
                                </div>
                                <?php
                                $args = array(
                                    'taxonomy' => 'job_listing_category',
                                    'hide_empty' => false,
                                );
                                $listing_categories = get_terms( $args );
                                ?>
                                <?php wp_enqueue_script( 'wp-job-manager-multiselect' ); ?>

                                <div class="input__block">
                                    <p class="section__header">Campaign   Category (Select all that apply)</p>
                                    <select name="traveler_type[]" class="job-manager-multiselect" multiple="multiple" data-no_results_text="<?php _e( 'No results match', 'wp-job-manager' ); ?>" data-multiple_text="<?php _e( ' Select all that apply', 'wp-job-manager' ); ?>">
                                        <?php

                                        if( $listing_categories && ! is_wp_error($listing_categories) ){
                                            foreach ($listing_categories as $listing_category){ ?>
                                                <option value="<?php echo $listing_category->slug; ?>"><?php echo $listing_category->name; ?></option>
                                            <?php }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="last-block__right">
                                <p class="section__header">Exclude</p>
                                <div class="checkbox__block">
                                    <div class="checkbox">
                                        <input type="checkbox" name="micro_exclude" id="micro">
                                        <label for="micro" class="checkbox__label">Micro</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="growth_exclude" id="growth">
                                        <label for="growth" class="checkbox__label">Growth</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="pro_exclude" id="pro">
                                        <label for="pro" class="checkbox__label">Pro</label>
                                    </div>
                                </div>
                                <div class="button__box">
                                    <a href="#" id="do_esimate" class="button button_green">Estimate Campaign</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section class="section section_campaigns" id="job-manager-job-dashboard">
        <div class="section__container">
            <?php if ( ! $jobs ) : ?>
                <?php esc_html_e( 'You do not have any active listings.', 'workscout' ); ?>
            <?php else : ?>
                <div class="table">
                    <div class="table__head">
                        <div class="table__row table__row_header">
                            <div class="table__header">
                                <p class="table__text">Campaign</p>
                            </div>
                            <div class="table__header">
                                <p class="table__text">Location</p>
                            </div>
                            <div class="table__header">
                                <p class="table__text">Campaign Start Date</p>
                            </div>
                            <div class="table__header">
                                <p class="table__text">Campaign Description</p>
                            </div>
                            <div class="table__header">
                                <p class="table__text">Influencers</p>
                            </div>
                        </div>
                    </div>
                    <div class="table__body">
                        <?php foreach ( $jobs as $job ) :?>
                            <div class="table__row table__row_body job_<?php echo $job->ID;  ?>">
                                <div class="table__data">
                                    <p class="table__text"><?php echo esc_html($job->post_title); ?><br>(<?php the_job_status( $job ); ?>)</p>
                                </div>
                                <div class="table__data">
                                    <p class="table__text"><?php
                                        $location = get_post_meta($job->ID, '_job_location', TRUE);
                                        if ( $location )echo wp_kses_post( $location );?>
                                    </p>
                                </div>
                                <div class="table__data">
                                    <p class="table__text"> <?php echo date_i18n( 'M d, Y  h:i A', strtotime( $job->post_date ) ); ?></p>
                                </div>
                                <div class="table__data">
                                    <p class="table__text">
                                        <?php
                                        $excerpt = wp_trim_words ( strip_shortcodes( $job->post_content), 15  );
                                        echo $excerpt;
                                        ?>
                                    </p>
                                </div>
                                <div class="table__data">
                                    <div class="table__influencers">
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer"></div>
                                        <div class="table__influencer">
                                            <?php
                                            $count = get_job_application_count( $job->ID );
                                            echo '<a href="'.home_url('/my-listings').'">';?>
                                            <div class="table__influencer__number">
                                                <?php echo ( $count > 0 ? "+".$count: "0" ); ?>
                                            </div>
                                            <?php echo "</a>"; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="table__data">
                                    <div class="table__buttons">
                                        <?php if ( $job->post_status == 'publish' ) : ?>
                                            <a class="button button_green" href="<?php echo get_permalink( $job->ID ); ?>">View Campaign</a>
                                        <?php endif; ?>
                                        <?php if ( $job->post_status == 'publish' ):
                                            $action_url = add_query_arg( array( 'action' => 'edit', 'job_id' => $job->ID ) );
                                            echo '<a class="button button_white job-dashboard-action-edit" href="' . esc_url( $action_url ) . '">Edit Campaign</a>';
                                        endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div><!--table__body-->
                </div><!--table-->
            <?php endif; ?>
            <div class="after-table">
                <a  href="<?php echo  home_url('/my-listings'); ?>" class="button button_green">View All Campaigns</a>
            </div>
        </div>
    </section>
    <section class="section section_browse">
        <div class="section__container">
            <p class="section__header section__header_browse">Browse Influencers</p>
            <div class="carousel">
                <?php
                $influencers = get_option('resume_manager_resumes_page_id');

                $args = array(
                    'orderby'           => 'ASC',
                    'order'             => 'date',
                    'posts_per_page'    => '12'
                );

                $resumes = get_resumes( apply_filters( 'resume_manager_get_resumes_args', $args ) );

                if ( $resumes->have_posts() ) :?>

                    <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>

                        <?php get_template_part('template-parts/content', 'influencer')?>

                    <?php endwhile; ?>

                <?php endif; ?>
            </div>
            <div class="after-table">
                <a  href="<?php echo  home_url('/my-listings'); ?>" class="button button_green">View All Influencers</a>
            </div>
        </div>
    </section>
<script>
    ( function( $ ) {
        $(document).ready(function () {
            $('#do_esimate').click(function(e){

                e.preventDefault();

                var base = $('form#estimator_module').serialize();
                var button = $(this).find( 'input[type=submit]' );

                var action = 'aj_do_estimate';
                var data = base + '&'
                data = data+'&action='+action;

                var request = $.ajax({
                    url: ws.ajaxurl,
                    data: data,
                    type: 'POST',
                    dataType: 'html',
                    cache: false,
                    success: function(response) {
                        jQuery.magnificPopup.open({
                            items: {
                                src:'<div id="estimator-dialog" class="estimate-dialog">'+response+'</div>',
                                type: 'inline'
                            },
                            callbacks: {
                                open: function() {
                                    $(".carousel").slick({dots: !0, arrows: !1, infinite: !0, speed: 500, slidesToShow: 4, slidesToScroll: 4, autoplay: !1, autoplaySpeed: 7500})
                                }
                            }
                        });
                    }
                });
            });
    })
    } )( jQuery );
</script>
</div>