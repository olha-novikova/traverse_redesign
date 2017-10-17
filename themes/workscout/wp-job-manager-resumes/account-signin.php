<?php if ( is_user_logged_in() ) : ?>

	<div class="notification notice closeable margin-bottom-40">
		
		
			<p><span><?php esc_html_e( 'Welcome to', 'workscout' ); echo ' '.get_bloginfo();?></span><br>
			<?php
				
				$user = wp_get_current_user();
				printf( esc_html__( 'You are currently signed in as %s.', 'workscout' ), $user->user_login );
			?>
			</p>
			<a class="button" href="<?php echo apply_filters( 'submit_resume_form_logout_url', wp_logout_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Sign out', 'workscout' ); ?></a>
	</div>

<?php else :

	$account_required             = resume_manager_user_requires_account();
	$registration_enabled         = resume_manager_enable_registration();
	$generate_username_from_email = resume_manager_generate_username_from_email();
	?>
	<div class="notification notice closeable margin-bottom-40">
		
		
			
			<p><span><?php esc_html_e( 'Have an account?', 'workscout' ); ?></span>
			<?php if ( $registration_enabled ) : ?>

				<?php esc_html_e( 'If you don&rsquo;t have an account you can create one below by entering your email address. A password will be automatically emailed to you.', 'workscout' ); ?>

			<?php elseif ( $account_required ) : ?>

				<?php echo apply_filters( 'submit_resume_form_login_required_message',  esc_html__( 'You must sign in to submit a resume.', 'workscout' ) ); ?>

			<?php endif; ?>
		</p>
		<a class="button" href="<?php echo apply_filters( 'submit_resume_form_login_url', wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Sign in', 'workscout' ); ?></a>
	</div>
	<?php if ( $registration_enabled ) : ?>
		<?php if ( ! $generate_username_from_email ) : ?>
			<fieldset>
				<label><?php esc_html_e( 'Username', 'workscout' ); ?> <?php echo apply_filters( 'submit_resume_form_required_label', ( ! $account_required ) ? ' <small>' . esc_html__( '(optional)', 'workscout' ) . '</small>' : '' ); ?></label>
				<div class="field">
					<input type="text" class="input-text" name="create_account_username" id="account_username" value="<?php if ( ! empty( $_POST['create_account_username'] ) ) echo sanitize_text_field( stripslashes( $_POST['create_account_username'] ) ); ?>" />
				</div>
			</fieldset>
		<?php endif; ?>
		<?php do_action( 'resume_manager_register_form' ); ?>
	<?php endif; ?>

<?php endif; ?>