<div class="consent-screen google-consent-screen-redirect">
    <h3><?php echo __('Please complete the authorization process', 'tutor-pro'); ?></h3>
    <p><?php echo __('Press the button to grant permissions to your Google Classroom. Please allow all required permissions.', 'tutor-pro'); ?></p>
    
    <br/>
    <div>
        <img src="<?php echo TUTOR_GC()->url; ?>/assets/images/classroom.svg"/>
    </div>
    <br/>

    <a class="button button-primary button-large" href="<?php echo $classroom->get_consent_screen_url(); ?>">
        <?php echo __('Allow Permissions', 'tutor-pro'); ?>
    </a>
    <p>
        <a href="#" id="tutor_gc_credential_upgrade">
            <?php echo __('Change Credential', 'tutor-pro'); ?>
        </a>
    </p>
</div>