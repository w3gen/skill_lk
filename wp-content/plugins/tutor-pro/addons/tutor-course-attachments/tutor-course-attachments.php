<?php
/*
Plugin Name: Tutor Course Attachments
Plugin URI: https://www.themeum.com/product/tutor-course-attachments
Description: Add unlimited attachments/ private files to any Tutor course
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: tutor-course-attachments
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_CA_VERSION', '1.0.0');
define('TUTOR_CA_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_course_attachments_config');
function tutor_course_attachments_config($config){
	$newConfig = array(
		'name'          => __('Course Attachments', 'tutor-pro'),
		'description'   => __('Add unlimited attachments/ private files to any Tutor course', 'tutor-pro'),
	);

	$basicConfig = (array) TUTOR_CA();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_CA_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_CA')) {
	function TUTOR_CA() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_CA_FILE ),
			'url'               => plugin_dir_url( TUTOR_CA_FILE ),
			'basename'          => plugin_basename( TUTOR_CA_FILE ),
			'version'           => TUTOR_CA_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new TUTOR_CA\init();
$tutor->run(); //Boom