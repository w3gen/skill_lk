<?php
/**
 * Tutor Multi Instructor
 */

namespace TUTOR_GB;

class GradeBook{

	public function __construct() {
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
		add_action('tutor_admin_register', 	array($this, 'register_menu'));

		add_action('tutor_action_add_new_gradebook', array($this, 'add_new_gradebook'));
		add_action('tutor_action_update_gradebook', array($this, 'update_gradebook'));
		add_action('tutor_action_delete_gradebook', array($this, 'delete_gradebook'));

		add_action('tutor_quiz/attempt_ended', array($this, 'quiz_attempt_ended'));
		add_action('tutor_assignment/evaluate/after', array($this, 'generate_grade'));
		add_filter('tutor_assignment/single/results/after', array($this, 'filter_assignment_result'), 10, 3);
		add_filter('tutor_quiz/previous_attempts_html', array($this, 'previous_attempts_html'), 10, 3);
		add_action('tutor_course/single/actions_btn_group/after', array($this, 'course_single_actions_btn_group'), 10, 0);

		add_action('tutor_action_gradebook_generate_for_course', array($this, 'gradebook_generate_for_course'), 10, 0);

		add_filter('tutor_course/single/enrolled/nav_items_rewrite', array($this, 'add_course_nav_rewrite'));
		add_filter('tutor_course/single/enrolled/nav_items', array($this, 'add_course_nav_item'));
		add_action('tutor_course/single/enrolled/gradebook', array($this, 'generate_gradebook_html'));

		add_action('tutor_action_gradebook_result_list_bulk_actions', array($this, 'gradebook_result_list_bulk_actions'), 10, 0);

		//Install Sample Grade Data
		add_action('wp_ajax_import_gradebook_sample_data', array($this, 'import_gradebook_sample_data'));
	}

	public function admin_scripts($page){
		if ($page === 'tutor-lms-pro_page_tutor_gradebook') {
			wp_enqueue_script( 'tutor-gradebook', TUTOR_GB()->url . 'assets/js/gradebook.js', array(), TUTOR_GB()->version, true );
		}
	}

	public function register_menu(){
		add_submenu_page('tutor', __('Gradebook', 'tutor-pro'), __('Gradebook', 'tutor-pro'), 'manage_tutor', 'tutor_gradebook', array($this, 'tutor_gradebook') );
	}

	public function tutor_gradebook(){
		include TUTOR_GB()->path.'views/pages/grade_book.php';
	}

	public function add_new_gradebook(){
		global $wpdb;

		//Checking nonce
		tutor_utils()->checking_nonce();

		$required_fields = apply_filters('tutor_gradebook_required_fields', array(
			'grade_name'        => __('Grade name field is required', 'tutor-pro'),
			'percent_from'      => __('Minimum Grade Percentile', 'tutor-pro'),
			'percent_to'        => __('Maximum Grade Percentile', 'tutor-pro'),
		));

		$validation_errors = array();
		foreach ($required_fields as $required_key => $required_value){
			if (empty($_POST[$required_key])){
				$validation_errors[$required_key] = $required_value;
			}
		}

		if (tutils()->count($validation_errors)){
			$errors_msg = '<ul><li>'.implode('</li><li>', $validation_errors).'</li></ul>';
			tutor_flash_set('danger', $errors_msg);
			wp_redirect(tutils()->referer()); exit();
		}

		$percent_from = (int) sanitize_text_field(tutils()->array_get('percent_from', $_POST));
		$data = array(
			'grade_name'            => sanitize_text_field(tutils()->array_get('grade_name', $_POST)),
			'grade_point'           => sanitize_text_field(tutils()->array_get('grade_point', $_POST)),
			'percent_from'          => $percent_from,
			'percent_to'            => sanitize_text_field(tutils()->array_get('percent_to', $_POST)),
			'grade_config'          => maybe_serialize(tutils()->array_get('grade_config', $_POST)),
		);

		$wpdb->insert($wpdb->tutor_gradebooks, $data);
		$gradebook_id = (int) $wpdb->insert_id;

		tutor_flash_set('success', __('Gradebook has been added successfully', 'tutor-pro') );
		wp_redirect(admin_url('admin.php?page=tutor_gradebook&sub_page=gradebooks'));
		exit();
	}

