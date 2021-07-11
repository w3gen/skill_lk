<?php
namespace TUTOR_GC;

if ( ! defined( 'ABSPATH' ) )
	exit;

// include 'Quick_Start.php';

class init{
	
	private $gc_dashboard_url;
	private $gc_stream_slug = 'google-classroom-stream';
	private $gc_dashboard_slug = 'tutor-google-classroom';
	private $gc_code_privilege = 'tutor_gc_classrooom_code_only_for_logged_in';
	public static $google_callback_string = 'tutor-google-classroom-callback';
	private $gc_metabox = array(
		'tutor_gc_enable_classroom_stream' => 'Enable Google Clasroom Stream',
		'tutor_gc_show_stream_files' => 'Show Google Classroom Files in Stream',
		'tutor_gc_include_classroom_files' => 'Include Google Classroom Files in Resources',
	);
	

	function __construct() {
		if ( ! function_exists('tutor')){
			return;
		}
		
		$addonConfig = tutor_utils()->get_addon_config(TUTOR_GC()->basename);
		$isEnable = (bool) tutor_utils()->avalue_dot('is_enable', $addonConfig);
		if ( ! $isEnable){
			return;
		}

		$this->gc_dashboard_url = get_admin_url(null, 'admin.php?page='.$this->gc_dashboard_slug);
		spl_autoload_register(array($this, 'loader'));

		$this->register_hooks();
	}

	private function loader($className) {
		if ( ! class_exists($className)){
			$className = preg_replace(
				array('/([a-z])([A-Z])/', '/\\\/'),
				array('$1$2', DIRECTORY_SEPARATOR),
				$className
			);

			$className = str_replace('TUTOR_GC'.DIRECTORY_SEPARATOR, 'classes'.DIRECTORY_SEPARATOR, $className);
			$file_name = TUTOR_GC()->path.$className.'.php';

			if (file_exists($file_name) && is_readable( $file_name ) ) {
				require_once $file_name;
			}
		}
	}

