<?php
/*
Plugin Name: Tutor LMS Pro
Plugin URI: https://www.themeum.com/product/tutor-lms/
Description: Power up Tutor LMS plugins by Tutor Pro
Author: Themeum
Version: 1.8.10
Author URI: http://themeum.com
Requires at least: 5.3
Tested up to: 5.7
Text Domain: tutor-pro
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

update_option( 'tutor_license_info', [ 'activated' => true, 'license_to' => $_SERVER['SERVER_NAME'] ] );

/**
 * Defined the tutor main file
 */
define( 'TUTOR_PRO_VERSION', '1.8.10' );
define( 'TUTOR_PRO_FILE', __FILE__ );

/**
 * Load tutor-pro text domain for translation
 */
add_action( 'init', 'tutor_pro_language_load' );
function tutor_pro_language_load() {
	load_plugin_textdomain( 'tutor-pro', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

if ( ! function_exists('tutor_pro') ) {
	function tutor_pro() {
		$path = plugin_dir_path( TUTOR_PRO_FILE );
		$info = array(
			'path'              => $path,
			'url'               => plugin_dir_url( TUTOR_PRO_FILE ),
			'basename'          => plugin_basename( TUTOR_PRO_FILE ),
			'version'           => TUTOR_PRO_VERSION,
			'nonce_action'      => 'tutor_pro_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';

$tutorPro = new \TUTOR_PRO\init();
$tutorPro->run(); //Boom
