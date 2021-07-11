<?php
/*
Plugin Name: Tutor Assignments
Plugin URI: https://www.themeum.com/product/tutor-assignemnts
Description: Tutor assignments is a great way to assign tasks to students.
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: tutor-assignments
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_ASSIGNMENTS_VERSION', '1.0.0');
define('TUTOR_ASSIGNMENTS_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_tutor_assignments_config');
function tutor_tutor_assignments_config($config){
	$newConfig = array(
		'name'          => __('Assignments', 'tutor-pro'),
		'description'   => __('Tutor assignments is a great way to assign tasks to students.', 'tutor-pro'),
	);
	$basicConfig = (array) TUTOR_ASSIGNMENTS();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_ASSIGNMENTS_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_ASSIGNMENTS')) {
	function TUTOR_ASSIGNMENTS() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_ASSIGNMENTS_FILE ),
			'url'               => plugin_dir_url( TUTOR_ASSIGNMENTS_FILE ),
			'basename'          => plugin_basename( TUTOR_ASSIGNMENTS_FILE ),
			'version'           => TUTOR_ASSIGNMENTS_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new TUTOR_ASSIGNMENTS\init();
$tutor->run(); //Boom