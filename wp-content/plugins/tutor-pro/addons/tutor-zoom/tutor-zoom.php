<?php
/*
Plugin Name: Tutor Zoom Integration
Plugin URI: https://www.themeum.com/product/tutor-lms
Description: Connect Tutor LMS with Zoom to host live online classes. Students can attend live classes right from the lesson page.
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 5.4
Text Domain: tutor-pro
Domain Path: /languages/
*/
if (!defined('ABSPATH'))
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_ZOOM_VERSION', '1.0.0');
define('TUTOR_ZOOM_FILE', __FILE__);
define('TUTOR_ZOOM_PLUGIN_DIR', plugin_dir_url(__FILE__));

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_zoom_config');
function tutor_zoom_config($config) {
	$newConfig = array(
		'name'          => __('Zoom Integration', 'tutor-pro'),
		'description'   => __('Connect Tutor LMS with Zoom to host live online classes. Students can attend live classes right from the lesson page.', 'tutor-pro'),
	);
	$basicConfig = (array) TUTOR_ZOOM();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename(TUTOR_ZOOM_FILE)] = $newConfig;
	return $config;
}

if (!function_exists('TUTOR_ZOOM')) {
	function TUTOR_ZOOM() {
		$info = array(
			'path'              => plugin_dir_path(TUTOR_ZOOM_FILE),
			'url'               => plugin_dir_url(TUTOR_ZOOM_FILE),
			'basename'          => plugin_basename(TUTOR_ZOOM_FILE),
			'version'           => TUTOR_ZOOM_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'includes/helper.php';
include 'classes/Init.php';
$tutor = new TUTOR_ZOOM\Init();
$tutor->run(); //Boom