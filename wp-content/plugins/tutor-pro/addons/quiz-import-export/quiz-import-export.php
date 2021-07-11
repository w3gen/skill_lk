<?php
/*
Plugin Name: Quiz Export/Import
Plugin URI: https://www.themeum.com/product/quiz-import-export
Description: Save time by exporting/importing quiz data with easy options
Author: Themeum
Version: 1.0.0
Author URI: http://themeum.com
Requires at least: 4.5
Tested up to: 4.9
Text Domain: quiz-import-export
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define('QUIZ_IMPORT_EXPORT_VERSION', '1.0.0');
define('QUIZ_IMPORT_EXPORT_FILE', __FILE__);

/**
 * Showing config for addons central lists
 */
add_filter('tutor_addons_lists_config', 'tutor_quiz_import_export_config');
function tutor_quiz_import_export_config($config){
	$newConfig = array(
		'name'          => __('Quiz Export/Import', 'quiz-import-export'),
		'description'   => __('Save time by exporting/importing quiz data with easy options.', 'quiz-import-export'),
	);
	$basicConfig = (array) QUIZ_IMPORT_EXPORT();
	$newConfig = array_merge($newConfig, $basicConfig);

	$config[plugin_basename( QUIZ_IMPORT_EXPORT_FILE )] = $newConfig;
	return $config;
}

if ( ! function_exists('QUIZ_IMPORT_EXPORT')) {
	function QUIZ_IMPORT_EXPORT() {
		$info = array(
			'path'              => plugin_dir_path( QUIZ_IMPORT_EXPORT_FILE ),
			'url'               => plugin_dir_url( QUIZ_IMPORT_EXPORT_FILE ),
			'basename'          => plugin_basename( QUIZ_IMPORT_EXPORT_FILE ),
			'version'           => QUIZ_IMPORT_EXPORT_VERSION,
			'nonce_action'      => 'tutor_nonce_action',
			'nonce'             => '_wpnonce',
		);

		return (object) $info;
	}
}

include 'classes/init.php';
$tutor = new \QUIZ_IMPORT_EXPORT\init();
$tutor->run(); //Boom