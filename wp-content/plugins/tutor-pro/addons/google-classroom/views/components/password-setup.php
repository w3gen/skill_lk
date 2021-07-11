<div id="tutor_gc_student_password_set">
    <h4>Set Password</h4>

    <label>
        <?php echo __('Password', 'tutor-pro'); ?>
        <input type="password" class="regular-text" name="password-1"/>
    </label>
    
    <label>
        <?php echo __('Re-type Password', 'tutor-pro'); ?>
        <input type="password" class="regular-text" name="password-2"/>
    </label>
    
    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>"/>
    
    <div>
        <button class="button button-primary"> 
            <?php echo __('Set Password', 'tutor-pro'); ?>
        </button>
    </div>
</div>