<?php
$course_id = get_the_ID();
$zoom_meetings = get_tutor_zoom_meetings(array(
    'course_id' => $course_id
));

$is_enrolled = tutor_utils()->is_enrolled($course_id);
$is_administrator = current_user_can('administrator');
$is_instructor = tutor_utils()->is_instructor_of_this_course();
$course_content_access = (bool) get_tutor_option('course_content_access_for_ia');

if ($is_enrolled || ($course_content_access && ($is_administrator || $is_instructor))) {
    if (!empty($zoom_meetings)) {
    ?>
        <div class="tutor-single-course-segment  tutor-course-topics-wrap">
            <div class="tutor-course-topics-header">
                <div class="tutor-course-topics-header-left">
                    <h4 class="tutor-segment-title"><?php _e('Zoom Live Meetings', 'tutor-pro'); ?></h4>
                </div>
            </div>
            <div class="tutor-course-topics-contents">
                <?php
                $index = 0;
                foreach ($zoom_meetings as $meeting) {
                    $zoom_meeting   = tutor_zoom_meeting_data($meeting->ID);
                    $meeting_data   = $zoom_meeting->data;
                ?>
                    <div class="tutor-course-topic tutor-zoom-meeting <?php echo ($index==0) ? 'tutor-active': ''; ?>">
                        <div class="tutor-course-title">
                            <div class="tutor-zoom-meeting-detail">
                                <h3>
                                    <?php echo $meeting->post_title; ?> 
                                    <?php if ($zoom_meeting->is_expired) {
                                        echo '<span class="tutor-zoom-label">'.__('Expired', 'tutor-pro').'</span>';
                                    } else if ($zoom_meeting->is_started) {
                                        echo '<span class="tutor-zoom-label tutor-zoom-live-label">'.__('Live', 'tutor-pro').'</span>';
                                    }
                                    ?>
                                </h3>
                                <div>
                                    <p><?php _e('ID', 'tutor-pro'); ?>: <span><?php echo $meeting_data['id']; ?></span> <i class="tutor-icon-copy"></i></p>
                                    <p><?php _e('Password', 'tutor-pro'); ?>: <span><?php echo $meeting_data['password']; ?></span> <i class="tutor-icon-copy"></i></p>
                                </div>
                            </div>
                            <div class="tutor-zoom-meeting-toggle-icon">
                                <i class="tutor-icon-angle-right"></i>
                            </div>
                        </div>
                        <div class="tutor-course-lessons tutor-zoom-meeting-session" style="display: <?php echo ($index==0) ? 'block': 'none'; ?>">
                            <?php if ($zoom_meeting->is_expired) { ?>
                                <div class="msg-expired-section">
                                    <img src="<?php echo TUTOR_ZOOM()->url.'assets/images/zoom-icon-expired.png'; ?>" alt="" />
                                    <div>
                                        <h3><?php _e('The video conference has expired', 'tutor-pro'); ?></h3>
                                        <p><?php _e('Please contact your instructor for further information', 'tutor-pro'); ?></p>
                                    </div>
                                </div>
                            <?php 
                            } else { ?>
                                <div class="tutor-zoom-meeting-countdown" data-timer="<?php echo $zoom_meeting->countdown_date; ?>" data-timezone="<?php echo $zoom_meeting->timezone; ?>">
                                </div>
                                <div class="session-link">
                                    <p><?php _e('Host Email', 'tutor-pro'); ?>: <?php echo $meeting_data['host_email']; ?></p>
                                    <a href="<?php echo get_permalink($meeting->ID); ?>" class="tutor-btn bordered-btn"><?php _e('Continue to Meeting', 'tutor-pro'); ?></a>
                                </div>
                            <?php
                            } ?>
                        </div>
                    </div>
                <?php
                $index++;
                }
                ?>
            </div>
        </div>
    <?php
    } 
} ?>