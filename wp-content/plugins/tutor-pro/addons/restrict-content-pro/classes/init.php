<?php
namespace TUTOR_RC;

if ( ! defined( 'ABSPATH' ) )
	exit;

class init{
	public $version = TUTOR_RC_VERSION;
	public $path;
	public $url;
	public $basename;

	//Module
	public $restrict_content;

	function __construct() {
		if ( ! function_exists('tutor')){
			return;
		}

		add_filter('tutor_monetization_options', array($this, 'tutor_monetization_options'));

		$addonConfig = tutils()->get_addon_config(TUTOR_RC()->basename);
		$monetize_by = tutils()->get_option('monetize_by');
		$isEnable = (bool) tutils()->array_get('is_enable', $addonConfig);
		$has_pmpro = $this->has_rc();
		if ( ! $isEnable || ! $has_pmpro || $monetize_by !== 'restrict-content-pro' ){
			return;
		}

		$this->path = plugin_dir_path(TUTOR_RC_FILE);
		$this->url = plugin_dir_url(TUTOR_RC_FILE);
		$this->basename = plugin_basename(TUTOR_RC_FILE);

		$this->load_TUTOR_RC();
	}

	public function load_TUTOR_RC(){
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));
		$this->restrict_content = new RestrictContent();
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

			$className = str_replace('TUTOR_RC'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
			$file_name = $this->path.$className.'.php';

			if (file_exists($file_name) && is_readable( $file_name ) ) {
				require_once $file_name;
			}
		}
	}


	//Run the TUTOR right now
	public function run(){
		
	}

	public function has_rc(){
		$activated_plugins = apply_filters('active_plugins', get_option( 'active_plugins' ));
		$depends = array('restrict-content-pro/restrict-content-pro.php');
		return count(array_intersect($depends, $activated_plugins)) == count($depends);
	}

	public function tutor_monetization_options($arr){
		$has_rc = $this->has_rc();
		if ($has_rc){
			$arr['restrict-content-pro'] = __('Restrict Content Pro', 'tutor-pro');
		}
		return $arr;
	}


}