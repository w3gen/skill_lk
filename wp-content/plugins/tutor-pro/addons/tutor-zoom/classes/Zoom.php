<?php

/**
 * TutorZoom Class
 * @package TUTOR
 *
 * @since v.1.7.1
 */

namespace TUTOR_ZOOM;

if (!defined('ABSPATH'))
	exit;

class Zoom {

	private $api_key;
	private $settings_key;
	private $zoom_meeting_post_type;
	private $zoom_meeting_base_slug;
	private $zoom_meeting_post_meta;

	function __construct() {
		$this->api_key = 'tutor_zoom_api';
		$this->settings_key = 'tutor_zoom_settings';
		$this->zoom_meeting_post_type = 'tutor_zoom_meeting';
		$this->zoom_meeting_base_slug = 'tutor-zoom-meeting';
		$this->zoom_meeting_post_meta = '_tutor_zm_data';

		add_action('init', array($this, 'register_zoom_post_types'));

		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
		add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
		add_action('tutor_admin_register', array($this, 'register_menu'));

		add_filter('tutor_course_contents_post_types', array($this, 'tutor_course_contents_post_types'));

		// Saving zoom settings
		add_action('wp_ajax_tutor_save_zoom_api', array($this, 'tutor_save_zoom_api'));
		add_action('wp_ajax_tutor_save_zoom_settings', array($this, 'tutor_save_zoom_settings'));
		add_action('wp_ajax_tutor_check_api_connection', array($this, 'tutor_check_api_connection'));

		// Add meeting button options
		add_action('edit_form_after_editor', array($this, 'add_meetings_metabox'), 9, 0);
		add_action('tutor/frontend_course_edit/after/description', array($this, 'add_meetings_metabox'), 9, 0);
		add_action('tutor_course_builder_after_btn_group', array($this, 'add_meeting_option_in_topic'));

		// Meeting modal form and save action 
		add_action('wp_ajax_tutor_zoom_meeting_modal_content', array($this, 'tutor_zoom_meeting_modal_content'));
		add_action('wp_ajax_tutor_zoom_save_meeting', array($this, 'tutor_zoom_save_meeting'));

		add_action('wp_ajax_tutor_zoom_delete_meeting', array($this, 'tutor_zoom_delete_meeting'));

		add_action('tutor_course/single/before/topics', array($this, 'tutor_zoom_course_meeting'));
		add_filter('template_include', array($this, 'load_meeting_template'), 99);
	}

	public function register_zoom_post_types() {

		$labels = array(
			'name'               => _x('Meetings', 'post type general name', 'tutor-pro'),
			'singular_name'      => _x('Meeting', 'post type singular name', 'tutor-pro'),
			'menu_name'          => _x('Meetings', 'admin menu', 'tutor-pro'),
			'name_admin_bar'     => _x('Meeting', 'add new on admin bar', 'tutor-pro'),
			'add_new'            => _x('Add New', $this->zoom_meeting_post_type, 'tutor-pro'),
			'add_new_item'       => __('Add New Meeting', 'tutor-pro'),
			'new_item'           => __('New Meeting', 'tutor-pro'),
			'edit_item'          => __('Edit Meeting', 'tutor-pro'),
			'view_item'          => __('View Meeting', 'tutor-pro'),
			'all_items'          => __('Meetings', 'tutor-pro'),
			'search_items'       => __('Search Meetings', 'tutor-pro'),
			'parent_item_colon'  => __('Parent Meetings:', 'tutor-pro'),
			'not_found'          => __('No Meeting found.', 'tutor-pro'),
			'not_found_in_trash' => __('No Meetings found in Trash.', 'tutor-pro')
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __('Description.', 'tutor-pro'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array('slug' => $this->zoom_meeting_base_slug),
			'menu_icon'          => 'dashicons-list-view',
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array('title', 'editor'),
			'exclude_from_search' => true,
		);

		register_post_type($this->zoom_meeting_post_type, $args);
	}

	/**
	 * Enqueue admin scripts
	 */
	public function admin_scripts() {
		wp_enqueue_script('tutor_zoom_timepicker_js', TUTOR_ZOOM()->url . 'assets/js/jquery-ui-timepicker.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-slider'), TUTOR_ZOOM_VERSION, true);
		wp_enqueue_script('tutor_zoom_admin_js', TUTOR_ZOOM()->url . 'assets/js/admin.js', array('jquery'), TUTOR_ZOOM_VERSION, true);
		wp_enqueue_script('tutor_zoom_common_js', TUTOR_ZOOM()->url . 'assets/js/common.js', array('jquery', 'jquery-ui-datepicker'), TUTOR_ZOOM_VERSION, true);
		wp_enqueue_style('tutor_zoom_timepicker_css', TUTOR_ZOOM()->url . 'assets/css/jquery-ui-timepicker.css', false, TUTOR_ZOOM_VERSION);
		wp_enqueue_style('tutor_zoom_common_css', TUTOR_ZOOM()->url . 'assets/css/common.css', false, TUTOR_ZOOM_VERSION);
		wp_enqueue_style('tutor_zoom_admin_css', TUTOR_ZOOM()->url . 'assets/css/admin.css', false, TUTOR_ZOOM_VERSION);
	}

