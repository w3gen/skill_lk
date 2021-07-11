<div id="tutor_gc_dashboard">
    <h2><?php echo __('Google Classroom', 'tutor-pro'); ?></h2>

    <?php
        if(!$classroom->is_credential_loaded()){
            include 'components/import-credential.php';
        }
        else if(!$classroom->is_app_permitted()){
            include 'components/consent-screen.php';
        }
        else {
            include 'components/class-list.php';
            
            echo '<br/><br/><br/>';

            include 'components/footer-settings.php';
        }
    ?>
</div>