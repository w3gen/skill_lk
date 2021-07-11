<?php
/**
 * Tutor Assignments
 */

namespace TUTOR_ASSIGNMENTS;

class Assignments {

	public function __construct() {
		add_action('admin_menu', array(
			$this,
			'register_menu'
		));

		add_action('tutor_course_builder_after_btn_group', array(
			$this,
			'add_assignments_btn'
		));

		add_action('wp_ajax_tutor_load_assignments_builder_modal', array(
			$this,
			'tutor_load_assignments_builder_modal'
		));
		add_action('wp_ajax_tutor_modal_create_or_update_assignment', array(
			$this,
			'tutor_modal_create_or_update_assignment'
		));

		add_filter('tutor_course_contents_post_types', array(
			$this,
			'tutor_course_contents_post_types'
		));

		add_filter('post_type_link', array(
			$this,
			'change_assignment_single_url'
		) , 1, 2);
		add_action('wp_ajax_tutor_start_assignment', array(
			$this,
			'tutor_start_assignment'
		));

		//Handle assignment submit form
		add_action('tutor_action_tutor_assignment_submit', array(
			$this,
			'tutor_assignment_submit'
		));

		add_action('tutor_action_tutor_evaluate_assignment_submission', array(
			$this,
			'tutor_evaluate_assignment_submission'
		));

		add_filter('tutor_dashboard/instructor_nav_items', array(
			$this,
			'frontend_dashboard_nav_items'
		));

		/**
		 * Lesson List in frontend end
		 */
		add_action('tutor/lesson_list/right_icon_area', array(
			$this,
			'show_assignment_submitted_icon'
		));

		add_action('admin_enqueue_scripts', array(
			$this,
			'load_admin_scripts'
		));

		add_action('wp_ajax_delete_tutor_course_assignment_submission', array(
			$this,
			'delete_tutor_course_assignment_submission'
		));
	}

	public function load_admin_scripts() {
		if (isset($_GET['page']) && $_GET['page'] == 'tutor-assignments') {
			wp_enqueue_script('assignment-list-js', tutor_pro()->url . 'addons/tutor-assignments/assets/js/assignment.js');

			/**
			 * @since 1.8.0
			 */
			wp_enqueue_style(
				'assignments-css',
				TUTOR_ASSIGNMENTS()->url."assets/css/assignments.css",
				'',
				time()
			);				
		}
	
	}