	public function update_gradebook(){
		global $wpdb;

		//Checking nonce
		tutor_utils()->checking_nonce();

		$required_fields = apply_filters('tutor_gradebook_required_fields', array(
			'grade_name'        => __('Grade name field is required', 'tutor-pro'),
			'percent_from'      => __('Minimum Grade Percentile', 'tutor-pro'),
			'percent_to'        => __('Maximum Grade Percentile', 'tutor-pro'),
		));

		$validation_errors = array();
		foreach ($required_fields as $required_key => $required_value){
			if (empty($_POST[$required_key])){
				$validation_errors[$required_key] = $required_value;
			}
		}

		if (tutils()->count($validation_errors)){
			$errors_msg = '<ul><li>'.implode('</li><li>', $validation_errors).'</li></ul>';
			tutor_flash_set('danger', $errors_msg);
			wp_redirect(tutils()->referer()); exit();
		}

		$gradebook_id = (int) sanitize_text_field(tutils()->array_get('gradebook_id', $_GET));
		$percent_from = (int) sanitize_text_field(tutils()->array_get('percent_from', $_POST));
		$data = array(
			'grade_name'            => sanitize_text_field(tutils()->array_get('grade_name', $_POST)),
			'grade_point'           => sanitize_text_field(tutils()->array_get('grade_point', $_POST)),
			'percent_from'          => $percent_from,
			'percent_to'            => sanitize_text_field(tutils()->array_get('percent_to', $_POST)),
			'grade_config'          => maybe_serialize(tutils()->array_get('grade_config', $_POST)),
		);

		$wpdb->update($wpdb->tutor_gradebooks, $data, array('gradebook_id' => $gradebook_id));

		tutor_flash_set('success', __('Gradebook has been updated successfully', 'tutor-pro') );
		wp_redirect(admin_url('admin.php?page=tutor_gradebook&sub_page=gradebooks'));
		exit();
	}

	public function delete_gradebook(){
		global $wpdb;
		$gradebook_id = (int) sanitize_text_field(tutils()->array_get('gradebook_id', $_GET));
		$wpdb->delete($wpdb->tutor_gradebooks, array('gradebook_id' => $gradebook_id));

		tutor_flash_set('success', __('The grade has been deleted successfully.', 'tutor-pro') );
		wp_redirect(admin_url('admin.php?page=tutor_gradebook&sub_page=gradebooks'));
		exit();
	}

	/**
	 * @param $attempt_id
	 *
	 * Generate Quiz Result
	 * @since v.1.4.2
	 */

