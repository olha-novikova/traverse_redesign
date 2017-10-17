<?php
/**
 * Template Name: PreListing
 *
 */

get_header();

if(get_post_meta($post->ID, 'pp_page_slider_status', true) == 'on'){
    $slider = get_post_meta($post->ID, 'pp_page_layer', true);
    if($slider) { putRevSlider($slider); }
}
$userid = get_current_user_id();
$layout  = get_post_meta( $post->ID, 'pp_sidebar_layout', true ); if ( empty( $layout ) ) { $layout = 'full-width'; }
$class = ($layout !="full-width") ? "eleven columns" : "sixteen columns"; ?>

    <div class="container <?php echo esc_attr($layout); ?>">
        <article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>

              <h1 class="centered">Choose Your Campaign Type:</h1>

               <?php

                while ( have_posts() ) : the_post();

                   the_content();

                endwhile;

            ?>
        </article>
    </div>       

    <div id = "custom-campaign" class="small-dialog zoom-anim-dialog mfp-hide apply-popup ">
        <div class="small-dialog-headline">
            <h2><?php esc_html_e('Create Campaign','workscout'); ?></h2>
        </div>
        <div class="small-dialog-content">
            <form method="post" class="workscout_form" id ="custom-campaign-form">

                <p class="form-row form-row-first">
                    <label for="name"><?php _e( 'First / Last Name', 'workscout' ); ?> <span class="required">*</span>
                        <i class="ln ln-icon-Male"></i>
                        <input type="text" class="input-text" name="name" />
                    </label>
                </p>

                <p class="form-row form-row-last">
                    <label for="brand"><?php _e( 'Brand', 'workscout' ); ?> <span class="required"></span>
                        <i class="ln  ln-icon-Add-User"></i><input type="text" class="input-text" name="brand" />
                    </label>
                </p>

                <p class="form-row form-row-first">
                    <label for="email"><?php _e( 'Email address', 'workscout' ); ?> <span class="required">*</span>
                        <i class="ln ln-icon-Mail"></i><input type="email" class="input-text" name="email" />
                    </label>
                </p>

                <p class="form-row form-row-last">
                    <label for="phone"><?php _e( 'Phone Number', 'workscout' ); ?> <span class="required">*</span>
                        <i class="ln ln-icon-Phone-2"></i><input type="text" class="input-text" name="phone" />
                    </label>
                </p>

                <p class="form-row form-row-first">
                    <label for="website"><?php _e( 'Website URL', 'workscout' ); ?> <span class="required"></span>
                        <i class="ln ln-icon-URL-Window"></i><input type="text" class="input-text" name="website" />
                    </label>
                </p>

                <p class="form-row form-row-last">
                    <label for="budget"><?php _e( 'Budget (in USD)', 'workscout' ); ?> <span class="required">*</span>
                        <i class="ln ln-icon-Money-2"></i><input type="text" class="input-text" name="budget" />
                    </label>
                </p>

                <p class="form-row form-row-wide">
                    <label for="description"><?php _e( 'Brief Description of Campaign', 'workscout' ); ?> <span class="required">*</span>
                        <i class="ln ln-icon-File-HorizontalText"></i><textarea name="description"></textarea>
                    </label>
                </p>

                <p class="form-row form-row-wide">
                    <?php wp_nonce_field( 'custom-campaign' ); ?>
                    <input type="submit"  name="submit-campaign"  value="Submit"/>
                 </p>

            </form>

        </div>
    </div>

<script>
    jQuery(document).ready(function ($) {
        $('.open-popup-custom-campaign').magnificPopup({
            type:'inline',
            midClick: true
        });

    });
</script>

<?php

get_footer();