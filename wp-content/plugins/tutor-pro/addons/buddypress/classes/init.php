<?php
namespace TUTOR_BP;

if ( ! defined( 'ABSPATH' ) )
	exit;

class init{
	public $version = TUTOR_BP_VERSION;
	public $path;
	public $url;
	public $basename;

	//Module
	private $buddypress_messages;
	private $buddypress_groups;
	private $buddypress_group_settings;

	function __construct() {
		if ( ! function_exists('tutor')){
			return;
		}

		$addonConfig = tutils()->get_addon_config(TUTOR_BP()->basename);
		$isEnable = (bool) tutils()->array_get('is_enable', $addonConfig);
		$has_bp = tutils()->has_bp();
		if ( ! $isEnable || ! $has_bp){
			return;
		}

		$this->path = plugin_dir_path(TUTOR_BP_FILE);
		$this->url = plugin_dir_url(TUTOR_BP_FILE);
		$this->basename = plugin_basename(TUTOR_BP_FILE);


		add_action('bp_init', array($this, 'load_group_extension'));
	}

	public function load_TUTOR_BP(){
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));

		if( bp_is_active('groups')){
			$this->buddypress_groups = new BuddyPressGroups();
			$this->buddypress_group_settings = new BuddyPressGroupSettings();
		}
		if (bp_is_active('messages')){
			$this->buddypress_messages = new BuddyPressMessages();
		}
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

			$className = str_replace('TUTOR_BP'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
			$file_name = $this->path.$className.'.php';

			if (file_exists($file_name)  ) {
				require_once $file_name;
			}
		}
	}

	/**
	 *
	 * @since TutorPro v.1.5.0
	 */

	function load_group_extension() {
		$this->load_TUTOR_BP();

		if ( bp_is_active('groups') && current_user_can('manage_tutor') ) {
			bp_register_group_extension( 'TUTOR_BP\BuddyPressGroupSettings' );
		}
	}

	//Run the TUTOR right now
	public function run(){
		//
	}


}