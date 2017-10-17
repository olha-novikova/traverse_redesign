<?php if ( is_user_logged_in() ) : ?>
	<div class="notification notice closeable margin-bottom-40">
		
		<p><span><?php esc_html_e( 'Welcome to', 'workscout' ); echo ' '.get_bloginfo(); ?></span><br>
			<?php
				$user = wp_get_current_user();
				printf( __( 'You are currently signed in as <strong>%s</strong>.', 'workscout' ), $user->user_login );
			?>
		</p>
		<a class="button" href="<?php echo apply_filters( 'submit_job_form_logout_url', wp_logout_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Sign out', 'workscout' ); ?></a>
	</div>
	
<?php else :

	$account_required             = job_manager_user_requires_account();
	$registration_enabled         = job_manager_enable_registration();
	$generate_username_from_email = job_manager_generate_username_from_email();
	?>
	<fieldset>
	<div class="notification notice closeable margin-bottom-40">
		
		<p><span><?php esc_html_e( 'Have an account?', 'workscout' ); ?></span>

			<?php if ( $registration_enabled ) : ?>

				<?php printf( esc_html__( 'If you don&rsquo;t have an account you can %screate one below by entering your email address/username. Your account details will be confirmed via email.', 'workscout' ), $account_required ? '' : esc_html__( 'optionally', 'workscout' ) . ' ' ); ?>

			<?php elseif ( $account_required ) : ?>

				<?php echo apply_filters( 'submit_job_form_login_required_message',  esc_html__('You must sign in to create a new listing.', 'workscout' ) ); ?>

			<?php endif; ?>
		</p>
		<a class="button" href="<?php echo apply_filters( 'submit_job_form_login_url', wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Sign in', 'workscout' ); ?></a>
	</div>
	</fieldset>
	<?php if ( $registration_enabled ) : ?>
		<?php if ( ! $generate_username_from_email ) : ?>
			<fieldset class="form">
				<label><?php esc_html_e( 'Username', 'workscout' ); ?> <?php echo apply_filters( 'submit_job_form_required_label', ( ! $account_required ) ? ' <small>' . esc_html__( '(optional)', 'workscout' ) . '</small>' : '' ); ?></label>
				<div class="field">
					<input type="text" class="input-text" name="create_account_username" id="account_username" value="<?php echo empty( $_POST['create_account_username'] ) ? '' : esc_attr( sanitize_text_field( stripslashes( $_POST['create_account_username'] ) ) ); ?>" />
				</div>
			</fieldset>
		<?php endif; ?>
		<fieldset class="form">
			<label><?php esc_html_e( 'Your email', 'workscout' ); ?> <?php echo apply_filters( 'submit_job_form_required_label', ( ! $account_required ) ? ' <small>' . esc_html__( '(optional)', 'workscout' ) . '</small>' : '' ); ?></label>
			<div class="field">
				<input type="email" class="input-text" name="create_account_email" id="account_email" placeholder="<?php esc_attr( 'you@yourdomain.com', 'workscout' ); ?>" value="<?php echo empty( $_POST['create_account_email'] ) ? '' : esc_attr( sanitize_text_field( stripslashes( $_POST['create_account_email'] ) ) ); ?>" />
			</div>
		</fieldset>
		<?php do_action( 'job_manager_register_form' ); ?>
	<?php endif; ?>

<?php endif; ?>