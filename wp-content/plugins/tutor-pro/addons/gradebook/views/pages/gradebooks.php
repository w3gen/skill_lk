
<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Gradebook', 'tutor-pro'); ?>  </h1>
    <a href="<?php echo admin_url('admin.php?page=tutor_gradebook&sub_page=add_new_gradebook'); ?>" class="page-title-action"><i class="tutor-icon-plus"></i>
		<?php _e('Add New Grade', 'tutor-pro'); ?>
    </a>
    <hr class="wp-header-end">

    <nav class="nav-tab-wrapper tutor-gradebook-nav-wrapper">
        <a href="<?php echo remove_query_arg('sub_page'); ?>" class="nav-tab-item "><?php _e('Overview', 'tutor-pro'); ?></a>
        <a href="<?php echo add_query_arg(array('sub_page' => 'gradebooks')); ?>" class="nav-tab-item nav-tab-item-active"><?php _e('Gradebook', 'tutor-pro');
        ?></a>
    </nav>

    <div class="tutor_admin_gradebook_list">

		<?php tutor_alert(null, 'success'); ?>

		<?php
		$gradebooks = tutils()->get_gradebooks();

		if (tutils()->count($gradebooks)){
			?>
            <table class="wp-list-table gradebooks-lists">
                <thead>
                <tr>
                    <th><?php _e('Grade Name', 'tutor-pro'); ?></th>
                    <th><?php _e('Grade Point', 'tutor-pro'); ?></th>
                    <th><?php _e('Grade Range %', 'tutor-pro'); ?></th>
                    <th><?php _e('Action', 'tutor-pro'); ?> </th>
                </tr>
                </thead>

				<?php foreach ($gradebooks as $gradebook){
				    $config = maybe_unserialize($gradebook->grade_config);
					?>
                    <tr>
                        <td>
                            <span class="gradename-bg" style="background-color: <?php echo tutils()->array_get('grade_color', $config); ?>;" >
                                <?php echo $gradebook->grade_name; ?>
                            </span>
                        </td>
                        <td><?php echo $gradebook->grade_point; ?></td>
                        <td><?php echo $gradebook->percent_from.'-'.$gradebook->percent_to; ?></td>
                        <td class="gradebook-actions-wrap">

                            <a href="<?php echo add_query_arg(array('sub_page' => 'edit_gradebook', 'gradebook_id' => $gradebook->gradebook_id));
                            ?>" class="gradebook-edit-btn">
                                <i class="tutor-icon-pencil"></i>
                            </a>

                            <a href="<?php echo add_query_arg(array('tutor_action' => 'delete_gradebook', 'gradebook_id' => $gradebook->gradebook_id)); ?>" class="gradebook-delete-btn" onclick="return confirm('<?php _e('Are you Sure?', 'tutor-pro'); ?>')">
                                <i class="tutor-icon-garbage"></i>
                            </a>

                        </td>
                    </tr>
					<?php
				} ?>
            </table>
			<?php
		}else{
		    $install_msg = sprintf(__("No grading system has been defined to manage student grades. %s Import Sample Grade Data %s",
                'tutor-pro'), "<p><button id='import-gradebook-sample-data' class='tutor-button tutor-button-primary'>", "</button></p>");
			tutor_alert($install_msg);
		}
		?>

    </div>

</div>