<?php
namespace TUTOR_PMPRO;

if ( ! defined( 'ABSPATH' ) )
	exit;

class init{
	public $version = TUTOR_PMPRO_VERSION;
	public $path;
	public $url;
	public $basename;

	//Module
	private $paid_memberships_pro;

	function __construct() {
		if ( ! function_exists('tutor')){
			return;
		}
		//Adding monetization options to core
		add_filter('tutor_monetization_options', array($this, 'tutor_monetization_options'));

		$addonConfig = tutils()->get_addon_config(TUTOR_PMPRO()->basename);
		$monetize_by = tutils()->get_option('monetize_by');
		$isEnable = (bool) tutils()->array_get('is_enable', $addonConfig);
		$has_pmpro = tutils()->has_pmpro();
		if ( ! $isEnable || ! $has_pmpro || $monetize_by !== 'pmpro' ){
			return;
		}

		$this->path = plugin_dir_path(TUTOR_PMPRO_FILE);
		$this->url = plugin_dir_url(TUTOR_PMPRO_FILE);
		$this->basename = plugin_basename(TUTOR_PMPRO_FILE);

		$this->load_TUTOR_PMPRO();
	}

	public function load_TUTOR_PMPRO(){
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));
		$this->paid_memberships_pro = new PaidMembershipsPro();
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

			$className = str_replace('TUTOR_PMPRO'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
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

	/**
	 * @param $arr
	 *
	 * @return mixed
	 *
	 * Returning monetization options
	 *
	 * @since v.1.3.6
	 */
	public function tutor_monetization_options($arr){
		$has_pmpro = tutils()->has_pmpro();
		if ($has_pmpro){
			$arr['pmpro'] = __('Paid Memberships Pro', 'tutor-pro');
		}
		return $arr;
	}

}