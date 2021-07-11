<?php
	$logged_in_as = $classroom->get_who_logged_in();
?>

<div class="container-fluid">
    <div class="row tutor-gc-setting-container">
        <div class="col-12 col-xl-6">
            <div>
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="tutor-gc-setting-content">
                            <h3><?php echo __('Classroom List', 'tutor-pro'); ?></h3>
                            <p><?php echo __('Here is a list of Classrooms on your current Google account.', 'tutor-pro'); ?></p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6" style="text-align:right">
                        <div class="tutor-gc-setting-content">
                            <button id="tutor_gc_credential_upgrade" data-message="<?php echo __('Sure to use another account?', 'tutor-pro'); ?>">
                                <?php _e('Use Another Account', 'tutor-pro'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="tutor-gc-setting-content">
                            <?php echo __('Google Classroom Account', 'tutor-pro'); ?>: <b><?php echo $logged_in_as->emailAddress; ?></b>
                        </div>
                    </div>
                    <div class="col-12 col-md-6" style="text-align:right">
                        <div class="tutor-gc-setting-content">
                            <?php _e('Classlist Shortcode:', 'tutor-pro'); ?> <span><b>[tutor_gc_classes]</b> <span class="tutor-icon-copy tutor-gc-copy-text" data-text="[tutor_gc_classes]"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div>
                <div class="tutor-gc-setting-content">
                    <h3><?php echo __('Classroom Access Settings', 'tutor-pro'); ?></h3>
                    <p><?php echo __('Control the visibility and privacy for the Google Classroom data', 'tutor-pro'); ?></p>
                </div>
                <hr/>
                <div class="tutor-gc-setting-content">
                    <label class="switch">
                        <input type="checkbox" id="tutor_gc_classroom_code_privilege" <?php echo $is_code_for_only_logged ? 'checked="checked"' : ''; ?>>
                        <span class="slider round"></span>
                    </label>

                    &nbsp; <?php echo __('Only logged in students can see the classroom invite code', 'tutor-pro'); ?>
                </div>
            </div>
        </div>
    </div>
</div>