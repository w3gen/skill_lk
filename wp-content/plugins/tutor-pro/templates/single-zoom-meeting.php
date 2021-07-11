<?php
/**
 * Template for displaying single live meeting page
 *
 * @since v.1.7.1
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.7.1
 */

get_tutor_header();

global $post;
$currentPost    = $post;
$zoom_meeting   = tutor_zoom_meeting_data($post->ID);
$meeting_data   = $zoom_meeting->data;
$browser_url    = "https://us04web.zoom.us/wc/join/{$meeting_data['id']}?wpk={$meeting_data['encrypted_password']}";
$browser_text   = __('Join in Browser', 'tutor-pro');

if (get_current_user_id() == $post->post_author) {
    $browser_url    = $meeting_data['start_url'];
    $browser_text   = __('Start Meeting', 'tutor-pro');
}

$enable_spotlight_mode = tutor_utils()->get_option('enable_spotlight_mode');

$show_error = apply_filters( 'tutor_zoom/single/content', null );
?>

<?php do_action('tutor_zoom/single/before/wrap'); ?>
    <div class="tutor-single-lesson-wrap <?php echo $enable_spotlight_mode ? "tutor-spotlight-mode" : ""; ?>">
        <div class="tutor-lesson-sidebar">
			<?php tutor_lessons_sidebar(); ?>
        </div>
        <div id="tutor-single-entry-content" class="tutor-lesson-content tutor-single-entry-content tutor-single-entry-content-<?php the_ID(); ?>">
            <div class="tutor-single-page-top-bar">
                <div class="tutor-topbar-item tutor-hide-sidebar-bar">
                    <a href="javascript:;" class="tutor-lesson-sidebar-hide-bar"><i class="tutor-icon-angle-left"></i> </a>
                    <?php $course_id = get_post_meta($post->ID, '_tutor_zm_for_course', true); ?>
                    <a href="<?php echo get_the_permalink($course_id); ?>" class="tutor-topbar-home-btn">
                        <i class="tutor-icon-home"></i> <?php echo __('Go to Course Home', 'tutor-pro') ; ?>
                    </a>
                </div>
                <div class="tutor-topbar-item tutor-topbar-content-title-wrap">
                    <?php
                    tutor_utils()->get_lesson_type_icon(get_the_ID(), true, true);
                    echo $post->post_title; ?>
                </div>

                <div class="tutor-topbar-item tutor-topbar-mark-to-done">
                    <?php tutor_lesson_mark_complete_html(); ?>
                </div>
            </div>

            <?php if( $show_error ): ?>

            <?php echo $show_error ?>

            <?php else : ?>

            <div class="tutor-zoom-meeting-content">
                <?php if ($zoom_meeting->is_expired) { ?>
                    <div class="tutor-zoom-meeting-expired-msg-wrap">
                        <h2 class="meeting-title"><?php echo $post->post_title; ?></h2>
                        <div class="msg-expired-section">
                            <img src="<?php echo TUTOR_ZOOM()->url.'assets/images/zoom-icon-expired.png'; ?>" alt="" />
                            <div>
                                <h3><?php _e('The video conference has expired', 'tutor-pro'); ?></h3>
                                <p><?php _e('Please contact your instructor for further information', 'tutor-pro'); ?></p>
                            </div>
                        </div>
                        <div class="meeting-details-section">
                            <p><?php echo $post->post_content; ?></p>
                            <div>
                                <div>
                                    <span><?php _e('Meeting Date', 'tutor-pro'); ?>:</span>
                                    <p><?php echo $zoom_meeting->start_date; ?></p>
                                </div>
                                <div>
                                    <span><?php _e('Host Email', 'tutor-pro'); ?>:</span>
                                    <p><?php echo $meeting_data['host_email']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                } else { ?>
                    <div class="zoom-meeting-countdown-wrap">
                        <p><?php _e('Meeting Starts in', 'tutor-pro'); ?></p>
                        <div class="tutor-zoom-meeting-countdown" data-timer="<?php echo $zoom_meeting->countdown_date; ?>" data-timezone="<?php echo $zoom_meeting->timezone; ?>"></div>
                        <div class="tutor-zoom-join-button-wrap">
                            <a href="<?php echo $browser_url; ?>" target="_blank" class="tutor-btn tutor-button-block"><?php echo $browser_text; ?></a>
                            <a href="<?php echo $meeting_data['join_url']; ?>" target="_blank" class="tutor-btn bordered-btn tutor-button-block"><?php _e('Join in Zoom App', 'tutor-pro'); ?></a>
                        </div>
                    </div>
                    <div class="zoom-meeting-content-wrap">
                        <h2 class="meeting-title"><?php echo $post->post_title; ?></h2>
                        <p class="meeting-summary"><?php echo $post->post_content; ?></p>
                        <div class="meeting-details">
                            <div>
                                <span><?php _e('Meeting Date', 'tutor-pro'); ?></span>
                                <p><?php echo $zoom_meeting->start_date; ?></p>
                            </div>
                            <div>
                                <span><?php _e('Meeting ID', 'tutor-pro'); ?></span>
                                <p><?php echo $meeting_data['id']; ?></p>
                            </div>
                            <div>
                                <span><?php _e('Password', 'tutor-pro'); ?></span>
                                <p><?php echo $meeting_data['password']; ?></p>
                            </div>
                            <div>
                                <span><?php _e('Host Email', 'tutor-pro'); ?></span>
                                <p><?php echo $meeting_data['host_email']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php
                } ?>
            </div>

            <?php endif ?>

        </div>
    </div>
<?php do_action('tutor_zoom/single/after/wrap');

get_tutor_footer();