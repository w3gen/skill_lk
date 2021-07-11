<?php
namespace TUTOR_EMAIL;

if ( ! defined( 'ABSPATH' ) )
	exit;

class init{
	public $version = TUTOR_EMAIL_VERSION;
	public $path;
	public $url;
	public $basename;

	//Module
	private $email_notification;

	function __construct() {
		if ( ! function_exists('tutor')){
			return;
		}
		$addonConfig = tutor_utils()->get_addon_config(TUTOR_EMAIL()->basename);
		$isEnable = (bool) tutor_utils()->avalue_dot('is_enable', $addonConfig);
		if ( ! $isEnable){
			return;
		}

		$this->path = plugin_dir_path(TUTOR_EMAIL_FILE);
		$this->url = plugin_dir_url(TUTOR_EMAIL_FILE);
		$this->basename = plugin_basename(TUTOR_EMAIL_FILE);

		$this->load_TUTOR_EMAIL();
	}

	public function load_TUTOR_EMAIL(){
		/**
		 * Loading Autoloader
		 */

		spl_autoload_register(array($this, 'loader'));
		$this->email_notification = new EmailNotification();

		add_filter('tutor/options/extend/attr', array($this, 'add_options'), 10); // Priority index is important. 'Content Drip' add-on uses 11.
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

			$className = str_replace('TUTOR_EMAIL'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
			$file_name = $this->path.$className.'.php';

			if (file_exists($file_name) && is_readable( $file_name ) ) {
				require_once $file_name;
			}
		}
	}


	//Run the TUTOR right now
	public function run(){
		register_activation_hook( TUTOR_EMAIL_FILE, array( $this, 'tutor_activate' ) );
	}

	/**
	 * Do some task during plugin activation
	 */
	public function tutor_activate(){
		$version = get_option('TUTOR_EMAIL_version');
		//Save Option
		if ( ! $version){
			update_option('TUTOR_EMAIL_version', TUTOR_EMAIL_VERSION);
		}
	}

	public function add_options($attr){
		$attr['email_notification'] = array(
			'label'     => __('E-Mail Notification', 'tutor-pro'),
			'sections'    => array(
				'general' => array(
					'label' => __('Enable/Disable', 'tutor-pro'),
					'desc' => __('Enable Disable Option to on/off notification on various event', 'tutor-pro'),
					'fields' => array(
						'email_to_students' => array(
							'type'      => 'checkbox',
							'label'     => __('E-Mail to Students', 'tutor-pro'),
							'options'   => array(
								'course_enrolled' 				=> __('Course Enrolled', 'tutor-pro'),
								'quiz_completed' 				=> __('Quiz Completed', 'tutor-pro'),
								'completed_course' 				=> __('Completed a Course', 'tutor-pro'),
								'remove_from_course' 			=> __('Remove from Course', 'tutor-pro'),
								'assignment_graded' 			=> __('Assignment Graded', 'tutor-pro'),
								'new_announcement_posted' 		=> __('New Announcement Posted', 'tutor-pro'),
								'after_question_answered' 		=> __('Q&A Message Answered', 'tutor-pro'),
								'feedback_submitted_for_quiz' 	=> __('Feedback submitted for Quiz Attempt', 'tutor-pro'),
								'rate_course_and_instructor' 	=> __('Rate Course and Instructor After Course Completed', 'tutor-pro'),
								'enrollment_expired' 			=> __('Course enrolment expired', 'tutor-pro'),
							),
							'desc'      => __('Select when to send notification to the students',	'tutor-pro'),
						),
						'email_to_teachers' => array(
							'type'      => 'checkbox',
							'label'     => __('E-Mail to Teachers', 'tutor-pro'),
							'options'   => array(
								'a_student_enrolled_in_course' 	=> __('A Student Enrolled in Course', 'tutor-pro'),
								'a_student_completed_course'    => __('A Student Completed Course', 'tutor-pro'),
								'a_student_completed_lesson'    => __('A Student Completed Lesson', 'tutor-pro'),
								'a_student_placed_question'     => __('A Student asked a Question in Q&amp;A', 'tutor-pro'),
								'student_submitted_quiz'        => __('Student Submitted Quiz', 'tutor-pro'),
								'student_submitted_assignment'  => __('Student Submitted Assignment', 'tutor-pro'),
								'withdrawal_request_approved'  	=> __('Withdrawal Request Approved', 'tutor-pro'),
								'withdrawal_request_rejected'  	=> __('Withdrawal Request Rejected', 'tutor-pro'),
								'withdrawal_request_received'  	=> __('Withdrawal Request Received', 'tutor-pro'),
								'instructor_application_accepted'=> __('Instructor Application Accepted ', 'tutor-pro'),
								'instructor_application_rejected'=> __('Instructor Application Rejected', 'tutor-pro'),
								'instructor_application_received'=> __('Instructor Application Received', 'tutor-pro'),
							),
							'desc'      => __('Select when to send notification to the teachers',	'tutor-pro'),
						),
						'email_to_admin' => array(
							'type'      => 'checkbox',
							'label'     => __('E-Mail to Admin', 'tutor-pro'),
							'options'   => array(
								'new_instructor_signup' 	=> __('New Instructor Signup', 'tutor-pro'),
								'new_student_signup' 		=> __('New Student Signup', 'tutor-pro'),
								'new_course_submitted' 		=> __('New Course Submitted for Review', 'tutor-pro'),
								'new_course_published' 		=> __('New Course Published', 'tutor-pro'),
								'course_updated' 			=> __('Course Edited/Updated', 'tutor-pro'),
								'new_withdrawal_request' 	=> __('New Withdrawal Request', 'tutor-pro'),
							),
							'desc'      => __('Select when to send notification to the admin',	'tutor-pro'),
						),
						'tutor_email_disable_wpcron' => array(
							'type'          => 'checkbox',
							'label'         => __('WP Cron for bulk mailing', 'tutor-pro'),
							'label_title'   => __('Disable', 'tutor-pro'),
							'default' 		=> '0',
							'desc'          => __('Enable this option to let Tutor LMS use WordPress native scheduler for email sending activities', 'tutor-pro') ,
						),
						'tutor_email_cron_frequency' => array(
							'type'      	=> 'select',
							'label'     	=> __('WP email cron frequency', 'tutor-pro'),
							'options'		=> array(
								'3600' => __('Lowest', 'tutor-pro'),
								'1800' => __('Low', 'tutor-pro'),
								'900' => __('Normal', 'tutor-pro'),
								'300' => __('High', 'tutor-pro')
							),
							'default'   	=> '300',
							'desc'  	=> __('Select the frequency mode in which the Cron Setup will run', 'tutor-pro'),
						),
						'tutor_bulk_email_limit' => array(
							'type'      => 'number',
							'label'     => __('Email per cron execution', 'tutor-pro'),
							'default'   => '10',
							'desc'  	=> __('Number of emails you\'d like to send per cron execution', 'tutor-pro'),
						),
					),
				),
			),
		);


		return $attr;
	}

}