	public function register_hooks(){

		add_action('admin_menu', array($this, 'add_sub_menu'));
		add_action('wp_loaded', array($this, 'save_token'));
		add_action('wp_loaded', array($this, 'reset_tutor_student_password'));
		add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));
		add_action('wp_enqueue_scripts', array($this, 'load_frontend_script'));

		add_action('wp_ajax_tutor_gc_load_more_stream', array($this, 'stream_tab_content'));
		add_action('wp_ajax_tutor_gc_credential_save', array($this, 'save_credential'));
		add_action('wp_ajax_tutor_gc_class_action', array($this, 'dispatch_class_action'));
		add_action('wp_ajax_tutor_gc_classroom_code_privilege', array($this, 'set_classroom_code_privilege'));
		add_action('wp_ajax_tutor_gc_credential_upgrade', array($this, 'upgrade_credential'));
		add_action('wp_ajax_nopriv_tutor_gc_student_set_password', array($this, 'set_student_password'));

		add_action('add_meta_boxes', array($this, 'register_metabox'), 10, 2);
		add_action('save_post_'.tutor()->course_post_type, array($this, 'save_course_meta'));

		add_filter('tutor_course/single/enrolled/nav_items', array($this, 'stream_tab'));
		add_filter('tutor_course/single/enrolled/nav_items_rewrite', array($this, 'add_stream_nav_rewrite'));
		add_action("tutor_course/single/enrolled/".$this->gc_stream_slug, array($this, 'stream_tab_content'));
		add_action('tutor_course/single/after/complete_form', array($this, 'add_start_course_button'));
		add_action('tutor_global/after/attachments', array($this, 'load_gc_attachments'), 10, 3);
		add_action('tutor/dashboard_course_builder_form_field_after', array($this, 'course_connection_metabox_frontend'));

		add_shortcode('tutor_gc_classes', array($this, 'tutor_gc_classes'));
		
		add_action('wp_head', [$this, 'load_tutor_values']);
		add_action('admin_head', function(){
			$this->is_tutor_gc_dashboard() ? $this->load_tutor_values() : 0;
		});
	}

	public function load_tutor_values(){
		
		echo '<script>
				window.tutor_gc_base_url="'.TUTOR_GC()->url.'";
				window.tutor_gc_loading_icon_url="'.get_admin_url().'images/loading.gif";
				window.tutor_gc_ajax_url="'.admin_url('admin-ajax.php').'";
				window.tutor_gc_dashboard_url="'.tutor_utils()->get_tutor_dashboard_page_permalink().'";
			</script>';
	}

	private function is_tutor_gc_dashboard(){
		$page = isset($_GET['page']) ? $_GET['page'] : '';
		return (is_admin() && $page==$this->gc_dashboard_slug);
	}

	public function load_admin_scripts(){
		if($this->is_tutor_gc_dashboard()){
			wp_enqueue_style('tutor-gc-dashboard-style', TUTOR_GC()->url.'assets/css/classroom-dashboard.css');
			wp_enqueue_style('tutor-gc-dashboard-bootstrap', TUTOR_GC()->url.'assets/css/bootstrap-grid.min.css');
			wp_enqueue_script('tutor-gc-dashboard-script', TUTOR_GC()->url.'assets/js/classroom-dashboard.js', [], null, true);
		}
	}

	public function load_frontend_script(){
		wp_enqueue_style('tutor-gc-frontend-style', TUTOR_GC()->url.'assets/css/classroom-frontend.css');
		wp_enqueue_script('tutor-gc-frontend-js', TUTOR_GC()->url.'assets/js/classroom-frontend.js', [], null, true);
	}

	public function register_metabox( $post_type=null, $post=null ){
		
		if($post && (new Classroom(null, null, true))->is_google_class($post->ID)){
			$coursePostType = tutor()->course_post_type;
			add_meta_box( 'tutor-gc-course-connection-side', __( 'Connect Tutor Course', 'tutor-pro' ), array($this, 'course_connection_metabox'), $coursePostType, 'side' );
		}
	}

	public function save_course_meta($post_ID) {
		$additional_data_edit = tutils()->avalue_dot('_tutor_course_additional_data_edit', $_POST);
		
		if ($additional_data_edit) {

			foreach($this->gc_metabox as $key=>$title){
				
				$value = isset($_POST[$key]) ? 'yes' : 'no';
				update_post_meta($post_ID, $key, $value);
			}
		}
	}

	public function course_connection_metabox($post){

		foreach($this->gc_metabox as $key=>$title){
		
			$checked = get_post_meta($post->ID, $key, true);
			$checked = (empty($checked) || $checked=='yes') ? 'checked="checked"' : '';
			
			?>
				<div class="tutor-course-sidebar-settings-item">
					<label for="<?php echo $key; ?>">
						<input id="<?php echo $key; ?>" type="checkbox" name="<?php echo $key; ?>" value="yes" <?php echo $checked; ?> />
						<?php echo __($title, 'tutor-pro'); ?>
					</label>
				</div>
			<?php
		}
	}

	
	public function course_connection_metabox_frontend( $post ){
		
		$post_id = is_object($post) ? ($post->ID ? $post->ID : 0) : 0;

		if(!(new Classroom(null, null, true))->is_google_class($post_id)){
			// Make sure it is imported google class
			return;
		}

		?>		
			<div class="tutor-course-builder-section tutor-course-builder-info">
				<div class="tutor-course-builder-section-title">
					<h3>
						<i class="tutor-icon-down"></i>
						<span>
							<?php esc_html_e('Connect Tutor Course', 'tutor-pro'); ?>
						</span>
					</h3>
				</div>
				<div class="tutor-course-builder-section-content">
					<div class="tutor-frontend-builder-item-scope">
						<div class="tutor-form-group">
							<?php $this->course_connection_metabox($post); ?>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	public function add_sub_menu(){
		add_submenu_page('tutor', __('Google Classroom', 'tutor-pro'), __('Google Classroom', 'tutor-pro'), 'manage_tutor_instructor', $this->gc_dashboard_slug, array($this, 'admin_page_content'));
	}

	public function admin_page_content(){
		
		$classroom = new Classroom;
		$is_code_for_only_logged = $this->is_class_restricted();
		
		include TUTOR_GC()->path.'/views/classroom-dashboard.php';
	}

	private function is_class_restricted(){
		return get_option($this->gc_code_privilege)=='yes';
	}

	public function stream_tab($nav_menus){

		$course_id = get_the_ID();
		
		if((new Classroom(null, null, true))->is_google_class($course_id) && $this->is_stream_enabled($course_id)){
			$nav_menus[$this->gc_stream_slug]=__('Stream', 'tutor-pro');
		}
		
		return $nav_menus;
	}

	public function add_stream_nav_rewrite($items){
		$items[$this->gc_stream_slug] = __('Stream', 'tutor-pro');
		return $items;
	}

	private function is_stream_enabled($course_id=null){
		!$course_id ? $course_id=0 : 0;
		$value = get_post_meta($course_id, 'tutor_gc_enable_classroom_stream', true);
		return (empty($value) || $value=='yes');
	}

	private function is_stream_file_enabled($course_id){
		$value = get_post_meta($course_id, 'tutor_gc_show_stream_files', true);
		return (empty($value) || $value=='yes');
	}

	private function is_resource_file_enabled($course_id){
		$value = get_post_meta($course_id, 'tutor_gc_include_classroom_files', true);
		return (empty($value) || $value=='yes');
	}
	
	public function stream_tab_content($course_id=null){
		
		strtolower($_SERVER['REQUEST_METHOD'])!='get' ? tutils()->checking_nonce() : 0;

		if($this->is_stream_enabled($course_id)){
			
			$course_id = isset($_POST['course_id']) ? $_POST['course_id'] : $course_id;
			if(!$course_id){
				return;
			}
			
			$classroom = new Classroom(null, $course_id);
			$next_token = isset($_POST['next_token']) ? $_POST['next_token'] : null;
			
			$classroom_info = $classroom->get_remote_class($course_id);
			$_stream = $classroom->get_stream($course_id, $next_token);
			$classroom_stream = isset($_stream['announcements']) ? $_stream['announcements'] : array();
			$stream_next_token = isset($_stream['next_token']) ? $_stream['next_token'] : '';

			$show_stream_files = $this->is_stream_file_enabled($course_id);

			if(!isset($_POST['course_id'])){
				include dirname(__DIR__).'/views/components/stream.php';
				return;
			}
			
			ob_start();
			include dirname(__DIR__).'/views/components/stream-individual.php';
			$html = ob_get_clean();

			exit(json_encode(array('html'=>$html, 'next_token'=>$stream_next_token)));
		}
	}

	public function add_start_course_button(){
		$classroom_url = (new Classroom(null, null, true))->is_google_class(get_the_ID(), true);

		if($classroom_url){
			include dirname(__DIR__).'/views/components/start-class.php';
		}
	}

	public function load_gc_attachments(){
		$local_id = get_the_ID();
		
		if($this->is_resource_file_enabled($local_id)){
			
			$classroom = new Classroom(null, $local_id);
			$materials_array = $classroom->get_all_remote_attachments($local_id);
			include dirname(__DIR__).'/views/components/materials.php';
		}
	}


	public function save_credential(){
		tutils()->checking_nonce();
		
		if(isset($_FILES['credential'],$_FILES['credential']['error']) && $_FILES['credential']['error']==0){
			// Save credential file if exist and no error
			(new Classroom)->save_credential($_FILES['credential']);
		}
		
		header('Location: '.$this->gc_dashboard_url);
		exit;
	}

	public function reset_tutor_student_password(){
		
		if(!strpos($_SERVER['REQUEST_URI'], Classroom::$password_reset_base) || !isset($_GET['token'])){
			return;
		}

		if(is_user_logged_in()){
			wp_redirect(get_home_url());
			exit;
		}

		get_header();

		if(!(new Classroom)->is_reset_token_valid($_GET['token'])){
			?>
				<div style="text-align:center;margin:150px auto">
					Invalid Token or Password is already set.
				</div>
			<?php
		}
		else {
			include dirname(__DIR__).'/views/components/password-setup.php';
		}

		get_footer();
		exit;
	}

	public function save_token(){
		if(!strpos($_SERVER['REQUEST_URI'], self::$google_callback_string)){
			// It is Other page request
			return;
		}

		if(!isset($_GET['code'])){
			echo 'No token.';
		}
		else{
			(new Classroom)->save_token($_GET['code']);
			header('Location: '.$this->gc_dashboard_url);
		}

		exit;
	}

	public function dispatch_class_action(){
		tutils()->checking_nonce();

		$action = $_POST['action_name'];
		$local_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';

		if($action=='import'){
			$this->import_class();
			return;
		}

		if(!is_numeric($local_id)){
			return;
		}
		
		switch($action){
			case 'publish'	: wp_publish_post($local_id); break;
			case 'trash'	: wp_trash_post( $local_id ); break;
			case 'delete'	: wp_delete_post( $local_id, true ); break;
			case 'restore'	: wp_untrash_post( $local_id ); break;
		}
		
		$status = $action=='trash' ? 'trash' : get_post_field( 'post_status', $local_id);
		
		$response = $action=='delete' ? 
		array(
			'class_status' => 'not-imported',
			'status_text' => 'Not Imported'
		) : 
		array(
			'class_status' => $status,
			'status_text' => ucfirst($status)
		);

		exit(json_encode($response));
	}

	private function import_class(){
		
		$class_id = $_POST['class_id'];

		$enroll_student = (isset($_POST['enroll_student']) ? $_POST['enroll_student'] : '')=='yes';
		$local_id = (new Classroom)->import_class($class_id, $enroll_student);
		$is_valid = is_numeric($local_id);
		
		$status   = $is_valid ? get_post_field( 'post_status', $local_id) : '';

		$response = !$is_valid ? null : 
		array(
			'post_id' => $local_id,
			'edit_link' => get_edit_post_link($local_id, ''),
			'preview_link' => get_permalink($local_id),
			'class_status' => $status,
			'status_text' => ucfirst($status)
		);

		exit($response ? json_encode($response) : '');
	}

	public function set_classroom_code_privilege(){
		tutils()->checking_nonce();

		if(!isset($_POST['enabled']) || !in_array($_POST['enabled'], ['yes', 'no'])){
			return;
		}
		
		update_option($this->gc_code_privilege, $_POST['enabled']);
	}

	public function upgrade_credential(){
		(new Classroom)->upgrade_credential_serial();
	}

	public function set_student_password(){
		tutils()->checking_nonce();

		$token = isset($_POST['token']) ? $_POST['token'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';

		(new Classroom)->set_student_password($token, $password);
		
		exit;
	}

	public function tutor_gc_classes($attr=[]){

		$page = isset($_GET['class_page']) ? $_GET['class_page'] : 1;
		(!is_numeric($page) || $page<1) ? $page=1 : 0;

		$google_classes = (new Classroom)->get_imported_class_list($page);
		$is_class_restricted = $this->is_class_restricted();

		// Define responsive class
		$column = is_array($attr) ? (isset($attr['max-column']) ? $attr['max-column'] : 3) : 3;
		$column = is_numeric($column) ? (int)$column : 0;
		$column_class = '';

		switch($column){
			case 1	: $column_class='col-xs-12'; break;
			case 2 	: $column_class='col-xs-12 col-sm-6'; break;
			case 3 	: $column_class='col-xs-12 col-sm-6 col-md-4'; break;
			case 4 	: $column_class='col-xs-12 col-sm-6 col-md-4 col-lg-3'; break;
			case 6 	: $column_class='col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-2'; break;
			default	: $column_class='col-xs-12 col-sm-6 col-md-4';
		}
			
		wp_enqueue_style('tutor-gc-frontend-bootstrap', TUTOR_GC()->url.'assets/css/bootstrap-grid.min.css');

		ob_start();
		include dirname(__DIR__).'/views/components/class-list-shortcode.php';
		return ob_get_clean();
	}
}