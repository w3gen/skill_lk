<?php
$enrolmentList = new \TUTOR_ENROLLMENTS\Enrollments_List();
$enrolmentList->prepare_items();
?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e('Enrollments', 'tutor-pro'); ?></h1>
    <a href="?page=enrollments&sub_page=enroll_student" class="page-title-action"><?php _e('Enroll a student', 'tutor-pro'); ?></a>
    <hr class="wp-header-end">

    <div class="tnotice tnotice--blue">
        <div class="tnotice__icon">&iexcl;</div>
        <div class="tnotice__content">
            <p class="tnotice__type"><?php _e('Info', 'tutor-pro'); ?></p>
            <p class="tnotice__message"><?php _e('You can search enrollments with enrol id, enrolment name, enrolment email, course title', 'tutor-pro'); ?>.</p>
        </div>
        <!--<div class="tnotice__close">
            &times;
        </div>-->
    </div>

    <form id="enrollments-filter" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php
		$enrolmentList->search_box(__('Search', 'tutor-pro'), 'enrollments');
		$enrolmentList->display(); ?>
	</form>
</div>