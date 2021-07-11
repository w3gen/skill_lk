<?php
namespace TUTOR_CONTENT_DRIP;

if ( ! defined( 'ABSPATH' ) )
	exit;

class init{
	public $version = TUTOR_CONTENT_DRIP_VERSION;
	public $path;
	public $url;
	public $basename;

	//Module
	private $content_drip;

	function __construct() {
		if ( ! function_exists('tutor')){
			return;
		}

		$addonConfig = tutils()->get_addon_config(TUTOR_CONTENT_DRIP()->basename);
		$isEnable = (bool) tutils()->array_get('is_enable', $addonConfig);
		if ( ! $isEnable){
			return;
		}

		$this->path = plugin_dir_path(TUTOR_CONTENT_DRIP_FILE);
		$this->url = plugin_dir_url(TUTOR_CONTENT_DRIP_FILE);
		$this->basename = plugin_basename(TUTOR_CONTENT_DRIP_FILE);

		$this->load_TUTOR_CONTENT_DRIP();
	}

	public function load_TUTOR_CONTENT_DRIP(){
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));
		$this->content_drip = new ContentDrip();
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

			$className = str_replace('TUTOR_CONTENT_DRIP'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
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