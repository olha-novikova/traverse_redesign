<?php 
	
	$minicart_status 	= Kirki::get_option( 'workscout', 'pp_minicart_in_header' );
	$custom_register 	= Kirki::get_option( 'workscout', 'pp_login_custom_register' );
	$custom_login 		= Kirki::get_option( 'workscout', 'pp_login_custom_login' );
	$custom_userpage 	= Kirki::get_option( 'workscout', 'pp_login_custom_userpage' );

?>

<ul class="float-right">
	<?php if($minicart_status) {  get_template_part( 'inc/mini_cart'); } 
	if ( is_user_logged_in() ) { 
		if( ! empty( $custom_userpage )) { 
			$userpage_link = apply_filters('workscout_user_page_url', get_permalink($custom_userpage)); ?>
			<li>
				<a href="<?php echo esc_url($userpage_link); ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('User Page','workscout') ?></a>
			</li>
		<?php } ?>
		<li><a href="<?php echo wp_logout_url( home_url() );  ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('Log Out','workscout') ?></a></li>
	</ul>
<?php } else { //user not logged in

	$login_popup = Kirki::get_option('workscout','pp_login_form_type',true);

	if(!$login_popup) {
		
		$login_page_url = !empty($custom_login) ?  get_permalink(  $custom_login ) : wp_login_url( get_permalink() );
		$register_page_url = !empty($custom_register) ?  get_permalink(  $custom_register ) : wp_registration_url();
		?>
			<li><a href="<?php echo esc_url($register_page_url); ?>"><i class="fa fa-user"></i> <?php esc_html_e('Sign Up','workscout') ?></a></li>
			<li><a href="<?php echo esc_url($login_page_url); ?>"><i class="fa fa-lock"></i> <?php esc_html_e('Log In','workscout') ?></a></li>
		<?php 
	//login in popup:	
	} else { ?>
			<li><a href="#signup-dialog" class="small-dialog popup-with-zoom-anim"><i class="fa fa-user"></i> <?php esc_html_e('Sign Up','workscout') ?></a></li>
			<li><a href="#login-dialog" class="small-dialog popup-with-zoom-anim"><i class="fa fa-lock"></i> <?php esc_html_e('Log In','workscout') ?></a></li>
	<?php } ?>
</ul>

<?php 
if($login_popup) {

	$register_shortcode =  Kirki::get_option( 'workscout', 'pp_registration_box_shortcode' ); 
	if($register_shortcode) : ?>
	<div id="signup-dialog" class="small-dialog zoom-anim-dialog mfp-hide apply-popup custom-signup-popup">
		<div class="small-dialog-headline">
			<h2><?php esc_html_e('Sign Up','workscout'); ?></h2>
		</div>
		<div class="small-dialog-content">
			<?php echo do_shortcode($register_shortcode); ?> 
		</div>
	</div>
	<?php 
	endif;

	$login_shortcode =  Kirki::get_option( 'workscout', 'pp_login_box_shortcode' );
	if($login_shortcode) : ?>
	<div id="login-dialog" class="small-dialog zoom-anim-dialog mfp-hide apply-popup custom-login-popup">
		<div class="small-dialog-headline">
			<h2><?php esc_html_e('Login','workscout'); ?></h2>
		</div>
		<div class="small-dialog-content">
			<?php echo do_shortcode($login_shortcode);  ?> 
		</div>
	</div>
	<?php 
	endif;
	}
} ?>