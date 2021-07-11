<?php
$user_id = get_current_user_id();
$zoom_meetings = get_tutor_zoom_meetings(array(
    'author'    =>  $user_id,
    'course_id' => $course_id
));

if (!empty($zoom_meetings)) {
?>
    <div class="tutor-zoom-meetings-metabox">
        <div class="zoom-meetings-heading">
            <h3><?php _e('Zoom Live Meetings', 'tutor-pro'); ?></h3>
        </div>
        <div class="zoom-meetings-container">
            <?php
            foreach ($zoom_meetings as $meeting) { 
                
                $tzm_start      = get_post_meta($meeting->ID, '_tutor_zm_start_datetime', true);
                $meeting_data   = get_post_meta($meeting->ID, $this->zoom_meeting_post_meta, true);
                $meeting_data   = json_decode($meeting_data, true);

                if(!$tzm_start) {
                    continue;
                }

                $input_date     = \DateTime::createFromFormat('Y-m-d H:i:s', $tzm_start);
                $start_date     = $input_date->format('j M, Y');
                $start_time     = $input_date->format('h:i A');
            ?>
                <div class="tutor-zoom-meeting-item">
                    <div class="start-time">
                        <span><?php _e('Start Time', 'tutor-pro'); ?></span>
                        <p><?php echo $start_date; ?> <span><?php echo $start_time; ?></span></p>
                    </div>
                    <div class="meeting-name">
                        <span><?php _e('Meeting Name', 'tutor-pro'); ?></span>
                        <p><?php echo $meeting->post_title; ?></p>
                    </div>
                    <div class="meeting-token">
                        <span><?php _e('Meeting ID', 'tutor-pro'); ?></span>
                        <p><?php echo $meeting_data['id']; ?></p>
                    </div>
                    <div class="meeting-pass">
                        <span><?php _e('Password', 'tutor-pro'); ?></span>
                        <p><?php echo $meeting_data['password']; ?></p>
                    </div>
                    <div class="meeting-action">
                        <a href="<?php echo $meeting_data['start_url']; ?>" class="tutor-btn bordered-btn" target="_blank"><img src="<?php echo TUTOR_ZOOM()->url . 'assets/images/zoom-icon.svg'; ?>" alt="Zoom" /> <?php _e('Start Meeting', 'tutor-pro'); ?></a>
                        <a href="javascript:void(0);" class="tutor-zoom-meeting-modal-open-btn edit" data-meeting-id="<?php echo $meeting->ID; ?>" data-topic-id="0" data-click-form="metabox"><i class="tutor-icon-pencil"></i></a>
                        <a href="javascript:void(0);" class="tutor-zoom-meeting-delete-btn delete" data-meeting-id="<?php echo $meeting->ID; ?>"><i class="tutor-icon-garbage"></i></a>
                    </div>
                </div>
            <?php } ?>
            <div class="zoom-icon-button">
                <a class="button button-primary tutor-zoom-meeting-modal-open-btn" data-meeting-id="0" data-topic-id="0" data-click-form="metabox"><img src="<?php echo TUTOR_ZOOM()->url . 'assets/images/meeting.svg'; ?>" alt="Zoom" /> <?php _e('Create a Zoom Meeting', 'tutor-pro'); ?></a>
            </div>
        </div>
    </div>
<?php 
} else { ?>
    <div class="tutor-zoom-create-meeting">
        <div class="zoom-icon">
            <img src="<?php echo TUTOR_ZOOM()->url . 'assets/images/zoom-icon.svg'; ?>" alt="Zoom" />
            <div><?php _e('Connect with your students using Zoom', 'tutor-pro'); ?></div>
        </div>
        <div class="zoom-icon-button">
            <a class="button button-primary tutor-zoom-meeting-modal-open-btn" data-meeting-id="0" data-topic-id="0" data-click-form="metabox"><img src="<?php echo TUTOR_ZOOM()->url . 'assets/images/meeting.svg'; ?>" alt="Zoom" /> <?php _e('Create a Zoom Meeting', 'tutor-pro'); ?></a>
        </div>
    </div>
<?php
} ?>