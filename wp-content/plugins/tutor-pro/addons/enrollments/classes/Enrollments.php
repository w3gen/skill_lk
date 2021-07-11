<?php
/**
 * Enrollments class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.4.0
 */

namespace TUTOR_ENROLLMENTS;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Enrollments {

	protected $success_msgs = '';

	public function __construct() {
		add_action('admin_menu', array($this, 'register_menu'));

		add_action('wp_ajax_tutor_json_search_students', array($this, 'tutor_json_search_students'));
		add_action('tutor_action_enrol_student', array($this, 'enrol_student'));
	}

	public function register_menu(){
		add_submenu_page('tutor', __('Enrollments', 'tutor-pro'), __('Enrollments', 'tutor-pro'), 'manage_tutor', 'enrollments', array($this, 'enrollments') );
	}

	/**
	 * View the page of
	 */
	public function enrollments(){
		$sub_page = tutils()->array_get('sub_page', $_GET);
		if ($sub_page){
			include TUTOR_ENROLLMENTS()->path."views/{$sub_page}.php";
		}else{
			include TUTOR_ENROLLMENTS()->path."views/enrollments.php";
		}
	}




	public function tutor_json_search_students(){
		tutils()->checking_nonce();

		global $wpdb;

		$term = sanitize_text_field(tutils()->array_get('term', $_POST));
		$term = '%' . $term. '%';

		$student_res = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE display_name LIKE %s OR user_email LIKE %s", $term, $term));
		$students = array();

		if (tutils()->count($student_res)){
			foreach ($student_res as $student){
				$students[$student->ID] = sprintf(
					esc_html__( '%1$s (#%2$s - %3$s)', 'tutor-pro' ),
					$student->display_name,
					$student->ID,
					$student->user_email
				);
			}
		}

		wp_send_json($students);
	}

	/**
	 * Manually enrol a student this this course
	 * By Course ID, Student ID
	 *
	 * @since v.1.4.0
	 */
	public function enrol_student(){
		$user_id = (int) sanitize_text_field(tutils()->array_get('student_id', $_POST));
		$course_id = (int) sanitize_text_field(tutils()->array_get('course_id', $_POST));

		$is_enrolled = tutils()->is_enrolled($course_id, $user_id);
		$total_enrolled = tutils()->count_enrolled_users_by_course($course_id);
		$maximum_students = (int) tutils()->get_course_settings($course_id, 'maximum_students');

		if ($is_enrolled) {
			$this->success_msgs = get_tnotice(__('This user has been already enrolled on this course', 'tutor-pro'), 'Error', 'danger');
		} else if ($maximum_students && $maximum_students <= $total_enrolled) {
			$this->success_msgs = get_tnotice(__('Maximum student is reached!', 'tutor-pro'), 'Error', 'danger');
		} else {
			/**
			 * Enroll Now
			 */

			do_action('tutor_before_enroll', $course_id);
			$title = __('Course Enrolled', 'tutor-pro')." &ndash; ".date_i18n(get_option('date_format')) .' @ '.date_i18n(get_option('time_format') ) ;
			$enroll_data = apply_filters('tutor_enroll_data',
				array(
					'post_type'     => 'tutor_enrolled',
					'post_title'    => $title,
					'post_status'   => 'completed',
					'post_author'   => $user_id,
					'post_parent'   => $course_id,
				)
			);

			// Insert the post into the database
			$is_enrolled = wp_insert_post( $enroll_data );
			if ($is_enrolled) {
				do_action('tutor_after_enroll', $course_id, $is_enrolled);

				// Run this hook for only completed enrollment regardless of payment provider and free/paid mode
				if ($enroll_data['post_status'] == 'completed') {
					do_action('tutor_after_enrolled', $course_id, $user_id, $is_enrolled);
				}

				//Change the enrol status again. to fire complete hook
                tutils()->course_enrol_status_change($is_enrolled, 'completed');
				//Mark Current User as Students with user meta data
				update_user_meta( $user_id, '_is_tutor_student', tutor_time() );

				do_action('tutor_enrollment/after/complete', $is_enrolled);
			}

			$this->success_msgs = get_tnotice(__('Enrolment has been done', 'tutor-pro'), 'Success', 'success');
		}

		add_filter('student_enrolled_to_course_msg', array($this, 'return_message'));
	}


	public function return_message(){
		return $this->success_msgs;
	}

}