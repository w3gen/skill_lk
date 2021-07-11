<?php
$groups = groups_get_groups(array('show_hidden' => true));
$attached_group = (array) \TUTOR_BP\BuddyPressGroups::get_group_ids_by_course(get_the_ID());
?>

<div class="tutor-option-field-row">
	<div class="tutor-option-field-label">
		<label for="">
			<?php _e('BuddyPress Groups', 'tutor-pro'); ?>
		</label>
	</div>

	<div class="tutor-option-field tutor-field-number">
		<select name="_tutor_bp_course_attached_groups[]" class="tutor_select2" multiple="multiple">
			<!--<option value="-1"><?php /*_e('Select groups', 'tutor-pro'); */?></option>-->
            <?php
            foreach ($groups['groups'] as $group){
                $selected = in_array($group->id, $attached_group) ? 'selected="selected"' : '';
                echo "<option value='{$group->id}' {$selected} > {$group->name} </option>";
            }
            ?>
		</select>

		<p class="desc"><?php _e('Assign this course to BuddyPress Groups', 'tutor-pro'); ?></p>
	</div>

</div>
