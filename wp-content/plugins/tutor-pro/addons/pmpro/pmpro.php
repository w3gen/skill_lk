<?php
/*
Plugin Name: Tutor Email Notification
Plugin URI: https://www.themeum.com/product/tutor-pmpro
Description: Allow Membership to your LMS website
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
define('TUTOR_PMPRO_VERSION', '1.0.0');
define('TUTOR_PMPRO_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_pmpro_config');
function tutor_pmpro_config($config){
	$newConfig = array(
		'name'          => __('Paid Memberships Pro', 'tutor-pro'),
		'description'   => __('Maximize revenue by selling membership access to all of your courses.', 'tutor-pro'),
		'depend_plugins'   => array('paid-memberships-pro/paid-memberships-pro.php' => 'Paid Memberships Pro'),
	);
	$basicConfig = (array) TUTOR_PMPRO();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_PMPRO_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_PMPRO')) {
	function TUTOR_PMPRO() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_PMPRO_FILE ),
			'url'               => plugin_dir_url( TUTOR_PMPRO_FILE ),
			'basename'          => plugin_basename( TUTOR_PMPRO_FILE ),
			'version'           => TUTOR_PMPRO_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new \TUTOR_PMPRO\init();
$tutor->run(); //Boom