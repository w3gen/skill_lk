<?php

/**
 * Tutor Multi Instructor
 */

namespace TUTOR_CERT;

class Certificate {
	private $template;
	private $certificates_dir_name = 'tutor-certificates';
	private $certificate_stored_key = 'tutor_certificate_has_image';
	private $disable_certificate_key = '_tutor_disable_certificate';
	public static $certificate_img_url_base = 'https://preview.tutorlms.com/certificate-templates/';

	public function __construct() {
		if (!function_exists('tutor_utils')) {
			return;
		}

		add_action('tutor_options_before_tutor_certificate', array($this, 'generate_options'));
		add_action('tutor_enrolled_box_after', array($this, 'certificate_download_btn'));

		add_action('wp_loaded', array($this, 'get_fonts'));
		add_action('template_redirect', array($this, 'view_certificate'));
		add_action('wp_loaded', array($this, 'send_certificate_html'));
		add_action('wp_loaded', array($this, 'store_certificate_image'));

		
		// Register necessary scripts for certificate rendering
        add_action('wp_head', function() {
            echo '<script>var tutor_loading_icon_url="'.get_admin_url().'images/loading.gif";</script>';
        });

		add_action('wp_enqueue_scripts', array($this, 'load_script'));
		
		/**
		 * Disable certificate feature
		 * @since v.1.7.0
		 */
		add_action('tutor_after_course_sidebar_settings_metabox', array($this, 'disable_certificate_metabox'));
		add_action('save_post_'. tutor()->course_post_type, array($this, 'save_course_meta'));
		add_action('save_tutor_course', array($this, 'save_course_meta'));


		/**
		 * Add certificate link to course completion email
		 * @since v.1.8.2
		 */
		add_filter( 'tutor_certificate_add_url_to_email', array($this, 'add_certificate_to_email'), 10, 2 );
	}

    public function load_script() {
        if (is_single_course() || !empty($_GET['cert_hash'])) {
            $base = tutor_pro()->url . 'addons/tutor-certificate/assets/js/';

            wp_enqueue_script('html-to-image-converter', $base . 'html2canvas.min.js');
            wp_enqueue_script('html-to-image-js-pdf', $base . 'js-pdf.js');
            wp_enqueue_script('html-to-image', $base . 'html-to-image.js');
        }
	}
	
	public function get_fonts() {
		if(($_GET['tutor_action'] ?? '') !== 'get_fonts') { return; }

		$url_base = tutor_pro()->url .'addons/tutor-certificate/assets/fonts/';
		$path_base = $this->cross_platform_path(dirname(__DIR__).'/assets/css/');

		$default_files = $path_base . 'font-loader.css';
		$default_fonts = file_get_contents($default_files);

		$font_faces = str_replace('./fonts/', $url_base, $default_fonts);

		// Now load template fonts
		$this->prepare_template_data();
		$font_css = $this->template['path'].'font.css';
		if (file_exists($font_css)) {
			$faces = file_get_contents($font_css);
			$faces = str_replace('./fonts/', $this->template['url'].'fonts/', $faces);
			$font_faces .= $faces;
		}
		
		exit($font_faces);
	}

	public function send_certificate_html() {
		$id = $_GET['course_id'] ?? '';
		$cert_hash = isset($_GET['certificate_hash']) ? $_GET['certificate_hash'] : null;
		$action = $_GET['tutor_action'] ?? '';

		if (is_numeric($id) && $action == 'generate_course_certificate') {

			$this->prepare_template_data();
			$completed = $cert_hash ? $this->completed_course($cert_hash) : false;
			
			// Get certificate html
			$content = $this->generate_certificate($id, $completed);
			exit($content);
		}
	}

	private function cross_platform_path($path) {
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);

