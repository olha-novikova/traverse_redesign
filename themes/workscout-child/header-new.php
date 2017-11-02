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
<?php $layout = Kirki::get_option( 'workscout','pp_body_style','fullwidth' ); $user = wp_get_current_user(); ?>
<body <?php body_class($layout); ?>>
<header class="header">

    <p class="page__name">Range Dashboard</p>
    <div class="search__block">
        <input type="search" placeholder="Search here people or pages..." class="search"/>
    </div>
    <?php

    if ( in_array( 'employer', (array) $user->roles) || in_array( 'administrator', (array) $user->roles) ) {
        ?>
        <a href="<?php echo home_url(). '/brandhome'?>" class="header__link">Find Influencers</a>
    <?php
    }elseif(in_array( 'candidate', (array) $user->roles ) ){
        ?>
        <a href="<?php echo home_url(). '/my-opportunities'?>" class="header__link">Find Opportunities</a>
    <?php
    }
    ?>

    <div class="icons">
        <a href="<?php echo  home_url('/messages'); ?>" class="icon__block"><i class="icon icon_chat"></i><i class="icon__number icon__number_purple">0</i></a>
<!--        <div class="icon__block"><i class="icon icon_notifications"></i><i class="icon__number icon__number_orange"></i></div>-->
    </div>
    <?php

    if ( in_array( 'employer', (array) $user->roles) || in_array( 'administrator', (array) $user->roles) ) {
        $pagename= get_user_meta( $user->ID, 'company_name', true );

        if ( $pagename == '' )  $pagename = get_user_meta($user->ID, 'first_name', true )." ".get_user_meta($user->ID, 'last_name', true );

        if ( $pagename == '' ) $pagename = $user->display_name;

        $user_img = get_user_meta( $user->ID, 'logo', true );

        if( $user_img ) {
            $dir = wp_get_upload_dir();
            $link = $dir['baseurl'].'/users/'.$user_img;
        }
    }elseif(in_array( 'candidate', (array) $user->roles ) ){
        $pagename = get_user_meta($user->ID, 'first_name', true )." ".get_user_meta($user->ID, 'last_name', true );
        if ( $pagename == '' ) $pagename = $user->display_name;

        $user_img = get_user_meta( $user->ID, 'photo', true );

        if( $user_img ) {
            $dir = wp_get_upload_dir();
            $link = $dir['baseurl'].'/users/'.$user_img;
        }

    }
    ?>
    <div class="profile">
        <div class="profile__logo"><img src="<?php if ( $link) echo $link; ?>" alt="" class="profile__image"/>
            <div class="profile__backup__logo"></div>
        </div>
        <p class="profile__name">
            <?php echo $pagename ?>
            <span class="logout"><a href="<?php echo wc_logout_url()?>">Log Out</a></span>
        </p>
    </div>
</header>
<div class="wrapper">

