<?php
namespace TUTOR_ASSIGNMENTS;

if ( ! defined( 'ABSPATH' ) )
	exit;

if (! class_exists('Tutor_List_Table')){
	include_once tutor()->path.'classes/Tutor_List_Table.php';
}

class Assignments_List extends \Tutor_List_Table {

	function __construct(){
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'assignment',     //singular name of the listed records
			'plural'    => 'assignments',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		) );
	}

	function column_default($item, $column_name){
		switch($column_name){
			case 'user_email':
				return $item->$column_name;
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_mark($item){
		echo tutor_utils()->get_assignment_option($item->comment_post_ID, 'total_mark');
	}
	function column_passing_mark($item){
		echo tutor_utils()->get_assignment_option($item->comment_post_ID, 'pass_mark');
	}
	function column_student($item){
		echo '<img class="assignment-submitter-avatar" src="' , get_avatar_url( $item->user_id, [ 'size' => 20 ] ) , '"/>' , $item->comment_author;
	}
	function column_title($item){
		$post 		= get_post($item->comment_post_ID);
		$topic 		= get_post($post->post_parent);
		$course 	= get_post($topic->post_parent);

		echo '<a class="tutor-assignment-course-title" href="'.get_the_permalink($item->comment_post_ID).'" target="_blank">'.get_the_title($item->comment_post_ID).'</a>';
		echo'<div>
				<b>'.__('Course:','tutor-pro').'</b>
					<span> '.$course->post_title.' </span>	
			</div>';
	}
	function column_duration($item) {
		$value = tutor_utils()->get_assignment_option($item->comment_post_ID, 'time_duration.value');
		
		$time = tutor_utils()->get_assignment_option($item->comment_post_ID, 'time_duration.time');
		$time = trim($time, 's');

		echo $value ? ($value . ' ' . $time . ($value>1 ? 's' : '')) : __('No Limit', 'tutor-pro');
	}
	function column_date($item) {
		$format = get_option('date_format').' '.get_option('time_format');
		$deadline = tutor_utils()->get_assignment_deadline_date($item->comment_post_ID, $format);

		if($deadline) {
			echo '<p>';
			echo '<b>' . __('Deadline', 'tutor-pro') . ' : </b>';
			echo $deadline;
			echo '</p>';
		}
		
		echo '<p>';
		echo '<b>' . __('Started', 'tutor-pro') . ' : </b>';
		echo date($format, strtotime($item->comment_date_gmt)); //Submit Finished
		echo '</p>';
	}

	function column_action($item){
		$evaluated = get_comment_meta($item->comment_ID, 'assignment_mark', true);
		$button_text = $evaluated ? __('Details', 'tutor-pro') : __('Evaluate', 'tutor-pro');

		echo "<a href='".admin_url('admin.php?page=tutor-assignments&view_assignment='.$item->comment_ID)."' class='tutor-assignment-action button'>".$button_text."</a> ";
		echo '<a data-assignment_id="'. $item->comment_ID.'" data-assignment_action="delete" data-warning_message="'. __('Are you sure?', 'tutor-pro').'" href="#" class="tutor-assignment-action button button-delete"  data-toast_error="'.__('Error', 'tutor-pro').'" data-toast_error_message="'.__('Action Failed', 'tutor-pro').'" data-toast_success="'.__('Success', 'tutor-pro').'" data-toast_success_message="'.__('Deleted', 'tutor-pro').'">'.__(' Delete', 'tutor-pro').'</a>';
	}

	/**
	 * @param $item
	 *
	 * Completed Course by User
	 */
	function column_course($item){
		echo '<a href="'.get_the_permalink($item->comment_parent).'" target="_blank">'.get_the_title($item->comment_parent).'</a>';
	}

	function column_evaluated($item){
		$not_checked = get_comment_meta($item->comment_ID, 'assignment_mark', true)==='';
		echo $not_checked ? _e('No', 'tutor-pro') :  _e('Yes', 'tutor-pro');
	}

	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("assignment")
			/*$2%s*/ $item->comment_ID                //The value of the checkbox should be the record's id
		);
	}

	function get_columns(){
		$columns = array(
			'title'		   => __('Assignment Name', 'tutor-pro'),
			'student'      => __('Student', 'tutor-pro'),
			'mark'         => __('Total Points', 'tutor-pro'),
			'passing_mark' => __('Pass Points', 'tutor-pro'),
			'duration'     => __('Duration', 'tutor-pro'),
			'date'         => __('Date', 'tutor-pro'),
			'action'       => __('', 'tutor-pro'),
		);
		return $columns;
	}

	/**
	 * @since 1.8.0
	 * optional args for sorting
	 * result will be change for course wise sorting
	 */
	function prepare_items() {
		list($course_id, $order, $date, $search) = func_get_args();
		/**
		 * sanitize all fields
		 */
		$course_id 	= sanitize_text_field( $course_id );
		$order 		= sanitize_text_field( $order );
		$date 		= sanitize_text_field( $date );
		$search 	= sanitize_text_field( $search );

		global $wpdb;
		$per_page = 20;

		$search_term = '';

		$search !== '' ? $search_term =" AND (post.post_title LIKE '%{$search}%' OR user.display_name LIKE '%{$search}%') " : 0;

		$date !== '' ? $date = "  AND comment.comment_date like '%{$date}%' " : 0;
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);
		//$this->process_bulk_action();

		$current_page = $this->get_pagenum();
		$start = ($current_page-1)*$per_page;
		$total_items = 0;

		// Is current user instructor
		$is_instructor = !current_user_can('administrator') && current_user_can(tutor()->instructor_role);

		// Prepare course ID to show only own assignments to insrtuctor
		$courses_ids = $is_instructor ? tutor_utils()->get_assigned_courses_ids_by_instructors() : [];
		$in_courses_ids = tutor_utils()->count($courses_ids) ? "'".implode("','", $courses_ids)."'" : '';
		
		// Create base query including specific course ID if necessary
		$where_instructor = $is_instructor ? " AND comment.comment_parent IN({$in_courses_ids}) " : '';		
		$from_base_query = 
			"FROM {$wpdb->comments} comment LEFT JOIN {$wpdb->posts} post ON comment.comment_post_ID=post.ID 
			LEFT JOIN {$wpdb->users} user ON comment.user_id=user.ID
			WHERE comment.comment_type = 'tutor_assignment' 
				AND comment.comment_approved = 'submitted' 
				{$where_instructor} 
				{$search_term} 
				{$date}  
			ORDER BY comment.comment_ID {$order} ";
		
		// Get total count
		$total_items = $wpdb->get_var("SELECT COUNT(comment.comment_ID) {$from_base_query}");

		// Get submitted assignment

		$this->items = $wpdb->get_results("SELECT comment.* {$from_base_query} LIMIT {$start}, {$per_page} ");

		if($course_id){
			$date !== '' ? $date = "  AND c.comment_date like '%{$date}%' " : 0;

			$results =  $wpdb->get_results($wpdb->prepare(" SELECT c.* FROM {$wpdb->comments} as c INNER JOIN {$wpdb->posts} as assignment ON assignment.ID = c.comment_post_ID INNER JOIN {$wpdb->posts} as topic ON topic.ID = assignment.post_parent INNER JOIN {$wpdb->posts} as post ON post.ID = topic.post_parent WHERE post.ID = %d AND c.comment_type = 'tutor_assignment' AND c.comment_approved = 'submitted' {$where_instructor} {$search_term} {$date} ORDER BY c.comment_ID {$order} ",$course_id)) ;
			
			$this->items = $results;
		}
	
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
	}

	/**
	 * @since 1.8.0
	 * custom search box
	 * override default searchbox
	 */
	public function search_box($search,$id=''){
		
		$markup = '
			<div class="alignright assignment-search-box">
				<label>
					'.__('Search','tutor-pro').'
				</label>
				<input class="tutor-assignment-search-field" type="search" id="assignment-search" name="s" value="'.$search.'" >
				<i class="tutor-icon-magnifying-glass-1 tutor-assignment-search-sorting"></i>
			</div>';
		echo $markup;	
	} 

}