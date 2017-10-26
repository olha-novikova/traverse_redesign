<?php session_start(); $_SESSION['job_id'] = $form->get_job_id();?>
<div class="content create-page">
    <section class="section section_listing">
        <form method="post" id="job_preview" action="<?php echo esc_url( $form->get_action() ); ?>" class="section__container form form_listing">
            <div class="listing__wrapper">
                <?php global   $redux_demo; ?>
                <p class="section__header section__header_listing">Create Listing</p>
                <?php
                $budget = get_post_meta($form->get_job_id(), '_targeted_budget', true);
                $categories = get_the_terms($form->get_job_id(), 'job_listing_category');
                $category = array_shift($categories);
                $term = $category->slug;

                ?>
                <p class="listing__view__header">
                    <span class="company-name"><?php echo get_the_company_name($form->get_job_id()); ?></span> campaign <span class="company-campaign"> estimate</span>
                </p>
                <div class="list__options">
                    <?php
                    $budget = get_post_meta( $form->get_job_id(), '_targeted_budget', true);
                    $possible_products = array('pro_inf', 'growth_inf', 'micro_inf');

                    foreach( $possible_products as $possible_product){
                        $product_id = wc_get_product_id_by_sku( $possible_product );
                        $product = new WC_Product( $product_id );
                        $price = $product -> get_price();
                        ?>
                        <input type="button" class="button button_orange add_prod_to_job" data-prod_id = "<?php echo $product_id; ?>" data-prod_count = "<?php echo floor( $budget/$price );?>" value="<?php _e( floor( $budget/$price )." ".$product ->get_name(). _n(" influencer"," influencers",floor( $budget/$price )) , 'wp-job-manager' ); ?>" />
                    <?php
                    }
                    ?>
                </div>
                <p class="list__description">
                    Lorem ipsum dolor sit amet, at eam virtute corpora assueverit.
                    Eam ne mutat regione eruditi, nulla persecuti adolescens sed no, ferri neglegentur cum an. In vix facer accumsan interesset.
                    Cum ea idque dolore quidam, natum clita vivendum per ad. Primis scaevola per eu, ne unum quaeque qui, vis oblique verterem an.
                    Sit feugiat ancillae partiendo no, vis te facete recteque.
                </p>

                <?php
                $args = array(
                    'orderby'           => 'ASC',
                    'order'             => 'date',
                    'posts_per_page'    => -1,
                    'search_categories' => array($term)
                );

                $resumes = get_resumes( apply_filters( 'resume_manager_get_resumes_args', $args ) );
                $count =  $resumes -> post_count;
                ?>
                <p class="list__number"><span>Number of influencers: </span> <span> <?php echo $count?></span></p>
                <p>
                    Example of influencers
                </p>
            </div>
            <section class="section section_browse">
                <div class="section__container">
                    <div class="carousel">
                        <?php
                        $possible_reach = 0;
                        if ( $resumes->have_posts() ) :?>

                            <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>

                                <?php get_template_part('template-parts/content', 'influencer')?>
                                <?php
                                $resume_id = get_the_ID();
                                $possible_reach += get_influencer_audience($resume_id);
                                ?>

                            <?php endwhile; wp_reset_postdata();?>

                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <div class="listing__wrapper">
                <p class="list__number"><span>Possible Reach: </span><span><?php echo $possible_reach; ?></span></p>
                <p>Average   audience   size   of   all   influencers   in   the   category selected   by   the   brand.</p>
                <p class="list__number"><span>Estimated Engagement: </span><span><?php echo round($possible_reach*0.03)." - ".round($possible_reach*0.07)?> </span></p>
                <p>Average   possible   reach</p>
            </div>
            <div class="buttons">
                <input type="hidden" name="job_id" value="<?php echo esc_attr( $form->get_job_id() ); ?>" />
                <input type="hidden" name="prod_id" class="prod_id"/>
                <input type="hidden" name="prod_count" class="prod_count"/>
                <input type="hidden" name="job_id" value="<?php echo esc_attr( $form->get_job_id() ); ?>" />
                <input type="hidden" name="step" value="<?php echo esc_attr( $form->get_step() ); ?>" />
                <input type="hidden" name="job_manager_form" value="<?php echo $form->get_form_name(); ?>" />

                <input type="submit" name="edit_job" class="button job-manager-button-edit-listing button_grey" value="<?php _e( 'Edit listing', 'wp-job-manager' ); ?>" />
                <input type="submit" name="continue" id="job_preview_submit_button" class="job-manager-button-submit-listing button button_green" value="<?php echo apply_filters( 'submit_job_step_preview_submit_text', __( 'Let\'s Build My Campaign', 'wp-job-manager' ) ); ?>" />
            </div>
        </form>
    </section>
    <script>
        jQuery(document).ready(function () {
            jQuery('.add_prod_to_job').click(function(){

                var $this = $(this);
                jQuery('.add_prod_to_job').removeClass('active');
                $this.addClass('active');
                var prodId = $this.data('prod_id');
                var prodCount = $this.data('prod_count');
                jQuery('.prod_id').val(prodId);
                jQuery('.prod_count').val(prodCount);
           });
        })
    </script>
</div>  <?php /*echo "<pre>";
                print_r(get_post_meta($form->get_job_id()));
                print_r(get_the_terms($form->get_job_id(), 'job_listing_type'));
                echo "<pre>";*/ ?>