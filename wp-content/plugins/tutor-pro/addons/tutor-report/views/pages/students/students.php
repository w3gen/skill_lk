<?php
if ( ! defined( 'ABSPATH' ) )
exit;
?>

<div class="tutor-report-student-data">
	<?php
	$_search = isset($_GET['search']) ? $_GET['search'] : '';
	$_student = isset($_GET['student_id']) ? $_GET['student_id'] : '';
	if(!$_student){
		include $view_page.$page."/student-table.php";
	} else {
		include $view_page.$page."/student-profile.php";
	} ?>
</div>