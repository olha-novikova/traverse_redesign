<?php 

	$loginpage = get_option('woocommerce_myaccount_page_id'); 
	$minicart_status = Kirki::get_option( 'workscout', 'pp_minicart_in_header', false );
	$user_page_status 	= Kirki::get_option( 'workscout', 'pp_user_page_status',true );

?>

<ul class="float-right">
	<?php if($minicart_status) {  get_template_part( 'inc/mini_cart'); } 
	if ( is_user_logged_in() ) { 
		if( ! empty( $loginpage )) { 
			$loginlink = get_permalink($loginpage);
			if($user_page_status){	?>
			<li>
				<a href="<?php echo esc_url(apply_filters('workscout_woo_userpage', $loginlink)); ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('User Page','workscout') ?></a>
			</li>
			<?php }
			} ?>
		<li><a href="<?php echo wp_logout_url( home_url() );  ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('Log Out','workscout') ?></a></li>
	</ul>
<?php } else { //user not logged in

	$login_popup = Kirki::get_option('workscout','pp_login_form_type',true);

	if(!$login_popup) {
		
		if( ! empty( $loginpage )) {
		    $loginlink = get_permalink(  $loginpage );
		} else {
	    	$loginlink = wp_login_url( get_permalink() );
	    } ?>
			<li><a href="<?php echo esc_url($loginlink); ?>#tab-register"><i class="fa fa-user"></i> <?php esc_html_e('Sign Up','workscout') ?></a></li>
			<li><a href="<?php echo esc_url($loginlink); ?>"><i class="fa fa-lock"></i> <?php esc_html_e('Log In','workscout') ?></a></li>
		<?php 
	//login in popup:	
	} else { ?>
			<li><a href="#signup-dialog" class="small-dialog popup-with-zoom-anim"><i class="fa fa-user"></i> <?php esc_html_e('Sign Up','workscout') ?></a></li>
			<li><a href="#login-dialog" class="small-dialog popup-with-zoom-anim"><i class="fa fa-lock"></i> <?php esc_html_e('Log In','workscout') ?></a></li>
	<?php } ?>
</ul>

<?php if($login_popup)  { ?>
	<div id="signup-dialog" class="small-dialog zoom-anim-dialog mfp-hide apply-popup woocommerce-signup-popup">

		<div class="small-dialog-headline">
			<h2><?php esc_html_e('Sign Up','workscout'); ?></h2>
		</div>
		<div class="small-dialog-content woo-reg-box">
			<?php
            if(!is_user_logged_in()) {

                // check to make sure user registration is enabled
                $registration_enabled = get_option('users_can_register');

                // only show the registration form if allowed
                if($registration_enabled) { ?>
                    <h4>Congrats! You’re on your way.</h4>
                    <p class="inner-header">Begin registration here.</p>
                    <form method="post" class="register workscout_form">

                        <?php do_action( 'woocommerce_register_form_start' ); ?>

                        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                            <p class="form-row form-row-wide">
                                <label for="reg_username"><?php _e( 'Username', 'workscout' ); ?> <span class="required">*</span>
                                    <i class="ln ln-icon-Male"></i>
                                    <input type="text" class="input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
                                </label>
                            </p>
                        <?php endif; ?>

                        <p class="form-row form-row-wide">
                            <label for="reg_email"><?php _e( 'Email address', 'workscout' ); ?> <span class="required">*</span>
                                <i class="ln ln-icon-Mail"></i><input type="email" class="input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
                            </label>
                        </p>

                        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

                            <p class="form-row form-row-wide">
                                <label for="reg_password"><?php _e( 'Password', 'workscout' ); ?> <span class="required">*</span>
                                    <i class="ln ln-icon-Lock-2"></i><input type="password" class="input-text" name="password" id="reg_password" />
                                </label>
                            </p>

                        <?php endif; ?>

                        <!-- Spam Trap -->
                        <div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'workscout' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

                        <?php do_action( 'woocommerce_register_form' ); ?>
                        <?php do_action( 'register_form_child' ); ?>

                        <p class="form-row">
                            <?php wp_nonce_field( 'woocommerce-register' ); ?>
                            <input type="submit" class="button" name="register" value="<?php esc_attr_e( 'Register', 'workscout' ); ?>" />
                        </p>

                        <?php do_action( 'woocommerce_register_form_end' ); ?>

                    </form>
                <?php } else {
                    _e('User registration is not enabled','workscout');
                }

            }else{?>
                <h4>Congrats! You are already with us. To create new account please log out</h4>
           <?php }
            ?>

		</div>
	</div>
	<div id="login-dialog" class="small-dialog zoom-anim-dialog mfp-hide apply-popup woocommerce-login-popup">
		<div class="small-dialog-headline">
			<h2><?php esc_html_e('Login','workscout'); ?></h2>
		</div>
		<div class="small-dialog-content woo-reg-box">
			<?php echo do_shortcode('[workscout_login_form]');  ?> 
		</div>
	</div>
	<?php }
	} 
