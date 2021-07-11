<?php
/*
Plugin Name: Tutor Gradebook
Plugin URI: https://www.themeum.com/product/tutor-gradebook
Description: Shows student progress from assignment and quiz
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: tutor-gradebook
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_GB_VERSION', '1.0.0');
define('TUTOR_GB_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_gradebook_config');
function tutor_gradebook_config($config){
	$newConfig = array(
		'name'          => __('Gradebook', 'tutor-multi-instructors'),
		'description'   => __('Shows student progress from assignment and quiz', 'tutor-pro'),
	);
	$basicConfig = (array) TUTOR_GB();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_GB_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_GB')) {
	function TUTOR_GB() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_GB_FILE ),
			'url'               => plugin_dir_url( TUTOR_GB_FILE ),
			'basename'          => plugin_basename( TUTOR_GB_FILE ),
			'version'           => TUTOR_GB_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new TUTOR_GB\init();
$tutor->run(); //Boom