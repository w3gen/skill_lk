<?php
/*
Plugin Name: Tutor Enrollments
Plugin URI: https://www.themeum.com/product/tutor-pmpro
Description: Take advanced control on enrollments. Enroll student manually.
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: tutor-pmpro
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_ENROLLMENTS_VERSION', '1.0.0');
define('TUTOR_ENROLLMENTS_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_enrollments_config');
function tutor_enrollments_config($config){
	$newConfig = array(
		'name'          => __('Enrollments', 'tutor-pmpro'),
		'description'   => __('Take advanced control on enrollments. Enroll the student manually.', 'tutor-pro'),
	);
	$basicConfig = (array) TUTOR_ENROLLMENTS();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_ENROLLMENTS_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_ENROLLMENTS')) {
	function TUTOR_ENROLLMENTS() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_ENROLLMENTS_FILE ),
			'url'               => plugin_dir_url( TUTOR_ENROLLMENTS_FILE ),
			'basename'          => plugin_basename( TUTOR_ENROLLMENTS_FILE ),
			'version'           => TUTOR_ENROLLMENTS_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new \TUTOR_ENROLLMENTS\init();
$tutor->run(); //Boom