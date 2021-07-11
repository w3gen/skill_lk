<?php
/*
Plugin Name: Google Classroom
Plugin URI: https://www.themeum.com/product/google-classroom
Description: Sync Tutor LMS with Google Classroom
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: tutor-gc
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_GC_VERSION', '1.0.0');
define('TUTOR_GC_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_gc_config');
function tutor_gc_config($config){
	$newConfig = array(
		'name'          => __('Google Classroom Integration', 'tutor-pro'),
		'description'   => __('Helps connect Google Classrooms with Tutor LMS courses, allowing you to use features like Classroom streams and files directly from the Tutor LMS course.', 'tutor-pro'),
	);

	$basicConfig = (array) TUTOR_GC();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_GC_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_GC')) {
	function TUTOR_GC() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_GC_FILE ),
			'url'               => plugin_dir_url( TUTOR_GC_FILE ),
			'basename'          => plugin_basename( TUTOR_GC_FILE ),
			'version'           => TUTOR_GC_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);
		return (object) $info;
	}
}

include 'classes/init.php';
new TUTOR_GC\init(); //Boom