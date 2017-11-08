<div class="content">
    <?php // get_template_part('template-parts/estimator-module');  /* Turn Off  for now* /?>
    <section class="section section_campaigns" id="job-manager-job-dashboard">
        <div class="section__container">
            <?php if ( ! $jobs ) :
                $submit_job_page = get_option('job_manager_submit_job_form_page_id'); ?>
                <p style="padding: 1.45vw;"><?php esc_html_e( 'You’ll need to add a listing before you add influencers!', 'workscout' ); ?> </p>
                <div class="after-table">
                    <a  href="<?php echo get_permalink($submit_job_page) ?>" class="button button_green large_text">Create Listing</a>
                </div>
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
                                        <?php
                                        $count = get_job_application_count( $job->ID );
                                        for ( $i=1; $i<$count; $i++){
                                            echo '<div class="table__influencer"></div>';
                                        }
                                        ?>
                                        <div class="table__influencer">
                                            <?php
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
                <div class="after-table">
                    <a  href="<?php echo  home_url('/my-listings'); ?>" class="button button_green large_text">View All Campaigns</a>
                </div>
            <?php endif; ?>

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