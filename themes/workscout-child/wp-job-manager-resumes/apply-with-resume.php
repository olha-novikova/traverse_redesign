<?php global $post;

if ( ! get_option( 'resume_manager_force_application' ) ) {
	echo '<hr />';
}

if ( is_user_logged_in() && sizeof( $resumes ) ) : ?>
	<form class="apply_with_resume" method="post">

		<p><?php _e( 'Apply using your online portfolio; just enter a short message and choose one of your resumes to send your proposal.', 'workscout' ); ?></p>
		<p>
			<label for="resume_id"><?php _e( 'Online Portfolio', 'workscout' ); ?>:</label>
			<select name="resume_id" class="chosen-select" id="resume_id" required>
				<option value=""><?php _e( 'Select Portfolio...', 'workscout' ); ?></option>
				<?php
				$count =1;
					foreach ( $resumes as $resume ) {
                      //  $user_data = get_user_meta($resume->post_author) ;
                        $portfolio_name = get_post_meta($resume->ID , '_portfolio_name', true);
                        $candidate_name = $resume->post_title;
						echo '<option value="' . absint( $resume->ID ) . ' "'; 
							if($count==1)
							{
								echo 'selected';
							}
						echo '>' . $candidate_name.' - ' .$portfolio_name . '</option>';
				$count++;	}
				?>
			</select>
		</p>
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