<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

<?php
$GLOBALS['header_type'] = 'newhomepage';
wp_head();

?>
</head>
<?php $layout = Kirki::get_option( 'workscout','pp_body_style','fullwidth' ); ?>
<body <?php body_class($layout); ?>>

  <header class="header">
    <div class="header__logo">
      <img src="http://traverseinfluence.com/wp-content/uploads/2017/10/RangeLogoWhite-e1508105643871.png">
    </div><!-- /.header__logo -->
    <div class="header__menu">
      <?php
      if ( is_user_logged_in() ) {
            wp_nav_menu( array( 'menu' => 'Logedin Menu','menu_id' => 'responsive_new','container' => false ) );
      } else {
            wp_nav_menu( array( 'menu' => 'Primary Menu','menu_id' => 'responsive_new','container' => false ) );
      }
      ?>
    </div><!-- /.header__menu -->
  </header><!-- /.header -->
