<?php
if ( ! defined( 'ABSPATH' ) )
exit;

    // single
    $all_data = $wpdb->get_results(
        "SELECT ID, post_title 
        FROM {$wpdb->posts} 
        WHERE post_type ='{$course_type}' 
        AND post_status = 'publish' "
    );
    $current_id = isset($_GET['course_id']) ? $_GET['course_id'] : (isset($all_data[0]) ? $all_data[0]->ID : '');

    $totalCount = (int) $wpdb->get_var(
        "SELECT COUNT(ID) 
        FROM {$wpdb->posts} 
        WHERE post_parent = {$current_id} 
        AND post_type = 'tutor_enrolled';"
    );

    $per_page = 50;
    $total_items = $totalCount;
    $current_page = isset( $_GET['paged'] ) ? $_GET['paged'] : 0;
    $start =  max( 0,($current_page-1)*$per_page );


    $lesson_type = tutor()->lesson_post_type;

    $course_completed = $wpdb->get_results(
        "SELECT ID, post_author, meta.meta_value as order_id from {$wpdb->posts} 
        JOIN {$wpdb->postmeta} meta 
        ON ID = meta.post_id
        WHERE post_type = 'tutor_enrolled' 
        AND meta.meta_key = '_tutor_enrolled_by_order_id'
        AND post_parent = {$current_id} 
        AND post_status = 'completed' 
        ORDER BY ID DESC LIMIT {$start},{$per_page};"
    );

    $complete_data = 0;
    $course_single = array();
    if(is_array($course_completed) && !empty($course_completed)){
        $complete = 0;
        foreach ($course_completed as $data) {
            $var = array();
            $var['order_id'] = $data->order_id;
            $var['post_id'] = $current_id;
            $var['complete'] = tutor_utils()->get_course_completed_percent($current_id, $data->post_author);
            $var['user_id'] = $data->post_author;
            $course_single[] = $var;
            if($var['complete'] == 100){ $complete_data++; }
        }
    } else {
        $complete_data = 0;
    }
    ?>

