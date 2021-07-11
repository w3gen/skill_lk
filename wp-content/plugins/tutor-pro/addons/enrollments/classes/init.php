<?php
namespace TUTOR_ENROLLMENTS;

if ( ! defined( 'ABSPATH' ) )
	exit;

class init{
	public $version = TUTOR_ENROLLMENTS_VERSION;
	public $path;
	public $url;
	public $basename;

	//Module
	private $enrollments;

	function __construct() {
		if ( ! function_exists('tutor')){
			return;
		}

		$addonConfig = tutils()->get_addon_config(TUTOR_ENROLLMENTS()->basename);
		$isEnable = (bool) tutils()->array_get('is_enable', $addonConfig);
		if ( ! $isEnable){
			return;
		}

		$this->path = plugin_dir_path(TUTOR_ENROLLMENTS_FILE);
		$this->url = plugin_dir_url(TUTOR_ENROLLMENTS_FILE);
		$this->basename = plugin_basename(TUTOR_ENROLLMENTS_FILE);

		$this->load_TUTOR_ENROLLMENTS();
	}

	public function load_TUTOR_ENROLLMENTS(){
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));
		$this->enrollments = new Enrollments();
	}

	/**
	 * @param $className
	 *
	 * Auto Load class and the files
	 */
	private function loader($className) {
		if ( ! class_exists($className)){
			$className = preg_replace(
				array('/([a-z])([A-Z])/', '/\\\/'),
				array('$1$2', DIRECTORY_SEPARATOR),
				$className
			);

			$className = str_replace('TUTOR_ENROLLMENTS'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
			$file_name = $this->path.$className.'.php';

			if (file_exists($file_name)  ) {
				require_once $file_name;
			}
		}
	}

	//Run the TUTOR right now
	public function run(){
		//
	}

}