<?php
/**
 * ContentDrip class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.4.1
 */

namespace TUTOR_CONTENT_DRIP;

if ( ! defined( 'ABSPATH' ) )
	exit;

class ContentDrip {

	private $unlock_timestamp = false;
	private $unlock_message = null;
	private $drip_type = null;
	private $mail_log_meta_key = '_tutor_pro_content_drip_mail_log';
	private $sent_mail_log=array();
	private $send_limit = 5;
	
	public function __construct() {

		/**
		 * add meta box for lesson post type
		 * add support content drip on single lesson
		 * @since 1.8.9
		*/
		add_action('add_meta_boxes', array($this, 'register_content_drip_meta_box'));

		add_filter('tutor_course_settings_tabs', array($this, 'settings_attr') );

		add_action('tutor_lesson_edit_modal_form_after', array($this, 'content_drip_lesson_metabox'), 10, 0);
		add_action('tutor_quiz_edit_modal_settings_tab_after', array($this, 'content_drip_lesson_metabox'), 10, 0);
		add_action('tutor_assignment_edit_modal_form_after', array($this, 'content_drip_lesson_metabox'), 10, 0);

		add_action('tutor/lesson_update/after', array($this, 'lesson_updated'));
		add_action('tutor_quiz_settings_updated', array($this, 'lesson_updated'));
		add_action('tutor_assignment_updated', array($this, 'lesson_updated'));

		/**
		 * on save lesson update content drip meta
		 * @since 1.8.9
		*/
		add_action('save_post_'.tutor()->lesson_post_type, array($this, 'lesson_updated'), 10, 1 );

		add_action('tutor/lesson_list/right_icon_area', array($this, 'show_content_drip_icon'));

		add_filter('tutor_lesson/single/content', array($this, 'drip_content_protection'));
		add_filter('tutor_assignment/single/content', array($this, 'drip_content_protection'));
		add_filter('tutor_single_quiz/body', array($this, 'drip_content_protection'));

		// Lesson-Quiz-Assignment onPublish Mailing
		add_action('init', array($this, 'execute_content_drip_publish_hook'));
		add_filter('tutor/options/extend/attr', array($this, 'register_emails'), 11);
		add_filter('tutor_emails/dashboard/list', array($this, 'register_email_list'), 11);

		/**
		 * add admin script 
		 * @since 1.8.9
		*/
		add_action('admin_enqueue_scripts', array($this, 'content_drip_scripts'));
	}

	public function settings_attr($args){
		$args['contentdrip'] = array(
			'label' => __('Content Drip', 'tutor-pro'),
			'desc' => __('Tutor Content Drip allow you to schedule publish topics / lesson', 'tutor-pro'),
			'icon_class' => 'dashicons dashicons-clock',
			'callback'  => '',
			'fields'    => array(
				'enable_content_drip' => array(
					'type'      => 'checkbox',
					'label'     => '',
					'label_title' => __('Enable', 'tutor-pro'),
					'default' => '0',
					'desc'      => __('Enable / Disable content drip', 'tutor-pro'),
				),
				'content_drip_type' => array(
					'type'      => 'radio',
					'label'     => __('Content Drip Type','tutor-pro'),
					'default' => 'unlock_by_date',
					'options'   => array(
						'unlock_by_date'                =>  __('Schedule course contents by date', 'tutor-pro'),
						'specific_days'                 =>  __('Content available after X days from enrollment', 'tutor-pro'),
						'unlock_sequentially'           =>  __('Course content available sequentially', 'tutor-pro'),
						'after_finishing_prerequisites'    =>  __('Course content unlocked after finishing prerequisites', 'tutor-pro'),
					),
					'desc'      => __('You can schedule your course content using the above content drip options.', 'tutor-pro'),
				),
			),
		);
		return $args;
	}


	public function content_drip_lesson_metabox(){
		include  TUTOR_CONTENT_DRIP()->path.'views/content-drip-lesson.php';
	}

	public function lesson_updated($lesson_id){
		$content_drip_settings = tutils()->array_get('content_drip_settings', $_POST);
		if (tutils()->count($content_drip_settings)){
			update_post_meta($lesson_id, '_content_drip_settings', $content_drip_settings);
		}
	}

	/**
	 * @param $post
	 *
	 * Show lock icon based on condition
	 */
	public function show_content_drip_icon($post){
		$is_lock = $this->is_lock_lesson($post);

		if ($is_lock){
			echo '<i class="tutor-icon-lock"></i>';
		}
	}

