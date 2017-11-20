<ul class="float-right">
<?php 
if(function_exists('um_get_option')) {
	$minicart_status = Kirki::get_option( 'workscout', 'pp_minicart_in_header', false );
	$login_page 	 = um_get_option('core_login');
	$register_page 	 = um_get_option('core_register');
	$user_page 		 = um_get_option('core_user');
	$user_page_status 	= Kirki::get_option( 'workscout', 'pp_user_page_status' );

	if($minicart_status) {  get_template_part( 'inc/mini_cart'); } 

	if ( is_user_logged_in() ) { 
		//user logged in, display user page button
		if( ! empty( $user_page )) { 

			$user_page_url = apply_filters('workscout_user_page_url',get_permalink($user_page)); 
			if( $user_page_status ) { ?>
				<li>
					<a href="<?php echo esc_url($user_page_url); ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('User Page','workscout') ?></a>
				</li>
		
			<?php }
			} ?>
		<li><a href="<?php echo wp_logout_url( home_url() );  ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('Log Out','workscout') ?></a></li>
	</ul>

<?php } else { 
		//user not logged in  popup not available with UM
	
		$login_page_url = !empty($login_page) ?  get_permalink(  $login_page ) : wp_login_url( get_permalink() );
		$register_page_url = !empty($register_page) ?  get_permalink(  $register_page ) : wp_registration_url();
		?>
			<li><a href="<?php echo esc_url($register_page_url); ?>"><i class="fa fa-user"></i> <?php esc_html_e('Sign Up','workscout') ?></a></li>
			<li><a href="<?php echo esc_url($login_page_url); ?>"><i class="fa fa-lock"></i> <?php esc_html_e('Log In','workscout') ?></a></li>
		<?php 
	
	} 
}?>
</ul>
