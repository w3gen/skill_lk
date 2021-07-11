<?php
/*
Plugin Name: Tutor Content Drip
Plugin URI: https://www.themeum.com/product/tutor-pmpro
Description: Take advanced control on content_drip. Enroll student manually.
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
define('TUTOR_CONTENT_DRIP_VERSION', '1.0.0');
define('TUTOR_CONTENT_DRIP_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_content_drip_config');
function tutor_content_drip_config($config){
	$newConfig = array(
		'name'          => __('Content Drip', 'tutor-pmpro'),
		'description'   => __('Unlock lessons by schedule or when the student meets specific condition.', 'tutor-pro'),
	);
	$basicConfig = (array) TUTOR_CONTENT_DRIP();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_CONTENT_DRIP_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_CONTENT_DRIP')) {
	function TUTOR_CONTENT_DRIP() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_CONTENT_DRIP_FILE ),
			'url'               => plugin_dir_url( TUTOR_CONTENT_DRIP_FILE ),
			'basename'          => plugin_basename( TUTOR_CONTENT_DRIP_FILE ),
			'version'           => TUTOR_CONTENT_DRIP_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new \TUTOR_CONTENT_DRIP\init();
$tutor->run(); //Boom