		return $path;
	}

	private function prepare_template_data() {	
		if (!$this->template) {
			//Get the selected template
			$templates = $this->templates();

			$template = tutor_utils()->get_option('certificate_template');
			!$template ? $template = 'default' : 0;

			$this->template = tutor_utils()->avalue_dot($template, $templates);
		}
	}

	public function store_certificate_image() {
		// Collect post data
		$hash = $_POST['cert_hash'] ?? '';
		$action = $_POST['tutor_action'] ?? '';
		$image = $_POST['certificate_image'] ?? null;
		$completed = $this->completed_course($hash);

		if ($completed && is_string($hash) && $action == 'store_certificate_image') {

			// et the dir from outside of the filter hook. Otherwise infinity loop will coccur.
			$certificates_dir = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $this->certificates_dir_name;
			$rand_string = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);

			// Store new file
			wp_mkdir_p($certificates_dir);
			$decode = base64_decode(str_replace('data:image/jpeg;base64,', '', $image));
			$file_dest = $certificates_dir . DIRECTORY_SEPARATOR . $rand_string . '-' . $hash . '.jpg';
			file_put_contents($file_dest, $decode);
			
			// Delete old one
			$old_rand_string = get_comment_meta($completed->certificate_id, $this->certificate_stored_key, true);
			$old_path = $this->cross_platform_path($certificates_dir . '/' . $old_rand_string . '-' . $hash . '.jpg');
			file_exists($old_path) ? unlink($old_path) : 0;

			// Update new 
			update_comment_meta($completed->certificate_id, $this->certificate_stored_key, $rand_string);

			exit('ok');
		}
	}

	/**
	 * View Certificate
	 * @since v.1.5.1
	 */
	public function view_certificate() {
		$cert_hash = sanitize_text_field(tutils()->array_get('cert_hash', $_GET));

		if (!$cert_hash || !empty($_GET['tutor_action'])) {
			return;
		}

		$completed = $this->completed_course($cert_hash);
		if (!is_object($completed) || !property_exists($completed, 'completed_user_id')) {
			return;
		}

		$show_certificate = (bool) tutils()->get_option('tutor_course_certificate_view');
		if(!$show_certificate && get_current_user_id()!=$completed->completed_user_id) {
			return;
		}
		
		$course = get_post($completed->course_id);
		$upload_dir = wp_upload_dir();
		
		$certificate_dir_url = $upload_dir['baseurl'] . '/' . $this->certificates_dir_name;
		$certificate_dir_path = $upload_dir['basedir'] . '/' . $this->certificates_dir_name;

		$rand_string = get_comment_meta($completed->certificate_id, $this->certificate_stored_key, true);

		$cert_path = '/' . $rand_string . '-' . $cert_hash . '.jpg';
		$cert_file = $certificate_dir_path . $cert_path;
		!file_exists($cert_file) ? $cert_file=null : 0;
		
		$cert_img = !$cert_file ? get_admin_url().'images/loading.gif' : $certificate_dir_url . $cert_path;

		$this->certificate_header_content($course->post_title, $cert_img);

		ob_start();
		tutor_load_template('single-certificate', compact('course', 'cert_file', 'cert_img', 'cert_hash'), true);
		echo ob_get_clean();
		die();
	}

	private function get_signature_url($instructor_id){

		$custom_signature = (new Instructor_Signature(false))->get_instructor_signature($instructor_id);
		$signature_image_url = $custom_signature['url'];

		if(!$signature_image_url){
			// Get default ID
			$default_sinature_id = tutor_utils()->get_option('tutor_cert_signature_image_id');

			// Assign default 
			$signature_image_url = $default_sinature_id ? wp_get_attachment_url($default_sinature_id) : TUTOR_CERT()->url.'/assets/images/signature.png';
		}

		return $signature_image_url;
	}

	public function generate_certificate($course_id, $completed = false) {
		$duration           = get_post_meta($course_id, '_course_duration', true);
		$durationHours      = (int) tutor_utils()->avalue_dot('hours', $duration);
		$durationMinutes    = (int) tutor_utils()->avalue_dot('minutes', $duration);
		$course             = get_post($course_id);
		$completed          = $completed ? $completed : tutor_utils()->is_completed_course($course_id);
		$user 				= $completed ? get_userdata($completed->completed_user_id) : wp_get_current_user();
		$completed_date		= '';
		if ($completed) {
			$wp_date_format		= get_option('date_format');
			$completed_date 	= date($wp_date_format, strtotime($completed->completion_date));

			// Translate month name
			$converter = function ($matches) {
				$month = __($matches[0]);

				// Make first letter uppercase if it's not unicode character.
				strlen($month) == strlen(utf8_decode($month)) ? $month = ucfirst($month) : 0;

				return $month;
			};
			$completed_date		= preg_replace_callback('/[a-z]+/i', $converter, $completed_date);

			// Translate day and year digits
			$completed_date		= preg_replace_callback('/[0-9]/', function ($m) {
				return __($m[0]);
			}, $completed_date);
		}

		// Prepare signature image
		$signature_image_url = $this->get_signature_url($course->post_author);

		// Include instructor name if enabled
		$enabled = tutils()->get_option('show_instructor_name_on_certificate', false);

		if($enabled) {

			$user_info = get_userdata($course->post_author);
			$instructor_name = $user_info ? $user_info->display_name : '';
			  
			add_filter('tutor_cert_authorised_name', function($authorized) use($instructor_name) {
				$suthorized = is_string($authorized) ? trim($authorized) : '';
				$authorized = $instructor_name . (strlen($authorized) ? ', ' : '') . $authorized;

				return $authorized;
			});
		}

		ob_start();
		include $this->template['path'] . 'certificate.php';
		$content = ob_get_clean();

		return $content;
	}

	public function pdf_style() {
		$css = $this->template['path'] . 'pdf.css';

		ob_start();
		if (file_exists($css)) {
			include($css);
		}
		$css = ob_get_clean();
		$css = apply_filters('tutor_cer_css', $css, $this);

		echo $css;
	}

	public function certificate_download_btn() {

		$course_id = get_the_ID();
		$certificate = $this->get_certificate($course_id, true);

		if (!$certificate) {
			return;
		}

		extract($certificate);

		ob_start();
		include TUTOR_CERT()->path . 'views/lesson-menu-after.php';
		$content = ob_get_clean();

		echo $content;
	}

	public function generate_options() {
		$templates = $this->templates();

		ob_start();
		include TUTOR_CERT()->path . 'views/template_options.php';
		$content = ob_get_clean();

		echo $content;
	}


	public function templates() {
		$templates = array(
			'default'       => array('name' => 'Default', 'orientation' => 'landscape'),
			'template_1'    => array('name' => 'Abstract Landscape', 'orientation' => 'landscape'),
			'template_2'    => array('name' => 'Abstract Portrait', 'orientation' => 'portrait'),
			'template_3'    => array('name' => 'Decorative Landscape', 'orientation' => 'landscape'),
			'template_4'    => array('name' => 'Decorative Portrait', 'orientation' => 'portrait'),
			'template_5'    => array('name' => 'Geometric Landscape', 'orientation' => 'landscape'),
			'template_6'    => array('name' => 'Geometric Portrait', 'orientation' => 'portrait'),
			'template_7'    => array('name' => 'Minimal Landscape', 'orientation' => 'landscape'),
			'template_8'    => array('name' => 'Minimal Portrait', 'orientation' => 'portrait'),
			'template_9'    => array('name' => 'Floating Landscape', 'orientation' => 'landscape'),
			'template_10'   => array('name' => 'Floating Portrait', 'orientation' => 'portrait'),
			'template_11'   => array('name' => 'Stripe Landscape', 'orientation' => 'landscape'),
			'template_12'   => array('name' => 'Stripe Portrait', 'orientation' => 'portrait'),
		);
		foreach ($templates as $key => $template) {

			$path = trailingslashit(TUTOR_CERT()->path . 'templates/' . $key);
			$url = trailingslashit(TUTOR_CERT()->url . 'templates/' . $key);

			$templates[$key]['path'] = $path;
			$templates[$key]['url'] = $url;
			$templates[$key]['preview_src'] = self::$certificate_img_url_base . $key . '/preview.png';
			$templates[$key]['background_src'] = self::$certificate_img_url_base . $key . '/background.png';
		}

		$filtered = apply_filters('tutor_certificate_templates', $templates);

		// Customizer plugin compatibility
		foreach($filtered as $index=>$values) {
			if(isset($values['background_src'])) {
				continue;
			}

			$filtered[$index]['preview_src'] = $values['url'] . 'preview.png';
			$filtered[$index]['background_src'] = $values['url'] . 'preview.png';
		}

		return $filtered;
	}

	/**
	 * Get completed course data
	 * @since v.1.5.1
	 */
	public function completed_course($cert_hash) {
		global $wpdb;
		$is_completed = $wpdb->get_row(
			"SELECT comment_ID as certificate_id, 
					comment_post_ID as course_id, 
					comment_author as completed_user_id, 
					comment_date as completion_date, 
					comment_content as completed_hash 
			FROM	$wpdb->comments
			WHERE 	comment_agent = 'TutorLMSPlugin' 
					AND comment_type = 'course_completed' 
					AND comment_content = '$cert_hash';"
		);

		if ($is_completed) {
			return $is_completed;
		}

		return false;
	}


	/**
	 * Certificate header og content
	 * @since v.1.5.1
	 */
	public function certificate_header_content($course_title, $cert_img) {
		add_action('wp_head', function () use ($course_title, $cert_img) {
			$title = __('Course Completion Certificate', 'tutor-pro');
			$description = __('My course completion certificate for', 'tutor-pro') . ' "' . $course_title . '"';
			echo '
				<meta property="og:title" content="' . $title . '"/>
				<meta property="og:description" content="' . $description . '"/>
				<meta property="og:image" content="' . $cert_img . '"/>
				<meta name="twitter:title" content="Your title here"/>
				<meta name="twitter:description" content="' . $description . '"/>
				<meta name="twitter:image" content="' . $cert_img . '"/>
			';
		});
	}

	/**
	 * Disable Certificate Metabox
	 * @since v.1.7.0
	 */
	public function disable_certificate_metabox($post) {
		$disable_certificate = $this->disable_certificate_key;
		$disable_certificate_value = get_post_meta($post->ID, $disable_certificate, true);
		$disable_certificate_checked = ($disable_certificate_value == "yes") ? 'checked="checked"' : '';
		?>
		<div class="tutor-course-sidebar-settings-item">
			<label for="<?php echo $disable_certificate; ?>">
				<input id="<?php echo $disable_certificate; ?>" type="checkbox" name="<?php echo $disable_certificate; ?>" value="yes" <?php echo $disable_certificate_checked; ?> />
				<?php _e('Disable Certificate', 'tutor-pro'); ?>
			</label>
		</div>
		<?php
	}

	/**
	 * Save course meta for certificate
	 * @since v.1.7.0
	 */
	public function save_course_meta($post_ID) {
		$additional_data_edit = tutils()->avalue_dot('_tutor_course_additional_data_edit', $_POST);
		$disable_certificate = $this->disable_certificate_key;
		if ($additional_data_edit) {
			$disable_certificate_value = ( isset($_POST[$disable_certificate]) ) ? 'yes' : 'no';
			update_post_meta($post_ID, $disable_certificate, $disable_certificate_value);
		}
	}

	private function get_certificate($course_id, $full=false) {

		$is_completed = tutor_utils()->is_completed_course($course_id);
		$url = $is_completed ? get_home_url( ) . '?cert_hash=' . $is_completed->completed_hash : null;
		
		if($full && $is_completed) {
			return [
				'certificate_url' => $url,
				'certificate_hash' => $is_completed->completed_hash
			];
		}

		return $url;
	}

	/**
	 * Add certificate link to course completion email
	 * @since v.1.8.2
	 */
	public function add_certificate_to_email($html, $course_id) {

		$enabled = tutils()->get_option('send_certificate_link_to_course_completion_email', false);

		if($enabled) {

			$cert_url = $this->get_certificate($course_id);
			
			if($cert_url) {
				$anchor = '<a target="_blank" href="'.$cert_url.'">'.$cert_url.'</a>';
				$html = $html . '<p>' . sprintf(__('To view or download certificate please click %s', 'tutor-pro'), $anchor) . '</p>';
			}
		}
		
		return $html;
	}
}
