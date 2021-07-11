
<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Gradebooks', 'tutor-pro'); ?>  </h1>
	<?php
	tutor_alert(null, 'danger');
	tutor_alert(null, 'success');
	?>

    <hr class="wp-header-end">

    <form action="" id="add-gradebook-form" method="post">
        <input type="hidden" name="tutor_action" value="add_new_gradebook">
		<?php
		tutor_nonce_field();
		?>

		<?php do_action('tutor_add_new_instructor_form_fields_before'); ?>

        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label">
                <label for="">
					<?php _e('Grade Name', 'tutor-pro'); ?>
                    <span class="tutor-required-fields">*</span>
                </label>
            </div>
            <div class="tutor-option-field">
                <input type="text" name="grade_name" value="<?php echo tutor_utils()->input_old('grade_name'); ?>" placeholder="<?php _e('Grade Name',
					'tutor-pro'); ?>">
            </div>
        </div>

        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label">
                <label for="">
					<?php _e('Grade Point', 'tutor-pro'); ?>
                </label>
            </div>
            <div class="tutor-option-field">
                <input type="text" name="grade_point" value="<?php echo tutor_utils()->input_old('grade_point'); ?>" placeholder="<?php _e('Grade Point',
					'tutor-pro'); ?>">
            </div>
        </div>

        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label">
                <label for="">
					<?php _e('Minimum Grade Percentile', 'tutor-pro'); ?>
                    <span class="tutor-required-fields">*</span>
                </label>
            </div>
            <div class="tutor-option-field">
                <input type="text" name="percent_from" value="<?php echo tutor_utils()->input_old('percent_from'); ?>" placeholder="<?php _e('Minimum Grade Percentile', 'tutor-pro'); ?>">
            </div>
        </div>

        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label">
                <label for="">
					<?php _e('Maximum Grade Percentile', 'tutor-pro'); ?>
                    <span class="tutor-required-fields">*</span>
                </label>
            </div>
            <div class="tutor-option-field">
                <input type="text" name="percent_to" value="<?php echo tutor_utils()->input_old('percent_to'); ?>" placeholder="<?php _e('Maximum Grade Percentile', 'tutor-pro'); ?>">
            </div>
        </div>

        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label">
                <label for="">
					<?php _e('Grade Colour', 'tutor-pro'); ?>
                </label>
            </div>
            <div class="tutor-option-field">
                <input type="text" class="tutor_colorpicker" name="grade_config[grade_color]" value="<?php echo tutils()->input_old('grade_config.grade_color'); ?>" >
            </div>
        </div>

        <div class="tutor-option-field-row">
            <div class="tutor-option-field-label"></div>

            <div class="tutor-option-field">
                <div class="tutor-form-group tutor-reg-form-btn-wrap">
                    <button type="submit" name="tutor_add_gradebook_btn" value="register" class="tutor-button tutor-button-primary">
                        <i class="tutor-icon-plus-square-button"></i>
						<?php _e('Add new Grade', 'tutor-pro'); ?></button>
                </div>
            </div>
        </div>

    </form>


</div>