	public function is_lock_lesson($post = null){
		$post = get_post($post);
		$lesson_id = $post->ID;

		$lesson_post_type = tutor()->lesson_post_type;

		$course_id = tutils()->get_course_id_by_content($post);
		$enable = (bool) get_tutor_course_settings($course_id, 'enable_content_drip');
		if ( ! $enable){
			return false;
		}

		$drip_type = get_tutor_course_settings($course_id, 'content_drip_type');
		$this->drip_type = $drip_type;

		$courseObg = get_post_type_object( $post->post_type );
		$singular_post_type = '';
		if ( ! empty($courseObg->labels->singular_name)){
			$singular_post_type = $courseObg->labels->singular_name;
		}

		//if ($lesson_post_type === $post->post_type){
			if ($drip_type === 'unlock_by_date'){
				$unlock_timestamp = strtotime(get_item_content_drip_settings($lesson_id, 'unlock_date'));
				if ($unlock_timestamp){
					$unlock_date = date_i18n(get_option('date_format'), $unlock_timestamp);
					$this->unlock_message = sprintf(__("This %s will be available from %s", 'tutor-pro'), $singular_post_type, $unlock_date);

					return $unlock_timestamp > current_time('timestamp');
				}
			}elseif ($drip_type === 'specific_days'){
				$days = (int) get_item_content_drip_settings($lesson_id, 'after_xdays_of_enroll');

				if ($days > 0){
					$enroll = tutils()->is_course_enrolled_by_lesson($lesson_id);
					$enroll_date = tutils()->array_get('post_date', $enroll);
					$enroll_date = date('Y-m-d', strtotime($enroll_date));
					$days_in_time = 60*60*24*$days;

					$unlock_timestamp = strtotime($enroll_date) + $days_in_time;

					$unlock_date = date_i18n(get_option('date_format'), $unlock_timestamp);
					$this->unlock_message = sprintf(__("This lesson will be available for you from %s", 'tutor-pro'), $unlock_date);

					return $unlock_timestamp > current_time('timestamp');
				}
			}
		//}

		if ($drip_type === 'unlock_sequentially'){
			$previous_id = tutor_utils()->get_course_previous_content_id($post);

			if ($previous_id){
				$previous_content = get_post($previous_id);

				$obj = get_post_type_object( $previous_content->post_type );

				if ($previous_content->post_type === $lesson_post_type){
					$is_lesson_complete = tutils()->is_completed_lesson($previous_id);
					if ( ! $is_lesson_complete){
						$this->unlock_message = sprintf(__("Please complete previous %s first", 'tutor-pro'), $obj->labels->singular_name);
						return true;
					}
				}
				if ($previous_content->post_type === 'tutor_assignments') {
					$is_submitted = tutils()->is_assignment_submitted($previous_id);
					if ( ! $is_submitted){
						$this->unlock_message = sprintf(__("Please submit previous %s first", 'tutor-pro'), $obj->labels->singular_name);
						return true;
					}
				}
				if ($previous_content->post_type === 'tutor_quiz'){
					$attempts = tutils()->quiz_ended_attempts($previous_id);
					if ( ! $attempts){
						$this->unlock_message = sprintf(__("Please complete previous %s first", 'tutor-pro'), $obj->labels->singular_name);
						return true;
					}
				}
			}

		}elseif ($drip_type === 'after_finishing_prerequisites'){
			$prerequisites = (array) get_item_content_drip_settings($lesson_id, 'prerequisites');
			$prerequisites = array_filter($prerequisites);
			
			if (tutils()->count($prerequisites)){
				$required_finish = array();
				
				foreach ($prerequisites as $id){
					$item = get_post($id);

					if ($item->post_type === $lesson_post_type){
						$is_lesson_complete = tutils()->is_completed_lesson($id);
						if ( ! $is_lesson_complete){
							$required_finish[] = "<a href='".get_permalink($item)."' target='_blank'>{$item->post_title}</a>";
						}
					}
					if ($item->post_type === 'tutor_assignments') {
						$is_submitted = tutils()->is_assignment_submitted($id);
						if ( ! $is_submitted){
							$required_finish[] = "<a href='".get_permalink($item)."' target='_blank'>{$item->post_title}</a>";
						}
					}
					if ($item->post_type === 'tutor_quiz'){
						$attempts = tutils()->quiz_ended_attempts($id);
						if ( ! $attempts){
							$required_finish[] = "<a href='".get_permalink($item)."' target='_blank'>{$item->post_title}</a>";
						}
					}
				}

				if (tutils()->count($required_finish)){
					$output = '<h4>' .sprintf(__("You can take this %s after finishing the following prerequisites:", 'tutor-pro'), $singular_post_type) . '</h4>';
					$output .= "<ul>";
					foreach ($required_finish as $required_finish_item){
						$output .= "<li>{$required_finish_item}</li>";
					}
					$output .= "</ul>";

					$this->unlock_message = $output;
					return true;
				}
			}
		}

		return false;
	}

