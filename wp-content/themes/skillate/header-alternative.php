<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) {
    	$skillate_favicon = get_theme_mod( 'favicon', get_template_directory_uri().'/images/favicon.ico' ); 
    }
    ?>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head><!--end head-->
<body <?php body_class(); ?>>