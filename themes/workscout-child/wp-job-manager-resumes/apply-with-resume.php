<?php global $post;

if ( ! get_option( 'resume_manager_force_application' ) ) {
	echo '<hr />';
}

if ( is_user_logged_in() && sizeof( $resumes ) ) : ?>
	<form class="apply_with_resume" method="post">
        <?php $resume = array_shift($resumes);?>
        <input type="hidden" name="resume_id" value="<?php echo $resume->ID; ?>"/>
		<p>
			<label><?php _e( 'Message', 'workscout' ); ?>:</label>
			<textarea name="application_message" cols="20" rows="4" required><?php
				if ( isset( $_POST['application_message'] ) ) {
					echo esc_textarea( stripslashes( $_POST['application_message'] ) );
				} else {

					echo _x( ' Hey there!', 'default cover letter', 'workscout' ) . "\n\n";

					printf( _x( 'I would really love to take on the %s campaign with %s. I have a stellar skill set and unique wealth of experience that I think would make me the ideal influencer for this role.', 'default cover letter', 'workscout' ), $post->post_title, get_post_meta( $post->ID, '_company_name', true ) );
          

					echo "\n\n" . _x( 'Thank you for your consideration, and please let me know if you have any questions!', 'default cover letter', 'workscout' );
				}
			?></textarea>
		</p>
		<p>
			<input type="submit" name="wp_job_manager_resumes_apply_with_resume" value="<?php esc_attr_e( 'Send application', 'workscout' ); ?>" />
			<input type="hidden" name="job_id" value="<?php echo absint( $post->ID ); ?>" />
		</p>
	</form>
<?php else : ?>
	<form class="apply_with_resume" method="post" action="<?php echo get_permalink( get_option( 'resume_manager_submit_resume_form_page_id' ) ); ?>">
		<p><?php _e( 'You can apply to this job and others using your online portfolio. Click the link below to submit your online portfolio and email your application to this employer.', 'workscout' ); ?></p>

		<p>
			<input type="submit" name="wp_job_manager_resumes_apply_with_resume_create" value="<?php esc_attr_e( 'Submit resume and apply', 'workscout' ); ?>" />
			<input type="hidden" name="job_id" value="<?php echo absint( $post->ID ); ?>" />
		</p>
	</form>
<?php endif; ?>