	/**
	 * Enqueue frontend scripts
	 */
	public function frontend_scripts() {
		global $wp_query;
		$is_frontend_course_builder = tutils()->array_get('tutor_dashboard_page', $wp_query->query_vars) === 'create-course';

		if ($wp_query->is_page && $is_frontend_course_builder) {
			wp_enqueue_script('tutor_zoom_timepicker_js', TUTOR_ZOOM()->url . 'assets/js/jquery-ui-timepicker.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-slider'), TUTOR_ZOOM_VERSION, true);
			wp_enqueue_style('tutor_zoom_timepicker_css', TUTOR_ZOOM()->url . 'assets/css/jquery-ui-timepicker.css', false, TUTOR_ZOOM_VERSION);
			wp_enqueue_script('tutor_zoom_common_js', TUTOR_ZOOM()->url . 'assets/js/common.js', array('jquery', 'jquery-ui-datepicker'), TUTOR_ZOOM_VERSION, true);
			wp_enqueue_style('tutor_zoom_common_css', TUTOR_ZOOM()->url . 'assets/css/common.css', false, TUTOR_ZOOM_VERSION);
		}

		if (is_single_course() || is_single_tutor_zoom_meeting_page()) {
			wp_enqueue_script('tutor_zoom_moment_js', TUTOR_ZOOM()->url . 'assets/js/moment.min.js', array(), TUTOR_ZOOM_VERSION, true);
			wp_enqueue_script('tutor_zoom_moment_tz_js', TUTOR_ZOOM()->url . 'assets/js/moment-timezone-with-data.min.js', array(), TUTOR_ZOOM_VERSION, true);
			wp_enqueue_script('tutor_zoom_countdown_js', TUTOR_ZOOM()->url . 'assets/js/jquery.countdown.min.js', array('jquery'), TUTOR_ZOOM_VERSION, true);
		}

