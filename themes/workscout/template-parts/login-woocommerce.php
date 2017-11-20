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
			<?php echo do_shortcode('[workscout_register_form]'); ?> 
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
