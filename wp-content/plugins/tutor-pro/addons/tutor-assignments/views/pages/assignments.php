
<?php
$assignmentList = new \TUTOR_ASSIGNMENTS\Assignments_List();

/**
 * @since 1.8.0
 */
$selected_course = '';
$selected_order = 'DESC';
$selected_date = '';
$selected_search = '';
isset($_GET['course-id']) ? $selected_course = $_GET['course-id'] : '';
isset($_GET['order']) ? $selected_order = $_GET['order'] : '';
isset($_GET['date']) ? $selected_date = $_GET['date'] : '';
isset($_GET['search']) ? $selected_search = $_GET['search'] : '';

$assignmentList->prepare_items($selected_course, $selected_order, $selected_date, $selected_search);

?>
<div class="tutor-assignment-page-title">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
</div>
<div class="wrap">
	<div class="tutor-assignment-filter-box">
		<?php
			$assignmentList->search_box($selected_search,'');
			$assignmentList->sorting_date($selected_date);
			$assignmentList->sorting_order($selected_order);
			$assignmentList->course_dropdown($selected_course);
		?>
	</div>	

	<form id="assignments-filter" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $assignmentList->display(); ?>
	</form>
</div>