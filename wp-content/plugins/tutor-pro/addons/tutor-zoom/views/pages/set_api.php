<?php
if (!defined('ABSPATH'))
    exit;
?>
<div class="tutor-zoom-api-container">
    <form id="tutor-zoom-settings" action="">
        <input type="hidden" name="action" value="tutor_save_zoom_api">
        <div class="tutor-zoom-form-container">
            <div class="input-area">
                <h3><?php _e('Setup your Zoom Integration', 'tutor-pro'); ?></h3>
                <p><?php _e('Visit your Zoom account and fetch the API key to connect Zoom with your eLearning website. Create an app on Zoom by following this', 'tutor-pro'); ?><a href="https://marketplace.zoom.us/develop/create" target="_blank"> <?php _e('link', 'tutor-pro'); ?></a></p>
                <div class="tutor-form-group">
                    <label for="tutor_zoom_api_key"><?php _e('API Key', 'tutor-pro'); ?></label>
                    <input type="text" id="tutor_zoom_api_key" name="<?php echo $this->api_key; ?>[api_key]" value="<?php echo $this->get_api('api_key'); ?>" placeholder="<?php _e('Enter Your Zoom Api Key', 'tutor-pro'); ?>"/>
                </div>
                <div class="tutor-form-group">
                    <label for="tutor_zoom_api_secret"><?php _e('Secret Key', 'tutor-pro'); ?></label>
                    <input type="text" id="tutor_zoom_api_secret" name="<?php echo $this->api_key; ?>[api_secret]" value="<?php echo $this->get_api('api_secret'); ?>" placeholder="<?php _e('Enter Your Zoom Secret Key', 'tutor-pro'); ?>"/>
                </div>
                <div class="tutor-zoom-button-container">
                    <button type="submit" id="save-changes" class="button button-primary"><?php _e('Save Changes', 'tutor-pro'); ?></button>
                    <button type="button" id="check-zoom-api-connection" class="button"><?php _e('Check API Connection', 'tutor-pro'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>