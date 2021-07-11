<?php
/*
Plugin Name: Certificate customizer for Tutor LMS
Plugin URI: https://www.themeum.com/product/tutor-lms/
Description: An example of a custom certificate development process for Tutor LMS Pro
Author: Themeum
Version: 1.0.1
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 5.7
Text Domain: certificate-customizer-for-tutor-lms
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

define('DHORITRY_FILE', __FILE__);

/**
 * Filter name of certificate bank.
 */
add_filter('tutor_certificate_templates', 'tutor_new_certificate_template');

/**
 * @param $templates
 *
 * @return mixed
 *
 * Pass array of certificate information, key is certificate name in slug format
 */

if ( ! function_exists('tutor_new_certificate_template')) {
	function tutor_new_certificate_template( $templates ) {
		$templates['dhoritry'] = array(
			'name'        => __( 'Dhoritry', 'dhoritry-cert' ),
			'orientation' => 'landscape', //landscape, portrait
			'path'        => trailingslashit( plugin_dir_path( DHORITRY_FILE ) . 'templates/dhoritry' ),
			'url'         => trailingslashit( plugin_dir_url( DHORITRY_FILE ) . 'templates/dhoritry' ),
		);

		return $templates;
	}
}