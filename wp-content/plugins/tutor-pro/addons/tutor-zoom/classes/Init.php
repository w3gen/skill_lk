<?php

namespace TUTOR_ZOOM;

if (!defined('ABSPATH'))
	exit;

class Init {
	public $version = TUTOR_ZOOM_VERSION;
	public $path;
	public $url;
	public $basename;

	function __construct() {
		if (!function_exists('tutor')) {
			return;
		}
		$addonConfig = tutor_utils()->get_addon_config(TUTOR_ZOOM()->basename);
		$isEnable = (bool) tutor_utils()->avalue_dot('is_enable', $addonConfig);
		if (!$isEnable) {
			return;
		}

		$this->path = plugin_dir_path(TUTOR_ZOOM_FILE);
		$this->url = plugin_dir_url(TUTOR_ZOOM_FILE);
		$this->basename = plugin_basename(TUTOR_ZOOM_FILE);

		$this->load_tutor_zoom();
	}

	public function load_tutor_zoom() {
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));
		$this->zoom = new Zoom();
	}

	/**
	 * @param $className
	 *
	 * Auto Load class and the files
	 */
	private function loader($className) {
		if (!class_exists($className)) {
			$className = preg_replace(
				array('/([a-z])([A-Z])/', '/\\\/'),
				array('$1$2', DIRECTORY_SEPARATOR),
				$className
			);

			$className = str_replace('TUTOR_ZOOM' . DIRECTORY_SEPARATOR, 'classes' . DIRECTORY_SEPARATOR, $className);
			$file_name = $this->path . $className . '.php';

			if (file_exists($file_name) && is_readable($file_name)) {
				require_once $file_name;
			}
		}
	}

	//Run the TUTOR right now
	public function run() {
		register_activation_hook(TUTOR_ZOOM_FILE, array($this, 'tutor_activate'));
	}

	/**
	 * Do some task during plugin activation
	 */
	public function tutor_activate() {
		$version = get_option('tutor_zoom_version');
		//Save Option
		if (!$version) {
			update_option('tutor_zoom_version', TUTOR_ZOOM_VERSION);
		}
	}
}