		if (is_single_course() || is_single_tutor_zoom_meeting_page() || $is_frontend_course_builder) {
			wp_enqueue_script('tutor_zoom_frontend_js', TUTOR_ZOOM()->url . 'assets/js/frontend.js', array('jquery'), TUTOR_ZOOM_VERSION, true);
			wp_enqueue_style('tutor_zoom_frontend_css', TUTOR_ZOOM()->url . 'assets/css/frontend.css', false, TUTOR_ZOOM_VERSION);
		}
	}

	public function register_menu() {
		add_submenu_page('tutor', __('Zoom', 'tutor-pro'), __('Zoom', 'tutor-pro'), 'manage_tutor_instructor', 'tutor_zoom', array($this, 'tutor_zoom'));
	}

	public function tutor_course_contents_post_types($post_types) {
		$post_types[] = $this->zoom_meeting_post_type;

		return $post_types;
	}

	public function add_meetings_metabox() {
		global $post;
		$user_id    = get_current_user_id();
		$settings   = json_decode(get_user_meta($user_id, $this->api_key, true), true);
		$api_key    = (!empty($settings['api_key'])) ? $settings['api_key'] : '';
		$api_secret = (!empty($settings['api_secret'])) ? $settings['api_secret'] : '';
		if ($post->post_type == tutor()->course_post_type && !empty($api_key) && !empty($api_secret)) {
			$course_id = $post->ID;
			echo '<div id="tutor-zoom-metabox-wrap">';
			include TUTOR_ZOOM()->path . "views/metabox/meetings.php";
			echo '</div>';
		}
	}

	public function add_meeting_option_in_topic($topic_id)
	{
		$user_id    = get_current_user_id();
		$settings   = json_decode(get_user_meta($user_id, $this->api_key, true), true);
		$api_key    = (!empty($settings['api_key'])) ? $settings['api_key'] : '';
		$api_secret = (!empty($settings['api_secret'])) ? $settings['api_secret'] : '';
		if (!empty($api_key) && !empty($api_secret)) {
?>
			<a href="javascript:;" class="tutor-zoom-meeting-modal-open-btn" data-meeting-id="0" data-topic-id="<?php echo $topic_id; ?>" data-click-form="course-builder">
				<i class="tutor-icon-plus-square-button"></i>
				<?php _e('Zoom Live Lesson', 'tutor-pro'); ?>
			</a>
<?php
		}
	}


	public function tutor_zoom_meeting_modal_content() {
		tutils()->checking_nonce();

		$meeting_id = (int) sanitize_text_field($_POST['meeting_id']);
		$topic_id   = (int) sanitize_text_field($_POST['topic_id']);
		$course_id  = (int) sanitize_text_field($_POST['course_id']);
		$click_form = sanitize_text_field($_POST['click_form']);

		$post = null;
		$meeting_data = null;
		if ($meeting_id) {
			$post = get_post($meeting_id);
			$meeting_start  = get_post_meta($meeting_id, '_tutor_zm_start_datetime', true);
			$meeting_data   = get_post_meta($meeting_id, $this->zoom_meeting_post_meta, true);
			$meeting_data   = json_decode($meeting_data, true);
		}

		$start_date     = '';
		$start_time     = '';
		$host_id        = !empty($meeting_data) ? $meeting_data['host_id'] : '';
		$title          = !empty($meeting_data) ? wp_strip_all_tags($meeting_data['topic']) : '';
		$summary        = !empty($post) ? $post->post_content : '';
		$timezone       = !empty($meeting_data) ? $meeting_data['timezone'] : '';
		$duration       = !empty($meeting_data) ? $meeting_data['duration'] : 60;
		$duration_unit  = !empty($post) ? get_post_meta($meeting_id, '_tutor_zm_duration_unit', true) : 'min';
		$password       = !empty($meeting_data) ? $meeting_data['password'] : '';
		$auto_recording = !empty($meeting_data) ? $meeting_data['settings']['auto_recording'] : $this->get_settings('auto_recording');

		if (!empty($meeting_data)) {
			$input_date = \DateTime::createFromFormat('Y-m-d H:i:s', $meeting_start);
			$start_date = $input_date->format('d/m/Y');
			$start_time = $input_date->format('h:i A');
			$duration   = ($duration_unit == 'hr') ? $duration / 60 : $duration;
		}

		ob_start();
		include  TUTOR_ZOOM()->path . 'views/modal/meeting.php';
		$output = ob_get_clean();

		wp_send_json_success(array('output' => $output));
	}

	/**
	 * Save meeting
	 */
	public function tutor_zoom_save_meeting() {
		tutils()->checking_nonce();

		$meeting_id = (int) sanitize_text_field($_POST['meeting_id']);
		$topic_id = (int) sanitize_text_field($_POST['topic_id']);
		$course_id = (int) sanitize_text_field($_POST['course_id']);
		$click_form = sanitize_text_field($_POST['click_form']);

		$user_id    = get_current_user_id();
		$settings   = json_decode(get_user_meta($user_id, $this->api_key, true), true);
		$api_key    = (!empty($settings['api_key'])) ? $settings['api_key'] : '';
		$api_secret = (!empty($settings['api_secret'])) ? $settings['api_secret'] : '';
		if (!empty($api_key) && !empty($api_secret)) {
			$host_id            = !empty($_POST['meeting_host']) ? sanitize_text_field($_POST['meeting_host']) : '';
			$title              = !empty($_POST['meeting_title']) ? sanitize_text_field($_POST['meeting_title']) : '';
			$summary            = !empty($_POST['meeting_summary']) ? sanitize_text_field($_POST['meeting_summary']) : '';
			$timezone           = !empty($_POST['meeting_timezone']) ? sanitize_text_field($_POST['meeting_timezone']) : '';
			$start_date         = !empty($_POST['meeting_date']) ? sanitize_text_field($_POST['meeting_date']) : '';
			$start_time         = !empty($_POST['meeting_time']) ? sanitize_text_field($_POST['meeting_time']) : '';

			$input_duration     = !empty($_POST['meeting_duration']) ? intval($_POST['meeting_duration']) : 60;
			$duration_unit      = !empty($_POST['meeting_duration_unit']) ? $_POST['meeting_duration_unit'] : 'min';
			$password           = !empty($_POST['meeting_password']) ? sanitize_text_field($_POST['meeting_password']) : '';

			$join_before_host   = ($this->get_settings('join_before_host')) ? true : false;
			$host_video         = ($this->get_settings('host_video')) ? true : false;
			$participants_video = ($this->get_settings('participants_video')) ? true : false;
			$mute_participants  = ($this->get_settings('mute_participants')) ? true : false;
			$enforce_login      = ($this->get_settings('enforce_login')) ? true : false;
			$auto_recording     = !empty($_POST['auto_recording']) ? sanitize_text_field($_POST['auto_recording']) : '';


			$input_date = \DateTime::createFromFormat('d/m/Y h:i A', $start_date . ' ' . $start_time);
			$meeting_start =  $input_date->format('Y-m-d\TH:i:s');

			$duration = ($duration_unit == 'hr') ? $input_duration * 60 : $input_duration;
			$data = array(
				'topic'         => $title,
				'type'          => 2,
				'start_time'    => $meeting_start,
				'timezone'      => $timezone,
				'duration'      => $duration,
				'password'      => $password,
				'settings'      => array(
					'join_before_host'  => $join_before_host,
					'host_video'        => $host_video,
					'participant_video' => $participants_video,
					'mute_upon_entry'   => $mute_participants,
					'auto_recording'    => $auto_recording,
					'enforce_login'     => $enforce_login,
				)
			);

			//save post
			$post_content = array(
				'ID'            => ($meeting_id) ? $meeting_id : 0,
				'post_title'    => $title,
				'post_name'     => sanitize_title($title),
				'post_content'  => $summary,
				'post_type'     => $this->zoom_meeting_post_type,
				'post_parent'   => ($topic_id) ? $topic_id : $course_id,
				'post_status'   => 'publish'
			);

			//save zoom meeting
			if (!empty($api_key) && !empty($api_secret) && !empty($host_id)) {
				
				$post_id      = wp_insert_post($post_content);
				$meeting_data = get_post_meta($post_id, $this->zoom_meeting_post_meta, true);
				$meeting_data = json_decode($meeting_data, true);

				$zoom_endpoint = tutils()->get_package_object( true, '\Zoom\Endpoint\Meetings', $api_key, $api_secret );
				if (!empty($meeting_data) && isset($meeting_data['id'])) {
					$zoom_endpoint->update($meeting_data['id'], $data);
					$saved_meeting = $zoom_endpoint->meeting($meeting_data['id']);
					do_action('tutor_zoom_after_update_meeting', $post_id);
				} else {
					$saved_meeting = $zoom_endpoint->create($host_id, $data);
					update_post_meta($post_id, '_tutor_zm_for_course', $course_id);
					update_post_meta($post_id, '_tutor_zm_for_topic', $topic_id);

					do_action('tutor_zoom_after_save_meeting', $post_id);
				}
				update_post_meta($post_id, '_tutor_zm_start_date', $input_date->format('Y-m-d'));
				update_post_meta($post_id, '_tutor_zm_start_datetime', $input_date->format('Y-m-d H:i:s'));
				update_post_meta($post_id, '_tutor_zm_duration', $input_duration);
				update_post_meta($post_id, '_tutor_zm_duration_unit', $duration_unit);
				update_post_meta($post_id, $this->zoom_meeting_post_meta, json_encode($saved_meeting));
			}

			$course_contents = '';
			$selector = '';
			if ($click_form == 'course-builder') {
				ob_start();
				$current_topic_id = $topic_id;
				include  tutor()->path . 'views/metabox/course-contents.php';
				$course_contents = ob_get_clean();
				$selector = '#tutor-course-content-wrap';
			} else if ($click_form == 'metabox') {
				ob_start();
				include  TUTOR_ZOOM()->path . 'views/metabox/meetings.php';
				$course_contents = ob_get_clean();
				$selector = '#tutor-zoom-metabox-wrap';
			}

			wp_send_json(array(
				'success' => true,
				'post_id' => $post_id,
				'msg' => __('Meeting Successfully Saved', 'tutor-pro'),
				'course_contents' => $course_contents,
				'selector' => $selector
			));
		} else {
			wp_send_json(array(
				'success' => false,
				'post_id' => false,
				'msg' => __('Invalid Api Credentials', 'tutor-pro'),
			));
		}
	}

	/**
	 * Delete meeting
	 */
	public function tutor_zoom_delete_meeting() {
		tutils()->checking_nonce();

		$user_id    = get_current_user_id();
		$post_id    = (int) sanitize_text_field($_POST['meeting_id']);
		$settings   = json_decode(get_user_meta($user_id, $this->api_key, true), true);
		$api_key    = (!empty($settings['api_key'])) ? $settings['api_key'] : '';
		$api_secret = (!empty($settings['api_secret'])) ? $settings['api_secret'] : '';
		if (!empty($api_key) && !empty($api_secret)) {
			$meeting_data = get_post_meta($post_id, $this->zoom_meeting_post_meta, true);
			$meeting_data = json_decode($meeting_data, true);

			$zoom_endpoint = tutils()->get_package_object( true, '\Zoom\Endpoint\Meetings', $api_key, $api_secret );
			$zoom_endpoint->remove($meeting_data['id']);

			wp_delete_post($post_id, true);

			do_action('tutor_zoom_after_delete_meeting', $post_id);

			wp_send_json(array(
				'success' => true,
				'post_id' => $post_id,
				'msg' => __('Meeting Successfully Deleted', 'tutor-pro'),
			));
		} else {
			wp_send_json(array(
				'success' => false,
				'post_id' => false,
				'msg' => __('Invalid Api Credentials', 'tutor-pro'),
			));
		}
	}

	private function get_option_data($key, $data) {
		if (empty($data) || !is_array($data)) {
			return false;
		}
		if (!$key) {
			return $data;
		}
		if (array_key_exists($key, $data)) {
			return apply_filters($key, $data[$key]);
		}
	}

	private function get_transient_key() {
		$user_id       = get_current_user_id();
		$transient_key = 'tutor_zoom_users_' . $user_id;
		return $transient_key;
	}

	private function get_api($key = null) {
		$user_id  = get_current_user_id();
		$api_data = json_decode(get_user_meta($user_id, $this->api_key, true), true);
		return $this->get_option_data($key, $api_data);
	}

	private function get_settings($key = null) {
		$user_id       = get_current_user_id();
		$settings_data = json_decode(get_user_meta($user_id, $this->settings_key, true), true);
		return $this->get_option_data($key, $settings_data);
	}

	public function tutor_zoom() {
		include TUTOR_ZOOM()->path . 'views/pages/main.php';
	}

	public function tutor_save_zoom_api() {
		tutils()->checking_nonce();

		do_action('tutor_save_zoom_api_before');
		$api_data   = (array) isset($_POST[$this->api_key]) ? $_POST[$this->api_key] : array();
		$api_data   = apply_filters('tutor_zoom_api_input', $api_data);
		$user_id    = get_current_user_id();
		update_user_meta($user_id, $this->api_key, json_encode($api_data));
		do_action('tutor_save_zoom_api_after');
		wp_send_json_success(array('msg' => __('Settings Updated', 'tutor-pro')));
	}

	public function tutor_save_zoom_settings() {
		tutils()->checking_nonce();

		do_action('tutor_save_zoom_settings_before');
		$settings = (array) isset($_POST[$this->settings_key]) ? $_POST[$this->settings_key] : array();
		$settings = apply_filters('tutor_zoom_settings_input', $settings);
		$user_id  = get_current_user_id();
		update_user_meta($user_id, $this->settings_key, json_encode($settings));
		do_action('tutor_save_zoom_settings_after');
		wp_send_json_success(array('msg' => __('Settings Updated', 'tutor-pro')));
	}

	public function tutor_check_api_connection() {
		tutils()->checking_nonce();
		$transient_key = $this->get_transient_key();
		delete_transient($transient_key); //delete temporary cache
		$users = $this->tutor_zoom_get_users();
		if (!empty($users)) {
			wp_send_json(__('Zoom successfully connected', 'tutor-pro'));
		} else {
			wp_send_json(__('Please Enter Valid Credentials', 'tutor-pro'));
		}
		wp_die();
	}

	/**
	 * Get Zoom Users from Zoom API
	 * @return array
	 */
	public function tutor_zoom_get_users() {
		$user_id        = get_current_user_id();
		$transient_key  = $this->get_transient_key();
		$users          = get_transient($transient_key);
		$settings       = json_decode(get_user_meta($user_id, $this->api_key, true), true);

		if (empty($users)) {
			$api_key    = (!empty($settings['api_key'])) ? $settings['api_key'] : '';
			$api_secret = (!empty($settings['api_secret'])) ? $settings['api_secret'] : '';
			if (!empty($api_key) && !empty($api_secret)) {
				$users = array();
				$users_data = tutils()->get_package_object( true, '\Zoom\Endpoint\Users', $api_key, $api_secret );
				$users_list = $users_data->userlist();
				if (!empty($users_list) && !empty($users_list['users'])) {
					$users = $users_list['users'];
					set_transient($transient_key, $users, 36000);
				}
			} else {
				$users = array();
			}
		}
		return $users;
	}

	/**
	 * Get Zoom Users
	 * @return array
	 */
	public function get_users_options() {
		$users = $this->tutor_zoom_get_users();
		if (!empty($users)) {
			foreach ($users as $user) {
				$first_name         = $user['first_name'];
				$last_name          = $user['last_name'];
				$email              = $user['email'];
				$id                 = $user['id'];
				$user_list[$id]   = $first_name . ' ' . $last_name . ' (' . $email . ')';
			}
		} else {
			return array();
		}
		return $user_list;
	}

	/**
	 * Load zoom meeting template
	 * @return array
	 */
	public function tutor_zoom_course_meeting() {
		ob_start();
		tutor_load_template('single.course.zoom-meetings', null, true);
		$output = apply_filters('tutor_course/single/zoom_meetings', ob_get_clean());
		echo $output;
	}

	/**
	 * Load zoom meeting template
	 * @return array
	 */
	public function load_meeting_template($template) {
		global $wp_query, $post;
		if ($wp_query->is_single && !empty($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] === $this->zoom_meeting_post_type) {
			if (is_user_logged_in()) {
				$content_type = (get_post_type($post->post_parent) === tutor()->course_post_type) ? 'topic' : 'lesson';
				$has_content_access = tutils()->has_enrolled_content_access($content_type, $post->ID);
				if ($has_content_access) {
					$template = tutor_get_template('single-zoom-meeting', true);
				} else {
					$template = tutor_get_template('single.lesson.required-enroll'); //You need to enroll first
				}
			} else {
				$template = tutor_get_template('login');
			}
			return $template;
		}
		return $template;
	}
}
