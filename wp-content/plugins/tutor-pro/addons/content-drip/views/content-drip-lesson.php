<?php

$post_id = get_the_ID();

/**
 * define vars first
 * prevent undefined error
 * @since 1.8.9
*/
$lesson_id		= 0;
$quiz_id		= 0;
$assignment_id	= 0;

if( count($_POST) > 0 ) {
	$lesson_id = tutils()->array_get('lesson_id', $_POST);
	$quiz_id = tutils()->array_get('quiz_id', $_POST);
	$assignment_id = tutils()->array_get('assignment_id', $_POST);
} else {
	
	/**
	 * retrieve post 
	 * if not null set lesson id
	 * @since 1.8.9
	*/
	$post = get_post($post_id);
	if( !is_null($post) ) {
		if( $post->post_type == tutor()->lesson_post_type ) {
			$lesson_id = $post->ID;
		}
	}
}



$course_item_id = 0;
if ($lesson_id){
	$course_item_id = $lesson_id;
}elseif ($quiz_id){
	$course_item_id = $quiz_id;
}elseif ($assignment_id){
	$course_item_id = $assignment_id;
}

if ( $course_item_id){
	$post_id = (int) sanitize_text_field($course_item_id);
}

/**
 * check for $_POST
 * if not set item then get course id from utils
 * by lesson id
 * @since 1.8.9
*/
$course_id = 0;
if( count($_POST) > 0 ) {
	$course_id = (int) sanitize_text_field(tutils()->array_get('course_id', $_POST));
} else {
	$course_id = tutils()->get_course_id_by_lesson( $lesson_id );
}

$enable_content_drip = get_tutor_course_settings($course_id, 'enable_content_drip');
if ( ! $enable_content_drip){
	return;
}
$content_drip_type = get_tutor_course_settings($course_id, 'content_drip_type');
if ($content_drip_type === 'unlock_sequentially'){
    return;
}
?>

<div class="lesson-content-drip-wrap">

	<!--if empty $_POST then it is meta box dont need to show title twice 
		since meta box has its own title-->
		
    <?php if( count($_POST) > 0 ): ?>
    	<h3><?php _e('Content Drip Settings', 'tutor-pro'); ?></h3>
   	<?php endif;?> 	

	<?php
	if ($content_drip_type === 'unlock_by_date'){
		$unlock_date = get_item_content_drip_settings($course_item_id, 'unlock_date');
		?>
        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label">
                <label for=""><?php _e('Lesson unlocking date:', 'tutor-pro'); ?></label>
            </div>
            <div class="tutor-option-field">
                <input type="text" value="<?php echo $unlock_date; ?>" name="content_drip_settings[unlock_date]" class="tutor_date_picker">
                <p class="desc"><?php _e('Date Format:', 'tutor-pro'); ?> <code>yyyy-mm-dd</code> </p>
            </div>
        </div>
		<?php
	}elseif ($content_drip_type === 'specific_days'){
		$days = get_item_content_drip_settings($course_item_id, 'after_xdays_of_enroll', 7);
		?>
        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label">
                <label for=""><?php _e('Days', 'tutor-pro'); ?></label>
            </div>
            <div class="tutor-option-field">
                <input type="number" value="<?php echo $days; ?>" name="content_drip_settings[after_xdays_of_enroll]">
                <p class="desc"><?php _e('This lesson will be available after the given number of days.', 'tutor-pro'); ?> </p>
            </div>
        </div>

		<?php
	}elseif($content_drip_type === 'after_finishing_prerequisites'){
		$prerequisites = (array) get_item_content_drip_settings($course_item_id, 'prerequisites');
		$query_topics = tutor_utils()->get_topics($course_id);

		if (tutils()->count($query_topics->posts)){
			?>
            <div class="tutor-option-field-row">
                <div class="tutor-option-field-label">
                    <label for=""><?php _e('Prerequisites', 'tutor-pro'); ?></label>
                </div>
                <div class="tutor-option-field">
                    <select name="content_drip_settings[prerequisites][]" multiple="multiple" class="select2_multiselect">
                        <option value=""><?php _e('Select prerequisites item', 'tutor-pro'); ?></option>
						<?php
						foreach ($query_topics->posts as $topic){
							echo "<optgroup label='{$topic->post_title}'>";
							$topic_items = tutor_utils()->get_course_contents_by_topic($topic->ID, -1);
							foreach ($topic_items->posts as $topic_item){
							    if ($topic_item->ID != $course_item_id){

							        $isSelected = '';
							        if (in_array($topic_item->ID, $prerequisites)){
								        $isSelected = 'selected="selected"';
                                    }

								    echo "<option value='{$topic_item->ID}' {$isSelected} >{$topic_item->post_title}</option>";
							    }
							}
							echo "</optgroup>";
						}
						?>
                    </select>
                    <p class="desc"><?php _e('Select items that should be complete before this item', 'tutor-pro'); ?> </p>
                </div>
            </div>
			<?php
		}
	}
	?>
</div>