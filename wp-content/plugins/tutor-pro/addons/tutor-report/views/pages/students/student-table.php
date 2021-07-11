<?php
if ( ! defined( 'ABSPATH' ) )
exit;
?>
<!-- .report-date-filter -->
<div class="report-date-filter">
    <div class="menu-label"><?php _e('User Search', 'tutor-pro'); ?></div>
    <div class="date-range-input">
        <input type="text" class="tutor-report-search" value="<?php echo $_search; ?>"  placeholder="<?php _e('Search', 'tutor-pro'); ?>" />
        <i class="tutor-icon-magnifying-glass-1 tutor-report-search-action"></i>
    </div>
</div>
<!-- /.report-date-filter -->

<!-- .report-review -->
<div class="tutor-list-wrap student-single">
    <div class="tutor-list-header">
        <div class="heading"><?php _e('Students', 'tutor-pro'); ?></div>
    </div>
    <div class="student-single-wrap">
        <?php
        $per_page = 20;
        $current_page = isset( $_GET['paged'] ) ? $_GET['paged'] : 0;
        $start =  max( 0,($current_page-1)*$per_page );
        $total_items = count(tutor_utils()->get_students(0, 10000, $_search));
        $students_list = tutor_utils()->get_students($start, $per_page, $_search);
        
        if(!empty($students_list)) { ?>
            <table class="tutor-list-table">
                <thead>
                    <tr>
                        <th><?php _e('ID', 'tutor-pro'); ?></th>
                        <th><?php _e('Name', 'tutor-pro'); ?></th>
                        <th><?php _e('Username', 'tutor-pro'); ?></th>
                        <th><?php _e('Email', 'tutor-pro'); ?></th>
                        <th><?php _e('Registered', 'tutor-pro'); ?></th>
                        <th><?php _e('Course Taken', 'tutor-pro'); ?></th>
                        <th><?php _e('Progress', 'tutor-pro'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($students_list as $student) { ?>
                        <tr>
                            <td><?php echo $student->ID; ?></td>
                            <td>
                                <div class="instructor">
                                    <div class="instructor-thumb">
                                        <span class="instructor-icon"><?php echo get_avatar($student->ID, 50); ?></span>
                                    </div>
                                    <div class="instructor-meta">
                                        <span class="instructor-name"><?php echo $student->display_name; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $student->user_login; ?></td>
                            <td><?php echo $student->user_email; ?></td>
                            <td><?php echo date('j M, Y. h:i a', strtotime($student->user_registered)); ?></td>
                            <td><?php echo count(tutor_utils()->get_enrolled_courses_ids_by_user($student->ID)); ?></td>
                            <td>
                                <div class="details-button">
                                    <a class="tutor-report-btn default" href="<?php echo admin_url('admin.php?page=tutor_report&sub_page=students&student_id='.$student->ID); ?>"><?php _e('Details', 'tutor-pro') ?></a>
                                    <a target="_blank" href="<?php echo tutor_utils()->profile_url($student->ID); ?>"><i class="tutor-icon-detail-link"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="no-data-found">
                <img src="<?php echo tutor_pro()->url."addons/tutor-report/assets/images/empty-data.svg"?>" alt="">
                <span><?php _e('No Students Data Found!', 'tutor-pro'); ?></span>
            </div>
        <?php } ?>
    </div>
    <div class="tutor-list-footer ">
        <div class="tutor-report-count">
            <div class="tutor-report-count">
                <?php 
                    if($total_items > 0){
                        printf(__('Items <strong>%s</strong> of <strong>%s</strong> total', 'tutor-pro'), $total_items, count($students_list));
                    }
                ?>
            </div>	
        </div>
        <div class="tutor-pagination">
            <?php
                echo paginate_links( array(
                    'base' => str_replace( 1, '%#%', "admin.php?page=tutor_report&sub_page=students&paged=%#%" ),
                    'current' => max( 1, $current_page ),
                    'total' => ceil($total_items/$per_page)
                ));
            ?>
        </div>
    </div>
</div>
<!-- // .report-review -->
