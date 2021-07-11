<?php
$per_page = get_tutor_option('pagination_per_page', 10);
$current_page = isset( $_GET['paged'] ) ? $_GET['paged'] : 0;
$start =  max( 0,($current_page-1)*$per_page );


$course_id = (int) sanitize_text_field(tutils()->array_get('course_id', $_GET));
$gradebooks = get_generated_gradebooks(array('course_id' => $course_id, 'start' => $start, 'limit' => $per_page));
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Gradebooks', 'tutor-pro'); ?>  </h1>

    <hr class="wp-header-end">

    <nav class="nav-tab-wrapper tutor-gradebook-nav-wrapper">
        <a href="<?php echo remove_query_arg('sub_page'); ?>" class="nav-tab-item nav-tab-item-active"><?php _e('Overview', 'tutor-pro'); ?></a>
        <a href="<?php echo add_query_arg(array('sub_page' => 'gradebooks')); ?>" class="nav-tab-item"><?php _e('Gradebook', 'tutor-pro'); ?></a>
    </nav>

	<?php
	tutor_alert(null, 'warning');
	tutor_alert(null, 'success');

	if ($gradebooks->count){
		?>

        <form id="gradebook-results-filter" method="get">

            <div class="gradebook-overview-header">
                <div class="tutor-flex-row">
                    <div class="tutor-col-6">

                        <input type="hidden" name="tutor_action" value="gradebook_result_list_bulk_actions" />
						<?php tutor_nonce_field(); ?>

                        <div class="tablenav top">
                            <div class="alignleft actions bulkactions">

                                <select name="action" id="bulk-action-selector-top">
                                    <option value="-1"><?php _e('Bulk Actions', 'tutor-pro'); ?></option>
                                    <option value="regenerate_gradebook"><?php _e('Re-generate Gradebook', 'tutor-pro'); ?></option>
                                    <option value="trash">Delete</option>
                                </select>
                                <input type="submit" id="doaction" class="button action" value="Apply">
                            </div>
                        </div>

                    </div>
                    <div class="tutor-col-6">

                        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                        <div class="gradebook-search-form-group">
                            <input type="text" name="s" id="gradebook-search-term" value="<?php echo tutils()->array_get('s', $_REQUEST) ?>" placeholder="<?php _e('Search by course or student name', 'tutor-pro'); ?>">
                            <button type="submit" id="gradebook-search-btn"><i class="tutor-icon-magnifying-glass-1"></i> </button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tutor_admin_gradebook_list">
				<?php
				if (tutils()->count($gradebooks->res)){
					?>
                    <table class="wp-list-table gradebooks-lists">
                        <thead>
                        <tr>
                            <th id="cb" class="manage-column column-cb check-column" width="20">
                                <input id="cb-select-all-1" type="checkbox">
                            </th>
                            <th><?php _e('Student', 'tutor-pro'); ?></th>
                            <th><?php _e('Course', 'tutor-pro'); ?></th>
                            <th><?php _e('Quiz', 'tutor-pro'); ?></th>
                            <th><?php _e('Assignments', 'tutor-pro'); ?></th>
                            <th><?php _e('Final Grade', 'tutor-pro'); ?></th>
                        </tr>
                        </thead>

                        <tbody>
						<?php
						foreach ($gradebooks->res as $gradebook){
							$quiz_grade = get_quiz_gradebook_by_course($gradebook->course_id);
							$assignment_grade = get_assignment_gradebook_by_course($gradebook->course_id);
							?>
                            <tr>
                                <td scope="row" class="check-column">
                                    <input type="checkbox" name="gradebooks_result_ids[]" value="<?php echo $gradebook->gradebook_result_id ?>">
                                </td>
                                <td>
                                    <div class="gradebooks-user-col">
                                        <div class="tutor-flex-row">
                                            <div class="tutor-col-4">
												<?php
												echo tutils()->get_tutor_avatar($gradebook->user_id);
												?>
                                            </div>
                                            <div class="tutor-col-8 user-info-col">
                                                <p class="user-display-name"><?php echo $gradebook->display_name; ?></p>
                                                <p class="gradebook-date"><?php echo date_i18n(get_option('date_format', strtotime($gradebook->update_date)
													)); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <p>
                                        <a href="<?php echo add_query_arg(array('course_id' => $gradebook->course_id)); ?>">
											<?php echo $gradebook->course_title; ?>
                                        </a>
                                    </p>
                                    <p>
										<?php
										echo tutils()->course_progress_status_context($gradebook->course_id, $gradebook->user_id);
										?>, <?php echo sprintf(__('%d quiz, %d assignment', 'tutor-pro'), $gradebook->quiz_count, $gradebook->assignment_count); ?>
                                    </p>
                                </td>

                                <td><?php echo tutor_generate_grade_html($quiz_grade); ?></td>
                                <td><?php echo tutor_generate_grade_html($assignment_grade); ?></td>
                                <td><?php echo tutor_generate_grade_html($gradebook, 'outline'); ?></td>
                            </tr>

							<?php
						} ?>
                        </tbody>
                    </table>
					<?php
				}
				?>

                <div class="gradebook-overview-footer">
                    <div class="tutor-flex-row">
                        <div class="tutor-col gradebook-overview-items-col">
							<?php echo $gradebooks->count.' '.__('Items', 'tutor-pro'); ?>
                        </div>
                        <div class="tutor-col gradebook-overview-pagination-col">
                            <div class="tutor-pagination">
								<?php
								echo paginate_links( array(
									'base' => str_replace( $current_page, '%#%', "admin.php?page=tutor_gradebook&paged=%#%" ),
									'current' => max( 1, $current_page ),
									'total' => ceil($gradebooks->count / $per_page)
								) );
								?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </form>

	<?php } else{
		tutor_alert(__('No enough data to show', 'tutor-pro'));
	} ?>


</div>