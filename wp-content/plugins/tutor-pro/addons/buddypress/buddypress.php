<?php
/*
Plugin Name: Tutor Email Notification
Plugin URI: https://www.themeum.com/product/tutor-buddypress
Description: Allow Membership to your LMS website
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: tutor-buddypress
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('TUTOR_BP_VERSION', '1.0.0');
define('TUTOR_BP_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_bp_config');
function tutor_bp_config($config){
	$newConfig = array(
		'name'              => __('BuddyPress', 'tutor-pro'),
		'description'       => __('Discuss about course and share your knowledge with your friends through BuddyPress', 'tutor-pro'),
		'depend_plugins'    => array('buddypress/bp-loader.php' => 'BuddyPress'),
	);
	$basicConfig = (array) TUTOR_BP();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( TUTOR_BP_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('TUTOR_BP')) {
	function TUTOR_BP() {
		$info = array(
			'path'              => plugin_dir_path( TUTOR_BP_FILE ),
			'url'               => plugin_dir_url( TUTOR_BP_FILE ),
			'basename'          => plugin_basename( TUTOR_BP_FILE ),
			'version'           => TUTOR_BP_VERSION,
			'nonce_action'      => 'nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new \TUTOR_BP\init();
$tutor->run(); //Boom
