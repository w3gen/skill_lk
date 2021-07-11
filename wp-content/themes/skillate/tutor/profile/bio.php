<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

$user_name = sanitize_text_field(get_query_var('tutor_student_username'));
$get_user = tutor_utils()->get_user_by_login($user_name);
$user_id = $get_user->ID;


$profile_bio = get_user_meta($user_id, '_tutor_profile_bio', true);
if ($profile_bio){
	?>
	<h3 class="instructor-single-title"><?php _e('About Me', 'skillate'); ?></h3>
	<div class="instructor-single-about">
		<?php echo wpautop($profile_bio); ?>
	</div>
	<h3 class="instructor-single-title">
		<?php echo tutor_utils()->get_course_count_by_instructor($user_id); ?>
		<?php echo esc_html__('Courses', 'skillate'); ?>
	</h3>
<?php } else{
    _e('Bio data is empty', 'skillate');
} ?>