	public function delete_tutor_course_assignment_submission() {

		$assignment_id = (int)tutils()->array_get('assignment_id', $_GET);

		if ($assignment_id) {
			global $wpdb;

			if (!tutils()->can_user_manage('assignment', $assignment_id)) {
				wp_send_json_error(array(
					'message'            => 'Access Denied'
				));
			}

			$assignment = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->comments} WHERE comment_ID = %d", $assignment_id));
			if ($assignment) {
				$wpdb->delete($wpdb->comments, array(
					'comment_ID' => $assignment_id
				));
				$wpdb->delete($wpdb->commentmeta, array(
					'comment_id' => $assignment_id
				));
			}

			wp_send_json_success();
		}
	}

	public function register_menu() {
		add_submenu_page('tutor', __('Submitted Assignments', 'tutor-pro') , __('Assignments', 'tutor-pro') , 'manage_tutor_instructor', 'tutor-assignments', array(
			$this,
			'tutor_assignments_page'
		));
	}

	public function frontend_dashboard_nav_items($nav_items) {
		$nav_items['assignments'] = array(
			'title' => __('Assignments', 'tutor-pro') ,
			'auth_cap' => tutor()->instructor_role
		);
		return $nav_items;
	}

	public function tutor_assignments_page() {
		if (tutor_utils()->array_get('view_assignment', $_GET)) {
			$assignment_submitted_id = (int)sanitize_text_field(tutor_utils()->array_get('view_assignment', $_GET));
			include TUTOR_ASSIGNMENTS()->path . '/views/pages/submitted_assignment.php';
		} else {
			include TUTOR_ASSIGNMENTS()->path . '/views/pages/assignments.php';
		}
	}

	public function add_assignments_btn($topic_id) {
?>
		<a href="javascript:;" class="tutor-create-assignments-btn" data-topic-id="<?php echo $topic_id; ?>">
            <i class="tutor-icon-plus-square-button"></i>
            <?php _e('Assignments', 'tutor-pro'); ?>
		</a>
		<?php
	}

	public function tutor_load_assignments_builder_modal() {
		tutils()->checking_nonce();

		$assignment_id = (int)tutor_utils()->avalue_dot('assignment_id', $_POST);
		$topic_id      = (int)sanitize_text_field($_POST['topic_id']);

		/**
		 * If Assignment Not Exists, provide dummy
		 */
		$post_arr = array(
			'ID'           => 0,
			'post_type'    => 'tutor_assignments',
			'post_title'   => __('Assignments', 'tutor-pro') ,
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id() ,
			'post_parent'  => $topic_id,
		);

		$post          = $assignment_id ? get_post($assignment_id) : (object)$post_arr;

		ob_start();
		include TUTOR_ASSIGNMENTS()->path . 'views/modal/assignments.php';
		$output = ob_get_clean();

		wp_send_json_success(array(
			'output' => $output
		));

	}

	/**
	 * Update assignment
	 */
	public function tutor_modal_create_or_update_assignment() {
		tutils()->checking_nonce();

		global $wpdb;

		$assignment_id     = (int)sanitize_text_field(tutor_utils()->avalue_dot('assignment_id', $_POST));
		$topic_id          = (int)sanitize_text_field(tutor_utils()->avalue_dot('current_topic_id', $_POST));
		$title             = sanitize_text_field($_POST['assignment_title']);
		$lesson_content    = wp_kses_post($_POST['assignment_content']);
		$assignment_option = tutor_utils()->avalue_dot('assignment_option', $_POST);
		$attachments       = tutor_utils()->avalue_dot('tutor_assignment_attachments', $_POST);

		$assignment_data   = array(
			'post_type'    => 'tutor_assignments',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id() ,
			'post_parent'  => $topic_id,
			'post_title'   => $title,
			'post_name'    => sanitize_title($title) ,
			'post_content' => $lesson_content,
			'menu_order'   => tutor_utils()->get_next_course_content_order_id($topic_id)
		);

		if ($assignment_id == 0) {

			$assignment_id     = wp_insert_post($assignment_data);

			if ($assignment_id) {
				$course_id         = $wpdb->get_var($wpdb->prepare("SELECT post_parent FROM {$wpdb->posts} WHERE ID=%d", $topic_id));
				update_post_meta($assignment_id, '_tutor_course_id_for_assignments', $course_id);
			} else {
				wp_send_json_error(array(
					'message' => __('Couldn\'t create assignment')
				));
			}
		} else {
			$assignment_data['ID'] = $assignment_id;

			wp_update_post($assignment_data);
			update_post_meta($assignment_id, 'assignment_option', $assignment_option);

			if (tutor_utils()->count($attachments)) {
				update_post_meta($assignment_id, '_tutor_assignment_attachments', $attachments);
			} else {
				delete_post_meta($assignment_id, '_tutor_assignment_attachments');
			}

			do_action('tutor_assignment_updated', $assignment_id);
		}

		$course_id = tutor_utils()->get_course_id_by_assignment($assignment_id);
		ob_start();
		include tutor()->path . 'views/metabox/course-contents.php';
		$course_contents = ob_get_clean();

		wp_send_json_success(array(
			'course_contents' => $course_contents
		));
	}

	public function tutor_course_contents_post_types($post_types) {
		$post_types[] = 'tutor_assignments';

		return $post_types;
	}

	/**
	 * @param $post_link
	 * @param int $id
	 *
	 * @return string
	 *
	 * @since  v.1.3.3
	 *
	 * Change Assignment single URL
	 */
	public function change_assignment_single_url($post_link, $id   = 0) {
		$post = get_post($id);

		global $wpdb;

		$course_base_slug = 'sample-course';
		$course_post_type = tutor()->course_post_type;

		if (is_object($post) && $post->post_type == 'tutor_assignments') {
			$course_id        = get_post_meta($post->ID, '_tutor_course_id_for_assignments', true);

			if ($course_id) {
				$course           = $wpdb->get_row("SELECT {$wpdb->posts}.post_name from {$wpdb->posts} where ID = {$course_id} ");
				if ($course) {
					$course_base_slug = $course->post_name;
				}
				return home_url("/{$course_post_type}/{$course_base_slug}/assignments/" . $post->post_name . '/');
			} else {
				return home_url("/{$course_post_type}/sample-course/assignments/" . $post->post_name . '/');
			}
		}

		return $post_link;
	}

	public function tutor_start_assignment() {
		tutor_utils()->checking_nonce();
		global $wpdb;

		$assignment_id     = (int)sanitize_text_field(tutor_utils()->array_get('assignment_id', $_POST));
		$user_id           = get_current_user_id();
		$user              = get_userdata($user_id);
		$date              = date("Y-m-d H:i:s");

		$is_running_submit = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(comment_ID) FROM {$wpdb->comments} 
			WHERE comment_type = 'tutor_assignment' 
			AND user_id = %d 
			AND comment_post_ID = %d ", $user_id, $assignment_id));

		if ($is_running_submit) {
			wp_send_json_error(__('An assignment is submitting currently, please submit previous one first', 'tutor-pro'));
		}

		$course_id = get_post_meta($assignment_id, '_tutor_course_id_for_assignments', true);

		do_action('tutor_before_assignment_submit_start');
		$data = apply_filters('tutor_assignment_start_submitting_data', array(
			'comment_post_ID'  => $assignment_id,
			'comment_author'   => $user->user_login,
			'comment_date'     => $date, //Submit Finished
			'comment_date_gmt' => $date, //Submit Started
			'comment_approved' => 'submitting', //submitting, submitted
			'comment_agent'    => 'TutorLMSPlugin',
			'comment_type'     => 'tutor_assignment',
			'comment_parent'   => $course_id,
			'user_id'          => $user_id,
		));

		$wpdb->insert($wpdb->comments, $data);
		$comment_id = (int)$wpdb->insert_id;
		do_action('tutor_after_assignment_submit_start', $comment_id);

		wp_send_json_success(__('Answer has been added successfully', 'tutor-pro'));
	}

	public function tutor_assignment_submit() {
		tutor_utils()->checking_nonce();

		global $wpdb;
		$assignment_id        = (int)sanitize_text_field(tutor_utils()->array_get('assignment_id', $_POST));
		$assignment_answer    = wp_kses_post(tutor_utils()->array_get('assignment_answer', $_POST));
		$allowd_upload_files  = (int)tutor_utils()->get_assignment_option($assignment_id, 'upload_files_limit');
		$assignment_submit_id = tutor_utils()->is_assignment_submitting($assignment_id);

		do_action('tutor_assignment/before/submit', $assignment_submit_id);

		$date = date("Y-m-d H:i:s");
		$data = apply_filters('tutor_assignment_submit_updating_data', array(
			'comment_content'  => $assignment_answer,
			'comment_date'     => $date, //Submit Finished
			'comment_approved' => 'submitted', //submitting, submitted
			
		));

		if ($allowd_upload_files) {
			$upload_attachment = $this->handle_assignment_attachment_uploads($assignment_id);
			if (tutor_utils()->count($upload_attachment)) {
				update_comment_meta($assignment_submit_id, 'uploaded_attachments', json_encode($upload_attachment));
			}
		}

		$wpdb->update($wpdb->comments, $data, array(
			'comment_ID' => $assignment_submit_id
		));

		do_action('tutor_assignment/after/submitted', $assignment_submit_id);

		if (function_exists('wc_get_raw_referer')) {
			wp_redirect(wc_get_raw_referer());
		} else {
			wp_redirect(sanitize_text_field(tutor_utils()->avalue_dot('_wp_http_referer', $_POST)));
		}
		exit();
	}

	public function handle_assignment_attachment_uploads($assignment_id = 0) {
		if (!$assignment_id) {
			return;
		}

		if (!function_exists('wp_handle_upload')) {
			require_once (ABSPATH . 'wp-admin/includes/file.php');
		}

		$attached_files = array();

		if (!empty($_FILES["attached_assignment_files"])) {
			$files          = $_FILES["attached_assignment_files"];
			$max_size_mb    = (int)tutor_utils()->get_assignment_option($assignment_id, 'upload_file_size_limit', 2);

			foreach ($files['name'] as $key            => $value) {
				$file_size      = $files['size'][$key];
				$size_in_mb     = round($file_size / (1024 * 1024));

				if ($size_in_mb > $max_size_mb) {
					exit(sprintf(__('Maximum attachment upload size allowed is %d MB', 'tutor-pro') , $max_size_mb));
				}
			}

			foreach ($files['name'] as $key              => $value) {
				if ($files['name'][$key]) {
					$file             = array(
						'name'                  => $files['name'][$key],
						'type'                  => $files['type'][$key],
						'tmp_name'                  => $files['tmp_name'][$key],
						'error'                  => $files['error'][$key],
						'size'                  => $files['size'][$key]
					);

					$upload_overrides = array(
						'test_form'                  => false
					);
					$movefile         = wp_handle_upload($file, $upload_overrides);

					if ($movefile && !isset($movefile['error'])) {
						$file_path        = $movefile['file'];
						unset($movefile['file']);
						$upload_dir    = wp_get_upload_dir();

						$file_sub_path = str_replace(trailingslashit($upload_dir['basedir']) , '', $file_path);
						$file_name     = str_replace(trailingslashit($upload_dir['path']) , '', $file_path);

						$movefile['uploaded_path']               = $file_sub_path;
						$movefile['name']               = $file_name;

						$attached_files[]               = $movefile;
					} else {
						/**
						 * Error generated by _wp_handle_upload()
						 * @see _wp_handle_upload() in wp-admin/includes/file.php
						 */
						echo $movefile['error'];
					}
				}
			}
		}

		return $attached_files;
	}

	/**
	 * Evaluate assignment submission
	 *
	 */
	public function tutor_evaluate_assignment_submission() {
		tutor_utils()->checking_nonce();
		$date = date("Y-m-d H:i:s");

		do_action('tutor_assignment/evaluate/before');

		$submitted_id    = (int)sanitize_text_field(tutor_utils()->array_get('assignment_submitted_id', $_POST));
		$evaluate_fields = tutor_utils()->array_get('evaluate_assignment', $_POST);

		foreach ($evaluate_fields as $field_key => $field_value) {
			update_comment_meta($submitted_id, $field_key, $field_value);
		}
		update_comment_meta($submitted_id, 'evaluate_time', $date);

		do_action('tutor_assignment/evaluate/after', $submitted_id);
	}

	public function show_assignment_submitted_icon($post) {
		if ($post->post_type === 'tutor_assignments') {
			$is_submitted = tutils()->is_assignment_submitted($post->ID);

			if ($is_submitted && $is_submitted->comment_approved === 'submitted') {
				echo '<i class="tutor-lesson-complete tutor-icon-mark tutor-done"></i>';
			} else {
				$is_submitting = tutils()->is_assignment_submitting($post->ID);
				if ($is_submitting) {
					echo '<i class="tutor-lesson-complete tutor-icon-spinner"></i>';
				}
			}
		}
	}
}
