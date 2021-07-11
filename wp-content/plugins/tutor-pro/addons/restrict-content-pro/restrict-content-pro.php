<?php
/*
Plugin Name: Restrict Content
Plugin URI: https://www.themeum.com/product/restrict-content
Description: Restrict Content integration for Tutor LMS
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: restrict-content
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_RC_VERSION', '1.0.0');
define('TUTOR_RC_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_restrict_content_config');
function tutor_restrict_content_config($config){
	$newConfig = array(
		'name'          => __('Restrict Content Pro', 'tutor-pro'),
		'description'   => __('Unlock Course depending on Restrict Content Permission.', 'tutor-pro'),
		'depend_plugins'   => array(
			'restrict-content-pro/restrict-content-pro.php' => 'Restrict Content Pro'
		),
	);
	$basicConfig = (array) TUTOR_RC();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_RC_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_RC')) {
	function TUTOR_RC() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_RC_FILE ),
			'url'               => plugin_dir_url( TUTOR_RC_FILE ),
			'basename'          => plugin_basename( TUTOR_RC_FILE ),
			'version'           => TUTOR_RC_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);
		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new TUTOR_RC\init();
$tutor->run(); //Boom