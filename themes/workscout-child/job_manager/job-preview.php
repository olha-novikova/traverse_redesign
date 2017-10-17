<?php
session_start();
$_SESSION['job_id'] = $form->get_job_id();
//echo $_SESSION['job_id'];
?>
<script>
 //alert(<?php  echo $_SESSION['job_id'];?>);
</script>
<form method="post" id="job_preview" action="<?php echo esc_url( $form->get_action() ); ?>">

 <?php global   $redux_demo; ?>
    <div class="job_listing_preview_title" >
   

        <input type="submit" name="continue" id="job_preview_submit_button" class="button job-manager-button-submit-listing" value="<?php echo apply_filters( 'submit_job_step_preview_submit_text', __( 'Submit Listing', 'wp-job-manager' ) ); ?>" />
        <input type="submit" name="edit_job" class="button job-manager-button-edit-listing" value="<?php _e( 'Edit listing', 'wp-job-manager' ); ?>" />
        <h2><?php _e( 'Preview', 'wp-job-manager' ); ?></h2>
    </div>
    <div class="job_listing_preview single_job_listing">
        <h1 style="background:url(<?php echo  $redux_demo['header_image']['url']; ?>)"><?php the_title(); ?></h1>

        <?php get_job_manager_template_part( 'content-single', 'job_listing' ); ?>
        <?php
            $target_budget  = get_post_meta($_SESSION['job_id'], '_targeted_budget', true);
            $deposit = $target_budget*0.5;

            $package_id = get_post_meta($_SESSION['job_id'], '_wcpl_jmfe_product_id', true);

            $package = wc_get_product( $package_id );
            $time = $package->get_duration();

            $_SESSION['deposit_value'] = $deposit;
        ?>

        <div id="titlebar">
            <div class="container">
                <p>
                    Your campaign for <strong>$<?php echo $target_budget; ?></strong> for <strong>"<?php the_title(); ?>"</strong>.<br/>
                    Please deposit <strong>$<?php echo $deposit; ?></strong> for your campaign to be published.<br/>
                    Campaign Deposits are fully refundable within <?php echo $time; ?> days.

                </p>
            </div>
        </div>


        <input type="hidden" name="job_id" value="<?php echo esc_attr( $form->get_job_id() ); ?>" />
        <input type="hidden" name="step" value="<?php echo esc_attr( $form->get_step() ); ?>" />
        <input type="hidden" name="job_manager_form" value="<?php echo $form->get_form_name(); ?>" />
    </div>
</form>
