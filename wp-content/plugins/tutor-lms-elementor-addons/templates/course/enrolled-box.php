<?php
global $wp_query;
?>
<div class="tutor-course-enrollment-box">
    <div class="tutor-lead-info-btn-group">
        <?php do_action('tutor_course/single/actions_btn_group/before'); ?>

        <?php
        if (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] !== 'lesson') {
            $lesson_url = tutor_utils()->get_course_first_lesson();
            $completed_lessons = tutor_utils()->get_completed_lesson_count_by_course();
            if ($editor_mode || $lesson_url) { ?>
                <a href="<?php echo $lesson_url; ?>" class="tutor-button tutor-success">
                    <?php
                    if ($completed_lessons) {
                        _e('Continue to lesson', 'tutor-lms-elementor-addons');
                    } else {
                        _e('Start Course', 'tutor-lms-elementor-addons');
                    }
                    ?>
                </a>
        <?php }
        }
        ?>
        <?php tutor_course_mark_complete_html(); ?>

        <?php do_action('tutor_course/single/actions_btn_group/after'); ?>
    </div>

    <div class="tutor-single-course-segment tutor-course-enrolled-wrap">
        <p>
            <i class="tutor-icon-purchase"></i>
            <?php _e('You have been enrolled on', 'tutor-lms-elementor-addons'); ?>
            <span>
                <?php
                $enrolled = tutor_utils()->is_enrolled();
                $enrolled_date = ($editor_mode) ? date('Y-m-d') : @$enrolled->post_date;
                echo date_i18n(get_option('date_format'), strtotime($enrolled_date));
                ?>
            </span>
        </p>
        <?php do_action('tutor_enrolled_box_after') ?>

    </div>

</div> <!-- tutor-price-preview-box -->