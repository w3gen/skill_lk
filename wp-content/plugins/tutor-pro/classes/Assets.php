<?php
namespace TUTOR_PRO;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Assets {
	public function __construct() {
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
		add_action('wp_enqueue_scripts', array($this, 'front_scripts'));
	}

	public function admin_scripts() {
		wp_enqueue_style('tutor-pro-admin', tutor_pro()->url.'assets/css/admin.css', array(), tutor_pro()->version);
		wp_enqueue_script('tutor-pro-admin', tutor_pro()->url.'assets/js/admin.js', array('jquery'), tutor_pro()->version, true);
	}

	public function front_scripts() {
		wp_enqueue_style('tutor-pro-front', tutor_pro()->url.'assets/css/front.css', array(), tutor_pro()->version);
	}
}