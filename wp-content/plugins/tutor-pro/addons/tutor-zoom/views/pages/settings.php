<?php
if (!defined('ABSPATH'))
    exit;

$zoom_settings_options = apply_filters('zoom_settings_options', array(
    'join_before_host' => array(
        'type'          => 'checkbox',
        'label'         => __('Join Before Host', 'tutor-pro'),
        'desc'          => __('Join meeting before the host starts the meeting. Only for scheduled or recurring meetings', 'tutor-pro'),
    ),
    'host_video' => array(
        'type'          => 'checkbox',
        'label'         => __('Host video', 'tutor-pro'),
        'desc'          => __('Host will join the meeting with video enabled', 'tutor-pro'),
    ),
    'participants_video' => array(
        'type'          => 'checkbox',
        'label'         => __('Participants video', 'tutor-pro'),
        'desc'          => __('Participant will join the meeting with video enabled', 'tutor-pro'),
    ),
    'mute_participants' => array(
        'type'          => 'checkbox',
        'label'         => __('Mute Participants', 'tutor-pro'),
        'desc'          => __('Participants will join the meeting with audio muted', 'tutor-pro'),
    ),
    'enforce_login' => array(
        'type'          => 'checkbox',
        'label'         => __('Enforce Login', 'tutor-pro'),
        'desc'          => __('Only users logged into Zoom App can join the meeting', 'tutor-pro'),
    ),
    'auto_recording' => array(
        'type'          => 'select',
        'label'         => __('Recording Settings', 'tutor-pro'),
        'options'         => array(
            'none'  => __('No Recordings', 'tutor-pro'),
            'local' => __('Local Drive', 'tutor-pro'),
            'cloud' => __('Zoom Cloud', 'tutor-pro'),
        ),
        'desc'          => __('Select Where You Want to Record', 'tutor-pro'),
    ),
));
?>

<div class="tutor-zoom-settings">
    <div class="tutor-zoom-page-title">
        <h3><?php _e('Settings', 'tutor-pro') ?></h3>
    </div>
    <form id="tutor-zoom-settings" action="">
        <input type="hidden" name="action" value="tutor_save_zoom_settings">
        <?php foreach ($zoom_settings_options as $key => $option) { ?>
            <div class="tutor-zoom-settings-card">
                <?php if ($option['type'] == 'checkbox') : ?>
                    <div class="card-icon">
                        <label class="btn-switch">
                            <input type="checkbox" class="hello" value="1" name="<?php echo $this->settings_key . '[' . $key . ']'; ?>" <?php checked($this->get_settings($key), '1'); ?>/>
                            <div class="btn-slider btn-round"></div>
                        </label>
                    </div>
                    <div class="card-content">
                        <p><?php echo $option['label']; ?></p>
                        <span><?php echo $option['desc']; ?></span>
                    </div>
                <?php elseif ($option['type'] == 'select') : ?>
                    <div class="card-content">
                        <p><?php echo $option['label']; ?></p>
                        <span><?php echo $option['desc']; ?></span>
                        <div class="card-radio">
                            <?php
                            $name = $this->settings_key . '[' . $key . ']';
                            foreach ($option['options'] as $optKey => $opt) {
                            ?>
                                <label class='single-radio'>
                                    <input type='radio' name="<?php echo $name; ?>" value="<?php echo $optKey; ?>" <?php checked($this->get_settings($key), $optKey); ?>/>
                                    <?php echo $opt; ?>
                                </label>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php } ?>
    </form>
</div>