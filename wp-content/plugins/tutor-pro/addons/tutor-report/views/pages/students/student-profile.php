<?php
if ( ! defined( 'ABSPATH' ) )
exit;
?>

<?php $user_info = get_userdata($_student); ?>
<div class="report-student-profile">
    <div class="report-student-profile-wrap">
        <div class="profile">
            <div class="thumb">
                <img src="<?php echo get_avatar_url($user_info->ID, array('size' => 90)); ?>" alt="<?php _e('tutor student profile photo', 'tutor-pro'); ?>">
            </div>
            <div>
                <div class="name"><?php echo $user_info->display_name; ?></div>
                <div class="meta">
                    <div class="date"><?php _e('Created:', 'tutor-pro'); ?> <span><?php echo date('j M, Y. h:i a', strtotime($user_info->user_registered)); ?></span></div>
                    <?php $last_time = get_user_meta($user_info->ID, 'wc_last_active', true); ?>
                    <div class="activity"><?php _e('Last Activity:', 'tutor-pro'); ?> 
                        <span>
                            <?php
                                if ($last_time) {
                                    echo human_time_diff( $last_time, current_time( 'timestamp', 1 ) ).' '.__('Ago');
                                } else {
                                    _e('Never Login Before', 'tutor-pro');
                                }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="show-profile">
                <a class="tutor-report-btn default" target="_blank" href="<?php echo tutor_utils()->profile_url($user_info->ID) ?>" class="btn show-profile-btn"><?php _e('View Profile', 'tutor-pro'); ?></a>
            </div></div>
        <div class="profile-table">
            <table>
                <tbody>
                    <tr>
                        <th>
                            <div><span><?php _e('Display Name', 'tutor-pro'); ?></span> <br> <?php echo $user_info->display_name; ?></div>
                        </th>
                        <th>
                            <div><span><?php _e('User Name', 'tutor-pro'); ?></span> <br> <?php echo $user_info->user_login; ?></div>
                        </th>
                        <th>
                            <div><span><?php _e('Email ID', 'tutor-pro'); ?></span> <br> <?php echo $user_info->user_email; ?> <a href="mailto:<?php echo $user_info->user_email;?>"><i class="tutor-icon-detail-link"></i></a></div>
                        </th>
                        <th>
                            <div><span><?php _e('User ID', 'tutor-pro'); ?></span> <br><?php echo $user_info->ID;?></div>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- /.report-student-profile -->

