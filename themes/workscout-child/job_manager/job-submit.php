<?php
/**
 * Job Submission Form
 */
if ( ! defined( 'ABSPATH' ) ) exit;

global $job_manager;
?>
<div class="content create-page">
    <section class="section section_listing">
        <div class="submit-page">
        <form action="<?php echo esc_url( $action ); ?>" method="post" id="submit-job-form" class="job-manager-form section__container form form_listing" enctype="multipart/form-data">
            <?php if ( job_manager_user_can_post_job() ) : ?>
                <p class="section__header section__header_listing"><?php if ( $form == 'submit-job' ) echo "Create Listing"; elseif ( $form == 'edit-job' ) echo "Update Listing"; ?> </p>
                <div class="form__inputs inputs inputs_listing">
                    <!-- Job Information Fields -->
                    <?php do_action( 'submit_job_form_job_fields_start' ); ?>

                    <?php foreach ( $job_fields as $key => $field ) :

                        if ($key == 'job_description')  echo "<div class=\"inputs__more\"><div class=\"inputs__more__left\">";?>

                        <div class="form input__block fieldset-<?php echo esc_attr( $key ); ?>">
                            <?php  get_job_manager_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
                            <label class="form__input__label" for="<?php echo esc_attr( $key ); ?>"><?php echo $field['label'] . apply_filters( 'submit_job_form_required_label', $field['required'] ? '' : ' <small>' . esc_html__( '(optional)', 'workscout' ) . '</small>', $field ); ?></label>
                        </div>
                        <?php
                        if ($key == 'job_description') echo "</div><div class=\"inputs__more__right\">";
                        if ($key == 'header_image') echo "</div></div>";?>
                    <?php endforeach; ?>

                    <?php do_action( 'submit_job_form_job_fields_end' ); ?>

                    </div>
                    <input type="hidden" name="job_manager_form" value="<?php echo esc_attr($form); ?>" />
                    <input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>" />
                    <input type="hidden" name="step" value="<?php echo esc_attr( $step ); ?>" />
                    <?php $user_id = get_current_user_id(); $company_name = get_user_meta( $user_id, 'company_name', true );?>
                    <input type="hidden"  id = "job_company_name" name="job_company_name" value="<?php echo $company_name; ?>" />
                    <div class="buttons">
                        <input type="submit" name="submit_job" class="button button_orange" value="<?php if ( $form == 'submit-job' ) echo "Estimate Campaign"; elseif ( $form == 'edit-job' ) echo "Update Campaign"; ?>" />
                    </div>
            <?php else : ?>

                <?php do_action( 'submit_job_form_disabled' ); ?>

            <?php endif; ?>
        </form>
        </div>
    </section>
    <script>
        (function($){
            $(document).ready(function () {
                var inputs = $(".input-text");
                $.each(inputs, function(i, val){
                    var e = $(val).val();
                    "" === e ? $(this).parent('div').removeClass("has-value") : $(this).parent('div').addClass("has-value")
                });
            });
        })(jQuery)
    </script>
</div>