<?php
namespace TUTOR_PRO;

if ( ! defined( 'ABSPATH' ) )
	exit;

class init{
	public $version = TUTOR_PRO_VERSION;
	public $path;
	public $url;
	public $basename;

	private $admin;
	private $assets;
	private $general;
	private $quiz;

	private $updater;

	//Components

	function __construct() {
		if ( ! function_exists('is_plugin_active')){
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$this->path = plugin_dir_path(TUTOR_PRO_FILE);
		$this->url = plugin_dir_url(TUTOR_PRO_FILE);
		$this->basename = plugin_basename(TUTOR_PRO_FILE);

		if ( is_plugin_active('tutor/tutor.php')){
			add_action('tutor_loaded', array($this, 'load_constructors_asset'));
		}else{
			spl_autoload_register(array($this, 'loader'));
			$this->admin = new Admin();
			$this->assets = new Assets();
		}
		$this->includes();

		//$this->load_constructors_asset();
	}


	public function load_constructors_asset(){
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));

		do_action('tutor_pro_before_load');
		//Load Component from Class
		$this->admin = new Admin();
		$this->assets = new Assets();
		$this->general = new General();
		$this->quiz = new Quiz();

		$this->course_duplicator = new Course_Duplicator();
		$this->instructor_percentage = new Instructor_Percentage();
		$this->enrollment_expiry = new Enrollment_Expiry();

		$this->load_addons();

		require_once( dirname( __DIR__ ). '/updater/update.php');
		$this->updater = new \ThemeumUpdater\Update(array(
			'product_title' => 'Tutor Pro',
			'product_slug' => 'tutor-pro',
			'product_basename' => tutor_pro()->basename,
			'product_type' => 'plugin',
			'current_version' => TUTOR_PRO_VERSION,

			'menu_title' => 'Tutor Pro License',
			'parent_menu' => 'tutor',
			'menu_capability' => 'manage_tutor',
			'license_option_key' => 'tutor_license_info',

			'updater_url' => tutor_pro()->url.'updater/',
			'header_content' => '<svg width="116" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><defs/><path d="M27.496 19.704V6.36h-4c-.452 0-.62-.288-.62-.988v-.62c0-.704.168-.992.62-.992H34.56c.456 0 .62.288.62.992v.62c0 .7-.208.988-.62.988h-3.964v13.344c0 .456-.372.704-1.24.704h-.62c-.868-.008-1.24-.248-1.24-.704zM53.148 14.292c0-3.924 1.944-6.072 5.6-6.072 3.656 0 5.576 2.148 5.576 6.072s-1.924 6.072-5.556 6.072-5.62-2.148-5.62-6.072zm8.18 0c0-2.52-.8-3.8-2.56-3.8s-2.604 1.28-2.604 3.8c0 2.52.8 3.8 2.604 3.8s2.56-1.292 2.56-3.8zM66 8.22h1.94a.324.324 0 01.332.332l.124 1.732c.576-.948 1.404-1.776 2.6-1.776 1.076 0 1.364.4 1.364 1.116 0 .456-.248 1.736-.66 1.736a8.735 8.735 0 00-1.156-.208c-1.076 0-1.82.952-2 1.28v7.272c0 .456-.372.704-1.28.704h-.372c-.868 0-1.24-.248-1.24-.704V8.552A.326.326 0 0166 8.22zM78.1 19.664V4.256c0-.332.288-.496.908-.496h.192c.66 0 .908.204.908.496v14.332h6.4c.332 0 .456.208.456.704v.372c0 .496-.124.7-.456.7H78.8a.611.611 0 01-.7-.7zM102.225 3.716h-.8a1.027 1.027 0 00-.988.4l-4.988 8.564-5-8.548c-.12-.248-.4-.4-.988-.4h-.828c-.66 0-.908.208-.908.496v15.64c0 .332.288.496.908.496h.168c.664 0 .912-.204.912-.496v-11.6c.007-.427-.008-.854-.044-1.28l.084-.04c.152.365.345.71.576 1.032L94.5 15.2a.448.448 0 00.492.292h.912a.576.576 0 00.496-.292l4.088-7.064c.208-.328.372-.66.58-1.032l.08.044c-.04.4-.04.864-.04 1.28v11.44c0 .332.288.496.908.496h.164c.664 0 .912-.204.912-.496v-15.6c.08-.344-.208-.552-.868-.552zM105.488 19.248c-.292-.204-.456-.4-.456-.576 0-.372.576-1.28.824-1.28.181.047.35.132.496.248a7.162 7.162 0 003.556.948c1.776 0 3.056-1.116 3.056-2.768 0-1.9-1.736-2.476-3.552-3.18-2-.8-4.092-1.528-4.092-4.628 0-2.52 2.068-4.336 5.2-4.336.992 0 2.52.288 3.308.828a.794.794 0 01.452.62c0 .368-.496 1.236-.744 1.236a1.438 1.438 0 01-.62-.288 5.09 5.09 0 00-2.436-.62c-1.776 0-3.016.952-3.016 2.644 0 1.692 1.488 2.148 3.18 2.8 2.108.8 4.464 1.736 4.464 4.96 0 2.8-2.068 4.544-5.2 4.544-2.028 0-3.68-.572-4.42-1.152zM51.828 17.888a5.25 5.25 0 01-.8.08c-.62 0-.992-.288-.992-1.28v-6.156h1.236a.632.632 0 00.62-.62V8.756a.636.636 0 00-.62-.62h-1.236V5.6a.636.636 0 00-.62-.616H47.68a.632.632 0 00-.604.616v2.52h-.8a.636.636 0 00-.62.62v1.156a.632.632 0 00.62.62h.8v6.212c0 2.644 1.448 3.6 3.304 3.6.704 0 2.068-.084 2.068-.992.04-.168-.208-1.448-.62-1.448zM43.856 8.18a.636.636 0 01.62.62v10.944a.636.636 0 01-.62.62h-1.572a.636.636 0 01-.62-.62v-1.156a4.798 4.798 0 01-3.6 1.776c-2.4 0-3.6-1.28-3.6-3.8V8.8a.636.636 0 01.62-.62h1.736a.632.632 0 01.632.62v7.352c0 1.116.576 1.736 1.776 1.736.992 0 1.816-.744 2.272-1.448V8.8a.631.631 0 01.62-.62h1.736z" fill="#092844"/><path fill-rule="evenodd" clip-rule="evenodd" d="M4.824 15.284a1.2 1.2 0 01-1.156-1.156v-2.644a1.156 1.156 0 112.312 0v2.644a1.128 1.128 0 01-1.156 1.156zM13.168 15.284A1.154 1.154 0 0112 14.128v-2.644a1.156 1.156 0 012.312 0v2.644a1.153 1.153 0 01-1.156 1.156" fill="#0049F8"/><path fill-rule="evenodd" clip-rule="evenodd" d="M2.512 9.5a2.928 2.928 0 012.56-1.692 2.948 2.948 0 012.852 3.016v5.412a1.084 1.084 0 002.148 0v-5.412a2.944 2.944 0 012.848-3.016 2.672 2.672 0 012.48 1.568A7.2 7.2 0 112.512 9.5zm4.544-7.1h4.048v1.564a8.905 8.905 0 00-2.064-.248c-.672.007-1.341.076-2 .208L7.056 2.4zm10.988 11.068c0-.248.04-.456.04-.704a9.077 9.077 0 00-4.624-7.888V2.4h1.74a1.2 1.2 0 000-2.4H2.924a1.264 1.264 0 00-1.2 1.24 1.2 1.2 0 001.2 1.2H4.7v2.44a9.028 9.028 0 00-3.264 12.8C4.8 23.38 13.912 23.96 17.012 24a1.2 1.2 0 00.744-.288 1.008 1.008 0 00.288-.744v-9.5z" fill="#0049F8"/></svg>'
		));

