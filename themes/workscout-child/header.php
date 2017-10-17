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
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php
wp_head();?>

</head>
<?php $layout = Kirki::get_option( 'workscout','pp_body_style','fullwidth' ); ?>
<body <?php body_class($layout); ?>>
<script>

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '1886251131695070',
            xfbml      : true,
            version    : 'v2.10'
        });
        FB.AppEvents.logPageView();
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
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

		<!-- Menu -new-menu-->
	
		<nav id="navigation">

			<?php 
			
if ( is_user_logged_in() ) {

	 $user = new WP_User(get_current_user_id());

	  if($user->roles[0] =="employer")
	  {
		  wp_nav_menu( array( 'menu' => 'Brand', 'menu_id' => 'responsive_new','container' => false ) );
	  }
	  if($user->roles[0] =="candidate")
	  {

		  wp_nav_menu( array( 'menu' => 'candidate', 'menu_id' => 'responsive_new','container' => false ) );
	  }
	  if($user->roles[0] =="administrator")
	  {
		  wp_nav_menu( array( 'menu' => 'Administrator', 'menu_id' => 'responsive_new','container' => false ) );
	  }
	  
				
    //wp_nav_menu( array( 'theme_location' => 'Logedin Menu','container' => false ) );
} else {

     	wp_nav_menu( array( 'menu' => 'Primary Menu','menu_id' => 'responsive_new','container' => false ) );

			/*wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'responsive','container' => false ) ); */
			}
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
			<a href="#menu" class="menu-trigger-new"><i class="fa fa-reorder"></i></a>
		</div>

	</div>
</div>
</header>

<div class="clearfix"></div>
    <?php

    if(isset($_GET['success']) && $_GET['success'] == 1 && is_user_logged_in())  {

        if($user->roles[0]=="candidate"){
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {

                    $.magnificPopup.open({
                        items: {
                            src:'<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                                '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                                '<div class="small-dialog-content"><p>You are registered and logged in. Now we’ll set up your profile and portfolio. Click NEXT to get started. </p><p style="text-align: right;"><a class="button" href="<?php echo get_home_url()?>/my-account/edit-account">Next</a></p></div>'+
                                '</div>', // can be a HTML string, jQuery object, or CSS selector
                            type: 'inline'
                        }
                    });
                });
            </script>
        <?php
        }elseif ($user->roles[0] == 'employer'){
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {

                    $.magnificPopup.open({
                        items: {
                            src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                                '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                                '<div class="small-dialog-content"><p class="margin-reset"><?php esc_html_e("Next, we’ll capture some information about your company, so that we can match you with the best influencers!","workscout"); ?></p></div>'+
                                '</div>', // can be a HTML string, jQuery object, or CSS selector
                            type: 'inline'
                        }
                    });
                });
            </script>
        <?php } else{
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                $.magnificPopup.open({
                    items: {
                        src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                            '<div class="small-dialog-content"><p class="margin-reset"><?php esc_html_e("Account details changed successfully. Thank you!","woocommerce"); ?></p></div>'+
                            '</div>', // can be a HTML string, jQuery object, or CSS selector
                        type: 'inline'
                    }
                });
            });
        </script>
    <?php }
    }elseif(isset($_GET['success']) && $_GET['success'] == 2 && is_user_logged_in())  {

        $myaccount = wc_get_page_permalink( 'myaccount' ) ;
        if($user->roles[0]=="employer") {
            $redirect =  home_url().'/brandhome';
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                $.magnificPopup.open({
                    items: {
                        src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e("Success!","workscout"); ?></h2></div>'+
                            '<div class="small-dialog-content"><p class="margin-reset"><?php esc_html_e( 'Account details changed successfully.', 'woocommerce' ); ?></p></div>'+
                            '</div>', // can be a HTML string, jQuery object, or CSS selector
                        type: 'inline'
                    }
                });
                setTimeout( function (){$.magnificPopup.close(); window.location.href = "<?php echo $redirect?>"}, 2000);
            });
        </script>

    <?php
        }
        elseif($user->roles[0] == 'candidate') {
            $redirect = home_url().'/influencer-php';
            $resume_page = get_option( 'resume_manager_submit_resume_form_page_id' );

    ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {                
                $.magnificPopup.open({
                    items: {
                        src: '<div id="singup-dialog" class="small-dialog zoom-anim-dialog apply-popup">'+
                            '<div class="small-dialog-headline"><h2><?php esc_html_e('Great! Now let’s set up your portfolio.', 'woocommerce'); ?></h2></div>'+
                            '<div class="small-dialog-content">'+
                            '<p class="margin-reset">This is where you will create specific portfolios for your blog, photography, social accounts, and more. The sky’s the limit! <br>*Remember: the more descriptive you are, the more brands will see how perfect a fit you can be.  You can create multiple portfolios for different accounts.</p>'+
                            '<p class="margin-top-20 margin-bottom-20"><a class="button" href="<?php echo esc_url( get_permalink( $resume_page ) ); ?>"><?php esc_html_e( 'Let&#39;s Go', 'workscout' ); ?></a></p>'+
                            "<p class='margin-reset'><a href ='<?php echo $redirect; ?>'>No thanks. I’ll set up my portfolio later.</a></p>"+
                            '</div>'+
                            '</div>', // can be a HTML string, jQuery object, or CSS selector
                        type: 'inline'
                    }
                });
            });
        </script>

    <?php
        }
    }

    ?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("body").removeClass("custom-background");
    });
</script>

