<?php session_start(); $_SESSION['job_id'] = $form->get_job_id();?>
<?php ?>
<div class="content create-page">
    <?php
    $categories = wp_get_post_terms($form->get_job_id(), 'job_listing_category', array("fields" => "id=>slug"));
    $types  = wp_get_post_terms($form->get_job_id(), 'job_listing_type', array("fields" => "id=>slug"));

    $meta_query = array('relation' => 'AND');

    $meta_use = false;

    if ( in_array('facebook', $types) ){
        $meta_query[] = array(
            'key'       => '_fb_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( in_array('instagram', $types) ){
        $meta_query[] = array(
            'key'       => '_instagram_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( in_array('youtube', $types) ){
        $meta_query[] = array(
            'key'       => '_youtube_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    if ( in_array('twitter', $types) ){
        $meta_query[] = array(
            'key'       => '_twitter_link',
            'compare'   => 'EXISTS'
        );
        $meta_use = true;
    }

    $budget = get_post_meta( $form->get_job_id(), '_targeted_budget', true);

    $product_id = wc_get_product_id_by_sku( 'pro_inf' );

    $product_pro = new WC_Product( $product_id );
    $price_pro = $product_pro -> get_price();

    if ( $budget < $price_pro ) {
        $meta_query[] = array(
            'key'       => '_audience',
            'compare'   => '<',
            'value'     => '500000',
            'type'      => 'NUMERIC'
        );
        $meta_use = true;
    }


    $product_id = wc_get_product_id_by_sku( 'growth_inf' );

    $product_growth = new WC_Product( $product_id );
    $price_growth = $product_growth -> get_price();

    if ( $budget < $price_growth ){
        $meta_query[] = array(
            'key'       => '_audience',
            'compare'   => '<',
            'value'     => '500000',
            'type'      => 'NUMERIC'
        );
        $meta_use = true;
    }

    $product_id = wc_get_product_id_by_sku( 'micro_inf' );

    $product_micro = new WC_Product( $product_id );
    $price_micro = $product_micro -> get_price();

    if ( $budget < $price_micro ){
        $meta_query[] = array(
            'key'       => '_audience',
            'compare'   => '<',
            'value'     => '50000',
            'type'      => 'NUMERIC'
        );
        $meta_use = true;
    }

    $args = array(
        'post_type'           => 'resume',
        'post_status'         => array( 'publish'),
        'ignore_sticky_posts' => 1,
        'orderby'             => 'ASC',
        'order'               => 'date',
        'posts_per_page'      => -1
    );

    if ( isset($_POST['traveler_type']) && !empty($_POST['traveler_type']) )
        $categories = $_POST['traveler_type'];

    if ( $categories ){
        $args['tax_query'][] = array(
            'taxonomy'         => 'resume_category',
            'field'            => 'slug',
            'terms'            => array_values( $categories ),
            'include_children' => false,
            'operator'         => 'IN'
        );
    }

    if ( $meta_use ) {
        $args['meta_query'] = $meta_query;
    }

    $resumes = new WP_Query($args);

    $possible_reach = 0;

    if ( $resumes->have_posts() ) :?>

        <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>
            <?php
            $resume_id = get_the_ID();
            $possible_reach += get_influencer_audience($resume_id);
            ?>
        <?php endwhile; wp_reset_postdata();?>
    <?php endif; ?>
    <section class="section section_listing">
        <form method="post" id="job_preview" action="<?php echo esc_url( $form->get_action() ); ?>" class="section__container form form_listing">
            <div class="listing__wrapper">
                <?php global   $redux_demo; ?>
                <p class="section__header section__header_listing">Create Listing</p>
                <?php
                $budget = get_post_meta($form->get_job_id(), '_targeted_budget', true);

                ?>
                <p class="listing__view__header">
                    <span class="company-name"><?php echo get_the_company_name($form->get_job_id()); ?></span> campaign <span class="company-campaign"> estimate</span>
                </p>
                <p>
                    You find different performances based upon your targeted influencer categories, your budget, and more.
                    We suggest packages to help make sure you get the performance you are looking for.
                </p>
              
                <p id="selected_option" class="listing__view__header"><span class="company-name">Select one of the following:</span> <span class="company-campaign option"></span></p>

                <div class="list__options">
                    <?php


                    $possible_products = array('pro_inf', 'growth_inf', 'micro_inf');
                    $text = "";
                    foreach( $possible_products as $possible_product){
                        $product_id = wc_get_product_id_by_sku( $possible_product );
                        $product = new WC_Product( $product_id );
                        $price = $product -> get_price();

                        if ( floor( $budget/$price ) > 0){
                            if ( $possible_product == 'pro_inf' )       $can_pro = true;
                            if ( $possible_product == 'growth_inf' )    $can_growth = true;
                            if ( $possible_product == 'micro_inf' )     $can_micro = true;
                        }
                        if ( floor( $budget/$price ) > 0){?>
                            <input type="button" class="button button_orange add_prod_to_job" data-include = "<?php echo $possible_product; ?>" data-prod_id = "<?php echo $product_id; ?>" data-prod_count = "<?php echo floor( $budget/$price );?>" value="<?php _e( floor( $budget/$price )." ".$product ->get_name(). _n(" influencer"," influencers",floor( $budget/$price )) , 'wp-job-manager' ); ?>" />
                        <?php }else{ ?>
                            <input type="button" class="button button_white" value="<?php _e( floor( $budget/$price )." ".$product ->get_name(). _n(" influencer"," influencers",floor( $budget/$price )) , 'wp-job-manager' ); ?>" />
                        <?php }
                    }
                    if ( !isset($can_pro) && isset($can_growth) && isset($can_micro))
                        $text = "For a chance to use a PRO influencer, please add more budget or check out how many GROW or MICRO influencers you can have." ;

                    if ( !isset($can_pro) && !isset($can_growth) && isset($can_micro))
                        $text = "For a chance to use a PRO or a GROW influencer, please add more budget or check out how many  MICRO influencers you can have." ;

                    if ( !isset($can_pro) && !isset($can_growth) && !isset($can_micro)){
                        $text = "For a chance to use an influencer, please add more budget." ;?>
                    <?php }
                    ?>
                </div>

                <div class="listing__wrapper">
                    <p class="list__number"><span>Estimated Reach: </span><span class="pos_rich"><?php echo $possible_reach; ?></span></p>
                </div>
                <div class="listing__wrapper">
                    <p class="list__number"><span>Estimated Engagement: </span><span class="pos_eng"><?php echo round($possible_reach*0.03)." - ".round($possible_reach*0.07)?> </span></p>
                </div>
            </div>

            <div class="buttons">
                <input type="hidden" name="job_id" value="<?php echo esc_attr( $form->get_job_id() ); ?>" />
                <input type="hidden" name="prod_id" class="prod_id"/>
                <input type="hidden" name="prod_count" class="prod_count"/>
                <input type="hidden" name="job_id" value="<?php echo esc_attr( $form->get_job_id() ); ?>" />
                <input type="hidden" name="step" value="<?php echo esc_attr( $form->get_step() ); ?>" />
                <input type="hidden" name="job_manager_form" value="<?php echo $form->get_form_name(); ?>" />

                <input type="submit" name="edit_job" class="button job-manager-button-edit-listing button_grey" value="<?php _e( 'Edit listing', 'wp-job-manager' ); ?>" />
                <?php if ( isset($can_pro) || isset( $can_growth) ||  isset($can_micro) ) { ?>
                    <input type="submit" name="continue" id="job_preview_submit_button" class="job-manager-button-submit-listing button button_green" value="<?php echo apply_filters( 'submit_job_step_preview_submit_text', __( 'Go to Checkout', 'wp-job-manager' ) ); ?>" />
                <?php } ?>
            </div>
        </form>

        <p class="listing__wrapper">
            <?php echo $text;
            if ( !isset($can_pro) && !$can_growth && !isset($can_micro) ){ ?>
                <input type="submit" name="edit_job" class="button job-manager-button-edit-listing button_grey" value="<?php _e( 'Edit listing', 'wp-job-manager' ); ?>" />
            <?php } ?>
        </p>

    </section>

    <section class="section section_browse">
        <div class="listing__wrapper">
            <p class="list__number"><span>Here are some possible influencers that match your campaign: </span></p>
        </div>
        <div class="section__container">
            <div class="carousel">
                <?php
                if ( $resumes->have_posts() ) :?>
                    <?php while ( $resumes->have_posts() ) : $resumes->the_post(); ?>
                        <?php get_template_part('template-parts/content', 'influencer')?>
                    <?php endwhile; wp_reset_postdata();?>

                <?php endif; ?>
            </div>
        </div>
    </section>
    <script>
        ( function( $ ) {
            $(document).ready(function () {
                $('.prod_id').val('');
                $('.prod_count').val('');

                var traveler_types = '';
                <?php foreach($categories as $category){ ?>
                    traveler_types = traveler_types +"&traveler_type[]=<?php echo $category?>";
                <?php } ?>

                $('.add_prod_to_job').click(function(){

                    var base = "target_budget=<?php echo  get_post_meta($form->get_job_id(), '_targeted_budget', true); ?>"+
                        "&fb_channel=<?php if (in_array('facebook', $types)) echo "on";?>"+
                        "&ig_channel=<?php if (in_array('instagram', $types)) echo "on";?>"+
                        "&yt_channel=<?php if (in_array('youtube', $types)) echo "on";?>"+
                        "&tw_channel=<?php if (in_array('twitter', $types)) echo "on";?>";

                    base += traveler_types;

                    var $this = $(this);
                    $('.add_prod_to_job').removeClass('active');
                    $this.addClass('active');

                    var prodId = $this.data('prod_id');
                    var prodCount = $this.data('prod_count');
                    var include = $this.data('include');

                    base = base+"&include=" +include;

                    var text = $this.val();

                    if (prodId && prodCount){
                        $('.prod_id').val(prodId);
                        $('.prod_count').val(prodCount);
                        $('#selected_option').find('span.option').html(text);
                    }

                     $.ajax({
                        url: ws.ajaxurl,
                        type: 'POST',
                        data: base + '&action=aj_preview_estimate_summary',
                        dataType: 'json',
                        success: function(response) {
                            if (response.possible_reach){
                                $('.pos_rich').html(response.possible_reach);
                            }else{
                                $('.pos_rich').html('');
                            }
                            if (response.possible_engagement){
                                $('.pos_eng').html(response.possible_engagement);
                            }else{
                                $('.pos_eng').html('');
                            }
                        }
                    });

                     $.ajax({
                        url: ws.ajaxurl,
                        type: 'POST',
                        data: base + '&action=aj_preview_estimate_influencers',
                        dataType: 'html',
                        success: function(response) {
                            $('.carousel').slick('unslick');
                            $('.carousel').html( response );
                            $('.carousel').slick({dots: !0, arrows: !1, infinite: !0, speed: 500, slidesToShow: 4, slidesToScroll: 4, autoplay: !1, autoplaySpeed: 7500});

                        }
                    });

                });

                $('#job_preview_submit_button').click(function(e) {
                    e.preventDefault();
                    if ($.trim($(".prod_id").val()) === "" || jQuery.trim(jQuery(".prod_count").val()) === "") {
                        $.magnificPopup.open({
                            items: {
                                src:'<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                                    '<div class="small-dialog-headline"><h2><?php esc_html_e("Warning!","workscout"); ?></h2></div>'+
                                    '<div class="small-dialog-content"><p>Please select which group of influencers you wish to have complete your campaign</p></div>'+
                                    '</div>',
                                type: 'inline'
                            }
                        });
                    }else{
                        $('#job_preview').append("<input type='hidden' name='continue' value='continue'/>");
                        $('#job_preview').submit();
                    }
                });
            })
        } )( jQuery );

    </script>
</div>