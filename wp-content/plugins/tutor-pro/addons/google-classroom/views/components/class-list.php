<?php
    $classes = $classroom->get_class_list();

    $column_head='<tr>
                    <td class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">'.__('Select All', 'tutor-pro').'</label>
                        <input id="cb-select-all-1" type="checkbox">
                    </td>
                    <th>'.__('Class Name', 'tutor-pro').'</th>
                    <th>'.__('Import Date', 'tutor-pro').'</th>
                    <th>'.__('Status', 'tutor-pro').'</th>
                    <th>'.__('Class Code', 'tutor-pro').'</th>
                    <th></th>
                </tr>';
?>
<div class="tutor-gc-filter-container">
    <div>
        &nbsp;<br/>
        <select class="regular-text" style="max-width:130px">
            <option value="import">Import</option>
            <option value="publish">Publish</option>
            <option value="trash">Trash</option>
            <option value="delete" title="Only trashed classes can be deleted."><?php _e('Delete Permanently', 'tutor-pro'); ?></option>
            <option value="restore">Restore</option>
        </select>
        <button class="button button-primary" id="tutor_gc_bulk_action_button"><?php _e('Apply', 'tutor-pro'); ?></button>
    </div>
    <div>
        <div>
            Search<br/>
            <input type="text" class="regular-text" id="tutor_gc_search_class"/>
        </div>
    </div>
</div>
<br/>
<table class="wp-list-table widefat fixed striped table-view-list posts google-classroom-class-list">
    <thead>
        <?php echo $column_head; ?>
    </thead>
    <tbody>
        <?php
            foreach($classes as $class){
                
                $is_imported = property_exists($class, 'local_class_post');
                $permalink = $is_imported ? get_permalink($class->local_class_post->ID) : '';
                $edit_link = $is_imported ? get_edit_post_link($class->local_class_post->ID) : '';
                $post_id = $is_imported ? $class->local_class_post->ID : '';
                
                ?>
                    <tr>
                        <th scope="row" class="check-column">			
                            <input type="checkbox" name="google_class_check" value="<?php echo $class->id; ?>"/>
                            <div class="locked-indicator">
                                <span class="locked-indicator-icon" aria-hidden="true"></span>
                                <span class="screen-reader-text"></span>
                            </div>
                        </th>
                        <td class="tutor-gc-title">
                            <a href="<?php echo $class->alternateLink; ?>" target="_blank">
                                <?php echo $class->name; ?>
                            </a>
                        </td>
                        <td>
                            <?php 
                                if($is_imported){
                                    echo get_post_meta($post_id, 'tutor_gc_post_time', true);
                                }
                            ?>
                        </td>
                        <td>
                            <?php 
                                $status = $is_imported ? $class->local_class_post->post_status : 'Not Imported'; 
                                $status = ucfirst($status);
                                $class_ = str_replace(' ', '-', strtolower($status));

                                echo '<span class="tutor-status tutor-status-'.$class_.'">'.$status.'</span>'; 
                            ?>
                        </td>
                        <td class="tutor-gc-code">
                            <?php echo $class->enrollmentCode; ?>  <span class="tutor-icon-copy tutor-gc-copy-text" data-text="<?php echo $class->enrollmentCode; ?>"></span>
                        </td>
                        <td data-class_actions="" class="<?php echo 'class-status-'.$class_; ?>">
                            <button class="button button-primary button-small" data-action="import" data-classroom_id="<?php echo $class->id; ?>">Import</button>
                            <button class="button button-primary button-small class-preview-link" data-action="publish" data-class_post_id="<?php echo $post_id; ?>"><?php _e('Publish', 'tutor-pro'); ?></button>
                            <a href="<?php echo $permalink; ?>" class="button button-primary button-small class-preview-link" data-action="preview"><?php _e('Preview', 'tutor-pro'); ?></a>
                            <a href="<?php echo $edit_link; ?>" class="button button-secondary button-small class-edit-link" data-action="edit"><?php _e('Edit', 'tutor-pro'); ?></a>
                            <button class="button button-primary button-small" data-action="restore" data-class_post_id="<?php echo $post_id; ?>"><?php _e('Restore', 'tutor-pro'); ?></button>
                            <span class="tutor-icon-garbage" data-action="trash" data-class_post_id="<?php echo $post_id; ?>"></span>
                            <span class="tutor-icon-garbage" data-action="delete" data-prompt="Sure to delete permanently?" data-class_post_id="<?php echo $post_id; ?>"></span>
                        </td>
                    </tr>
                <?php
            }
        ?>
    </tbody>
</table>