	public function drip_content_protection($html){
		if ($this->is_lock_lesson(get_the_ID())){

			if ($this->drip_type === 'after_finishing_prerequisites'){
				$img_url = trailingslashit(TUTOR_CONTENT_DRIP()->url).'assets/images/traffic-light.svg';

				$output = "<div class='content-drip-message-wrap content-drip-wrap-flex'> <div class='content-drip-left'><img src='{$img_url}' alt='' /> </div> <div class='content-drip-right'>{$this->unlock_message}</div> </div>";

				$output = apply_filters('tutor/content_drip/unlock_message', $output);
				return "<div class='tutor-lesson-content-drip-wrap'> {$output} </div>";
			}else{
				$output = apply_filters('tutor/content_drip/unlock_message', "<div class='content-drip-message-wrap tutor-alert'> {$this->unlock_message}</div>");
				return "<div class='tutor-lesson-content-drip-wrap'> {$output} </div>";
			}

		}

		return $html;
	}

	// Register emails to show in setting page that admin can enable disable.
	public function register_emails($attr){
		
		if(!isset($attr['email_notification'])){
			return $attr;
		}
		
		$options = &$attr['email_notification']['sections']['general']['fields']['email_to_students']['options'];
		$options['new_lesson_published'] = __('Content Drip: New Lesson Published', 'tutor-pro');
		$options['new_quiz_published'] = __('Content Drip: New Quiz Published', 'tutor-pro');
		$options['new_assignment_published'] = __('Content Drip: New Assignment Published', 'tutor-pro');

		return $attr;
	}

	// Register list for emails in dashboard
	public function register_email_list($emails){

		$lqa = '{site_url}, {site_name}, {student_username}, {lqa_type}, {course_title}, {lqa_title}';
		
		$emails['email_to_students.new_lesson_published']=array(
			__('Content Drip: New Lesson Published', 'tutor-pro'),
			$lqa
		);

		$emails['email_to_students.new_quiz_published']=array(
			__('Content Drip: New Quiz Published', 'tutor-pro'),
			$lqa
		);

		$emails['email_to_students.new_assignment_published']=array(
			__('Content Drip: New Assignment Published', 'tutor-pro'),
			$lqa
		);

		return $emails;
	}

	// List all the courses where content drip enabled
	private function get_mailing_courses(){
		
		global $wpdb;
		
		$drip_enabled = esc_sql('s:19:"enable_content_drip";s:1:"1";');

		// Get courses that is published and content drip enabled
		$courses = $wpdb->get_results(
			"SELECT {$wpdb->posts}.ID, {$wpdb->posts}.post_title, {$wpdb->postmeta}.meta_value
			FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta}
			ON {$wpdb->posts}.ID={$wpdb->postmeta}.post_id
			WHERE {$wpdb->posts}.post_status='publish'
				AND {$wpdb->postmeta}.meta_key='_tutor_course_settings'
				AND {$wpdb->postmeta}.meta_value LIKE '%{$drip_enabled}%'");

		$courses = array_map(function($element){
			$element->meta_value = unserialize($element->meta_value);
			return $element;
		}, $courses);
		
		return $courses;
	}

