<?php
/**
*    Header for new design
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
    $GLOBALS['header_type'] = 'newhomepage';
    wp_head();
    ?>
</head>
<?php $layout = Kirki::get_option( 'workscout','pp_body_style','fullwidth' ); ?>
<body <?php body_class($layout); ?>>
<header class="header">

    <p class="page__name">Profile Page</p>
    <div class="search__block">
        <input type="search" placeholder="Search here people or pages..." class="search"/>
    </div><a href="#" class="header__link">Find Influencers</a>
    <div class="icons">
        <div class="icon__block"><i class="icon icon_chat"></i><i class="icon__number icon__number_purple">2</i></div>
        <div class="icon__block"><i class="icon icon_notifications"></i><i class="icon__number icon__number_orange">8</i></div>
    </div>
    <div class="profile">
        <div class="profile__logo"><img src="#" alt="" class="profile__image"/>
            <div class="profile__backup__logo"></div>
        </div>
        <p class="profile__name">Brand Name</p>
    </div>
</header>
<div class="wrapper">

