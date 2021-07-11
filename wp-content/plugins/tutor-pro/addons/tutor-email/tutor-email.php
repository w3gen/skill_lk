<?php
/*
Plugin Name: Tutor Email Notification
Plugin URI: https://www.themeum.com/product/tutor-email
Description: Send email on various tutor events
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: tutor-email
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_EMAIL_VERSION', '1.0.0');
define('TUTOR_EMAIL_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_email_config');
function tutor_email_config($config){
	$newConfig = array(
		'name'          => __('E-Mail', 'tutor-pro'),
		'description'   => __('Send email on various tutor events', 'tutor-pro'),
	);
	$basicConfig = (array) TUTOR_EMAIL();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_EMAIL_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_EMAIL')) {
	function TUTOR_EMAIL() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_EMAIL_FILE ),
			'url'               => plugin_dir_url( TUTOR_EMAIL_FILE ),
			'basename'          => plugin_basename( TUTOR_EMAIL_FILE ),
			'version'           => TUTOR_EMAIL_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new TUTOR_EMAIL\init();
$tutor->run(); //Boom