<!-- .report-stats -->
<div class="report-stats">
    <div class="report-stat-box">
        <div class="report-stat-box-body">
            <div class="box-icon"><i class="tutor-icon-mortarboard"></i></div>
            <div class="box-stats-text">
                <h3>
                    <?php 
                        $enrolled_course = tutor_utils()->get_enrolled_courses_by_user($user_info->ID);
                        echo $enrolled_course->found_posts;
                    ?></h3>
                <p><?php _e('Enrolled Courses', 'tutor-pro'); ?></p>
            </div>
        </div>
    </div>

    <div class="report-stat-box">
        <div class="report-stat-box-body">
            <div class="box-icon"><i class="tutor-icon-graduate"></i></div>
            <div class="box-stats-text">
                <h3>
                    <?php 
                        $completed_course = tutor_utils()->get_completed_courses_ids_by_user($user_info->ID);
                        echo count($completed_course);
                    ?>
                </h3>
                <p><?php _e('Completed Courses', 'tutor-pro'); ?></p>
            </div>
        </div>
    </div>

    <div class="report-stat-box">
        <div class="report-stat-box-body">
            <div class="box-icon"><i class="tutor-icon-open-book-1"></i></div>
            <div class="box-stats-text">
                <h3><?php echo ($enrolled_course->found_posts - count($completed_course)); ?></h3>
                <p><?php _e('Course Continue', 'tutor-pro'); ?></p>
            </div>
        </div>
    </div>

    <div class="report-stat-box">
        <div class="report-stat-box-body">
            <div class="box-icon"><i class="tutor-icon-review"></i></div>
            <div class="box-stats-text">
                <h3>
                    <?php
                        $review_items = count(tutor_utils()->get_reviews_by_user($user_info->ID));
                        echo $review_items;
                    ?>
                </h3>
                <p><?php _e('Reviews Placed', 'tutor-pro'); ?></p>
            </div>
        </div>
    </div>

    <div class="report-stat-box">
        <div class="report-stat-box-body">
            <div class="box-icon"><i class="tutor-icon-clipboard"></i></div>
            <div class="box-stats-text">
                <h3>
                    <?php
                    $lesson = 0;
                    $courses_id = tutor_utils()->get_enrolled_courses_ids_by_user($user_info->ID);
                    foreach ($courses_id as $course) {
                        $lesson += tutor_utils()->get_lesson_count_by_course($course);
                    }
                    echo $lesson;
                    ?>
                </h3>
                <p><?php _e('Total Lesson', 'tutor-pro'); ?></p>
            </div>
        </div>
    </div>

    <div class="report-stat-box">
        <div class="report-stat-box-body">
            <div class="box-icon"><i class="tutor-icon-professor"></i></div>
            <div class="box-stats-text">
                <h3><?php echo tutor_utils()->get_total_quiz_attempts($user_info->user_email); ?></h3>
                <p><?php _e('Take Quiz', 'tutor-pro'); ?></p>
            </div>
        </div>
    </div>

    <div class="report-stat-box">
        <div class="report-stat-box-body">
            <div class="box-icon"><i class="tutor-icon-student"></i></div>
            <div class="box-stats-text">
                <h3>
                    <?php
                        global $wpdb;
                        $total_assignments = 0;
                        if (!empty($courses_id)) {
                            $str_course = implode(',', $courses_id);
                            $total_assignments = $wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->postmeta} post_meta
                                    INNER JOIN {$wpdb->posts} assignment ON post_meta.post_id = assignment.ID AND post_meta.meta_key = '_tutor_course_id_for_assignments'
                                    where post_type = 'tutor_assignments' AND post_meta.meta_value IN ({$str_course}) ORDER BY ID DESC ");	
                        }
                        echo $total_assignments;
                    ?>
                </h3>
                <p><?php _e('Assignment', 'tutor-pro'); ?></p>
            </div>
        </div>
    </div>

    <div class="report-stat-box">
        <div class="report-stat-box-body">
            <div class="box-icon"><i class="tutor-icon-conversation-1"></i></div>
            <div class="box-stats-text">
                <h3>
                <?php
                global $wpdb;
                $total_discussion = $wpdb->get_var($wpdb->prepare("SELECT COUNT(comment_ID) FROM {$wpdb->comments}
                    WHERE comment_author = %s AND comment_type = 'tutor_q_and_a'", $user_info->user_login));	
                echo $total_discussion;
                ?>
                </h3>
                <p><?php _e('Total Discussion', 'tutor-pro'); ?></p>
            </div>
        </div>
    </div>
</div>
<!-- /.report-stats -->

<!-- .report-course-list -->
<div class="tutor-list-wrap report-course-list">
    <div class="tutor-list-header report-course-list-header">
        <div class="heading"><?php _e('Course List', 'tutor-pro'); ?></div>
        <div class="status">
            <span class="complete"><?php _e('Complete', 'tutor-pro'); ?></span>
            <span class="incomplete"><?php _e('Incomplete', 'tutor-pro'); ?></span>
        </div>
    </div>
    <div class="report-course-list-wrap">
        <table class="tutor-list-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php _e('Course', 'tutor-pro'); ?></th>
                    <th><?php _e('Enroll Date & Time', 'tutor-pro'); ?></th>
                    <th><?php _e('Lesson', 'tutor-pro'); ?></th>
                    <th><?php _e('Quiz', 'tutor-pro'); ?></th>
                    <th><?php _e('Assignment', 'tutor-pro'); ?></th>
                    <th><?php _e('Percentage', 'tutor-pro'); ?></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 0;
                if ($enrolled_course && is_array($enrolled_course->posts) && count($enrolled_course->posts)){
                    foreach ($enrolled_course->posts as $course){ $counter++;
                        ?>
                        <tr>
                            <td><?php echo $counter; ?></td>
                            <td><?php echo get_the_title($course->ID); ?> <a href="<?php echo get_the_permalink($course->ID); ?>" target="_blank" class="course-link"><i class="tutor-icon-detail-link"></i></a></td>
                            <td><?php echo date('F j Y g:i a', strtotime($course->post_date)); ?></td>
                            <td>
                                <span class="complete">
                                    <?php 
                                        $total_lesson = tutor_utils()->get_lesson($course->ID, -1);
                                        echo $total_lesson->post_count;
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="complete">
                                    <?php
                                    $total_quiz = (array) $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_type='tutor_quiz' AND post_status = 'publish' AND post_parent IN (SELECT ID FROM {$wpdb->posts} WHERE post_type='topics' AND post_parent = {$course->ID} AND post_status = 'publish')");	
                                    echo count($total_quiz);
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="complete">
                                    <?php
                                    $total_assignment = (array) $wpdb->get_results("SELECT ID FROM {$wpdb->postmeta} post_meta
                                        INNER JOIN {$wpdb->posts} assignment ON post_meta.post_id = assignment.ID AND post_meta.meta_key = '_tutor_course_id_for_assignments'
                                        where post_type = 'tutor_assignments' AND post_meta.meta_value IN ({$course->ID}) ORDER BY ID DESC ");	
                                    echo count($total_assignment);
                                    ?>
                                </span>
                            </td>
                            <?php $completed_percent = tutor_utils()->get_course_completed_percent($course->ID, $user_info->ID); ?>
                            <td><div class="course-percentage" style="--percent: <?php echo $completed_percent; ?>%;"></div></td>
                            <td><?php echo $completed_percent; ?>%</td>
                            <td><a href="#" class="details-link" data-count="<?php echo $counter; ?>"><i class="tutor-icon-angle-down"></i></a></td>
                        </tr>

                        <tr class="table-toggle" id="table-toggle-<?php echo $counter; ?>">
                        <!-- complete running incomplete -->
                            <td colspan="100%">
                                <div class="course-list-details">
                                    <?php if($total_lesson->post_count > 0) { ?>
                                        <div class="detail">
                                            <div class="heading"><?php _e('Lesson', 'tutor-pro'); ?></div>
                                            <div class="status">
                                                <?php 
                                                $count = count($total_lesson->posts) - 1;
                                                $posts_data = $total_lesson->posts;
                                                for($count; $count >= 0; $count--) { 
                                                    $is = tutor_utils()->is_completed_lesson($posts_data[$count]->ID, $user_info->ID);
                                                    ?>
                                                    <span class="<?php echo ($is ? 'complete' : 'incomplete'); ?>"><?php echo get_the_title($posts_data[$count]->ID); ?></span><br>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if(count($total_quiz) > 0) { ?>
                                        <div class="detail">
                                            <div class="heading"><?php _e('Quiz', 'tutor-pro'); ?></div>
                                            <div class="status">
                                                <?php
                                                foreach ($total_quiz as $value) { ?>
                                                    <span class="complete"><?php echo get_the_title($value->ID); ?></span><br>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if(count($total_assignment) > 0) { ?>
                                        <div class="detail">
                                            <div class="heading"><?php _e('Assignment', 'tutor-pro'); ?></div>
                                            <div class="status">
                                                <?php foreach ($total_assignment as $value) { ?>
                                                    <span class="complete"><?php echo get_the_title($value->ID); ?></span><br>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>

                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<!-- /.report-course-list -->

<!-- .report-review -->
<div class="tutor-list-wrap report-review">
    <div class="tutor-list-header">
        <div class="heading"><?php _e('Review', 'tutor-pro'); ?></div>
    </div>
    <div class="report-review-wrap">
        <?php
            $count = 0;
            $per_review = 10;
            $review_page = isset( $_GET['rp'] ) ? $_GET['rp'] : 0;
            $review_start =  max( 0,($review_page-1)*$per_review );
            $total_reviews = tutor_utils()->get_reviews_by_user($user_info->ID, $review_start, $per_review);

        if (!empty($total_reviews)) {
            ?>
            <table class="tutor-list-table">
                <thead>
                    <tr>
                        <th><?php _e('No', 'tutor-pro'); ?></th>
                        <th><?php _e('Course', 'tutor-pro'); ?></th>
                        <th><?php _e('Date', 'tutor-pro'); ?></th>
                        <th><?php _e('Rating & Feedback', 'tutor-pro'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($total_reviews as $review) { $count++; ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><div class="course-title"><?php echo get_the_title($review->comment_post_ID); ?></div></td>
                            <td><div class="dates"><?php echo date('j M, Y', strtotime($review->comment_date)); ?><br><span><?php echo date('h:i a', strtotime($review->comment_date)); ?></span></div></td>
                            <td>
                                <div class="ratings-wrap">
                                    <div class="ratings">
                                        <?php tutor_utils()->star_rating_generator($review->rating); ?>
                                        <span><?php echo $review->rating; ?></span>
                                    </div>
                                    <div class="review"><?php echo $review->comment_content; ?></div>
                                </div>							
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="no-data-found">
                <img src="<?php echo tutor_pro()->url."addons/tutor-report/assets/images/empty-data.svg"?>" alt="">
                <span><?php _e('No Review Data Found!', 'tutor-pro'); ?></span>
            </div>
        <?php } ?>
    </div>
    <div class="tutor-list-footer ">
        <div class="tutor-report-count">
            <div class="tutor-report-count">
                <?php
                    if($review_items > 0){ 
                        printf(__('Items <strong> %s </strong> of<strong> %s </strong> total','tutor-pro'), count($total_reviews), $review_items);
                    }
                ?>
            </div>
        </div>
        <div class="tutor-pagination">
            <?php
                echo paginate_links( array(
                    'base' => str_replace( $review_page, '%#%', "admin.php?page=tutor_report&sub_page=students&student_id=".$user_info->ID."&rp=%#%" ),
                    'current' => max( 1, $review_page ),
                    'total' => ceil($review_items/$per_review)
                ) );
            ?>
        </div>
    </div>
</div>
<!-- /.report-review -->