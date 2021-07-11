<div class="consent-screen oauth-redirect-url">
    <?php
        echo sprintf(__('Create OAuth access data and upload Credentials JSON from %s Google Console %s. As a redirect URI set %s', 'tutor-pro'), '<a href="https://console.developers.google.com/" target="_blank"><b>', '</b></a>', '<b>'.get_home_url().'/'.\TUTOR_GC\init::$google_callback_string.'/</b>');
    ?>
</div>

<div class="consent-screen" id="tutor_gc_credential_upload">
    <div class="tutor-upload-area">
        <img src="<?php echo TUTOR_GC()->url; ?>/assets/images/upload-icon.svg"/>
        
        <h2><?php _e('Drag & Drop your JSON File here', 'tutor-pro'); ?></h2>

        <p><small><?php _e('or', 'tutor-pro'); ?></small></p>
        <button class="button button-primary"><?php _e('Browse File', 'tutor-pro'); ?></button>

        <input type="file" name="credential" accept=".json"/>
    </div>
    <button type="submit" class="button button-primary button-large" disabled="disabled">
        <?php _e('Load Credentials', 'tutor-pro'); ?> 
    </button>
</div>