	public function quiz_attempt_ended($attempt_id){
		global $wpdb;

		$attempt = tutils()->get_attempt($attempt_id);
		$earned_percentage = $attempt->earned_marks > 0 ? ( number_format(($attempt->earned_marks * 100) / $attempt->total_marks)) : 0;

		$gradebook = $wpdb->get_row("SELECT * FROM {$wpdb->tutor_gradebooks} 
		WHERE percent_from <= {$earned_percentage} 
		AND percent_to >= {$earned_percentage} ORDER BY gradebook_id ASC LIMIT 1  ");

		if ( ! $gradebook){
			return;
		}

		$gradebook_data = array(
			'user_id'   => $attempt->user_id,
			'course_id'   => $attempt->course_id,
			'quiz_id'   => $attempt->quiz_id,
			'gradebook_id'   => $gradebook->gradebook_id,
			'result_for'   => 'quiz',
			'grade_name'   => $gradebook->grade_name,
			'grade_point'   => $gradebook->grade_point,
			'earned_grade_point'   => $gradebook->grade_point,
			'generate_date'   => date("Y-m-d H:i:s"),
			'update_date'   => date("Y-m-d H:i:s"),
		);

		$gradebook_result_id = 0;
		$gradebook_result = $wpdb->get_row("SELECT * FROM {$wpdb->tutor_gradebooks_results} 
			WHERE result_for = 'quiz' 
			AND user_id = {$attempt->user_id} 
			AND course_id = {$attempt->course_id} 
			AND quiz_id = {$attempt->quiz_id} ");

		if ($gradebook_result){
			$gradebook_result_id = $gradebook_result->gradebook_result_id;
			//Update Gradebook Result
			unset($gradebook_data['generate_date']);
			$wpdb->update($wpdb->tutor_gradebooks_results, $gradebook_data, array('gradebook_result_id' => $gradebook_result->gradebook_result_id ) );
		}else{
			$wpdb->insert($wpdb->tutor_gradebooks_results, $gradebook_data);
			$gradebook_result_id = (int) $wpdb->insert_id;
		}

		do_action('tutor_gradebook/quiz_result/after', $gradebook_result_id);
	}

	public function generate_grade($submitted_id){
		global $wpdb;

		do_action('tutor_gradebook/assignment_generate/before', $submitted_id);
		do_action('tutor_gradebook/generate/before');

		$submitted_info = tutor_utils()->get_assignment_submit_info($submitted_id);
		if ( $submitted_info) {
			$max_mark = tutor_utils()->get_assignment_option( $submitted_info->comment_post_ID, 'total_mark' );
			$given_mark = get_comment_meta( $submitted_id, 'assignment_mark', true );

			$earned_percentage = ($given_mark > 0 && $max_mark > 0) ? ( number_format(($given_mark * 100) / $max_mark)) : 0;

			$gradebook = $wpdb->get_row("SELECT * FROM {$wpdb->tutor_gradebooks} 
			WHERE percent_from <= {$earned_percentage} 
			AND percent_to >= {$earned_percentage} ORDER BY gradebook_id ASC LIMIT 1  ");

			$gradebook_data = apply_filters('tutor_gradebook_data', array(
				'user_id'               => $submitted_info->user_id,
				'course_id'             => $submitted_info->comment_parent,
				'assignment_id'         => $submitted_info->comment_post_ID,
				'gradebook_id'          => $gradebook->gradebook_id,
				'result_for'            => 'assignment',
				'grade_name'            => $gradebook->grade_name,
				'grade_point'           => $gradebook->grade_point,
				'earned_grade_point'    => $gradebook->grade_point,
				'generate_date'         => date("Y-m-d H:i:s"),
				'update_date'           => date("Y-m-d H:i:s"),
			));

			$gradebook_result_id = 0;
			$gradebook_result = $wpdb->get_row("SELECT * FROM {$wpdb->tutor_gradebooks_results} 
			WHERE result_for = 'assignment' 
			AND user_id = {$submitted_info->user_id} 
			AND course_id = {$submitted_info->comment_parent} 
			AND assignment_id = {$submitted_info->comment_post_ID} ");

			if ($gradebook_result){
				$gradebook_result_id = (int)  $gradebook_result->gradebook_result_id;
				//Update Gradebook Result
				unset($gradebook_data['generate_date']);
				$wpdb->update($wpdb->tutor_gradebooks_results, $gradebook_data, array('gradebook_result_id' => $gradebook_result->gradebook_result_id ) );
			}else{
				$wpdb->insert($wpdb->tutor_gradebooks_results, $gradebook_data);
				$gradebook_result_id = (int) $wpdb->insert_id;
			}

			do_action('tutor_gradebook/assignment_generate/after', $gradebook_result_id);
			do_action('tutor_gradebook/generate/after', $gradebook_result_id);
		}

	}


	public function filter_assignment_result($content, $submit_id, $assignment_id){

		$max_mark = tutor_utils()->get_assignment_option($assignment_id, 'total_mark');
		$pass_mark = tutor_utils()->get_assignment_option($assignment_id, 'pass_mark');
		$given_mark = get_comment_meta($submit_id, 'assignment_mark', true);
		$grade = get_generated_gradebook('assignment', $assignment_id);

		ob_start();
		?>

        <div class="assignment-result-wrap">
            <h4><?php echo sprintf(__('You received %s points out of %s', 'tutor-pro'), "<span class='received-marks'>{$given_mark}</span>", "<span class='out-of-marks'>{$max_mark}</span>") ?></h4>
            <h4 class="submitted-assignment-grade">
				<?php _e('Your grade is ', 'tutor-pro');

				echo tutor_generate_grade_html($grade);
				echo $given_mark >= $pass_mark ? "<span class='submitted-assignment-grade-pass'> (".__('Passed', 'tutor-pro').")</span>" : "<span class='submitted-assignment-grade-failed'> (".__('Failed', 'tutor-pro').")</span>";
				?>
            </h4>
        </div>

		<?php
		return ob_get_clean();
	}

	public function previous_attempts_html($previous_attempts_html, $previous_attempts, $quiz_id){
		tutor_load_template('single.quiz.previous-attempts', compact('previous_attempts_html', 'previous_attempts', 'quiz_id'), true);
	}

	public function course_single_actions_btn_group(){
		get_gradebook_generate_form();
	}

	public function gradebook_generate_for_course(){
		$course_ID = (int) sanitize_text_field(tutils()->array_get('course_ID', $_POST));
		tutils()->checking_nonce();

		$this->gradebook_generate($course_ID);

		$url = trailingslashit(get_permalink($course_ID)).'gradebook';
		wp_redirect($url);
		exit();
	}


	/**
	 * @param $course_ID
	 * @param int $user_id
	 *
	 * Generate / update gradebook result by course id and user id
	 *
	 */

	public function gradebook_generate($course_ID, $user_id = 0){
		global $wpdb;

		$user_id = tutils()->get_user_id($user_id);

		$course_contents = tutils()->get_course_contents_by_id($course_ID);
		$previous_gen_item = get_generated_gradebook('all', $course_ID);


		if (tutils()->count($course_contents)) {
			$require_gradding = array();
			foreach ( $course_contents as $content ) {
				if ( $content->post_type === 'tutor_quiz' || $content->post_type === 'tutor_assignments' ) {
					$require_gradding[] = $content;
				}
			}
		}

		/**
		 * Delete if not exists
		 */
		if (tutils()->count($previous_gen_item)){
			$quiz_assignment_ids = wp_list_pluck($require_gradding, 'ID');

			if (tutils()->count($quiz_assignment_ids)){

			    foreach ($previous_gen_item as $previous_item){
			        if ( $previous_item->quiz_id && ! in_array($previous_item->quiz_id, $quiz_assignment_ids)){
				        $wpdb->delete($wpdb->tutor_gradebooks_results, array('quiz_id' => $previous_item->quiz_id));
			        }
				    if ( $previous_item->assignment_id && ! in_array($previous_item->assignment_id, $quiz_assignment_ids)){
					    $wpdb->delete($wpdb->tutor_gradebooks_results, array('assignment_id' => $previous_item->assignment_id));
				    }
                }

            }else{
				$wpdb->delete($wpdb->tutor_gradebooks_results, array('course_id' => $course_ID));
            }
        }


		if ( ! tutils()->count($require_gradding)){
			return;
		}

		/**
		 * re-grade again
		 */
		if (tutils()->count($require_gradding)){

			$require_graddings = array_values($require_gradding);

			foreach ($require_graddings as $course_item) {
				$earned_percentage = 0;

				if ($course_item->post_type === 'tutor_quiz') {
					//Get Attempt by grading method
					$attempt = tutils()->get_quiz_attempt($course_item->ID, $user_id);
					if ($attempt){
						$earned_percentage = $attempt->earned_marks > 0 ? ( number_format(($attempt->earned_marks * 100) / $attempt->total_marks)): 0;
					}

				}elseif ($course_item->post_type === 'tutor_assignments'){
					$submitted_info = tutils()->is_assignment_submitted($course_item->ID, $user_id);
					if ($submitted_info){
						$submitted_id = $submitted_info->comment_ID;
						$max_mark = tutor_utils()->get_assignment_option( $submitted_info->comment_post_ID, 'total_mark' );
						$given_mark = get_comment_meta( $submitted_id, 'assignment_mark', true );
						$earned_percentage = $given_mark > 0 ? ( number_format(($given_mark * 100) / $max_mark)) : 0;
					}
				}

				if ($earned_percentage > 100){
					$earned_percentage = 100;
				}

				$gradebook = $wpdb->get_row("SELECT * FROM {$wpdb->tutor_gradebooks} WHERE percent_from <= {$earned_percentage} AND percent_to >= {$earned_percentage} ORDER BY gradebook_id ASC LIMIT 1  ");

				if ( ! $gradebook){
					//continue;
				}

				$gradebook_data = array(
					'user_id'            => $user_id,
					'course_id'          => $course_ID,
					'gradebook_id'       => $gradebook->gradebook_id,
					'grade_name'         => $gradebook->grade_name,
					'grade_point'        => $gradebook->grade_point,
					'earned_grade_point' => $gradebook->grade_point,
					'earned_percent'     => $earned_percentage,
					'generate_date'      => date( "Y-m-d H:i:s" ),
					'update_date'        => date( "Y-m-d H:i:s" ),
				);

				$gradebook_result = false;

				if ($course_item->post_type === 'tutor_quiz'){
					$gradebook_data['quiz_id'] = $course_item->ID;
					$gradebook_data['result_for'] = 'quiz';

					$gradebook_result    = $wpdb->get_row( "SELECT * FROM {$wpdb->tutor_gradebooks_results} 
							WHERE result_for = 'quiz' 
							AND user_id = {$user_id} 
							AND course_id = {$course_ID} 
							AND quiz_id = {$course_item->ID} " );

				}elseif ($course_item->post_type === 'tutor_assignments'){
					$gradebook_data['assignment_id'] = $course_item->ID;
					$gradebook_data['result_for'] = 'assignment';

					$gradebook_result    = $wpdb->get_row( "SELECT * FROM {$wpdb->tutor_gradebooks_results} 
							WHERE result_for = 'assignment' 
							AND user_id = {$user_id} 
							AND course_id = {$course_ID} 
							AND assignment_id = {$course_item->ID} " );
				}

				if ( $gradebook_result ) {
					//Update Gradebook Result
					unset( $gradebook_data['generate_date'] );
					$wpdb->update( $wpdb->tutor_gradebooks_results, $gradebook_data, array( 'gradebook_result_id' => $gradebook_result->gradebook_result_id ) );
				} else {
					$wpdb->insert( $wpdb->tutor_gradebooks_results, $gradebook_data );
				}
			}

			$results = $wpdb->get_row("SELECT AVG(earned_percent) as earned_percent,
                AVG(grade_point) as earned_grade_point
                FROM {$wpdb->tutor_gradebooks_results} 
                WHERE user_id = {$user_id} 
                AND course_id = {$course_ID} 
                AND result_for !='final' ");


			if ($results){
				$gradebook = get_gradebook_by_percent($results->earned_percent);

				$gradebook_data = array(
					'user_id'            => $user_id,
					'course_id'          => $course_ID,
					'gradebook_id'       => $gradebook->gradebook_id,
					'result_for'         => 'final',
					'grade_name'         => $gradebook->grade_name,
					'grade_point'        => $gradebook->grade_point,
					'earned_grade_point' => number_format($results->earned_grade_point, 2),
					'earned_percent'     => $results->earned_percent,
					'generate_date'      => date( "Y-m-d H:i:s" ),
					'update_date'        => date( "Y-m-d H:i:s" ),
				);

				$generated_final = $wpdb->get_row("SELECT * FROM {$wpdb->tutor_gradebooks_results} 
                    WHERE user_id = {$user_id} AND course_id = {$course_ID} AND result_for ='final' ");

				if ($generated_final){
					unset($gradebook_data['generate_date'], $gradebook_data['result_for'] );

					$wpdb->update($wpdb->tutor_gradebooks_results, $gradebook_data, array('gradebook_result_id' => $generated_final->gradebook_result_id));
				}else{
					$wpdb->insert( $wpdb->tutor_gradebooks_results, $gradebook_data );
				}
			}
		}



	}

	public function generate_gradebook_html($course_id){
		tutor_load_template('single.course.enrolled.gradebook', array(), true);
	}

	/**
	 * @param int $quiz_id
	 * @param int $user_id
	 *
	 * Get Grade percent from quiz base on settings...
	 */
	public function get_quiz_earned_number_percent($quiz_id = 0, $user_id = 0){
		$quiz_grade_method = get_tutor_option('quiz_grade_method');
		echo $quiz_grade_method;
	}

	public function add_course_nav_item($items){
		if (is_single() && get_the_ID()){
			$gading_content = get_grading_contents_by_course_id();
			if (tutils()->count($gading_content)){
				$items['gradebook'] = __('Gradebook', 'tutor-pro');
			}
		}

		return $items;
	}

	public function add_course_nav_rewrite($items){
		$items['gradebook'] = __('Gradebook', 'tutor-pro');
		return $items;
	}

	public function gradebook_result_list_bulk_actions(){
		tutils()->checking_nonce('get');

		$action = sanitize_text_field(tutils()->array_get('action', $_GET));
		if ($action === '-1'){
			return;
		}

		global $wpdb;
		$gradebooks_result_ids = tutils()->array_get('gradebooks_result_ids', $_GET);

		if ($action === 'regenerate_gradebook'){
			if (tutils()->count($gradebooks_result_ids)){
				foreach ($gradebooks_result_ids as $result_id){
					$result = get_generated_gradebook('byID', $result_id);
					$this->gradebook_generate($result->course_id, $result->user_id);
				}

				tutor_flash_set('success', __('Gradebook has been re-generated', 'tutor-pro') );
			}
		}

		if ($action === 'trash'){
			if (tutils()->count($gradebooks_result_ids)){
				foreach ($gradebooks_result_ids as $result_id){
					$result = get_generated_gradebook('byID', $result_id);
					$wpdb->delete($wpdb->tutor_gradebooks_results, array('user_id' => $result->user_id, 'course_id' => $result->course_id ));
				}
				tutor_flash_set('warning', __('Gradebook has been deleted', 'tutor-pro') );
			}
		}

		wp_redirect(tutils()->referer());
		exit();
	}

	/**
	 * Import Sample Grade Data
	 */
	public function import_gradebook_sample_data(){
		tutils()->checking_nonce();

		global $wpdb;

		$data = "INSERT INTO {$wpdb->tutor_gradebooks} (grade_name, grade_point, grade_point_to, percent_from, percent_to, grade_config) VALUES
                ('A+', '4.0', NULL, 90, 100, 'a:1:{s:11:\"grade_color\";s:7:\"#27ae60\";}'),
                ('A', '3.50', NULL, 80, 89, 'a:1:{s:11:\"grade_color\";s:7:\"#1bbc9b\";}'),
                ('A-', '3.0', NULL, 70, 79, 'a:1:{s:11:\"grade_color\";s:7:\"#43bca4\";}'),
                ('B+', '2.50', NULL, 60, 69, 'a:1:{s:11:\"grade_color\";s:7:\"#1f3a93\";}'),
                ('B', '2.0', NULL, 50, 59, 'a:1:{s:11:\"grade_color\";s:7:\"#2574a9\";}'),
                ('B-', '1.5', NULL, 40, 49, 'a:1:{s:11:\"grade_color\";s:7:\"#19b5fe\";}'),
                ('C', '1.0', NULL, 30, 39, 'a:1:{s:11:\"grade_color\";s:7:\"#9a13b3\";}'),
                ('F', '0.0', NULL, 0, 29, 'a:1:{s:11:\"grade_color\";s:7:\"#d71830\";}');";

		$wpdb->query($data);
		wp_send_json_success();
	}

}