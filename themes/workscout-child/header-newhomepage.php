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
<body <?php body_class(); ?>>

  <header class="header">
    <div class="header__logo">
      <a href="<?php echo home_url()?>"> <img src="<?php echo get_stylesheet_directory_uri()?>/img/RangeLogoWhite-e1508105643871.png"></a>
    </div><!-- /.header__logo -->
    <div class="header__menu">
      <?php

      if ( is_user_logged_in() ) {

          $user = new WP_User(get_current_user_id());

          if($user->roles[0] =="employer" || $user->roles[0] =="administrator"){

              wp_nav_menu( array( 'menu' => 'Brand Menu', 'menu_id' => 'responsive_new','container' => false ) );

          }elseif($user->roles[0] =="candidate" ){

              wp_nav_menu( array( 'menu' => 'Influencer Menu', 'menu_id' => 'responsive_new','container' => false ) );

          } else{

              wp_nav_menu( array( 'menu' => 'Logedin Menu ( Without Role)', 'menu_id' => 'responsive_new','container' => false ) );

          }

      } else {

          wp_nav_menu( array( 'menu' => 'Primary Menu','menu_id' => 'responsive_new','container' => false ) );

      }
      ?>
    </div><!-- /.header__menu -->
  </header><!-- /.header -->
