<?php


namespace TUTOR_PRO;

if (!defined('ABSPATH'))
	exit;


class Quiz {

	public function __construct() {
		add_filter('tutor/options/extend/attr', array($this, 'extend_quiz_option'));

		if (get_tutor_option('tutor_quiz_student_attempt_view')) {
			add_action('tutor_quiz/previous_attempts/table/thead/col', array($this, 'add_attempts_thead_col'));
			add_action('tutor_quiz/previous_attempts/table/tbody/col', array($this, 'add_attempts_tbody_col'));
			add_filter('tutor_single_quiz/top', array($this, 'view_quiz_attempt'));
			add_filter('tutor_single_quiz/body', array($this, 'remove_quiz_body_if_attempt_view'));
		}

		if(get_tutor_option('tutor_quiz_student_attempt_view_in_profile')) {
			add_action('tutor_quiz/my_attempts/table/thead/col', array($this, 'add_attempts_thead_col'));
			add_action('tutor_quiz/my_attempts/table/tbody/col', array($this, 'add_attempts_tbody_col'));
		}
	}

	public function add_attempts_thead_col() {
		echo '<th>#</th>';
	}
	public function add_attempts_tbody_col($attempt) {
		echo '<td>';
		echo '<a href="' . add_query_arg(array('view_quiz_attempt_id' => $attempt->attempt_id)) . '">' . __('View Attempt', 'tutor-pro') . '</a>';
		echo '</td>';
	}

	public function view_quiz_attempt($html) {
		$attempt_id = (int) sanitize_text_field(tutils()->array_get('view_quiz_attempt_id', $_GET));
		if ($attempt_id) {
			$user_id = get_current_user_id();
			$attempt = tutils()->get_attempt($attempt_id);

			if (tutils()->array_get('user_id', $attempt) != $user_id) {
				return $html;
			}
			return tutor_get_template_html('single.quiz.view_attempt', compact('attempt_id'), true);
		}

		return $html;
	}

	public function remove_quiz_body_if_attempt_view($html) {
		$attempt_id = (int) sanitize_text_field(tutils()->array_get('view_quiz_attempt_id', $_GET));

		if ($attempt_id) {
			$user_id = get_current_user_id();
			$attempt = tutils()->get_attempt($attempt_id);

			if (tutils()->array_get('user_id', $attempt) == $user_id) {
				return '';
			}
		}

		return $html;
	}

	public function extend_quiz_option($attr) {
		$attr['quiz'] = array(
			'label' => __('Quiz', 'tutor-pro'),
			'sections'    => array(
				'quiz_attempt_tutor_pro' => array(
					'label' => __('Tutor LMS Pro Settings', 'tutor-pro'),
					'desc' => __('The values you set here define the default values that are used in the settings form when you create a new quiz.', 'tutor-pro'),
					'fields' => array(
						'tutor_quiz_student_attempt_view' => array(
							'type'      => 'checkbox',
							'label'     => __('Detail Attempt View in Course', 'tutor-pro'),
							'label_title' => __('Enable', 'tutor-pro'),
							'default' => '0',
							'desc'      => __('Enabling this option will let the students view quiz attempt details including the answer to each question in course.', 'tutor-pro'),
						),
						'tutor_quiz_student_attempt_view_in_profile' => array(
							'type'      => 'checkbox',
							'label'     => __('Detail Attempt View in Profile', 'tutor-pro'),
							'label_title' => __('Enable', 'tutor-pro'),
							'default' => '0',
							'desc'      => __('Enabling this option will let the students view quiz attempt details including the answer to each question in profile.', 'tutor-pro'),
						),
					)
				)
			),
		);

		return $attr;
	}
}
