<?php
/*
Plugin Name: Tutor Multi Instructors
Plugin URI: https://www.themeum.com/product/tutor-multi-instructors
Description: Start a course with multiple instructors by Tutor Multi Instructors
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: tutor-multi-instructors
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_MT_VERSION', '1.0.0');
define('TUTOR_MT_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_multi_instructors_config');
function tutor_multi_instructors_config($config){
	$newConfig = array(
		'name'          => __('Multi Instructors', 'tutor-pro'),
		'description'   => __('Start a course with multiple instructors by Tutor Multi Instructors', 'tutor-pro'),
	);
	$basicConfig = (array) TUTOR_MT();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_MT_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_MT')) {
	function TUTOR_MT() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_MT_FILE ),
			'url'               => plugin_dir_url( TUTOR_MT_FILE ),
			'basename'          => plugin_basename( TUTOR_MT_FILE ),
			'version'           => TUTOR_MT_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new TUTOR_MT\init();
$tutor->run(); //Boom