<div class="tutor-list-wrap tutor-report-course-details">
    <div class="tutor-list-header"><div class="heading"><?php echo get_the_title($current_id); ?></div>
        <div class="header-meta">
            <div class="date">
                <span><?php _e('Created:' ,'tutor-pro'); ?> <strong><?php echo get_the_date('d M, Y', $current_id); ?></strong></span>
                <span><?php _e('Last Update:' ,'tutor-pro'); ?> <strong><?php echo get_the_modified_date('d M, Y', $current_id); ?></strong></span>
            </div>
            <div class="action">
                <a class="tutor-report-btn default" href="<?php echo get_edit_post_link($current_id); ?>" target="_blank"><?php _e('Edit with Builder', 'tutor-pro'); ?></a>
                <a class="tutor-report-btn primary" href="<?php the_permalink($current_id); ?>" target="_blank"><?php _e('View Course', 'tutor-pro'); ?></a>
            </div>
        </div>
    </div>
    <div class="course-details-wrap">
        <div class="course-details-item">
            <div class="info">
                <strong>
                    <?php 
                        $info_lesson = tutor_utils()->get_lesson_count_by_course($current_id);
                        echo $info_lesson;
                    ?>
                </strong>
                <div><?php _e('Lesson', 'tutor-pro'); ?></div>
            </div>
        </div>
        <div class="course-details-item">
            <div class="info">
                <strong>
                    <?php 
                        $info_quiz = '';
                        if($current_id){
                            $info_quiz = $wpdb->get_var(
                                "SELECT COUNT(ID) FROM {$wpdb->posts}
                                WHERE post_parent IN (SELECT ID FROM {$wpdb->posts} WHERE post_type='topics' AND post_parent = {$current_id} AND post_status = 'publish')
                                AND post_type ='tutor_quiz' 
                                AND post_status = 'publish'");
                        }
                        echo $info_quiz;
                    ?>
                </strong>
                <div><?php _e('Total Quiz', 'tutor-pro'); ?></div>
            </div>
        </div>
        <div class="course-details-item">
            <div class="info">
                <strong>
                    <?php 
                        $info_assignment = tutor_utils()->get_assignments_by_course($current_id)->count; 
                        echo $info_assignment;
                    ?>
                </strong>
                <div><?php _e('Assignment', 'tutor-pro'); ?></div>
            </div>
        </div>
        <div class="course-details-item">
            <div class="info">
                <strong>
                    <?php
                        $info_students = tutor_utils()->count_enrolled_users_by_course($current_id);
                        echo $info_students;
                    ?>
                </strong>
                <div><?php _e('Total Students', 'tutor-pro'); ?></div>
            </div>
        </div>
        <div class="course-details-item">
            <div class="info">
                <strong><?php echo $complete_data; ?></strong>
                <div><?php _e('Course Completed', 'tutor-pro'); ?></div>
            </div>
        </div>
        <div class="course-details-item">
            <div class="info">
                <strong>
                    <?php 
                        $total_student = tutor_utils()->count_enrolled_users_by_course($current_id);
                        echo $total_student - $complete_data;
                    ?>
                </strong>
                <div><?php _e('Course Continue', 'tutor-pro'); ?></div>
            </div>
        </div>
        <div class="course-details-item">
            <div class="info">
                <strong>
                    <?php 
                        $course_rating = tutor_utils()->get_course_rating($current_id);
                        tutor_utils()->star_rating_generator($course_rating->rating_avg);
                    ?>
                </strong>
                <div><?php printf('%d (%d %s)',$course_rating->rating_avg ,$course_rating->rating_count, __('Ratings' ,'tutor-pro')); ?></div>
            </div>
        </div>
    </div>

</div>


<div class="tutor-report-graph-earnings">
    <div class="tutor-list-wrap tutor-report-graph">
        <div class="tutor-report-graph-wrap">
            
            <div class="heading"><?php _e('Sales Graph', 'tutor-pro'); ?></div>

            <?php 
            	$sub_page = 'this_year';
                $course_id = false;
                if ( ! empty($_GET['time_period'])){
                    $sub_page = sanitize_text_field($_GET['time_period']);
                }
                if ( ! empty($_GET['course_id'])){
                    $course_id = (int) sanitize_text_field($_GET['course_id']);
                }
                if ( ! empty($_GET['date_range_from']) && ! empty($_GET['date_range_to'])){
                    $sub_page = 'date_range';
                }
            
                include $view_page.$page."/graph/{$sub_page}.php";            
            ?>
        </div>

    </div>
    <div class="tutor-list-wrap tutor-report-earnings">
        <div class="tutor-list-header tutor-report-single-graph">
            <div class="heading"><?php _e('Earnings', 'tutor-pro'); ?></div>
        </div>
        <div class="tutor-report-earnings-wrap">
            <div class="earnings-item">
                <div class="icon"><i class="tutor-icon-total-earning"></i></div>
                <div class="text">
                    <div>
                        <?php
                            $total_price = $wpdb->get_var(
                                "SELECT SUM(meta.meta_value) FROM {$wpdb->posts} AS posts
                                LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id
                                LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
                                WHERE meta.meta_key = '_order_total'
                                AND posts.post_type = 'shop_order'
                                AND meta2.meta_key = '_tutor_order_for_course_id_{$current_id}'
                                AND posts.post_status IN ( '" . implode( "','", array( 'wc-completed' ) ) . "' )"
                            );
        
                            if (function_exists('wc_price')) {
                                echo wc_price($total_price);
                            } else {
                                echo '$'.($total_price ? $total_price : 0);
                            }
                        ?>
                    </div>
                    <div><?php _e('Total Earning' ,'tutor-pro'); ?></div>
                </div>
            </div>
            <div class="earnings-item">
                <div class="icon"><i class="tutor-icon-total-discount"></i></div>
                <div class="text">
                    <div>
                        <?php
                        $discount_price = $wpdb->get_var( "SELECT SUM(meta.meta_value) FROM {$wpdb->posts} AS posts
                            LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id
                            LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
                            WHERE meta.meta_key = '_cart_discount'
                            AND posts.post_type = 'shop_order'
                            AND meta2.meta_key = '_tutor_order_for_course_id_{$current_id}'
                            AND posts.post_status IN ( '" . implode( "','", array( 'wc-completed' ) ) . "' )" );

                            if (function_exists('wc_price')) {
                                echo wc_price($discount_price);
                            } else {
                                echo '$'.($discount_price ? $discount_price : 0);
                            }
                        ?>
                    </div>
                    <div><?php _e('Total Discount' ,'tutor-pro'); ?></div>
                </div>
            </div>
            <div class="earnings-item">
                <div class="icon"><i class="tutor-icon-refund"></i></div>
                <div class="text">
                    <div>
                        <?php
                        $refunded_price = $wpdb->get_var( "SELECT SUM(meta.meta_value) FROM {$wpdb->posts} AS posts
                            LEFT JOIN {$wpdb->posts} AS posts2 ON posts.ID = posts2.post_parent
                            LEFT JOIN {$wpdb->postmeta} AS meta ON posts2.ID = meta.post_id
                            WHERE meta.meta_key = '_refund_amount'
                            AND posts.post_type = 'shop_order'
                            AND posts.post_status IN ( '" . implode( "','", array( 'wc-refunded' ) ) . "' )" );

                            if (function_exists('wc_price')) {
                                echo wc_price($refunded_price);
                            } else {
                                echo '$'.($refunded_price ? $refunded_price : 0);
                            }
                        ?>
                    </div>
                    <div><?php _e('Refund' ,'tutor-pro'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="tutor-list-wrap tutor-report-students">
    <div class="tutor-list-header"><div class="heading"><?php _e('Students' ,'tutor-pro'); ?></div></div>
    <div class="tutor-list-data">
        <?php
        $per_student = 10;
        $student_page = isset( $_GET['lp'] ) ? $_GET['lp'] : 0;
        $start_student =  max( 0,($student_page-1)*$per_student );

        $student_items =$wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} AS posts
            WHERE posts.post_type = 'tutor_enrolled'
            AND posts.post_status = 'completed'
            AND posts.post_parent = {$current_id}"
        );

        $student_list = $wpdb->get_results( "SELECT ID, post_author, post_date, post_parent FROM {$wpdb->posts} AS posts
            WHERE posts.post_type = 'tutor_enrolled'
            AND posts.post_status = 'completed'
            AND posts.post_parent = {$current_id}
            ORDER BY ID DESC LIMIT {$start_student},{$per_student}");
        
        if(!empty($student_list)) {
        ?>
            <table class="tutor-list-table">
                <tr>
                    <th><?php _e('ID', 'tutor-pro'); ?></th>
                    <th><?php _e('Name', 'tutor-pro'); ?></th>
                    <th><?php _e('Email', 'tutor-pro'); ?></th>
                    <th><?php _e('Enroll Date', 'tutor-pro'); ?></th>
                    <th><?php _e('Lesson', 'tutor-pro'); ?></th>
                    <th><?php _e('Progress', 'tutor-pro'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($student_list as $student) { ?>
                    <tr>
                        <td><?php echo $student->ID; ?></td>
                        <td>
                            <div class="instructor">
                                <div class="instructor-thumb">
                                    <?php $user_info = get_userdata($student->post_author); ?>
                                    <span class="instructor-icon"><?php echo get_avatar($user_info->ID, 50); ?></span>
                                </div>
                                <div class="instructor-meta">
                                    <span class="instructor-name">
                                        <span><?php echo $user_info->display_name; ?> </span> <a target="_blank" href="<?php echo tutor_utils()->profile_url($user_info->ID); ?>"><i class="tutor-icon-detail-link"></i></a>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $user_info->user_email; ?></td>
                        <td><?php echo date('j M, Y', strtotime($student->post_date)); ?></td>
                        <td><strong><?php echo tutor_utils()->get_completed_lesson_count_by_course($current_id, $user_info->ID); ?></strong>/<span><?php echo $info_lesson; ?><span></td>
                        <td>
                            <div class="course-progress">
                                <?php $percentage = tutor_utils()->get_course_completed_percent($current_id, $user_info->ID); ?>
                                <span class="course-percentage" style="--percent: <?php echo $percentage; ?>%;"></span>
                                <span><?php echo $percentage; ?>%</span>   
                            </div>
                        </td>
                        <td><a class="tutor-report-btn default" target="_blank" href="<?php echo admin_url('admin.php?page=tutor_report&sub_page=students&student_id='.$user_info->ID); ?>"><?php _e('Details', 'tutor-pro'); ?></a></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <div class="no-data-found">
                <img src="<?php echo tutor_pro()->url."addons/tutor-report/assets/images/empty-data.svg"?>" alt="">
                <span><?php _e('No Students Data Found!', 'tutor-pro'); ?></span>
            </div>
        <?php } ?>
    </div>
    <div class="tutor-list-footer">
        <div class="tutor-report-count">
            <div class="tutor-report-count">
                <?php
                    if($student_items > 0){
                        printf( __('Items <strong> %s </strong> of <strong> %s </strong> total'), count($student_list), $student_items ); 
                    }
                ?>
            </div>	
        </div>
        <div class="tutor-pagination">
            <?php
                echo paginate_links( array(
                    'base' => "admin.php?page=tutor_report&sub_page=courses&course_id=".$current_id."&lp=%#%",
                    'current' => max( 1, $student_page ),
                    'total' => ceil($student_items/$per_student)
                ) );
            ?>           
        </div>
    </div>
</div>


<div class="tutor-list-wrap tutor-report-instructors">
    <div class="tutor-list-header"><div class="heading"><?php _e('Instructors' ,'tutor-pro'); ?></div></div>
    <div class="tutor-list-data">
        <?php $instructors = tutor_utils()->get_instructors_by_course($current_id); ?>
        <?php if(!empty($instructors)) { ?>
            <table class="tutor-list-table">
                <tr>
                    <th><?php _e('ID', 'tutor-pro'); ?></th>
                    <th><?php _e('Name', 'tutor-pro'); ?></th>
                    <th><?php _e('Rating', 'tutor-pro'); ?></th>
                    <th><?php _e('Total Courses', 'tutor-pro'); ?></th>
                    <th><?php _e('Total Students', 'tutor-pro'); ?></th>
                    <th></th>
                </tr>
                <?php 
                $count = 0;
                foreach ($instructors as $instructor) { 
                    $count++;
                    $authorTag = '';
                    $instructor_crown_src = tutor()->url.'assets/images/crown.svg';
                    if (get_post_field('post_author', $instructor->ID) == $instructor->ID) {
                        $authorTag = '<img src="'.$instructor_crown_src.'" />';
                    }
                    $user_info = get_userdata($instructor->ID);
                    ?>
                    <tr>
                        <td><?php echo $instructor->ID; ?> </td>
                        <td>
                            <div class="instructor">
                                <div class="instructor-thumb">
                                    <span class="instructor-icon"><?php echo get_avatar($instructor->ID, 50); ?></span>
                                </div>
                                <div class="instructor-meta">
                                    <span class="instructor-name">
                                        <?php echo $instructor->display_name.' '.$authorTag; ?>
                                    </span>
                                    <span class="instructor-email"><?php echo $user_info->user_email; ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $rating = tutor_utils()->get_instructor_ratings($instructor->ID);
                            tutor_utils()->star_rating_generator($rating->rating_avg);
                            ?>
                            <span class="instructor-rating"><?php printf( __('%s (%s Ratings)', 'tutor-pro'), $rating->rating_avg, $rating->rating_count ); ?></span>
                        </td>
                        <td><?php echo tutor_utils()->get_course_count_by_instructor($instructor->ID); ?></td>
                        <td><?php echo tutor_utils()->get_total_students_by_instructor($instructor->ID); ?></td>
                        <td>
                            <a class="tutor-report-btn default" target="_blank" href="<?php echo tutor_utils()->profile_url($instructor->ID); ?>"><?php _e('View Profile', 'tutor-pro'); ?> 
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <div class="no-data-found">
                <img src="<?php echo tutor_pro()->url."addons/tutor-report/assets/images/empty-data.svg"?>" alt="">
                <span><?php _e('No Instructor Data Found!', 'tutor-pro'); ?></span>
            </div>
        <?php } ?>
    </div>
</div>


<div class="tutor-list-wrap tutor-report-reviews">
    <div class="tutor-list-header"><div class="heading"><?php _e('Reviews' ,'tutor-pro'); ?></div></div>
    <div class="tutor-list-data">
        <?php
            $count = 0;
            $per_review = 10;
            $review_page = isset( $_GET['rp'] ) ? $_GET['rp'] : 0;
            $review_start =  max( 0,($review_page-1)*$per_review );
            $review_items = count(tutor_utils()->get_course_reviews($current_id));
            $total_reviews = tutor_utils()->get_course_reviews($current_id, $review_start, $per_review);

        if(!empty($total_reviews)){
            ?>
            <table class="tutor-list-table">
                <tr>
                    <th><?php _e('No', 'tutor-pro'); ?> </th>
                    <th><?php _e('Name', 'tutor-pro'); ?> </th>
                    <th><?php _e('Date', 'tutor-pro'); ?> </th>
                    <th><?php _e('Rating & Feedback', 'tutor-pro'); ?> </th>
                </tr>
                <?php
                    foreach ($total_reviews as $review) { $count++; ?>
                    <tr>
                        <td><?php echo $count; ?></td>
                        <td>
                            <div class="instructor">
                                <div class="instructor-thumb">
                                    <span class="instructor-icon"><?php echo get_avatar($review->user_id, 50); ?></span>
                                </div>
                                <div class="instructor-meta">
                                    <span class="instructor-name"><?php echo $review->display_name; ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="dates">
                                <span><?php echo date('j M, Y', strtotime($review->comment_date)); ?></span><br>
                                <span><?php echo date('h:i a', strtotime($review->comment_date)); ?></td></span>
                            </div>
                        <td>
                            <div class="ratings-wrap">
                                <div class="ratings">
                                    <?php tutor_utils()->star_rating_generator($review->rating); ?>
                                    <span><?php echo $review->rating; ?></span>
                                </div>
                                <div class="review">
                                    <?php echo $review->comment_content; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <div class="no-data-found">
                <img src="<?php echo tutor_pro()->url.'addons/tutor-report/assets/images/empty-data.svg'; ?>" alt="">
                <span><?php _e('No Review Data Found!', 'tutor-pro'); ?></span>
            </div>
        <?php } ?>
    </div>
    <div class="tutor-list-footer">
        <div class="tutor-report-count">
            <?php 
                if($review_items){
                    printf( __('Items <strong> %s </strong> of <strong> %s </strong> total'), count($total_reviews), $review_items );
                }
            ?>
        </div>
        <div class="tutor-pagination">
            <?php
                echo paginate_links( array(
                    'base' => str_replace( $review_page, '%#%', "admin.php?page=tutor_report&sub_page=courses&course_id=".$current_id."&rp=%#%" ),
                    'current' => max( 1, $review_page ),
                    'total' => ceil($review_items/$per_review)
                ) );
            ?>            
        </div>
    </div>
</div>