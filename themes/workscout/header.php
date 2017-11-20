<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WorkScout
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>
<?php $layout = Kirki::get_option( 'workscout','pp_body_style','fullwidth' ); ?>
<body <?php body_class($layout); ?>>
<div id="wrapper">

<header <?php workscout_header_class(); ?> id="main-header">
<div class="container">
	<div class="sixteen columns">
	
		<!-- Logo -->
		<div id="logo">
			 <?php
                
                $logo = Kirki::get_option( 'workscout', 'pp_logo_upload', '' ); 
                $logo_retina = Kirki::get_option( 'workscout', 'pp_retina_logo_upload', '' ); 
                
                if( is_page_template( 'template-home.php' ) ) {

					if(Kirki::get_option( 'workscout','pp_transparent_header')) {
						$logo_transparent = Kirki::get_option( 'workscout','pp_transparent_logo_upload');
						$logo =(!empty($logo_transparent)) ? $logo_transparent : $logo ;
					}
				}        
				if( is_page_template( 'template-home-resumes.php' ) ) {

					if(Kirki::get_option( 'workscout','pp_resume_home_transparent_header')) {
						$logo_transparent = Kirki::get_option( 'workscout','pp_transparent_logo_upload');
						$logo =(!empty($logo_transparent)) ? $logo_transparent : $logo ;
					}
				}
                if($logo) {
                    if(is_front_page()){ ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><img src="<?php echo esc_url($logo); ?>" data-rjs="<?php echo esc_url($logo_retina); ?>" alt="<?php esc_attr(bloginfo('name')); ?>"/></a>
                    <?php } else { ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo esc_url($logo); ?>" data-rjs="<?php echo esc_url($logo_retina); ?>" alt="<?php esc_attr(bloginfo('name')); ?>"/></a>
                    <?php }
                } else {
                    if(is_front_page()) { ?>
                    <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    <?php } else { ?>
                    <h2><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h2>
                    <?php }
                }
                ?>
                <?php if(get_theme_mod('workscout_tagline_switch','hide') == 'show') { ?><div id="blogdesc"><?php bloginfo( 'description' ); ?></div><?php } ?>
		</div>

		<!-- Menu -->
	
		<nav id="navigation" class="menu">

			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'responsive','container' => false ) ); 
			
			$minicart_status = Kirki::get_option( 'workscout', 'pp_minicart_in_header', false );
			if(Kirki::get_option( 'workscout', 'pp_login_form_status', true ) ) { 
				$login_system = Kirki::get_option( 'workscout', 'pp_login_form_system' );
				
				switch ($login_system) {
					case 'custom':
						get_template_part('template-parts/login-custom');
						break;

					case 'woocommerce':
						get_template_part('template-parts/login-woocommerce');
						
						break;

					case 'um':
						get_template_part('template-parts/login-um');
						break;					

					case 'workscout':
						get_template_part('template-parts/login-workscout');
						break;
					
					default:
						# code...
						break;
				}
			
			} 
			
			?>

		</nav>

		<!-- Navigation -->
		<div id="mobile-navigation">
			<a href="#menu" class="menu-trigger"><i class="fa fa-reorder"></i><?php esc_html_e('Menu','workscout'); ?></a>
		</div>

	</div>
</div>
</header>
<div class="clearfix"></div>
<?php if(isset($_GET['success']) && $_GET['success'] == 1 )  { ?>
	 <script type="text/javascript">
        jQuery(document).ready(function ($) {
    	
		    	$.magnificPopup.open({
				  items: {
				    src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
		                	'<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
		                	'<div class="small-dialog-content"><p class="margin-reset"><?php esc_html_e("You have successfully registered and logged in. Thank you!","workscout"); ?></p></div>'+
		        		'</div>', // can be a HTML string, jQuery object, or CSS selector
				    type: 'inline'
				  }
				});
	    	});
    </script>
<?php } ?>