		do_action('tutor_pro_loaded');
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

			$className = str_replace('TUTOR_PRO'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
			$file_name = $this->path.$className.'.php';

			if (file_exists($file_name) && is_readable( $file_name ) ) {
				require_once $file_name;
			}
		}
	}

	//Run the TUTOR right now
	public function run(){
		do_action('tutor_pro_before_run');

		register_activation_hook( TUTOR_PRO_FILE, array( $this, 'tutor_pro_activate' ) );

		do_action('tutor_pro_after_run');
	}

	/**
	 * Do some task during plugin activation
	 */
	public function tutor_pro_activate(){
		$version = get_option('tutor_pro_version');
		//Save Option
		if ( ! $version){
			update_option('tutor_pro_version', TUTOR_PRO_VERSION);
		}
	}


	public function includes(){
		include tutor_pro()->path.'includes/functions.php';
	}

	public function load_addons() {
		
		$addonsDir = array_filter(glob(tutor_pro()->path."addons".DIRECTORY_SEPARATOR."*"), 'is_dir');
		if (count($addonsDir) > 0) {
			foreach ($addonsDir as $key => $value) {
				$addon_dir_name = str_replace(dirname($value).DIRECTORY_SEPARATOR, '', $value);
				$file_name = tutor_pro()->path . 'addons'.DIRECTORY_SEPARATOR.$addon_dir_name.DIRECTORY_SEPARATOR.$addon_dir_name.'.php';
				if ( file_exists($file_name) ){
					include_once $file_name;
				}
			}
		}
	}

}