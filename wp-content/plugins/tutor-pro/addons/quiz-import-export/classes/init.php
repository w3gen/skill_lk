<?php
namespace QUIZ_IMPORT_EXPORT;

if ( ! defined( 'ABSPATH' ) )
	exit;

class init{
	public $version = QUIZ_IMPORT_EXPORT_VERSION;
	public $path;
	public $url;
	public $basename;

	function __construct() {
		if ( ! function_exists('tutor')){
			return;
		}

		$addonConfig = tutils()->get_addon_config(QUIZ_IMPORT_EXPORT()->basename);
		$isEnable = (bool) tutils()->array_get('is_enable', $addonConfig);
		if ( ! $isEnable){
			return;
		}

		$this->path = plugin_dir_path(QUIZ_IMPORT_EXPORT_FILE);
		$this->url = plugin_dir_url(QUIZ_IMPORT_EXPORT_FILE);
		$this->basename = plugin_basename(QUIZ_IMPORT_EXPORT_FILE);

		$this->load_QUIZ_IMPORT_EXPORT();
	}

	public function load_QUIZ_IMPORT_EXPORT(){
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));
		$this->quiz_import_export = new QuizImportExport();
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

			$className = str_replace('QUIZ_IMPORT_EXPORT'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
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