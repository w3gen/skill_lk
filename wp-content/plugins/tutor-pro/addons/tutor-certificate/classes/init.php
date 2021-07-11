<?php
namespace TUTOR_CERT;

if ( ! defined( 'ABSPATH' ) )
	exit;

class init{
	public $version = TUTOR_CERT_VERSION;
	public $path;
	public $url;
	public $basename;

	//Module
	public $certificate;

	function __construct() {
		if ( ! function_exists('tutor')){
			return;
		}
		$addonConfig = tutor_utils()->get_addon_config(TUTOR_CERT()->basename);
		$isEnable = (bool) tutor_utils()->avalue_dot('is_enable', $addonConfig);
		if ( ! $isEnable){
			return;
		}

		$this->path = plugin_dir_path(TUTOR_CERT_FILE);
		$this->url = plugin_dir_url(TUTOR_CERT_FILE);
		$this->basename = plugin_basename(TUTOR_CERT_FILE);

		$this->load_TUTOR_CERT();

		new Instructor_Signature;
	}

	public function load_TUTOR_CERT(){
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));
		$this->certificate = new Certificate();

		add_filter('tutor/options/attr', array($this, 'add_options'));
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

			$className = str_replace('TUTOR_CERT'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
			$file_name = $this->path.$className.'.php';

			if (file_exists($file_name) && is_readable( $file_name ) ) {
				require_once $file_name;
			}
		}
	}


	//Run the TUTOR right now
	public function run(){
		register_activation_hook( TUTOR_CERT_FILE, array( $this, 'tutor_activate' ) );
	}

	/**
	 * Do some task during plugin activation
	 */
	public function tutor_activate(){
		$version = get_option('TUTOR_CERT_version');
		//Save Option
		if ( ! $version){
			update_option('TUTOR_CERT_version', TUTOR_CERT_VERSION);
		}
	}

	public function add_options($attr){
		$attr['tutor_certificate'] = array(
			'label' => __( 'Tutor Certificate', 'tutor-pro' ),

			'sections'    => array(
				'general' => array(
					'label' => __('General', 'tutor-pro'),
					'desc' => __('Tutor Certificate', 'tutor-pro'),
					'fields' => array(
						'tutor_cert_authorised_name' => array(
							'type'      => 'text',
							'label'     => __('Authorised Name', 'tutor-pro'),
							'desc'      => __('Authorised name will be printed under signature.',	'tutor-pro'),
						),
						'tutor_cert_authorised_company_name' => array(
							'type'      => 'text',
							'label'     => __('Authorised Company Name', 'tutor-pro'),
							'desc'      => __('Authorised company name will be printed under authorised name.',	'tutor-pro'),
						),
						'show_instructor_name_on_certificate' => array(
							'type'       => 'checkbox',
							'label'      => __('Show instructor name on certificate', 'tutor-pro'),
							'label_title'=> __('Enable', 'tutor-pro'),
							'desc'       => __('Show instructor name on certificate before Authorised Name',	'tutor-pro'),
						),
						'tutor_cert_signature_image_id' => array(
							'type'          => 'media',
							'label'         => __('Upload Signature', 'tutor-pro'),
							'attr'          => array('media_type' => 'image'), //image,file
							'desc'          => __('Upload a signature that will be printed at certificate.',	'tutor-pro'),
						),
						'tutor_course_certificate_view' => array(
							'type'      	=> 'checkbox',
							'label'     	=> __('View Certificate', 'tutor-pro'),
							'label_title' 	=> __('Enable', 'tutor-pro'),
							'desc'      	=> __('By enabling this option, the student will be able to verify and share their certificates URL which is publicly accessible', 'tutor-pro'),
						),
						'send_certificate_link_to_course_completion_email' => array(
							'type'       => 'checkbox',
							'label'      => __('Certificate link in course completion email', 'tutor-pro'),
							'label_title'=> __('Enable', 'tutor-pro'),
							'desc'       => __('Send certificate link along with the course completion email. Student must be logged in to access the certificate if public view is not enabled.', 'tutor-pro'),
						),
					),
				),
			),
		);
		return $attr;
	}

}