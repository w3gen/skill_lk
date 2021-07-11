<?php
/**
 * Tutor Course attachments Main Class
 */

namespace TUTOR_CA;

use TUTOR\Tutor_Base;

class CourseAttachments extends Tutor_Base {

	private $open_mode = 'tutor_pro_attachment_open_type';

	public function __construct() {
		parent::__construct();
		add_action( 'add_meta_boxes', array($this, 'register_meta_box') );
		add_action('tutor/dashboard_course_builder_form_field_after', array($this, 'register_meta_box_in_frontend'));

		add_filter('tutor_course/single/enrolled/nav_items_rewrite', array($this, 'add_course_nav_rewrite'));
		add_filter('tutor_course/single/enrolled/nav_items', array($this, 'add_course_nav_item'));

		/**
 		 * listen only save_post will hook for every post type
 		 * course / lesson / quizz etc
 		 * removed save_post_courses hook to avoid redundancy
 		 * @since 1.8.9
		*/
		add_action('save_post', array($this, 'save_course_meta'));
		add_action('save_tutor_course', array($this, 'save_course_meta'));

		add_filter('tutor/options/attr', array($this, 'add_download_options_field'));
		add_filter('tutor_pro_attachment_open_mode', array($this, 'set_open_open_mode'));
	}

	public function set_open_open_mode(){
		return tutor_utils()->get_option($this->open_mode);
	}

	public function add_download_options_field($attr){
		$attr['course']['sections']['general']['fields'][$this->open_mode] = array(
			'type'          => 'radio',
			'label'         => __('Attachment Open Mode', 'tutor-pro'),
			'default'       => 'downlaod',
			'select_options'   => false,
			'options'   => array(
				'downlaod'  =>  __('Download', 'tutor-pro'),
				'view'    =>  __('View in new tab', 'tutor-pro'),
			),
			'desc'          => __('How you want users to view attached files.', 'tutor-pro'),
		);

		return $attr;
	}

	public function add_course_nav_item($items){
		if (is_single() && get_the_ID()) {
			/* $course_id = tutils()->get_post_id();
			$attachments = maybe_unserialize(get_post_meta($course_id, '_tutor_attachments', true));
			if (is_array($attachments) && count($attachments)) { */
				$items['overview'] = __('Resources', 'tutor-pro');
			/* } */
		}
		return $items;
	}

	public function add_course_nav_rewrite($items){
		$items['overview'] = __('Resources', 'tutor-pro');
		return $items;
	}

	public function register_meta_box(){
		$coursePostType = tutor()->course_post_type;

		/**
		 * Check is allow private file upload
		 */
		add_meta_box( 'tutor-course-attachments', __( 'Attachments (private files)', 'tutor-pro' ), array($this, 'course_attachments_metabox'),
			$coursePostType, 'advanced', 'high' );
	}

	public function register_meta_box_in_frontend(){
		course_builder_section_wrap($this->course_attachments_metabox($echo = false), __('Course Attachments', 'tutor-pro'));
	}

	public function course_attachments_metabox($echo = true){
		ob_start();
		include  TUTOR_CA()->path.'views/metabox/course-attachments-metabox.php';
		$content = ob_get_clean();

		if ($echo){
			echo $content;
		}else{
			return $content;
		}
	}

	/**
	 * upload attachment only if $_POST[tutor_attachments]
	 * is not empty else delete
	 * it will remove empty data in db
	 * @since 1.8.9 
	*/
	public function save_course_meta($post_ID){
		//Attachments
		$attachments = array();
		$attachments_main_edit = tutils()->avalue_dot('_tutor_attachments_main_edit', $_POST);
		if($attachments_main_edit) {

			if ( !empty($_POST['tutor_attachments']) ) {
				$attachments = tutils()->sanitize_array($_POST['tutor_attachments']);
				$attachments = array_unique($attachments);
			} 

			if(!empty($attachments)) {
					update_post_meta($post_ID, '_tutor_attachments', $attachments);	
			}   else {
				delete_post_meta($post_ID, '_tutor_attachments');	
			}
		}
	}


}