	// Get all the lesson, quizzes and assignments by course ID
	private function get_mailing_course_children($course_id){
		
		global $wpdb;
		$topic_ids = "SELECT ID FROM {$wpdb->posts} WHERE post_parent={$course_id} AND post_type='topics'";
		$content_types = "'lesson', 'tutor_quiz', 'tutor_assignments'";
		
		return $wpdb->get_results(
			"SELECT ID, post_title, post_type 
			FROM {$wpdb->posts} 
			WHERE post_parent IN ({$topic_ids}) 
				AND post_type IN ({$content_types})");
	}

	// Check if mail sent to specific user for specific lesson-quiz-assignment publish
	private function is_mail_sent($student_id, $content_id, $time_stamp){

		if(!isset($this->sent_mail_log[$student_id])){
			$log =  get_user_meta($student_id, $this->mail_log_meta_key, true);
			$this->sent_mail_log[$student_id] = is_array($log) ? $log : array();
		}

		$log = array_key_exists($content_id, $this->sent_mail_log[$student_id]) ? $this->sent_mail_log[$student_id][$content_id] : array();
		
		if(in_array($time_stamp, $log)){
			return true;
		}

		$log[] = $time_stamp;
		$this->sent_mail_log[$student_id][$content_id] = $log;
	}

	// Get the timestamp when the LQA should be considered as published
	private function get_content_publish_timestamp($content_id, $enroll_date, bool $unlock_by_date){
		$timestamp = null;

		if($unlock_by_date){
			$timestamp = (int)strtotime(get_item_content_drip_settings($content_id, 'unlock_date'));
		}
		else{
			$days = (int) get_item_content_drip_settings($content_id, 'after_xdays_of_enroll');

			if ($days > 0){
				$enroll_date = date('Y-m-d', strtotime($enroll_date));
				$days_in_time = 60*60*24*$days;

				$timestamp = strtotime($enroll_date) + $days_in_time;
			}
		}

		return $timestamp;
	}

	// Get enrollments of published course by course ID
	private function get_mailing_enrollments($course_id){
		
		global $wpdb;

		$enrollments = $wpdb->get_results(
			"SELECT {$wpdb->posts}.ID as enrolment_id, 
				{$wpdb->posts}.post_date AS enroll_date,
				{$wpdb->users}.ID as student_id,
				{$wpdb->users}.user_email,
				{$wpdb->users}.display_name
			FROM {$wpdb->posts} 
			LEFT JOIN {$wpdb->users} ON {$wpdb->posts}.post_author={$wpdb->users}.ID
			WHERE {$wpdb->posts}.post_parent={$course_id} 
				AND {$wpdb->posts}.post_type='tutor_enrolled' 
				AND {$wpdb->posts}.post_status='completed'");
		
		return $enrollments;
	}

	// Initialize content drip publication hooks
	public function execute_content_drip_publish_hook(){

		$last_call = get_option( 'tutor_cd_last_call_time', null );

		if(!$last_call || $last_call < (time()-3600)) {
			update_option( 'tutor_cd_last_call_time', time(), true );
		} else {
			return;
		}
		
		$mail_enable_status = array();

		// Loop through published courses 
		$courses = $this->get_mailing_courses();
		foreach($courses as $course){
			
			$drip_type = $course->meta_value['content_drip_type'];

			if($drip_type!=='unlock_by_date' && $drip_type!=='specific_days'){
				// No need to send mail for other drip types.	
				continue;
			}

			$students = $this->get_mailing_enrollments($course->ID);
			$contents = $this->get_mailing_course_children($course->ID);

			// Loop through lesson, quiz and assignments
			foreach($contents as $content){

				$event = trim($content->post_type, 'tutor_'); // lesson, quiz or assignments;
				$event = trim($event, 's'); // lesson, quiz or assignment;

				if( !array_key_exists($event, $mail_enable_status) ) {
					$mail_enable_status[$event] = tutor_utils()->get_option('email_to_students.new_' . $event . '_published');
				}

				if( !$mail_enable_status[$event] ) {
					continue;
				}

				foreach($students as $student){

					$unlocK_timestamp = $this->get_content_publish_timestamp($content->ID, $student->enroll_date, $drip_type=='unlock_by_date');
					
					if(!$unlocK_timestamp || $unlocK_timestamp>time()){
						// Check if publish time passed
						continue;
					}

					if(!$this->is_mail_sent($student->student_id, $content->ID, $unlocK_timestamp)){
						
						$arg = array(
							'student' => $student,
							'lqa' => $content,
							'course' => $course,
							'lqa_type' => ucfirst($event)
						);

						do_action('tutor-pro/content-drip/new_'.$event.'_published', $arg);
					}
				}
			}
		}
		
		foreach($this->sent_mail_log as $user_id=>$log){
			update_user_meta($user_id, $this->mail_log_meta_key, $log);
		}
	}

	/**
	 * register meta box on single lesson screen
	 * @since 1.8.9
	*/
	public function register_content_drip_meta_box() {
		add_meta_box(
			'tutor-content-drip-single-lesson',
			__( 'Content Drip Settings', 'tutor-pro' ),
			array($this, 'content_drip_lesson_metabox'),
			tutor()->lesson_post_type
		);		
	}

	/**
	 * enqueue admin script
	 * @since 1.8.9
	*/
	public function content_drip_scripts() {
		global $post;
		$ver = TUTOR_CONTENT_DRIP_VERSION;

		if( !is_null($post) && $post->post_type == tutor()->lesson_post_type ) {
			wp_enqueue_script(
				'tutor-content-drip-script',
				TUTOR_CONTENT_DRIP()->url.'assets/js/scripts.js',
				array('jquery'),
				$ver,
				true	
			);
		}

	}

}