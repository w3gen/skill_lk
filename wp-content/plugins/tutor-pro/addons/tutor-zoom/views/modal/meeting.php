<?php
    $meeting_host = $this->get_users_options();
    $timezone_options = tutor_zoom_get_timezone_options();
?>
<form id="tutor-meeting-modal-form" class="tutor-meeting-modal-form">
    <input type="hidden" name="action" value="tutor_zoom_save_meeting">
    <input type="hidden" name="meeting_id" value="<?php echo $meeting_id; ?>">
    <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
    <input type="hidden" name="click_form" value="<?php echo $click_form; ?>">

    <div class="meeting-modal-form-wrap">
        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Meeting Host', 'tutor-pro'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <select name="meeting_host" class="meeting-host" required>
                        <?php foreach ($meeting_host as $id => $host) { ?>
                            <option value="<?php echo $id; ?>" <?php selected($host_id, $id); ?>><?php echo $host; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Meeting Name', 'tutor-pro'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <input type="text" name="meeting_title" value="<?php echo $title; ?>" placeholder="Enter Meeting Name" required>
                </div>
            </div>
        </div>

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Meeting Summary', 'tutor-pro'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <textarea type="text" name="meeting_summary" rows="4"><?php echo $summary; ?></textarea>
                </div>
            </div>
        </div>

        <div class="tutor-quiz-builder-group">
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col meeting-time">
                    <h4><?php _e('Meeting Time', 'tutor-pro'); ?></h4>
                    <div class="tutor-quiz-builder-row">
                        <div class="tutor-quiz-builder-col meeting-time-col">
                            <div class="date-range-input">
                                <input type="text" name="meeting_date" class="tutor_zoom_datepicker readonly" value="<?php echo $start_date; ?>" autocomplete="off" placeholder="<?php echo date('d/m/Y'); ?>" required>
                                <i class="tutor-icon-calendar"></i>
                            </div>
                        </div>
                        <div class="meeting-time-separator">-</div>
                        <div class="tutor-quiz-builder-col">
                            <div class="date-range-input">
                                <input type="text" name="meeting_time" class="tutor_zoom_timepicker readonly" value="<?php echo $start_time; ?>" autocomplete="off" placeholder="08:30 PM" required>
                                <i class="tutor-icon-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tutor-quiz-builder-col">
                    <h4><?php _e('Meeting Duration', 'tutor-pro'); ?></h4>
                    <div class="tutor-quiz-builder-row meeting-duration-row">
                        <div class="tutor-quiz-builder-col">
                            <input type="number" name="meeting_duration"  value="<?php echo $duration; ?>" autocomplete="off" placeholder="30" required>
                        </div>
                        <div class="tutor-quiz-builder-col">
                            <select name="meeting_duration_unit" required>
                                <option value="min" <?php selected($duration_unit, 'min'); ?>><?php _e('Minutes', 'tutor-pro'); ?></option>
                                <option value="hr" <?php selected($duration_unit, 'hr'); ?>><?php _e('Hours', 'tutor-pro'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tutor-quiz-builder-group">
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col meeting-time">
                    <h4><?php _e('Time Zone', 'tutor-pro'); ?></h4>
                    <div class="tutor-quiz-builder-row">
                        <div class="tutor-quiz-builder-col">
                            <select name="meeting_timezone" class="tutor_select2">
                            <?php foreach ($timezone_options as $id => $option) { ?>
                                <option value="<?php echo $id; ?>" <?php selected($timezone, $id); ?>><?php echo $option; ?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="tutor-quiz-builder-col">
                    <h4><?php _e('Auto Recording', 'tutor-pro'); ?></h4>
                    <div class="tutor-quiz-builder-row">
                        <div class="tutor-quiz-builder-col">
                            <select name="auto_recording">
                                <option value="none" <?php selected($auto_recording, 'none'); ?>><?php _e('No Recordings', 'tutor-pro'); ?></option>
                                <option value="local" <?php selected($auto_recording, 'local'); ?>><?php _e('Local', 'Hours', 'tutor-pro'); ?></option>
                                <option value="cloud" <?php selected($auto_recording, 'cloud'); ?>><?php _e('Cloud', 'Days', 'tutor-pro'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tutor-quiz-builder-group">
            <h4><?php _e('Password', 'tutor-pro'); ?></h4>
            <div class="tutor-quiz-builder-row">
                <div class="tutor-quiz-builder-col">
                    <div class="date-range-input">
                        <input type="text" name="meeting_password" value="<?php echo $password; ?>" autocomplete="off" placeholder="Create a Password" required>
                        <i class="tutor-icon-lock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="tutor-btn" data-toast_success="<?php _e('Success', 'tutor-pro'); ?>" data-toast_success_message="<?php _e('Meeting has been saved'); ?>" data-toast_error="<?php _e('Error', 'tutor-pro'); ?>" data-toast_error_message="<?php _e('Request Error', 'tutor-pro'); ?>"><?php _e('Save Meeting', 'tutor-pro'); ?></button>
    </div>
</form>

<script>
(function ($) {
})(jQuery);
</script>