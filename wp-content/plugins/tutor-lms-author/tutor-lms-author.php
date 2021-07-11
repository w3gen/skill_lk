<?php
/*
* Plugin Name: Tutor LMS Author Ownership Changer
* Plugin URI: https://wordpress.org/plugins/tutor-lms-author
* Description: Easily change the Tutor LMS course author ownership.
* Version: 1.0.3
* Author: Fahim Murshed
* Author URI: https://murshidalam.com
* License: GNU/GPL V2 or Later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tutor_lms_course_author() {
	if ( function_exists('tutor')) {
		$tutor_post_type = tutor()->course_post_type;
		add_post_type_support( $tutor_post_type, 'author' );
	}
}
add_action('init', 'tutor_lms_course_author', 999 );